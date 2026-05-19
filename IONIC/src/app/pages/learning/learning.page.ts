import { Component, OnInit } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { Router } from '@angular/router';

@Component({
  selector: 'app-learning', // Sesuai dengan selektor bawaan kamu
  templateUrl: './learning.page.html',
  styleUrls: ['./learning.page.scss'],
  standalone: false,
})
export class LearningPage implements OnInit {
  apiUrl = environment.apiUrl;
  allEnrollments: any[] = []; // Menampung data mentah dari server
  filteredEnrollments: any[] = []; // Menampung data setelah difilter tab
  activeTab: string = 'ongoing'; // Default tab: ongoing (Kursus Saya)

  constructor(
    private http: HttpClient,
    private router: Router,
  ) {}

  ngOnInit() {
    this.loadMyLearning();
  }

  ionViewWillEnter() {
    this.loadMyLearning(); // Otomatis refresh data setiap kali halaman ini dibuka
  }

  loadMyLearning() {
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      Authorization: `Bearer ${token}`,
      Accept: 'application/json',
    });

    // Menembak endpoint GET /enrollments privat di cPanel kamu
    this.http.get(`${this.apiUrl}/enrollments`, { headers }).subscribe(
      (res: any) => {
        this.allEnrollments = res;
        this.filterData();
        console.log('Data Learning sukses dimuat:', this.allEnrollments);
      },
      (error) => {
        console.error('Gagal mengambil data enrollments:', error);
      },
    );
  }

  // Fungsi klik untuk pindah tab filter
  switchTab(tab: string) {
    this.activeTab = tab;
    this.filterData();
  }

  // Memisahkan kursus berjalan (< 100%) dan kursus tamat (= 100%)
  filterData() {
    if (this.activeTab === 'ongoing') {
      this.filteredEnrollments = this.allEnrollments.filter(
        (item) => (item.progress ?? 0) < 100,
      );
    } else {
      this.filteredEnrollments = this.allEnrollments.filter(
        (item) => (item.progress ?? 0) === 100,
      );
    }
  }

  goToNotif() {
    this.router.navigate(['/notifications']);
  }
}
