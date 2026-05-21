import { Component, OnInit } from '@angular/core';
import {
  NavController,
  AlertController,
  LoadingController,
} from '@ionic/angular';
import { ActivatedRoute } from '@angular/router';
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'app-quiz',
  templateUrl: './quiz.page.html',
  styleUrls: ['./quiz.page.scss'],
  standalone: false,
})
export class QuizPage implements OnInit {
  courseId: number | null = null;
  loading: boolean = true;

  currentQuestionIndex: number = 0;
  isFinished: boolean = false;
  selectedAnswer: string = '';

  // 🟢 Menampung data soal asli bentukan dari database Laravel lu
  questions: any[] = [];

  // 🟢 Menampung lembar jawaban siswa untuk dikirim ke API Koreksi otomatis
  userAnswers: { question_id: number; selected_option: string }[] = [];

  // Hasil respon kelulusan dari server
  quizScore: number = 0;
  quizStatus: string = ''; // 'passed' atau 'failed'

  constructor(
    private navCtrl: NavController,
    private route: ActivatedRoute,
    private courseService: CourseService,
    private alertCtrl: AlertController,
    private loadingCtrl: LoadingController,
  ) {}

  ngOnInit() {
    // 🟢 SAKTI FALLBACK: Cek semua kemungkinan nama parameter URL rute Angular lu mbut
    const idParam =
      this.route.snapshot.paramMap.get('courseId') ||
      this.route.snapshot.paramMap.get('id') ||
      this.route.snapshot.paramMap.get('course_id');

    console.log('--- DEBUG TOMBOL KUIS ---');
    console.log('ID Kursus yang tertangkap dari URL:', idParam);

    if (idParam && idParam !== 'undefined' && idParam !== 'null') {
      this.courseId = Number(idParam);
      this.cekValidasiAksesKuis();
    } else {
      console.error(
        'Waduh mbut, ID kursus ga kebaca sama sekali dari rute URL!',
      );
      this.goBack();
    }
  }

  // 🟢 BARIKADE KEAMANAN: Memastikan murid ga bypass ketik URL manual di browser
  cekValidasiAksesKuis() {
    this.loading = true;
    this.courseService.getMyEnrollments().subscribe(
      (res: any) => {
        if (res.success && res.data) {
          // 🟢 FIX SAKTI: Memaksa kedua sisi menjadi tipe data Number agar lolos perbandingan ketat (===)
          const kelasIni = res.data.find(
            (item: any) => Number(item.course_id) === Number(this.courseId),
          );

          // Jika data kelas tidak ditemukan atau status kuncian gemboknya masih mengunci (false)
          if (!kelasIni || !kelasIni.is_quiz_unlocked) {
            this.tampilkanAlertDitolak();
          } else {
            // Jika aman dan sudah berhak ikut ujian, baru panggil isi soalnya dari API
            this.muatSoalKuisAsli();
          }
        } else {
          this.goBack();
        }
      },
      (err) => {
        console.error('Gagal memvalidasi hak akses kuis:', err);
        this.goBack();
      },
    );
  }

  async tampilkanAlertDitolak() {
    const alert = await this.alertCtrl.create({
      header: 'Akses Ditolak!',
      message:
        'Lu wajib menonton dan menyelesaikan semua materi video di kelas ini terlebih dahulu sebelum bisa menempuh ujian kuis!',
      buttons: [
        {
          text: 'Siap, Kembali',
          handler: () => {
            this.goBack();
          },
        },
      ],
      backdropDismiss: false,
      mode: 'ios',
    });
    await alert.present();
  }

  // 🟢 AMBIL DATA SOAL REAL: Menarik data dari server cPanel via service
  muatSoalKuisAsli() {
    this.courseService.getQuizQuestions(this.courseId!).subscribe(
      async (res: any) => {
        // 🟢 POP-UP DETEKTIF: Memaksa browser memunculkan isi asli struktur JSON dari cPanel
        const alertLog = await this.alertCtrl.create({
          header: 'Isi Data Dari cPanel Lu',
          message: JSON.stringify(res),
          buttons: ['OK'],
        });
        await alertLog.present();

        // Logika mapping data
        if (res && res.success && res.data) {
          this.questions = res.data;
        } else if (res && res.questions) {
          this.questions = res.questions;
        } else if (Array.isArray(res)) {
          this.questions = res;
        } else if (res && res.data && Array.isArray(res.data)) {
          this.questions = res.data;
        }

        this.loading = false;
      },
      (err) => {
        console.error('Gagal total memuat soal kuis dari server:', err);
        this.loading = false;
      },
    );
  }

