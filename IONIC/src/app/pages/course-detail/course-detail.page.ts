import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CourseService } from '../../services/course.service';
import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';

@Component({
  selector: 'app-course-detail',
  templateUrl: './course-detail.page.html',
  styleUrls: ['./course-detail.page.scss'],
  standalone: false,
})
export class CourseDetailPage implements OnInit {
  course: any = {};
  contents: any[] = [];
  paymentStatus: string = 'none';
  paymentUrl: string = '';
  isWishlist: boolean = false;
  loadingBeli: boolean = false;

  // Variabel Kontrol Modal Rating Premium Kustom
  isModalRatingOpen: boolean = false;
  ratingInput: number = 5;
  isModalTransferOpen: boolean = false;
  fileGambarBukti: File | null = null;
  namaFileTerpilih: string = '';
  loadingUpload: boolean = false;
  imagePreviewUrl: string | undefined = undefined;

  // 🟢 VARIABEL BARU UNTUK KONTROL OVERLAY ION-ALERT PREMIUM KUSTOM
  isSuccessAlertOpen: boolean = false;
  isErrorAlertOpen: boolean = false;
  alertMessageCustom: string = '';

  // 🟢 KONFIGURASI HANDLER TOMBOL ALERT AGAR TIDAK ERROR DI HTML PARSER ANGULAR
  alertSuccessButtons = [
    {
      text: 'Selesai',
      role: 'confirm',
      handler: () => {
        this.tutupAlertKustom();
      },
    },
  ];

