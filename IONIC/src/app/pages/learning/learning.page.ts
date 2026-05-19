import { Component, OnInit } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { Router } from '@angular/router';
import { AlertController } from '@ionic/angular'; // 🟢 TAMBAHAN: Import Alert bawaan Ionic

@Component({
  selector: 'app-learning',
  templateUrl: './learning.page.html',
  styleUrls: ['./learning.page.scss'],
  standalone: false,
})
export class LearningPage implements OnInit {
  apiUrl = environment.apiUrl;
  allEnrollments: any[] = [];
  filteredEnrollments: any[] = [];
  activeTab: string = 'ongoing';

  constructor(
    private http: HttpClient,
    private router: Router,
    private alertController: AlertController // 🟢 TAMBAHAN: Inject AlertController ke constructor
  ) {}

  ngOnInit() {
    this.loadMyLearning();
  }

  ionViewWillEnter() {
    this.loadMyLearning();
  }

  loadMyLearning() {
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      Authorization: `Bearer ${token}`,
      Accept: 'application/json',
    });

    this.http.get(`${this.apiUrl}/enrollments`, { headers }).subscribe(
      (res: any) => {
        if (res && res.length > 0) {
          this.allEnrollments = res;
        } else {
          this.allEnrollments = this.getDummyEnrollments();
        }
        this.filterData();
        console.log('Data Learning sukses dimuat:', this.allEnrollments);
      },
      (error) => {
        console.error('Gagal mengambil data enrollments dari API, menggunakan dummy data:', error);
        this.allEnrollments = this.getDummyEnrollments();
        this.filterData();
      },
    );
  }

  switchTab(tab: string) {
    this.activeTab = tab;
    this.filterData();
  }

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

  openCourse(courseId: number) {
    console.log('Buka materi untuk course ID:', courseId);
    this.router.navigate([`/course-detail/${courseId}`]);
  }

  // 🟢 TAMBAHAN BARU: Logic pengecekan sebelum membuka kuis
  async openQuiz(item: any) {
    const currentProgress = item.progress ?? 0;

    // Cek jika progress belum menyentuh 100%
    if (currentProgress < 100) {
      const alert = await this.alertController.create({
        header: 'Akses Ditolak, Bre!',
        subHeader: `Progress kamu baru ${currentProgress}%`,
        message: 'Kamu wajib menyelesaikan seluruh materi sebelum bisa mengambil ujian kuis kursus ini.',
        buttons: ['Oke, Siap!'],
        cssClass: 'custom-alert-neon' // Bisa lo hias pakai scss neon lo nanti
      });

      await alert.present();
    } else {
      // JIKA SUDAH 100%, LOLOS KE HALAMAN QUIZ
      console.log('Lolos! Masuk ke kuis untuk course ID:', item.course_id);
      this.router.navigate([`/quiz/${item.course_id}`]); // Sesuaikan rute halaman kuis lo
    }
  }

  claimCertificate(courseId: number) {
    console.log('Proses klaim sertifikat untuk course ID:', courseId);
    alert('Selamat mbut! Sertifikat kamu berhasil digenerate.');
  }

  goToNotif() {
    this.router.navigate(['/notifications']);
  }

  private getDummyEnrollments(): any[] {
    return [
      {
        id: 1,
        course_id: 101,
        progress: 45, // < 100% -> Bakal memicu alert ditolak jika diklik kuisnya
        course: {
          title: 'Full-Stack Mobile Development dengan Ionic & Angular',
          image: 'assets/imgs/ionic-course.jpg'
        }
      },
      {
        id: 2,
        course_id: 102,
        progress: 100, // Supaya bisa kita coba di tab ongoing, ubah sementara ke 100 buat uji coba lolos kuis
        course: {
          title: 'Mastering Laravel 11 & MySQL Web API dari Dasar',
          image: ''
        }
      },
      {
        id: 3,
        course_id: 103,
        progress: 100,
        course: {
          title: 'UI/UX Design: Neon Glassmorphism Masterclass',
          image: 'assets/imgs/uiux-course.jpg'
        }
      }
    ];
  }
}