  goBack() {
    this.navCtrl.navigateRoot('/tabs/my-learning');
  }

  selectAnswer(val: string) {
    this.selectedAnswer = val;
  }

  nextQuestion() {
    this.simpanJawabanKeArrayLocalStorage();
    if (this.currentQuestionIndex < this.questions.length - 1) {
      this.currentQuestionIndex++;

      // Cek apakah soal berikutnya sudah pernah dijawab sebelumnya (biar ga ilang pas back-next)
      const jawabanSebelumnya = this.userAnswers.find(
        (a) => a.question_id === this.questions[this.currentQuestionIndex].id,
      );
      this.selectedAnswer = jawabanSebelumnya
        ? jawabanSebelumnya.selected_option
        : '';
    }
  }

  prevQuestion() {
    this.simpanJawabanKeArrayLocalStorage();
    if (this.currentQuestionIndex > 0) {
      this.currentQuestionIndex--;

      // Kembalikan pilihan jawaban yang sudah pernah dipilih murid di soal ini
      const jawabanSebelumnya = this.userAnswers.find(
        (a) => a.question_id === this.questions[this.currentQuestionIndex].id,
      );
      this.selectedAnswer = jawabanSebelumnya
        ? jawabanSebelumnya.selected_option
        : '';
    }
  }

  simpanJawabanKeArrayLocalStorage() {
    if (!this.selectedAnswer) return;

    const currentQuestion = this.questions[this.currentQuestionIndex];
    const indexAda = this.userAnswers.findIndex(
      (a) => a.question_id === currentQuestion.id,
    );

    // Jika sudah ada, tinggal timpa opsi barunya. Jika belum, tambah baru (push)
    if (indexAda >= 0) {
      this.userAnswers[indexAda].selected_option = this.selectedAnswer;
    } else {
      this.userAnswers.push({
        question_id: currentQuestion.id,
        selected_option: this.selectedAnswer,
      });
    }
  }

  // 🟢 KIRIM LEMBAR JAWABAN KE LARAVEL UNTUK DIKOREKSI MASSAL
  async submitQuiz() {
    this.simpanJawabanKeArrayLocalStorage(); // Simpan pilihan terakhir siswa

    // Validasi: Pastikan murid tidak mengosongkan jawaban satupun
    if (this.userAnswers.length < this.questions.length) {
      const alertInput = await this.alertCtrl.create({
        header: 'Belum Selesai, semua!',
        message: `Lu baru mengisi ${this.userAnswers.length} dari ${this.questions.length} soal. Pastikan semua soal terjawab ya!`,
        buttons: ['Oke'],
        mode: 'ios',
      });
      await alertInput.present();
      return;
    }

    const loader = await this.loadingCtrl.create({
      message: 'Sedang mengoreksi jawaban...',
      mode: 'ios',
    });
    await loader.present();

    this.courseService
      .submitQuizAnswers(this.courseId!, this.userAnswers)
      .subscribe(
        async (res: any) => {
          await loader.dismiss();
          if (res.success) {
            this.quizScore = res.score; // Ambil nilai angka lulus (misal: 80)
            this.quizStatus = res.status; // Ambil status kelulusan ('passed' / 'failed')
            this.isFinished = true; // Buka layar tampilan hasil kuis

            // Tembakkan pemberitahuan ke BehaviorSubject agar progress bar di halaman My Learning auto-update secara live
            this.courseService.progressChanged$.next(true);
          } else {
            console.error('Koreksi kuis gagal:', res.message);
          }
        },
        async (err) => {
          await loader.dismiss();
          console.error('Gagal mengirim jawaban kuis ke Laravel:', err);
        },
      );
  }

  finishQuiz() {
    this.goBack();
  }
}
