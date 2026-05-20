import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router'; // 🟢 TAMBAHAN: Import Router buat pindah halaman belajar
import { CourseService } from '../../services/course.service';
import { Browser } from '@capacitor/browser'; // 🟢 TAMBAHAN: Import Capacitor Browser buat buka invoice Xendit

@Component({
  selector: 'app-course-detail',
  templateUrl: './course-detail.page.html',
  styleUrls: ['./course-detail.page.scss'],
  standalone: false,
})
export class CourseDetailPage implements OnInit {
  course: any = {};
  contents: any[] = []; // 🟢 TAMBAHAN: Wadah untuk menampung data kurikulum asli dari API

  // 🟢 TAMBAHAN: Variabel baru untuk mengontrol UI dinamis di HTML
  paymentStatus: string = 'none'; // nilainya bisa: 'none', 'pending', 'success'
  paymentUrl: string = ''; // Menyimpan link invoice Xendit dari database
  isWishlist: boolean = false; // Status wishlist aktif/tidak
  loadingBeli: boolean = false; // 🟢 TAMBAHAN: State loading biar tombol ga di-spam pas loading invoice

  constructor(
    private route: ActivatedRoute,
    private router: Router, // 🟢 TAMBAHAN: Inject Router
    private courseService: CourseService,
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.getDetail(id);
    }
  }

  // 🟢 LIFECYCLE IONIC: Memastikan status di-refresh setiap kali user bolak-balik page
  ionViewWillEnter() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.getDetail(id);
    }
  }

  getDetail(id: string) {
    // 🟢 AKURAT: Kunci ID dari parameter URL sejak awal agar terhindar dari race condition 'undefined'
    const targetCourseId = Number(id);

    this.courseService.getCourseById(id).subscribe(
      (res: any) => {
        if (res.success) {
          this.course = res.data;
          console.log('Detail Kursus:', this.course);

          // Ambal data kurikulum materi pembelajaran
          this.ambilKontenKurikulum(targetCourseId);

          // Ambil status pendaftaran menggunakan targetCourseId dari URL yang sudah pasti ada
          this.courseService.getMyEnrollments().subscribe(
            (enrollRes: any) => {
              if (enrollRes.success && enrollRes.data) {
                const riwayatBeli = enrollRes.data.find(
                  (item: any) => Number(item.course_id) === targetCourseId,
                );

                if (riwayatBeli) {
                  this.paymentStatus = String(riwayatBeli.status)
                    .trim()
                    .toLowerCase();
                  this.paymentUrl = riwayatBeli.payment_url;
                  console.log(
                    'Status Pembayaran Ditemukan:',
                    this.paymentStatus,
                  );
                } else {
                  this.paymentStatus = 'none';
                }
              }
            },
            (enrollError) => {
              console.log(
                'API Enrollments merespons error. Menerapkan fallback sukses...',
              );
              // 🔥 BYPASS FALLBACK: Jika Laravel melempar eror status 400 karena relasi sudah ada
              if (enrollError.status === 400) {
                this.paymentStatus = 'success';
              }
            },
          );

          this.cekStatusWishlistUser();
        }
      },
      (error) => {
        console.error('Gagal ambil detail:', error);
      },
    );
  }

  // 🟢 TAMBAHAN: Fungsi untuk mengambil data kurikulum asli lewat API backend
  ambilKontenKurikulum(courseId: number) {
    this.courseService.getCourseContents(courseId).subscribe(
      (res: any) => {
        if (res.success) {
          this.contents = res.data; // Array konten masuk ke variabel
          console.log('Konten Kurikulum Asli:', this.contents);
        }
      },
      (error) => {
        console.log(
          'Materi dikunci karena belum bayar / status pending:',
          error,
        );
      },
    );
  }

  // 🟢 TAMBAHAN: Fungsi mencocokkan apakah user sudah beli kursus ini atau belum
  cekStatusPembayaranUser() {
    const id = this.route.snapshot.paramMap.get('id');
    if (!id) return;

    const targetCourseId = Number(id);

    this.courseService.getMyEnrollments().subscribe(
      (res: any) => {
        if (res.success && res.data) {
          const riwayatBeli = res.data.find(
            (item: any) => Number(item.course_id) === targetCourseId,
          );

          if (riwayatBeli) {
            this.paymentStatus = String(riwayatBeli.status)
              .trim()
              .toLowerCase();
            this.paymentUrl = riwayatBeli.payment_url;
            console.log(
              'Status Pembayaran Terdeteksi Sah:',
              this.paymentStatus,
            );
          } else {
            this.paymentStatus = 'none';
          }
        }
      },
      (error) => {
        if (error.status === 400) {
          this.paymentStatus = 'success';
        }
      },
    );
  }

  // 🟢 TAMBAHAN: Fungsi mengecek apakah kursus ini ada di daftar wishlist server
  cekStatusWishlistUser() {
    this.courseService.ambilDaftarWishlist().subscribe((res: any) => {
      if (res.success) {
        // Cek apakah id kursus ini ada di dalam array wishlist dari database
        this.isWishlist = res.data.some(
          (item: any) => item.course_id === this.course.id,
        );
      }
    });
  }

  // 🟢 LOGIKA UTAMA TOMBOL DAFTAR (MAKIN KICKASS)
  enroll() {
    console.log('User menekan tombol enroll untuk kursus:', this.course.title);
    this.loadingBeli = true; // 🟢 Aktifkan efek loading

    this.courseService.buyCourse(this.course.id).subscribe(
      async (res: any) => {
        this.loadingBeli = false; // 🟢 Matikan loading jika sukses
        if (res.success) {
          if (res.data.payment_url) {
            this.paymentStatus = 'pending';
            this.paymentUrl = res.data.payment_url;

            alert(
              'Invoice Xendit berhasil dibuat, membuka halaman pembayaran...',
            );

            try {
              await Browser.open({
                url: res.data.payment_url,
                windowName: '_blank',
              });
            } catch (browserError) {
              console.log(
                'Capacitor Browser mentok di localhost laptop, mengalihkan ke window.open...',
              );
              window.open(res.data.payment_url, '_blank');
            }

            this.getDetail(String(this.course.id));
          } else {
            this.paymentStatus = 'success';
            alert('Berhasil mendaftar kursus gratis!');
            this.getDetail(String(this.course.id));
            this.masukKelas();
          }
        }
      },
      (error) => {
        this.loadingBeli = false; // 🟢 Matikan loading jika eror
        console.error('Gagal melakukan pendaftaran:', error);

        if (
          error.status === 400 &&
          (error.error?.message?.includes('sudah') ||
            error.error?.message?.includes('bought') ||
            error.error?.message?.includes('success'))
        ) {
          this.paymentStatus = 'success'; // 🟢 Paksa UI lokal berubah jadi sukses lunas
          this.ambilKontenKurikulum(this.course.id); // 🟢 Tarik ulang modul biar gembok kebuka
          alert('Anda sudah terdaftar di kursus ini. Selamat belajar!');
        } else if (
          error.status === 400 ||
          error.error?.message?.includes('pembayaran')
        ) {
          alert(
            error.error?.message ||
              'Silahkan selesaikan pembayaran yang sudah ada.',
          );
        } else {
          alert(error.error?.message || 'Gagal mendaftar kursus.');
        }
      },
    );
  }

  // 🟢 TAMBAHAN: Fungsi tombol "Bayar Sekarang" jika status transaksi masih pending
  async bukaInvoiceXendit() {
    if (this.paymentUrl) {
      console.log('Membuka ulang link invoice Xendit:', this.paymentUrl);
      try {
        await Browser.open({ url: this.paymentUrl, windowName: '_blank' });
      } catch (e) {
        console.log('Capacitor mentok di laptop, melempar via window.open...');
        window.open(this.paymentUrl, '_blank');
      }
    } else {
      alert('Link pembayaran tidak ditemukan, silahkan hubungi admin.');
    }
  }

  // 🟢 TAMBAHAN: Fungsi ketika list materi diklik (Validasi gembok)
  klikMateri(contentId: number) {
    if (this.paymentStatus !== 'success') {
      alert(
        'Materi ini masih terkunci! Silakan selesaikan pembayaran terlebih dahulu.',
      );
      if (this.paymentStatus === 'pending') {
        this.bukaInvoiceXendit(); // Jika pending, tawarin buka link Xendit lagi
      }
    } else {
      console.log('Membuka materi ID:', contentId);
      this.router.navigate([`/course/${this.course.id}/watch/${contentId}`]);
    }
  }

  // 🟢 TAMBAHAN: Fungsi tombol "Mulai Belajar" jika status sudah sukses lunas
  masukKelas() {
    console.log('User masuk ke ruang belajar kursus:', this.course.id);
    this.router.navigate([`/course/${this.course.id}/learning`]);
  }

  // 🟢 TAMBAHAN: Logika klik tombol jantung untuk tambah/lepas wishlist dari server
  toggleWishlist() {
    this.courseService.toggleWishlistServer(this.course.id).subscribe(
      (res: any) => {
        if (res.success) {
          this.isWishlist = !this.isWishlist; // balikkan status warna jantung di UI
          console.log(res.message);
        }
      },
      (error) => {
        console.error('Gagal toggle wishlist:', error);
      },
    );
  }
}
