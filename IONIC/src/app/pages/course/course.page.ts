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
        // Simpan data asli ke allCourses dan listCourses
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

  // 🟢 FUNGSI CLIK LIVE UNTUK FILTER KATEGORI (Computer Science / Microsoft Office)
  pilihKategori(namaKategori: string) {
    this.kategoriAktif = namaKategori;

    // Jika yang di-klik adalah 'Semua', kembalikan seluruh data master tanpa filter
    if (namaKategori === 'Semua') {
      this.listCourses = this.allCourses;
      return;
    }

    // Filter data berdasarkan kolom 'category' yang sesuai dengan database cPanel lo
    this.listCourses = this.allCourses.filter((course: any) => {
      const kategoriDatabase = course.category || '';
      return kategoriDatabase.toLowerCase() === namaKategori.toLowerCase();
    });
  }

  goToDetail(id: any) {
    this.navCtrl.navigateForward(['/course-detail', id]);
  }

  // 🟢 TAMBAHAN FUNGSI LIVE SEARCH (Tanpa mengubah fungsi di atas)
  fungsiCariKursus() {
    console.log('User sedang mencari:', this.keywordPencarian);

    // 1. Tentukan data dasar awal berdasarkan kategori yang lagi aktif
    let dataDasar = this.allCourses;
    if (this.kategoriAktif !== 'Semua') {
      dataDasar = this.allCourses.filter((course: any) => {
        const kategoriDatabase = course.category || '';
        return (
          kategoriDatabase.toLowerCase() === this.kategoriAktif.toLowerCase()
        );
      });
    }

    // 2. Jika isi searchbar kosong, balikin ke data dasar kategori tersebut
    if (!this.keywordPencarian.trim()) {
      this.listCourses = dataDasar;
      return;
    }

    // 3. Jalankan filter pencarian berdasarkan judul (title) dari data dasar
    this.listCourses = dataDasar.filter((course: any) => {
      const judulKursus = course.title || '';
      return judulKursus
        .toLowerCase()
        .includes(this.keywordPencarian.toLowerCase());
    });
  }
}
