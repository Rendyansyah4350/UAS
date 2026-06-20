import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser'; // WAJIB IMPORT INI

@Component({
  selector: 'app-course-player',
  templateUrl: './course-player.page.html',
  styleUrls: ['./course-player.page.scss'],
  standalone: false,
})
export class CoursePlayerPage implements OnInit {
  courseId: string | null = '';
  isCompleted: boolean = false;

  courseDetail: any = {};
  contents: any[] = [];
  videoAktifUrl: string = '';

  //TAMBAHAN: Variabel khusus untuk menampung URL YouTube yang sudah lolos sensor security Angular
  safeVideoUrl: SafeResourceUrl | null = null;
  loading: boolean = false;

  //TAMBAHAN BARU: Untuk melacak ID materi mana yang sedang diputar/aktif oleh student
  activeContentId: number | null = null;

  constructor(
    private route: ActivatedRoute,
    private toastCtrl: ToastController,
    private courseService: CourseService,
    private sanitizer: DomSanitizer
  ) {}

  ngOnInit() {
    this.courseId = this.route.snapshot.paramMap.get('id');
    if (this.courseId) {
      this.muatDataKelasAsli(this.courseId);
    }
  }

  muatDataKelasAsli(id: string) {
    this.loading = true;
    this.courseService.getCourseById(id).subscribe(
      (res: any) => {
        if (res.success) {
          this.courseDetail = res.data;
        }
      },
      (err) => console.error('Gagal memuat info kelas:', err)
    );

    this.courseService.getCourseContents(Number(id)).subscribe(
      (res: any) => {
        this.loading = false;
        if (res.success) {
          this.contents = res.data || [];

          // Auto-play video pertama kalau ada isinya
          if (this.contents.length > 0) {
            this.putarMateri(this.contents[0]);
          }
        }
      },
      (err) => {
        this.loading = false;
        console.error('Materi gagal dimuat:', err);
      }
    );
  }

  // 🟢 LOGIKA UTAMA SAKTI UNTUK YOUTUBE:
  putarMateri(materi: any) {
    console.log('--- DEBUG MATERI YANG DIKLIK ---');
    console.log(materi);

    // 🟢 SAKTI: Ambil ID materi yang aktif dan cocokkan status is_completed dari backend Laravel (1 = Selesai, 0 = Belum)
    this.activeContentId = materi.id;
    this.isCompleted = materi.is_completed === 1;

    // 🟢 FIX: Tambahin materi.content_url di sini biar kebaca sama Angular
    this.videoAktifUrl =
      materi.content_url || materi.video_url || materi.video || '';

    console.log('Link video yang dideteksi:', this.videoAktifUrl);

    if (this.videoAktifUrl) {
      let embedUrl = this.videoAktifUrl;

      // Parsing link YouTube biar jadi format embed
      if (this.videoAktifUrl.includes('watch?v=')) {
        const videoId = this.videoAktifUrl.split('watch?v=')[1].split('&')[0];
        embedUrl = `https://www.youtube.com/embed/${videoId}`;
      } else if (this.videoAktifUrl.includes('youtu.be/')) {
        const videoId = this.videoAktifUrl.split('youtu.be/')[1].split('?')[0];
        embedUrl = `https://www.youtube.com/embed/${videoId}`;
      } else if (!this.videoAktifUrl.includes('embed')) {
        embedUrl = `https://www.youtube.com/embed/${this.videoAktifUrl}`;
      }

      this.safeVideoUrl =
        this.sanitizer.bypassSecurityTrustResourceUrl(embedUrl);
    } else {
      console.warn('field url videonya masih kosong nih!');
      this.safeVideoUrl = null;
    }
  }

  // 🟢 SINKRONISASI LIVE DATABASE: Mengirim data progress asli ke Laravel
  async markAsComplete() {
    if (!this.courseId || !this.activeContentId) {
      console.warn('ID Kursus atau ID Materi kosong!');
      return;
    }

    // 🟢 TENTUKAN AKSI DINAMIS: Jika aslinya true (sudah selesai), kita kirim nilai status 0 (artinya mau dicancel)
    // Jika aslinya false (belum selesai), kita kirim nilai status 1 (artinya mau ditandai selesai)
    const statusKirim = this.isCompleted ? 0 : 1;

    this.courseService
      .saveProgress(Number(this.courseId), this.activeContentId, statusKirim) // 🟢 FIX: Sekarang mengirim 3 argumen sempurna!
      .subscribe(
        async (res: any) => {
          if (res.success) {
            // Balik status variabel lokal di frontend biar tombolnya langsung berubah warna/teks
            this.isCompleted = statusKirim === 1;

            // Tembakkan sinyal ke BehaviorSubject biar halaman My Learning ikut nambah/berkurang persentasenya secara live
            this.courseService.progressChanged$.next(true);

            const toast = await this.toastCtrl.create({
              message:
                statusKirim === 1
                  ? 'Materi berhasil diselesaikan!'
                  : 'Selesai materi dibatalkan!',
              duration: 2000,
              color: statusKirim === 1 ? 'success' : 'warning', // Kalau cancel warnanya kuning/oranye manis
            });
            await toast.present();

            // AUTOMATIS REFRESH: Memuat ulang kurikulum biar list centang hijau langsung sinkron
            this.muatDataKelasAsli(this.courseId!);
          }
        },
        (err) => {
          console.error('Gagal menyimpan progress ke server Laravel:', err);
        }
      );
  }
}
