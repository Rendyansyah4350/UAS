import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-quiz',
  templateUrl: './quiz.page.html',
  styleUrls: ['./quiz.page.scss'],
  standalone: false,
})
export class QuizPage implements OnInit {
  courseId!: number;
  currentQuestionIndex: number = 0;
  score: number = 0;
  isFinished: boolean = false;
  selectedAnswer: string = '';

  loading: boolean = true;
  quizStatus: string = '';
  quizScore: number = 0;

  // Diisi dynamic dari database Laravel
  questions: any[] = [];

  // Ditambahkan: Array untuk menyimpan jawaban user di setiap index soal agar tidak hilang/ter-reset saat navigasi
  userAnswers: string[] = [];

  constructor(
    private navCtrl: NavController,
    private courseService: CourseService,
    private route: ActivatedRoute,
  ) {}

  ngOnInit() {
    // Mengambil parameter ID Kursus dari URL (misal: /quiz/:id)
    const idParam = this.route.snapshot.paramMap.get('id');
    if (idParam) {
      this.courseId = Number(idParam);
      this.ambilDataQuizAsli();
    } else {
      this.loading = false;
    }
  }

  ambilDataQuizAsli() {
    this.loading = true;
    this.courseService.getQuizQuestions(this.courseId).subscribe({
      next: (res: any) => {
        this.loading = false;
        console.log('Response Kuis dari Laravel:', res); // Cek isi objek asli di inspect console browser

        if (!res) {
          this.questions = [];
          return;
        }

        // Jalur 1: Jika Laravel membungkusnya di dalam res.data
        if (res.success && res.data) {
          this.questions = Array.isArray(res.data) ? res.data : [res.data];
        }
        // Jalur 2: Jika Laravel mengembalikan objek kuis yang di dalamnya ada array questions (res.data.questions)
        else if (res.data && res.data.questions) {
          this.questions = res.data.questions;
        }
        // Jalur 3: Jika API langsung melempar Array mentah []
        else if (Array.isArray(res)) {
          this.questions = res;
        }
        // Jalur 4: Cadangan jika struktur berupa objek tunggal langsung dimasukkan ke array
        else {
          this.questions = res.questions || [];
        }

        // Ditambahkan: Inisialisasi panjang array jawaban user sesuai jumlah soal dari Laravel
        this.userAnswers = new Array(this.questions.length).fill('');

        console.log('Hasil parsing array questions untuk UI:', this.questions);
      },
      error: (err: any) => {
        this.loading = false;
        console.error('Gagal memuat kuis dari server Laravel:', err);
        this.questions = [];
      },
    });
  }

  goBack() {
    this.navCtrl.navigateRoot('/tabs/my-learning');
  }

  selectAnswer(val: string) {
    console.log('User memilih opsi:', val);
    this.selectedAnswer = val;

    // Ditambahkan: Simpan nilai jawaban ke dalam array pelacak index soal saat ini
    this.userAnswers[this.currentQuestionIndex] = val;
  }

  nextQuestion() {
    // Fungsi checkScore() bawaan kamu tetap berjalan normal di sini
    if (this.currentQuestionIndex < this.questions.length - 1) {
      this.currentQuestionIndex++;

      // Diubah: Ambil jawaban yang sebelumnya sudah pernah dipilih di soal ini (jika ada)
      this.selectedAnswer = this.userAnswers[this.currentQuestionIndex] || '';
    }
  }

  prevQuestion() {
    if (this.currentQuestionIndex > 0) {
      this.currentQuestionIndex--;

      // Diubah: Kembalikan jawaban yang sudah dipilih sebelumnya saat student mundur ke soal lama
      this.selectedAnswer = this.userAnswers[this.currentQuestionIndex] || '';
    }
  }

  checkScore() {
    // Disesuaikan: Hitung total jawaban benar secara berkala dari seluruh isi array userAnswers
    this.score = 0;
    this.userAnswers.forEach((ans, index) => {
      if (ans === this.questions[index]?.answer) {
        this.score++;
      }
    });
  }

  submitQuiz() {
    this.checkScore();

    if (this.questions.length > 0) {
      this.quizScore = Math.round((this.score / this.questions.length) * 100);
    } else {
      this.quizScore = 0;
    }

    // DIUBAH: Tidak peduli berapa nilainya, statusnya selalu dianggap 'passed' (selesai)
    this.quizStatus = 'passed';

    // Kirim data ke endpoint progress Laravel
    this.courseService
      .updateQuizProgress(this.courseId, this.quizScore)
      .subscribe({
        next: (res: any) => {
          console.log('Progress kuis berhasil disimpan ke cPanel:', res);
        },
        error: (err: any) => {
          console.error('Gagal sinkronisasi progress ke server:', err);
        },
      });

    this.isFinished = true;
  }

  finishQuiz() {
    // Karena semua status sudah pasti 'passed', tombol akan langsung mengarahkan user pulang ke My Learning
    this.goBack();
  }
}
