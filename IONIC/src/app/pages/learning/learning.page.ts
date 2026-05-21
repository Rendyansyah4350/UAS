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
    if (this.activeTab === 'ongoing') {
      // 🟢 KURSUS SAYA (Ongoing)
      this.filteredEnrollments = this.allEnrollments.filter((item) => {
        // Ambil nilai progress secara dinamis (cek field progress_percent dulu, kalau gada baru pake progress)
        const nilaiProgress =
          item.progress_percent !== undefined
            ? item.progress_percent
            : item.progress;

        // Ambil status kuis dari API Laravel lu
        const statusKuis = item.quiz_status || item.status_quiz;
        const kuisLulus =
          item.quiz_passed === 1 ||
          item.quiz_passed === true ||
          statusKuis === 'passed';

        // Tampilkan di tab ongoing jika progress belum 100%, ATAU sudah di ujung materi (99%/100% materi) tapi BELUM lulus kuis asli
        return nilaiProgress < 100 || !kuisLulus;
      });
    } else if (this.activeTab === 'completed') {
      // 🟢 SELESAI (Completed)
      this.filteredEnrollments = this.allEnrollments.filter((item) => {
        const statusKuis = item.quiz_status || item.status_quiz;
        const kuisLulus =
          item.quiz_passed === 1 ||
          item.quiz_passed === true ||
          statusKuis === 'passed';

        // Baru boleh masuk tab selesai kalau kuisnya benar-benar dinyatakan lulus murni dari backend
        return kuisLulus;
      });
    }
  }

  // Mengarahkan student ke ruang nonton video materi pembelajaran
  // Mengarahkan student ke ruang nonton video materi pembelajaran (course-player)
  goToPlayer(courseId: any) {
    if (!courseId) {
      console.error('ID Kursus tidak ditemukan!');
      return;
    }

    // 🟢 SINKRONISASI ROUTE: Arahkan murni ke route /course-player diikuti dengan ID kursusnya
    this.navCtrl.navigateForward(['/course-player', courseId]);
  }

  async openQuiz(item: any) {
    // Ambil ID dengan aman, jika item.course_id kosong ambil dari dalam objek course
    const targetId =
      item.course_id || (item.course ? item.course.id : null) || item.id;

    console.log('Mengirim student ke kuis untuk ID Kursus:', targetId);

    if (targetId) {
      this.navCtrl.navigateForward(['/quiz', targetId]);
    } else {
      console.error('Gagal navigasi, ID kursus murni kosong melompong!');
    }
  }
}
