// src/app/services/course.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class CourseService {
  // Ganti dengan URL API backend marketplace kamu
  private apiUrl = 'https://eduvan.rehalivan.com/api/courses';
  private baseApiUrl = 'https://eduvan.rehalivan.com/api';

  public wishlistChanged$ = new BehaviorSubject<boolean>(false);
  public progressChanged$ = new BehaviorSubject<boolean>(false);

  constructor(private http: HttpClient) {}

  // Fungsi pembantu untuk menyisipkan Token Login JWT/Sanctum Laravel ke Header API
  private dapatkanHeaderAutentikasi() {
    let tokenUser = localStorage.getItem('token');

    // Jika 'token' kosong, coba cek apakah tokennya nyelip di dalam objek 'userData'
    if (!tokenUser) {
      const userDataRaw = localStorage.getItem('userData');
      if (userDataRaw) {
        try {
          // Jika isi userData itu berupa JSON string objek, kita bongkar dulu
          const parsedData = JSON.parse(userDataRaw);
          tokenUser = parsedData.token || parsedData.access_token || null;
        } catch (e) {
          // Jika bukan JSON string (berarti string token murni), langsung ambil
          tokenUser = userDataRaw;
        }
      }
    }

    // Bersihkan token dari karakter petik ganda gaib yang sering ikut dari JSON string
    if (tokenUser) {
      tokenUser = String(tokenUser).replace(/"/g, '').trim();
    }

    return new HttpHeaders({
      Authorization: `Bearer ${tokenUser}`,
      'Content-Type': 'application/json',
      Accept: 'application/json',
    });
  }

  getCourses(): Observable<any> {
    return this.http.get(this.apiUrl);
  }

  getCourseById(id: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/${id}`);
  }

  // 1. Fungsi untuk membeli kursus (Mendapatkan link Invoice Xendit)
  buyCourse(courseId: number): Observable<any> {
    const payload = { course_id: courseId };
    return this.http.post(`${this.baseApiUrl}/enrollments`, payload, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  // 2. Fungsi untuk mengambil isi materi/video berdasarkan ID Kursus (Gembok Akses)
  getCourseContents(courseId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/${courseId}/contents`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  // 3. Fungsi untuk melihat riwayat pembelian / daftar kursus saya (My Learning)
  getMyEnrollments(): Observable<any> {
    return this.http.get(`${this.baseApiUrl}/enrollments`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  saveProgress(
    courseId: number,
    contentId: number,
    isCompleted?: number,
  ): Observable<any> {
    const payload = {
      course_id: courseId,
      content_id: contentId,
      is_completed: isCompleted ?? 1,
    };
    return this.http.post(
      `${this.baseApiUrl}/contents/mark-complete`,
      payload,
      {
        headers: this.dapatkanHeaderAutentikasi(),
      },
    );
  }

  //notifikasi
  ambilDaftarNotifikasi(): Observable<any> {
    // 1. Ambil token bearer login mahasiswa yang tersimpan di memori hp/browser
    const token = localStorage.getItem('token'); 
    
    // 2. Pasang headers wajib agar lolos dari barikade middleware auth:sanctum
    const headers = {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    };

    // 3. Tembak endpoint API-nya! 
    return this.http.get(`${this.baseApiUrl}/notifications`, { headers });
  }

  // =========================================================================
  // LOGIKA WISHLIST ASLI (KONEKSI LIVE SERVERS CPANEL)
  // =========================================================================

  ambilDaftarWishlist(): Observable<any> {
    return this.http.get(`${this.baseApiUrl}/wishlist`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  toggleWishlistServer(courseId: number): Observable<any> {
    const payload = { course_id: courseId };
    return this.http.post(`${this.baseApiUrl}/wishlist/toggle`, payload, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  // =========================================================================
  // LOGIKA KUIS ASLI (KONEKSI LIVE SERVERS CPANEL)
  // =========================================================================

  // 1. Ambil semua soal kuis berdasarkan ID Kursus dari Laravel
  // =========================================================================
  // LOGIKA KUIS ASLI (KONEKSI LIVE SERVERS CPANEL)
  // =========================================================================

  // 1. Ambil semua soal kuis berdasarkan ID Kursus dari Laravel
  getQuizQuestions(courseId: number): Observable<any> {
    // Mengubah /quiz/{id} menjadi /courses/{id}/quizzes sesuai api.php Laravel
    return this.http.get(`${this.baseApiUrl}/courses/${courseId}/quizzes`, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  // 2. Kirim lembar jawaban kuis ke server Laravel untuk dikoreksi otomatis
  submitQuizAnswers(courseId: number, answers: any[]): Observable<any> {
    const payload = {
      course_id: courseId,
      answers: answers,
    };
    return this.http.post(`${this.baseApiUrl}/quiz/submit`, payload, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }

  // TAMBAHKAN FUNGSI INI DI PALING BAWAH (JANGAN HAPUS KODE DI ATASNYA)
  updateQuizProgress(courseId: number, score: number): Observable<any> {
    const payload = {
      course_id: courseId,
      score: score,
    };

    // Memicu RxJS BehaviorSubject agar halaman My Learning tahu ada progress baru yang selesai
    this.progressChanged$.next(true);

    // DIUBAH: Menembak endpoint progress kuis yang valid sesuai isi api.php Laravel kamu
    return this.http.post(`${this.baseApiUrl}/progress/submit-quiz`, payload, {
      headers: this.dapatkanHeaderAutentikasi(),
    });
  }
}
