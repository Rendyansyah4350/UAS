import { Component, OnInit, OnDestroy } from '@angular/core';
import { NavController, AlertController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-learning',
  templateUrl: './learning.page.html',
  styleUrls: ['./learning.page.scss'],
  standalone: false,
})
export class LearningPage implements OnInit, OnDestroy {
  activeTab: string = 'ongoing';
  allEnrollments: any[] = [];
  filteredEnrollments: any[] = [];
  loading: boolean = false;

  private progressSub: Subscription = new Subscription();

  constructor(
    private navCtrl: NavController,
    private alertCtrl: AlertController,
    private courseService: CourseService,
  ) {}

  // --- FUNGSI TAMBAHAN UNTUK GAMBAR ---
  // --- FUNGSI LOGO DINAMIS BERDASARKAN KATEGORI ---
  getCategoryLogo(category: string): string {
    // Pastikan kategori tidak null/undefined
    const cat = (category || '').toLowerCase();

    // Logika deteksi kategori
    if (cat.includes('computer science')) {
      return 'assets/icon/computer-science.jpeg';
    } else if (cat.includes('microsoft office')) {
      return 'assets/icon/microsoft-office.jpeg';
    } else {
      // Fallback jika kategori tidak dikenal atau kosong
      return 'assets/icon/favicon.png';
    }
  }
  // -------------------------------------------------
  // ------------------------------------

  ngOnInit() {
    this.loadData();

    this.progressSub = this.courseService.progressChanged$.subscribe(
      (berubah) => {
        if (berubah) {
          console.log('Sinyal diterima, menunggu sinkronisasi database...');
          setTimeout(() => {
            console.log('Menarik data segar setelah jeda...');
            this.loadData();
          }, 1000);
        }
      },
    );
  }

  ngOnDestroy() {
    if (this.progressSub) {
      this.progressSub.unsubscribe();
    }
  }

  ionViewWillEnter() {
    console.log(
      'User kembali membuka halaman My Learning, memuat data terbaru...',
    );
    this.loadData();
  }

  loadData() {
    this.loading = true;
    this.courseService.getMyEnrollments().subscribe({
      next: (res: any) => {
        this.loading = false;
        if (res && res.success && res.data) {
          this.allEnrollments = res.data.filter(
            (item: any) => String(item.status).toLowerCase() === 'success',
          );
          console.log(
            'Data Riwayat Belajar Asli Student:',
            this.allEnrollments,
          );
          this.filterData();
        }
      },
      error: (err: any) => {
        this.loading = false;
        console.error('Gagal menarik data My Learning dari server:', err);
      },
    });
  }

  segmentChanged(event: any) {
    this.activeTab = event.detail.value;
    this.filterData();
  }

  filterData() {
    this.filteredEnrollments = this.allEnrollments.filter((item) => {
      const nilaiProgress = parseInt(item.progress, 10) || 0;
      const statusKuis = (item.quiz_status || item.status_quiz || '')
        .toString()
        .toLowerCase();

      if (this.activeTab === 'ongoing') {
        return nilaiProgress < 100;
      } else {
        return nilaiProgress >= 100;
      }
    });
  }

  goToPlayer(courseId: any) {
    if (!courseId) {
      console.error('ID Kursus tidak ditemukan!');
      return;
    }
    this.navCtrl.navigateForward(['/course-player', courseId]);
  }

  async openQuiz(item: any) {
    const targetId =
      item.course_id || (item.course ? item.course.id : null) || item.id;
    if (targetId) {
      this.navCtrl.navigateForward(['/quiz', targetId]);
    } else {
      console.error('Gagal navigasi, ID kursus murni kosong!');
    }
  }
}
