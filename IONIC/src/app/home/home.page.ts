import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth';
import { SearchService } from '../services/search'; // Pastikan path ini benar
import { CourseService } from '../services/course.service'; // Memastikan import CourseService tersedia

@Component({
  selector: 'app-beranda',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
  standalone: false,
})
export class HomePage implements OnInit {
  namaUser: string = 'User';
  keywordPencarian: string = '';
  isLoading: boolean = true;
  kursusTersaring: any[] = [];
  unreadCount: number = 0; // Variabel penampung jumlah notifikasi yang belum dibaca

  constructor(
    private router: Router,
    private authService: AuthService,
    private searchService: SearchService,
    private courseService: CourseService, // Menggunakan CourseService di sini
  ) {}

  ngOnInit() {
    this.ambilNamaUserLive();
    this.muatKursusDariHosting();

    // 🟢 TAMBAHAN BARU: Otomatis memuat ulang jumlah angka lonceng jika ada sinyal perubahan dari service
    this.courseService.notifChanged$.subscribe((berubah: boolean) => {
      if (berubah) {
        this.muatJumlahNotifikasi();
      }
    });
  }

  // Menggunakan ionViewWillEnter agar angka notifikasi otomatis ter-refresh setiap kali kembali ke beranda
  ionViewWillEnter() {
    this.muatJumlahNotifikasi();
  }

  // Fungsi mengambil jumlah notifikasi unread dari backend Laravel melalui CourseService
  muatJumlahNotifikasi() {
    this.courseService.getNotificationsCount().subscribe({
      next: (res: any) => {
        if (res && res.status === 'success') {
          this.unreadCount = res.unread_count; // Memasukkan angka unread_count dari API
        }
      },
      error: (err: any) => {
        console.error('Gagal memuat jumlah notifikasi:', err);
      },
    });
  }

  ambilNamaUserLive() {
    this.authService.currentUser$.subscribe((user: any) => {
      if (user) {
        const namaLengkap = user.name || user.nama || user.fullname || 'User';
        this.namaUser = namaLengkap.split(' ')[0];
      } else {
        this.namaUser = 'User';
      }
    });
  }

  muatKursusDariHosting() {
    this.isLoading = true;
    this.authService.getCoursesFromServer().subscribe({
      next: (res: any) => {
        const dataMentah = res.data || [];
        this.kursusTersaring = dataMentah
          .filter((k: any) => Number(k.rating || 0) > 0)
          .sort(
            (a: any, b: any) => Number(b.rating || 0) - Number(a.rating || 0),
          )
          .slice(0, 3);
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Gagal:', err);
        this.isLoading = false;
      },
    });
  }

  goToDetail(id?: any) {
    if (id) {
      this.router.navigate(['/course-detail', id]);
    } else {
      this.router.navigate(['/course-detail']);
    }
  }

  // FUNGSI UNTUK IKLAN (Menghilangkan error Anda)
  goToBannerDetail() {
    this.router.navigate(['/tabs/course']);
  }

  fungsiCariKursus() {
    const keyword = this.keywordPencarian.trim();
    // Kirim keyword ke Service
    this.searchService.changeKeyword(keyword);

    this.router.navigate(['/tabs/course']);
    this.keywordPencarian = '';
  }

  goToNotif() {
    this.router.navigate(['/notifications']);
  }
  goToCourse() {
    this.router.navigateByUrl('/tabs/course');
  }

  // FIX UTAMA: Fungsi pembaca gambar default berdasarkan kategori kursus
  getDefaultImage(category: string): string {
    if (!category) return 'assets/icon/computer-science.jpeg';

    const kat = category.toLowerCase();
    if (
      kat.includes('computer') ||
      kat.includes('science') ||
      kat.includes('coding')
    ) {
      return 'assets/icon/computer-science.jpeg';
    } else if (
      kat.includes('microsoft') ||
      kat.includes('office') ||
      kat.includes('excel')
    ) {
      return 'assets/icon/microsoft-office.jpeg';
    }

    // Jika ada kategori lain diluar dua itu, arahkan ke salah satu sebagai default utama
    return 'assets/icon/computer-science.jpeg';
  }

  // FIX TAMBAHAN: Fungsi penangkap error tag img di HTML
  handleImageError(event: any, category: string) {
    event.target.src = this.getDefaultImage(category);
  }
  bukaChatCS() {
    const pesan = 'Halo Admin EduVan, saya ingin bertanya mengenai kursus...';
    const nomorWA = '628978665982'; // Ganti dengan nomor CS beneran lek
    window.open(
      `https://wa.me/${nomorWA}?text=${encodeURIComponent(pesan)}`,
      '_blank',
    );
  }
}
