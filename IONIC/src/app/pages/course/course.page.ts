import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';
import { Router } from '@angular/router';
import { SearchService } from '../../services/search'; // Tambahan

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
    private searchService: SearchService // Tambahan
  ) {
    const navigation = this.router.getCurrentNavigation();
    if (navigation?.extras.state?.['keyword']) {
      this.keywordPencarian = navigation.extras.state['keyword'];
    }
  }

  ngOnInit() {
    // 🔥 PENGHUBUNG: Dengarkan perubahan keyword dari service
    this.searchService.currentKeyword.subscribe(keyword => {
      if (keyword) {
        this.keywordPencarian = keyword;
      }
    });

    this.loadData();

    this.courseService.wishlistChanged$.subscribe((berubah) => {
      if (berubah) {
        this.loadData();
      }
    });
  }

  ionViewWillEnter() {
    this.loadData();
  }

  // Penting: Kosongkan search di service saat meninggalkan halaman course
  ionViewWillLeave() {
    this.searchService.changeKeyword('');
  }

  loadData() {
    this.isLoading = true;
    this.courseService.ambilDaftarWishlist().subscribe({
      next: (wishlistRes: any) => {
        const userWishlistIds = (wishlistRes.data || []).map((item: any) => Number(item.course_id));
        this.courseService.getCourses().subscribe({
          next: (res: any) => {
            const dataKatalog = res.data || [];
            this.allCourses = dataKatalog.map((course: any) => ({
              ...course,
              is_wishlist: userWishlistIds.includes(Number(course.id)),
            }));
            this.isLoading = false;
            setTimeout(() => this.fungsiCariKursus(), 100);
          },
          error: (error) => { this.isLoading = false; }
        });
      },
      error: (err) => {
        this.courseService.getCourses().subscribe({
          next: (res: any) => {
            this.allCourses = res.data || [];
            this.isLoading = false;
            setTimeout(() => this.fungsiCariKursus(), 100);
          }
        });
      },
    });
  }

  toggleFavorit(event: Event, course: any) {
    event.stopPropagation();
    course.is_wishlist = !course.is_wishlist;
    this.courseService.toggleWishlistServer(course.id).subscribe();
  }

  pilihKategori(namaKategori: string) {
    this.kategoriAktif = namaKategori;
    this.fungsiCariKursus();
  }

  fungsiCariKursus() {
    let dataHasil = this.kategoriAktif === 'Semua'
        ? [...this.allCourses]
        : this.allCourses.filter((c) => (c.category || '').toLowerCase() === this.kategoriAktif.toLowerCase());

    const keyword = this.keywordPencarian.toLowerCase().trim();
    if (keyword) {
      dataHasil = dataHasil.filter((c) =>
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