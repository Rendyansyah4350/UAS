import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'app-wishlist',
  templateUrl: './wishlist.page.html',
  styleUrls: ['./wishlist.page.scss'],
  standalone: false,
})
export class WishlistPage implements OnInit {
  wishlistCourses: any[] = [];
  isLoading: boolean = true;

  constructor(
    private navCtrl: NavController,
    private courseService: CourseService,
  ) {}

  ngOnInit() {
    // Dipicu saat pertama kali komponen di-load
  }

  // 🟢 PENTING: Gunakan lifecycle Ionic ini agar setiap kali user masuk ke tab Wishlist,
  // halamannya otomatis me-refresh dan mengambil data terbaru dari database cPanel.
  ionViewWillEnter() {
    this.loadWishlistData();
  }

  loadWishlistData() {
    this.isLoading = true;
    this.courseService.ambilDaftarWishlist().subscribe({
      next: (res: any) => {
        // Ambil array data wishlist dari API Laravel kamu lek
        this.wishlistCourses = res.data || [];
        this.isLoading = false;
        console.log('Data Wishlist Server Sukses:', this.wishlistCourses);
      },
      error: (err) => {
        console.error('Gagal memuat data wishlist dari live server:', err);
        this.isLoading = false;
      },
    });
  }

  // Fungsi untuk membatalkan atau menghapus kursus dari wishlist langsung di tempat
  hapusDariWishlist(event: Event, courseId: number, index: number) {
    event.stopPropagation(); // Mencegah agar tidak malah masuk ke detail page

    // Hapus instan dari tampilan UI lokal dulu biar gesit tanpa nunggu server delay
    this.wishlistCourses.splice(index, 1);

    // Kirim perintah toggle ke database server untuk menghapus status favoritnya
    this.courseService.toggleWishlistServer(courseId).subscribe({
      next: (res: any) => {
        console.log('Sukses menghapus item wishlist di server');
      },
      error: (err) => {
        console.error(
          'Gagal sinkronisasi hapus wishlist ke database, memuat ulang...',
          err,
        );
        this.loadWishlistData(); // reload ulang data asli jika koneksi bermasalah
      },
    });
  }

  goToDetail(id: any) {
    // Memastikan ID yang dilempar ke URL adalah ID Kursus murni, bukan ID pivot wishlist
    console.log('Navigasi ke Detail Kursus ID:', id);
    this.navCtrl.navigateForward([`/course-detail/${id}`]);
  }
}
