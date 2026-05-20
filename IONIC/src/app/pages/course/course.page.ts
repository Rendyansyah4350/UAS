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
    private router: Router
  ) {
    // Tangkap keyword dari Home
    const navigation = this.router.getCurrentNavigation();
    if (navigation?.extras.state?.['keyword']) {
      this.keywordPencarian = navigation.extras.state['keyword'];
    }
  }

  ngOnInit() {
    this.loadData();
  }

  ionViewWillEnter() {
    const currentNav = this.router.getCurrentNavigation();
    if (currentNav?.extras.state && currentNav.extras.state['keyword']) {
      this.keywordPencarian = currentNav.extras.state['keyword'];
      this.fungsiCariKursus();
    }
  }

  loadData() {
    this.isLoading = true;

    // 1. Ambil data wishlist dari database localhost/server lek
    this.courseService.ambilDaftarWishlist().subscribe({
      next: (wishlistRes: any) => {
        // Proteksi jika data wishlist kosong, buat jadi array murni
        const wishlistIds = (wishlistRes && wishlistRes.data ? wishlistRes.data : [])
                            .map((item: any) => item.course_id);

        // 2. Baru ambil data seluruh katalog kursus
        this.courseService.getCourses().subscribe({
          next: (res: any) => {
            // Jalankan proteksi: cek apakah API Laravel membungkus dalam 'data' atau langsung array polosan
            const rawCourses = res.data ? res.data : (Array.isArray(res) ? res : []);

            // 🌟 SINKRONISASI DATABASE: Menyuntikkan properti is_wishlist ke array katalog
            this.allCourses = rawCourses.map((course: any) => {
              return {
                ...course,
                is_wishlist: wishlistIds.includes(course.id)
              };
            });

            // 🛠️ PERBAIKAN UTAMA: Langsung set data master ke list utama yang dibaca HTML Ivan
            this.listCourses = [...this.allCourses];
            this.isLoading = false;
            console.log('Data API Katalog & Wishlist Berhasil Disinkronkan Lek:', this.listCourses);

            // Kondisi filter pencarian operan dari Home / searchbar lokal
            if (this.keywordPencarian && this.keywordPencarian.trim() !== '') {
              this.fungsiCariKursus();
            } else if (this.kategoriAktif !== 'Semua') {
              // Jika user sedang berada di kategori selain 'Semua', jalankan filter kategorinya
              this.listCourses = this.allCourses.filter((course: any) => {
                const kategoriDatabase = course.category || '';
                return kategoriDatabase.toLowerCase() === this.kategoriAktif.toLowerCase();
              });
              this.eksekusiFilterSort();
            } else {
              // Jika kategorinya 'Semua' dan tidak ada pencarian, jalankan sorting default bawaan Ivan
              this.eksekusiFilterSort();
            }
          },
          error: (error) => {
            console.error('Gagal ambil data katalog', error);
            this.isLoading = false;
          },
        });
      },
      error: (error) => {
        console.error('Gagal ambil data wishlist untuk sinkronisasi', error);
        // Keamanan: Jika API wishlist bermasalah/401, katalog harus TETAP tampil (jangan dibikin blank)
        this.courseService.getCourses().subscribe({
          next: (res: any) => {
            this.allCourses = res.data ? res.data : (Array.isArray(res) ? res : []);
            this.listCourses = [...this.allCourses];
            this.isLoading = false;
            this.eksekusiFilterSort();
          },
          error: (err) => {
            this.isLoading = false;
            console.error('Gagal total memuat katalog:', err);
          }
        });
      }
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
      }
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
    let dataHasil = this.kategoriAktif === 'Semua' 
      ? [...this.allCourses] 
      : this.allCourses.filter(c => (c.category || '').toLowerCase() === this.kategoriAktif.toLowerCase());

    // 2. Filter lanjutan berdasarkan Keyword
    const keyword = this.keywordPencarian.toLowerCase().trim();
    if (keyword) {
      dataHasil = dataHasil.filter(c => 
        (c.title || '').toLowerCase().includes(keyword) || 
        (c.description || '').toLowerCase().includes(keyword)
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