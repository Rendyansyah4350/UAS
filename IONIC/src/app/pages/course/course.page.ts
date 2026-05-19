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

  loadData() {
    this.isLoading = true;
    this.courseService.getCourses().subscribe({
      next: (res: any) => {
        this.allCourses = res.data || [];
        this.isLoading = false;
        
        // 🟢 FIX: Gunakan setTimeout agar filter berjalan 
        // setelah data 100% masuk ke variabel allCourses
        setTimeout(() => {
          this.fungsiCariKursus();
        }, 100);
      },
      error: (error) => {
        console.error('Gagal ambil data katalog', error);
        this.isLoading = false;
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