  alertErrorButtons = [
    {
      text: 'Coba Lagi',
      role: 'cancel',
      handler: () => {
        this.tutupAlertKustom();
      },
    },
  ];

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private courseService: CourseService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.getDetail(id);
    }
  }

  ionViewWillEnter() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.getDetail(id);
    }
  }

  // 🟢 DIUBAH TOTAL: Memisahkan jalur data Kursus & Enrollments agar data kelas TIDAK HILANG lek!
  getDetail(id: string) {
    const targetCourseId = Number(id);

    // Jalur Pipa 1: Ambil data detail kursus utama dari API (Gembok detail aman)
    this.courseService.getCourseById(id).subscribe({
      next: (res: any) => {
        if (res.success) {
          this.course = res.data;
          console.log('Detail Kursus Sukses Dimuat:', this.course);
          this.cdr.detectChanges();
          this.cekStatusWishlistUser(targetCourseId);
          this.ambilKontenKurikulum(targetCourseId);
        }
      },

      error: (error) => {
        console.error('Gagal ambil detail:', error);
      },
    });

    // Jalur Pipa 2: Cek status pendaftaran student (Independent pipeline)
    this.courseService.getMyEnrollments().subscribe({
      next: (enrollRes: any) => {
        if (enrollRes.success && enrollRes.data) {
          const riwayatBeli = enrollRes.data.find(
            (item: any) => Number(item.course_id) === targetCourseId
          );

          if (riwayatBeli) {
            // Mengambil status string asli database cPanel ('pending' atau 'success')
            this.paymentStatus = String(riwayatBeli.status)
              .trim()
              .toLowerCase();
          } else {
            this.paymentStatus = 'none';
          }
          this.cdr.detectChanges();
        }
      },

      error: (enrollError) => {
        if (enrollError.status === 400) {
          this.paymentStatus = 'success';
          this.cdr.detectChanges();
        }
      },
    });
  }

  // =========================================================================
  // 🟢 LOGIKA BARU: SEKERANJANG FUNGSI TRANSFER MULTIPART MANUAL (NON-XENDIT)
  // =========================================================================
  bukaModalUploadTransfer() {
    this.isModalTransferOpen = true;
    this.fileGambarBukti = null;
    this.namaFileTerpilih = '';
    this.imagePreviewUrl = undefined; // Reset preview pas modal dibuka lek
    this.cdr.detectChanges();
  }
  handleRefresh(event: CustomEvent) {
    console.log('User melakukan refresh halaman...');

    // Jalankan fungsi load data bawaan halaman Anda
    this.ngOnInit();

    // 🟢 EFEK TRANSISI HALUS: Beri jeda sedikit sebelum menutup spinner
    setTimeout(() => {
      if (event && event.target) {
        (event.target as any).complete();
      }
    }, 800); // Roda berputar akan selesai dengan transisi fade-out yang rapi
  }

  // 🔥 ROMBAK TOTAL: Ganti input file lama jadi pemicu dialog Kamera/Galeri native Android lek!
  async pilihFileBuktiTransfer() {
    try {
      const image = await Camera.getPhoto({
        quality: 50,
        allowEditing: false,
        source: CameraSource.Prompt,
        resultType: CameraResultType.Uri,
        promptLabelHeader: 'Pilih Bukti Pembayaran',
        promptLabelPhoto: 'Ambil dari Galeri',
        promptLabelPicture: 'Gunakan Kamera',
      });
      this.imagePreviewUrl = image.webPath;
      this.namaFileTerpilih = `bukti_transfer_${Date.now()}.jpg`;
      // Proses konversi aman terkendali:
      const response = await fetch(image.webPath!);
      const blob = await response.blob(); // Sudah diperbaiki mbut!
      this.fileGambarBukti = new File([blob], this.namaFileTerpilih, {
        type: 'image/jpeg',
      });
      this.cdr.detectChanges();
    } catch (error) {
      console.log('User membatalkan pemilihan media.', error);
    }
  }

  kirimBuktiTransferKeServer() {
    if (!this.fileGambarBukti) {
      this.alertMessageCustom =
        'Harap pilih file gambar bukti transfer terlebih dahulu!';
      this.isErrorAlertOpen = true;
      this.cdr.detectChanges();
      return;
    }
    this.loadingUpload = true;
    this.cdr.detectChanges();
    // Membungkus parameter ke objek FormData biner lek

    const formData = new FormData();
    formData.append('course_id', String(this.course.id));
    // 🟢 FIX SAKTI: Ubah key dari 'payment_proof' menjadi 'proof_of_payment' biar match sama Laravel Ivan
    formData.append('proof_of_payment', this.fileGambarBukti);
    this.courseService.buyCourseManual(formData).subscribe({
      next: (res: any) => {
        this.loadingUpload = false;
        this.isModalTransferOpen = false;

        // Memasukkan response teks kustom asli backend ke overlay kustom baru lek
        this.alertMessageCustom =
          res.message ||
          'Bukti transfer sukses dikirim! Mohon tunggu konfirmasi Admin.';
        this.isSuccessAlertOpen = true;

        this.paymentStatus = 'pending'; // Tombol otomatis berubah jadi "Menunggu Verifikasi Admin"
        this.getDetail(String(this.course.id));
        this.cdr.detectChanges();
      },

      error: (err) => {
        this.loadingUpload = false;
        console.error('Gagal upload bukti:', err);

        // Memasukkan response pesan error validasi asli backend ke overlay kustom lek
        this.alertMessageCustom =
          err.error?.message ||
          'Gagal mengirim bukti pembayaran, periksa format file Anda.';
        this.isErrorAlertOpen = true;
        this.cdr.detectChanges();
      },
    });
  }

  // 🟢 FUNGSI DISMISS OVERLAY ALERT UNTUK RESET STATE KUSTOM
  tutupAlertKustom() {
    this.isSuccessAlertOpen = false;
    this.isErrorAlertOpen = false;
    this.cdr.detectChanges();
  }

  // LOGIKA FITUR ULASAN & RATING KUSTOM
  setRatingBintang(bintang: number) {
    this.ratingInput = bintang;
    this.cdr.detectChanges();
  }

  kirimUlasanRatingLive() {
    console.log(
      `Mengirim rating bintang ${this.ratingInput} untuk course ID: ${this.course.id}`
    );
    this.courseService
      .kirimRatingCourse(this.course.id, this.ratingInput)
      .subscribe(
        (res: any) => {
          this.alertMessageCustom =
            res.message || 'Terima kasih, rating bintang berhasil disimpan.';
          this.isSuccessAlertOpen = true;
          this.isModalRatingOpen = false;
          this.getDetail(String(this.course.id));
        },

        (error: any) => {
          console.error('Gagal kirim rating:', error);
          this.alertMessageCustom =
            error.error?.message ||
            'Gagal menyimpan rating, silakan coba lagi.';
          this.isErrorAlertOpen = true;
          this.cdr.detectChanges();
        }
      );
  }

  ambilKontenKurikulum(courseId: number) {
    this.courseService.getCourseContents(courseId).subscribe(
      (res: any) => {
        if (res.success) {
          this.contents = res.data;
          this.cdr.detectChanges();
        }
      },
      (error) => {
        console.log('Materi dikunci:', error);
      }
    );
  }

  // 🟢 FIX TOTAL: Diarahkan langsung masuk ke halaman nonton course-player bawa ID video
  klikMateri(contentId: number) {
    if (this.paymentStatus !== 'success') {
      this.alertMessageCustom =
        'Materi ini masih terkunci! Silakan selesaikan pendaftaran dan tunggu verifikasi Admin.';
      this.isErrorAlertOpen = true;
      this.cdr.detectChanges();
    } else {
      console.log('Navigasi klikMateri bawa ID Kursus:', this.course.id);
      this.router.navigate(['/course-player', this.course.id]);
    }
  }

  masukKelas(courseId: any) {
    if (this.contents && this.contents.length > 0) {
      console.log('Navigasi masukKelas bawa ID Kursus:', this.course.id);
      // 🔥 FIX: Kirim ID Kursus ke URL player agar data header & video tidak tertukar
      this.router.navigate(['/course-player', this.course.id]);
    } else {
      this.alertMessageCustom =
        'Kelas ini sudah aktif, namun admin belum mengunggah modul video untuk kelas ini.';
      this.isErrorAlertOpen = true;
      this.cdr.detectChanges();
    }
  }

  toggleWishlist() {
    if (!this.course || !this.course.id) return;
    this.isWishlist = !this.isWishlist;
    this.courseService.toggleWishlistServer(this.course.id).subscribe(
      (res: any) => {
        this.courseService.wishlistChanged$.next(true);
        if (res.success && res.is_wishlist !== undefined) {
          this.isWishlist = res.is_wishlist;
          this.cdr.detectChanges();
        }
      },
      (error) => {
        this.isWishlist = !this.isWishlist;
        this.courseService.wishlistChanged$.next(true);
        this.cdr.detectChanges();
      }
    );
  }

  cekStatusWishlistUser(targetCourseId: number) {
    this.courseService.ambilDaftarWishlist().subscribe((res: any) => {
      if (res.success) {
        const listWishlist = res.data || [];
        this.isWishlist = listWishlist.some(
          (item: any) => Number(item.course_id) === targetCourseId
        );
        this.cdr.detectChanges();
      }
    });
  }
}
