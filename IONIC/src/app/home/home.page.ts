import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { AuthService } from '../services/auth';

@Component({
  selector: 'app-beranda',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
  standalone: false,
})
export class HomePage implements OnInit {
  // 1. Inisialisasi variabel untuk nama user dengan nilai default
  namaUser: string = 'User';
  keywordPencarian: string = '';

  daftarKursusMaster: any[] = [];
  kursusTersaring: any[] = [];

  // Indikator loading saat mengambil data dari internet
  isLoading: boolean = true;

  constructor(
    private router: Router,
    private authService: AuthService,
  ) {}

  ngOnInit() {
    // 2. Jalankan fungsi penangkap data saat halaman dashboard dimuat
    this.ambilNamaUser();
    this.muatKursusDariHosting();
  }

  // Fungsi untuk mengambil data kursus secara live dari database hosting cPanel
  // --- AMBIL DATA DARI LIVE SERVERS CPANEL + FILTER RATING > 0 + SORTING 3 TERTINGGI ---
  muatKursusDariHosting() {
    this.isLoading = true;

    this.authService.getCoursesFromServer().subscribe({
      next: (res: any) => {
        // Ambil data mentah array dari key 'data'
        const dataMentah = res.data || [];

        // 🟢 1. FILTER: Hanya ambil kursus yang beneran punya rating (di atas 0)
        const dataLolosFilter = dataMentah.filter((kursus: any) => {
          return Number(kursus.rating || 0) > 0;
        });

        // 🟢 2. SORT: Urutkan dari rating tertinggi ke terendah
        const dataTerurut = dataLolosFilter.sort((a: any, b: any) => {
          return Number(b.rating || 0) - Number(a.rating || 0);
        });

        // 🟢 3. SLICE: Potong array agar hanya mengambil maksimal 3 item teratas
        this.daftarKursusMaster = dataTerurut.slice(0, 3);

        // Samakan ke penampung filter search
        this.kursusTersaring = this.daftarKursusMaster;
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Koneksi ke server cPanel gagal bre:', err);
        this.isLoading = false;
      },
    });
  }

  // Logika filter search bar berdasarkan kolom 'title' hasil fillable Laravel
  fungsiCariKursus() {
    console.log('User lagi nyari kursus:', this.keywordPencarian);

    // Jika search bar dikosongkan, kembalikan semua list kursus master
    if (!this.keywordPencarian.trim()) {
      this.kursusTersaring = this.daftarKursusMaster;
      return;
    }

    // Filter daftar berdasarkan kecocokan judul/title (lowercase agar aman dari huruf kapital)
    this.kursusTersaring = this.daftarKursusMaster.filter((kursus) => {
      const judulKursus = kursus.title || '';
      return judulKursus
        .toLowerCase()
        .includes(this.keywordPencarian.toLowerCase());
    });
  }

  // Fungsi untuk mengambil data login dari database hosting yang tersimpan di device
  ambilNamaUser() {
    // 1. Coba ambil dari beberapa kemungkinan key local storage
    const dataLokal =
      localStorage.getItem('user') ||
      localStorage.getItem('userData') ||
      localStorage.getItem('name');

    if (dataLokal) {
      try {
        // Jika data berupa objek JSON
        const userObjek = JSON.parse(dataLokal);

        // Cek apakah propertinya bernama .name atau .nama atau .fullname
        const namaLengkap =
          userObjek.name || userObjek.nama || userObjek.fullname;

        if (namaLengkap) {
          this.namaUser = namaLengkap.split(' ')[0];
        } else if (typeof userObjek === 'string') {
          this.namaUser = userObjek.split(' ')[0];
        }
      } catch (e) {
        // Jika ternyata yang disimpan murni string nama langsung tanpa format JSON
        this.namaUser = dataLokal.split(' ')[0];
      }
    }
  }

  // Fungsi untuk ke halaman Notifikasi
  goToNotif() {
    this.router.navigate(['/notifications']);
  }

  // Fungsi untuk ke halaman Detail Kursus
  goToDetail() {
    this.router.navigate(['/course-detail']);
  }

  goToCourse() {
    this.router.navigateByUrl('/tabs/course');
  }
}
