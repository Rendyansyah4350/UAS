import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-course',
  templateUrl: './course.page.html',
  styleUrls: ['./course.page.scss'],
  standalone: false,
})
export class CoursePage implements OnInit {
  allCourses: any[] = [];
  listCourses: any[] = [];
  kategoriAktif: string = 'Semua';
  isLoading: boolean = true;
  keywordPencarian: string = '';
  filterAktif: string = 'default';

  constructor(
    private navCtrl: NavController,
    private courseService: CourseService,
    private router: Router,
  ) {
    // Tangkap keyword dari Home
    const navigation = this.router.getCurrentNavigation();
    if (navigation?.extras.state?.['keyword']) {
      this.keywordPencarian = navigation.extras.state['keyword'];
    }
  }

  ngOnInit() {
    this.loadData();

    // Dengerin sinyal perubahan dari page detail secara live
    this.courseService.wishlistChanged$.subscribe((berubah) => {
      if (berubah) {
        console.log('📢 Ada sinyal masuk! Refresh data katalog utama...');
        this.loadData();
      }
    });
  }

  // 🟢 LIVE REFRESH FIX: Lifecycle Ionic ini akan memaksa data ditarik ulang dari Laravel
  // setiap kali user memindahkan tab atau kembali ke halaman Semua Kursus.
  ionViewWillEnter() {
    console.log(
      'Menyegarkan katalog kursus dan status wishlist dari database live...',
    );
    this.loadData();
  }

  loadData() {
    this.isLoading = true;

    // 1. Ambil daftar wishlist teranyar dari server Laravel kamu lek
    this.courseService.ambilDaftarWishlist().subscribe({
      next: (wishlistRes: any) => {
        // Ambil array ID course yang dibungkus di dalam data pivot relasi Laravel
        const userWishlistIds = (wishlistRes.data || []).map((item: any) =>
          Number(item.course_id),
        );

        // 2. Baru load data katalog utama
        this.courseService.getCourses().subscribe({
          next: (res: any) => {
            const dataKatalog = res.data || [];

            // 3. 🟢 NYALAKAN LOVE JIKA ID NYA COCOK DENGAN ARRAY WISHLIST
            this.allCourses = dataKatalog.map((course: any) => {
              return {
                ...course,
                is_wishlist: userWishlistIds.includes(Number(course.id)),
              };
            });

            this.isLoading = false;

            // Picu filter pencarian & kategori biar jalan sinkron
            setTimeout(() => {
              this.fungsiCariKursus();
            }, 100);
          },
          error: (error) => {
            console.error('Gagal ambil data katalog', error);
            this.isLoading = false;
          },
        });
      },
      error: (err) => {
        console.error('Gagal sinkronisasi data wishlist awal', err);

        // 🟢 AMAN: Fallback kalau wishlist bermasalah, tetep tampilin katalog tanpa crash
        this.courseService.getCourses().subscribe({
          next: (res: any) => {
            this.allCourses = res.data || [];
            this.isLoading = false;
            setTimeout(() => this.fungsiCariKursus(), 100);
          },
          error: (katalogErr) => {
            console.error('Katalog ikut bermasalah:', katalogErr);
            this.isLoading = false;
          },
        });
      },
    });
  }

  toggleFavorit(event: Event, course: any) {
    event.stopPropagation();
    course.is_wishlist = !course.is_wishlist;

    this.courseService.toggleWishlistServer(course.id).subscribe({
      next: (res: any) => console.log('Wishlist updated:', res),
      error: (err) => {
        console.error('Gagal sinkronisasi wishlist:', err);
        course.is_wishlist = !course.is_wishlist;
      },
    });
  }

  pilihKategori(namaKategori: string) {
    this.kategoriAktif = namaKategori;
    // Jika ganti kategori, kita tetap pertahankan keyword yang ada
    // agar user tidak perlu mengetik ulang
    this.fungsiCariKursus();
  }

  fungsiCariKursus() {
    // 1. Filter dasar berdasarkan Kategori
    let dataHasil =
      this.kategoriAktif === 'Semua'
        ? [...this.allCourses]
        : this.allCourses.filter(
            (c) =>
              (c.category || '').toLowerCase() ===
              this.kategoriAktif.toLowerCase(),
          );

    // 2. Filter lanjutan berdasarkan Keyword
    const keyword = this.keywordPencarian.toLowerCase().trim();
    if (keyword) {
      dataHasil = dataHasil.filter(
        (c) =>
          (c.title || '').toLowerCase().includes(keyword) ||
          (c.description || '').toLowerCase().includes(keyword),
      );
    }

    this.listCourses = dataHasil;
    this.eksekusiFilterSort();
  }

  eksekusiFilterSort() {
    if (this.filterAktif === 'default') return;

    this.listCourses.sort((a: any, b: any) => {
      const hargaA = parseFloat(a.price) || 0;
      const hargaB = parseFloat(b.price) || 0;
      const ratingA = parseFloat(a.rating) || 0;
      const ratingB = parseFloat(b.rating) || 0;

      if (this.filterAktif === 'harga-termurah') return hargaA - hargaB;
      if (this.filterAktif === 'harga-termahal') return hargaB - hargaA;
      if (this.filterAktif === 'rating-tertinggi') return ratingB - ratingA;
      return 0;
    });
  }

  goToDetail(id: any) {
    this.navCtrl.navigateForward(['/course-detail', id]);
  }
}
