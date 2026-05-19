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
    const navigation = this.router.getCurrentNavigation();
    if (navigation?.extras.state && navigation.extras.state['keyword']) {
      this.keywordPencarian = navigation.extras.state['keyword'];
      console.log('Mantap! Dapat operan keyword dari Home:', this.keywordPencarian);
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
    this.courseService.getCourses().subscribe({
      next: (res: any) => {
        this.allCourses = res.data || [];
        this.listCourses = this.allCourses;
        this.isLoading = false;
        console.log('Data API Katalog Sukses:', this.listCourses);

        if (this.keywordPencarian) {
          this.fungsiCariKursus();
        }
      },
      error: (error) => {
        console.error('Gagal ambil data katalog', error);
        this.isLoading = false;
      },
    });
  }

  // 🟢 FUNGSI BARU: MENANGANI TOGGLE WISHLIST LIVE KE SERVERS
  toggleFavorit(event: Event, course: any) {
    event.stopPropagation(); // Biar pas diklik ikon hatinya, gak malah mental masuk ke Detail Course

    // Balik status di lokal dulu biar UI terasa super responsif tanpa delay (Instant Feedback)
    course.is_wishlist = !course.is_wishlist;

    // Tembak ke API Laravel cPanel kamu lek
    this.courseService.toggleWishlistServer(course.id).subscribe({
      next: (res: any) => {
        console.log('Sukses memperbarui status wishlist di server:', res);
      },
      error: (err) => {
        console.error('Gagal sinkronisasi wishlist ke server, kembalikan status:', err);
        // Jika internet putus atau server error, kembalikan status hati ke semula
        course.is_wishlist = !course.is_wishlist;
      }
    });
  }

  pilihKategori(namaKategori: string) {
    this.kategoriAktif = namaKategori;
    this.keywordPencarian = '';
    this.filterAktif = 'default';

    if (namaKategori === 'Semua') {
      this.listCourses = this.allCourses;
      return;
    }

    this.listCourses = this.allCourses.filter((course: any) => {
      const kategoriDatabase = course.category || '';
      return kategoriDatabase.toLowerCase() === namaKategori.toLowerCase();
    });
  }

  goToDetail(id: any) {
    this.navCtrl.navigateForward(['/course-detail', id]);
  }

  fungsiCariKursus() {
    console.log('User sedang mencari:', this.keywordPencarian);

    let dataDasar = this.allCourses;
    if (this.kategoriAktif !== 'Semua') {
      dataDasar = this.allCourses.filter((course: any) => {
        const kategoriDatabase = course.category || '';
        return (
          kategoriDatabase.toLowerCase() === this.kategoriAktif.toLowerCase()
        );
      });
    }

    if (!this.keywordPencarian.trim()) {
      this.listCourses = dataDasar;
      this.eksekusiFilterSort();
      return;
    }

    this.listCourses = dataDasar.filter((course: any) => {
      const judulKursus = course.title || '';
      return judulKursus
        .toLowerCase()
        .includes(this.keywordPencarian.toLowerCase());
    });

    this.eksekusiFilterSort();
  }

  eksekusiFilterSort() {
    if (this.filterAktif === 'default') return;

    this.listCourses.sort((a: any, b: any) => {
      const hargaA = parseFloat(a.price) || 0;
      const hargaB = parseFloat(b.price) || 0;
      const ratingA = parseFloat(a.rating) || 0;
      const ratingB = parseFloat(b.rating) || 0;

      if (this.filterAktif === 'harga-termurah') {
        return hargaA - hargaB;
      } else if (this.filterAktif === 'harga-termahal') {
        return hargaB - hargaA;
      } else if (this.filterAktif === 'rating-tertinggi') {
        return ratingB - ratingA;
      }
      return 0;
    });
  }
}