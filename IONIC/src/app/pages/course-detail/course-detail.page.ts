import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router'; 
import { CourseService } from '../../services/course.service';
import { Browser } from '@capacitor/browser'; 

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

  // 🟢 VARIABEL BARU UNTUK KONTROL MODAL RATING PREMIUM LEK
  isModalRatingOpen: boolean = false;
  ratingInput: number = 5; // Default bintang 5 mendatar mendatar

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

  getDetail(id: string) {
    const targetCourseId = Number(id);

    this.courseService.getCourseById(id).subscribe(
      (res: any) => {
        if (res.success) {
          this.course = res.data;
          console.log('Detail Kursus:', this.course);
          this.cdr.detectChanges();

          this.cekStatusWishlistUser(targetCourseId);
          this.ambilKontenKurikulum(targetCourseId);

          this.courseService.getMyEnrollments().subscribe(
            (enrollRes: any) => {
              if (enrollRes.success && enrollRes.data) {
                const riwayatBeli = enrollRes.data.find(
                  (item: any) => Number(item.course_id) === targetCourseId,
                );

                if (riwayatBeli) {
                  this.paymentStatus = String(riwayatBeli.status).trim().toLowerCase();
                  this.paymentUrl = riwayatBeli.payment_url;
                } else {
                  this.paymentStatus = 'none';
                }
                this.cdr.detectChanges();
              }
            },
            (enrollError) => {
              if (enrollError.status === 400) {
                this.paymentStatus = 'success';
                this.cdr.detectChanges();
              }
            },
          );
        }
      },
      (error) => {
        console.error('Gagal ambil detail:', error);
      },
    );
  }

  // 🟢 METODE BARU: Ubah kuantitas bintang interaktif pas di-tap
  setRatingBintang(bintang: number) {
    this.ratingInput = bintang;
    this.cdr.detectChanges();
  }

  // 🟢 METODE BARU: Kirim gabungan bintang & teks ke server cPanel
  kirimUlasanRatingLive() {
    console.log(`Mengirim rating bintang ${this.ratingInput} untuk course ID: ${this.course.id}`);

    this.courseService
      .kirimRatingCourse(this.course.id, this.ratingInput)
      .subscribe(
        (res: any) => {
          // 🟢 FIX FRONTLINE: Pastikan balikan pesan dari server diutamakan, atau pakai fallback formal lek!
          alert(res.message || 'Terima kasih, rating bintang berhasil disimpan.');
          this.isModalRatingOpen = false;
          this.getDetail(String(this.course.id)); 
        },
        (error: any) => {
          console.error('Gagal kirim rating:', error);
          alert(error.error?.message || 'Gagal menyimpan rating, silakan coba lagi.');
        },
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
      },
    );
  }

  bukaInvoiceXendit() {
    if (this.paymentUrl) {
      try {
        Browser.open({ url: this.paymentUrl, windowName: '_blank' });
      } catch (e) {
        window.open(this.paymentUrl, '_blank');
      }
    } else {
      alert('Link pembayaran tidak ditemukan.');
    }
  }

  klikMateri(contentId: number) {
    if (this.paymentStatus !== 'success') {
      alert('Materi ini masih terkunci! Silakan selesaikan pembayaran terlebih dahulu.');
      if (this.paymentStatus === 'pending') {
        this.bukaInvoiceXendit();
      }
    } else {
      this.router.navigate([`/course/${this.course.id}/watch/${contentId}`]);
    }
  }

  masukKelas() {
    this.router.navigate(['/tabs/my-learning']);
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
      },
    );
  }

  cekStatusWishlistUser(targetCourseId: number) {
    this.courseService.ambilDaftarWishlist().subscribe(
      (res: any) => {
        if (res.success) {
          const listWishlist = res.data || [];
          this.isWishlist = listWishlist.some(
            (item: any) => Number(item.course_id) === targetCourseId,
          );
          this.cdr.detectChanges();
        }
      },
    );
  }

  enroll() {
    this.loadingBeli = true;
    this.courseService.buyCourse(this.course.id).subscribe(
      async (res: any) => {
        this.loadingBeli = false;
        if (res.success) {
          if (res.data.payment_url) {
            this.paymentStatus = 'pending';
            this.paymentUrl = res.data.payment_url;
            this.cdr.detectChanges();
            alert('Invoice Xendit berhasil dibuat, membuka halaman pembayaran...');
            try {
              await Browser.open({ url: res.data.payment_url, windowName: '_blank' });
            } catch (browserError) {
              window.open(res.data.payment_url, '_blank');
            }
            this.getDetail(String(this.course.id));
          } else {
            this.paymentStatus = 'success';
            this.cdr.detectChanges();
            alert('Berhasil mendaftar kursus gratis!');
            this.masukKelas();
          }
        }
      },
      (error) => {
        this.loadingBeli = false;
        if (error.status === 400) {
          this.paymentStatus = 'success';
          this.cdr.detectChanges();
          alert('Anda sudah terdaftar di kursus ini. Selamat belajar!');
        }
      },
    );
  }
}