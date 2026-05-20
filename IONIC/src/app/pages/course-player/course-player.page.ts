import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { CourseService } from '../../services/course.service'; // 🟢 TAMBAHAN: Import service lu mbut

@Component({
  selector: 'app-course-player',
  templateUrl: './course-player.page.html',
  styleUrls: ['./course-player.page.scss'],
  standalone: false,
})
export class CoursePlayerPage implements OnInit {
  courseId: string | null = '';
  isCompleted: boolean = false;

  // 🟢 TAMBAHAN: Wadah penampung data asli dari backend Laravel
  courseDetail: any = {}; // Untuk judul kursus & instruktur
  contents: any[] = []; // Untuk list bab/kurikulum materi pembelajaran
  videoAktifUrl: string = ''; // Menyimpan link video yang sedang disetel student
  loading: boolean = false;

  constructor(
    private route: ActivatedRoute,
    private toastCtrl: ToastController,
    private courseService: CourseService, // 🟢 TAMBAHAN: Inject CourseService
  ) {}

  ngOnInit() {
    this.courseId = this.route.snapshot.paramMap.get('id');
    if (this.courseId) {
      this.muatDataKelasAsli(this.courseId);
    }
  }

  // 🟢 TAMBAHAN: Fungsi nembak API server EduVan
  muatDataKelasAsli(id: string) {
    this.loading = true;

    // 1. Tarik info nama course asli
    this.courseService.getCourseById(id).subscribe(
      (res: any) => {
        if (res.success) {
          this.courseDetail = res.data;
          console.log('Detail Kelas Player Terbuka:', this.courseDetail);
        }
      },
      (err) => console.error('Gagal memuat info kelas di player:', err),
    );

    // 2. Tarik isi materi yang gemboknya udah kebuka
    this.courseService.getCourseContents(Number(id)).subscribe(
      (res: any) => {
        this.loading = false;
        if (res.success) {
          this.contents = res.data || [];
          console.log('List Kurikulum Asli:', this.contents);

          // Otomatis play video pertama kalau kontennya ada isinya mbut
          if (this.contents.length > 0) {
            this.putarMateri(this.contents[0]);
          }
        }
      },
      (err) => {
        this.loading = false;
        console.error('Materi gagal dimuat karena belum lunas/error:', err);
      },
    );
  }

  // 🟢 TAMBAHAN: Fungsi ketika list materi kurikulum di-klik student
  putarMateri(materi: any) {
    console.log('Memutar Video Materi:', materi.title);
    // Menyesuaikan properti video dari response database lu (biasanya video_url atau video)
    this.videoAktifUrl = materi.video_url || materi.video || '';
  }

  // Fungsi dipicu saat tombol Selesai diklik
  async markAsComplete() {
    this.isCompleted = true;

    const toast = await this.toastCtrl.create({
      message: 'Materi berhasil diselesaikan!',
      duration: 2000,
      color: 'success',
    });
    await toast.present();

    console.log('Materi ID:', this.courseId, 'ditandai selesai.');
    // Nanti di sini lu tinggal hubungkan ke endpoint post progress belajar milik Laravel lu tot
  }
}
