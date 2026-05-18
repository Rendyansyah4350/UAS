import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';

@Component({
  selector: 'app-beranda',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
  standalone: false,
})
export class HomePage implements OnInit {
  // 1. Inisialisasi variabel untuk nama user dengan nilai default
  namaUser: string = 'User';

  constructor(private router: Router) {}

  ngOnInit() {
    // 2. Jalankan fungsi penangkap data saat halaman dashboard dimuat
    this.ambilNamaUser();
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
