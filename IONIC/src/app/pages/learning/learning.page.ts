import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { NavController, AlertController } from '@ionic/angular';

@Component({
  selector: 'app-learning',
  templateUrl: './learning.page.html',
  styleUrls: ['./learning.page.scss'],
  standalone: false
})
export class LearningPage implements OnInit {
  allEnrollments: any[] = [];
  filteredEnrollments: any[] = [];
  activeTab: string = 'ongoing';

  constructor(
    private http: HttpClient,
    private navCtrl: NavController,
    private alertCtrl: AlertController
  ) {}

  ngOnInit() {
    this.loadData();
  }

  loadData() {
    // Simulasi data
    this.allEnrollments = [
      { id: 1, course_id: 101, progress: 45, course: { title: 'Ionic & Angular', image: 'assets/imgs/ionic.png' } },
      { id: 2, course_id: 102, progress: 100, course: { title: 'Laravel 11', image: 'assets/imgs/laravel.png' } }
    ];
    this.filterData();
  }

  switchTab(tab: string) {
    this.activeTab = tab;
    this.filterData();
  }

  filterData() {
    this.filteredEnrollments = this.allEnrollments.filter(item => 
      this.activeTab === 'ongoing' 
        ? (item.progress ?? 0) < 100 
        : (item.progress ?? 0) === 100
    );
  }

  /**
   * Navigasi ke Course Player (Fullscreen)
   * Menggunakan rute root agar keluar dari scope Tabs
   */
goToPlayer(courseId: any) {
  console.log("Tombol diklik, mencoba navigasi ke:", courseId);
  this.navCtrl.navigateForward(['/course-player', courseId]).then((result) => {
    console.log("Hasil navigasi:", result);
  }).catch((err) => {
    console.error("Gagal navigasi:", err);
  });
}

  /**
   * Navigasi ke Kuis (Tetap di dalam Tabs)
   */
  async openQuiz(item: any) {
    if (item && item.course_id) {
      this.navCtrl.navigateForward(['/tabs', 'quiz', item.course_id]);
    }
  }
}