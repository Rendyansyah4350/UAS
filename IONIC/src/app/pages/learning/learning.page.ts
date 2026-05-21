import { Component, OnInit, OnDestroy } from '@angular/core'; // Tambah OnDestroy
import { NavController, AlertController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';
import { Subscription } from 'rxjs'; // Import Subscription

@Component({
  selector: 'app-learning',
  templateUrl: './learning.page.html',
  styleUrls: ['./learning.page.scss'],
  standalone: false,
})
export class LearningPage implements OnInit, OnDestroy {
  // Implement OnDestroy
  activeTab: string = 'ongoing';
  allEnrollments: any[] = [];
  filteredEnrollments: any[] = [];
  loading: boolean = false;

  // DEKLARASI PROPERTI YANG HILANG
  private progressSub: Subscription = new Subscription();

  constructor(
    private navCtrl: NavController,
    private alertCtrl: AlertController,
    private courseService: CourseService,
  ) {}

  ngOnInit() {
    this.loadData();

    this.progressSub = this.courseService.progressChanged$.subscribe(
      (berubah) => {
        if (berubah) {
          console.log('Sinyal diterima, menunggu sinkronisasi database...');

          // 🟢 Tambahkan jeda 1 detik (1000ms) untuk memberi waktu
          // server Laravel menyelesaikan penulisan data ke database
          setTimeout(() => {
            console.log('Menarik data segar setelah jeda...');
            this.loadData();
          }, 1000);
        }
      },
    );
  }

  // TAMBAHAN: Bersihkan memory agar aplikasi tidak berat
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
      // Memastikan nilai progres adalah angka murni
      const nilaiProgress = parseInt(item.progress, 10) || 0;

      // Ambil status kuis sebagai string aman
      const statusKuis = (item.quiz_status || item.status_quiz || '')
        .toString()
        .toLowerCase();

      // Kuis lulus jika status 'passed' atau progres sudah 100%
      const kuisLulus = statusKuis === 'passed' || nilaiProgress >= 100;

      if (this.activeTab === 'ongoing') {
        // Tampilkan di Ongoing jika progress belum 100%
        return nilaiProgress < 100;
      } else {
        // Tampilkan di Completed jika progress sudah 100%
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

    console.log('Mengirim student ke kuis untuk ID Kursus:', targetId);

    if (targetId) {
      this.navCtrl.navigateForward(['/quiz', targetId]);
    } else {
      console.error('Gagal navigasi, ID kursus murni kosong!');
    }
  }
}
