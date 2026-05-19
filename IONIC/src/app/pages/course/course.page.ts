import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';

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

  // 🟢 1. TAMBAHKAN VARIABEL UNTUK FILTER SORTING (Default: polosan/tanpa sort)
  filterAktif: string = 'default';

  constructor(
    private navCtrl: NavController,
    private courseService: CourseService,
  ) {}

  ngOnInit() {
    this.loadData();
  }

  loadData() {
    this.isLoading = true;
    this.courseService.getCourses().subscribe({
      next: (res: any) => {
        this.allCourses = res.data || [];
        this.listCourses = this.allCourses;
        this.isLoading = false;
        console.log('Data API Katalog Sukses:', this.listCourses);
      },
      error: (error) => {
        console.error('Gagal ambil data katalog', error);
        this.isLoading = false;
      },
    });
  }

  pilihKategori(namaKategori: string) {
    this.kategoriAktif = namaKategori;
    this.keywordPencarian = '';
    // Reset urutan filter ke default setiap ganti kategori biar gak pusing user-nya
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
      // Jalankan sorting ulang jika ada filter aktif sewaktu search kosong
      this.eksekusiFilterSort();
      return;
    }

    this.listCourses = dataDasar.filter((course: any) => {
      const judulKursus = course.title || '';
      return judulKursus
        .toLowerCase()
        .includes(this.keywordPencarian.toLowerCase());
    });

    // Jalankan sorting setelah data berhasil dicari
    this.eksekusiFilterSort();
  }

  // 🟢 2. TAMBAHKAN FUNGSI LIVE SORTING UNTUK RATING & HARGA
  eksekusiFilterSort() {
    if (this.filterAktif === 'default') return;

    // Lakukan sorting langsung pada array listCourses yang sedang tampil
    this.listCourses.sort((a: any, b: any) => {
      const hargaA = parseFloat(a.price) || 0;
      const hargaB = parseFloat(b.price) || 0;
      const ratingA = parseFloat(a.rating) || 0;
      const ratingB = parseFloat(b.rating) || 0;

      if (this.filterAktif === 'harga-termurah') {
        return hargaA - hargaB; // Urutkan dari angka kecil ke besar
      } else if (this.filterAktif === 'harga-termahal') {
        return hargaB - hargaA; // Urutkan dari besar ke kecil
      } else if (this.filterAktif === 'rating-tertinggi') {
        return ratingB - ratingA; // Urutkan dari rating 5.0 ke bawah
      }
      return 0;
    });
  }
}
