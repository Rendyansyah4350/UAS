import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-quiz',
  templateUrl: './quiz.page.html',
  styleUrls: ['./quiz.page.scss'],
  standalone: false,
})
export class QuizPage implements OnInit {
  currentQuestionIndex: number = 0;
  score: number = 0;
  isFinished: boolean = false; // Penanda kuis selesai

  // Data dummy 10 soal
  questions = [
    { question: "Apa fungsi dari [ngFor] pada Angular?", options: ["Styling", "Looping data", "Validasi form", "Navigasi"], answer: "Looping data" },
    { question: "Selector utama Ionic untuk tombol?", options: ["<ion-button>", "<div-btn>", "<button-ionic>", "<nav-btn>"], answer: "<ion-button>" },
    { question: "Perintah untuk membuat page di Ionic?", options: ["ionic start", "ionic generate", "ionic add", "ionic run"], answer: "ionic generate" },
    { question: "Apa itu TypeScript?", options: ["CSS Framework", "Superset JavaScript", "Database", "Library Python"], answer: "Superset JavaScript" },
    { question: "Fungsi dari ion-content?", options: ["Header", "Footer", "Wadah konten utama", "Sidebar"], answer: "Wadah konten utama" },
    { question: "Data binding dua arah di Angular?", options: ["{{}}", "[]", "()", "[(ngModel)]"], answer: "[(ngModel)]" },
    { question: "Apa itu Dependency Injection?", options: ["Desain pattern", "Nama database", "Jenis CSS", "Nama plugin"], answer: "Desain pattern" },
    { question: "Perintah untuk menjalankan aplikasi?", options: ["ionic build", "ionic serve", "ionic stop", "ionic clear"], answer: "ionic serve" },
    { question: "Apa itu Observable?", options: ["Event", "Data stream", "Variabel biasa", "CSS Class"], answer: "Data stream" },
    { question: "Keunggulan Ionic?", options: ["Cross-platform", "Hanya iOS", "Hanya Android", "Bukan framework"], answer: "Cross-platform" }
  ];

  constructor(private route: ActivatedRoute) { }

  ngOnInit() { }

  selectAnswer(selectedOption: string) {
    if (selectedOption === this.questions[this.currentQuestionIndex].answer) {
      this.score += 10; // 10 soal x 10 poin = 100
    }
    
    // Pindah soal atau selesai
    if (this.currentQuestionIndex < this.questions.length - 1) {
      this.currentQuestionIndex++;
    } else {
      this.isFinished = true;
    }
  }

  submitQuiz() {
    alert("Kuis selesai! Skor Anda: " + this.score);
    this.finishQuiz();
  }

  finishQuiz() {
    window.history.back();
  }
}