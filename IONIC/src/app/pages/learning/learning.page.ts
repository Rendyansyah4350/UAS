import { Component, OnInit } from '@angular/core';
import { NavController, AlertController } from '@ionic/angular';
import { CourseService } from '../../services/course.service'; // 🟢 TAMBAHAN: Import service lu mbut

@Component({
  selector: 'app-learning',
  templateUrl: './learning.page.html',
  styleUrls: ['./learning.page.scss'],
  standalone: false,
})
export class LearningPage implements OnInit {
  activeTab: string = 'ongoing';
  allEnrollments: any[] = [];
  filteredEnrollments: any[] = [];
  loading: boolean = false; // 🟢 TAMBAHAN: Indikator loading biar UI lebih smooth

  constructor(
    private navCtrl: NavController,
    private alertCtrl: AlertController,
    private courseService: CourseService, // 🟢 TAMBAHAN: Inject CourseService
  ) {}

  ngOnInit() {
    this.loadData();

    // 🟢 TAMBAHAN SAKTI: Dengarkan sinyal perubahan dari halaman player secara real-time!
    this.courseService.progressChanged$.subscribe((berubah) => {
      if (berubah) {
        console.log(
          'Progress berubah mbut! Mengupdate list My Learning otomatis di background...',
        );
        this.loadData(); // Ambil data ulang dari database Laravel secara diam-diam
      }
    });
  }

  // 🟢 PAKEM IONIC SIKLUS: Supaya pas student abis beli kelas terus pindah ke tab ini, datanya langsung ter-refresh otomatis
  ionViewWillEnter() {
    this.loadData();
  }

  loadData() {
    this.loading = true;

    // 🔥 AMBIL DATA ASLI: Tembak API riwayat pembelian student murni dari backend Laravel lu
    this.courseService.getMyEnrollments().subscribe(
      (res: any) => {
        this.loading = false;
        if (res.success && res.data) {
          // 🟢 FILTER STATUS: Hanya masukkan kelas yang status pembayarannya beneran lunas ('success')
          this.allEnrollments = res.data.filter(
            (item: any) => String(item.status).toLowerCase() === 'success',
          );

          console.log(
            'Data Riwayat Belajar Asli Student:',
            this.allEnrollments,
          );

          // Pecah data berdasarkan tab (ongoing atau completed)
          this.filterData();
        }
      },
      (error) => {
        this.loading = false;
        console.error('Gagal menarik data My Learning dari server:', error);
      },
    );
  }

  // Mengubah tab menu (Ongoing vs Completed)
  segmentChanged(event: any) {
    this.activeTab = event.detail.value;
    this.filterData();
  }

  filterData() {
    // Memisahkan progress: ongoing (< 100%) vs completed (=== 100%)
    // Kalau di DB Laravel lu belum ada field 'progress', default-kan ke 0 biar aman ga crash
    this.filteredEnrollments = this.allEnrollments.filter((item) => {
      const progressKursus = item.progress ? Number(item.progress) : 0;
      return this.activeTab === 'ongoing'
        ? progressKursus < 100
        : progressKursus === 100;
    });
  }

  // Mengarahkan student ke ruang nonton video materi pembelajaran
  // Mengarahkan student ke ruang nonton video materi pembelajaran (course-player)
  goToPlayer(courseId: any) {
    if (!courseId) {
      console.error('ID Kursus tidak ditemukan mbut!');
      return;
    }

    // 🟢 SINKRONISASI ROUTE: Arahkan murni ke route /course-player diikuti dengan ID kursusnya
    this.navCtrl.navigateForward(['/course-player', courseId]);
  }

  async openQuiz(item: any) {
    // Langsung navigasi ke menu kuis berdasarkan course_id asli server
    this.navCtrl.navigateForward(['/quiz', item.course_id]);
  }
}
