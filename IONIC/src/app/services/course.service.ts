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
      Accept: 'application/json', // 🟢 WAJIB: Memaksa server merespon format JSON, bukan halaman redirect HTML!
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

  // 🟢 SEKARANG BISA TERIMA STATUS: 1 untuk tandai selesai, 0 untuk cancel progress
  saveProgress(
    courseId: number,
    contentId: number,
    isCompleted: number,
  ): Observable<any> {
    const payload = {
      course_id: courseId,
      content_id: contentId,
      is_completed: isCompleted, 
    };
    return this.http.post(
      `${this.baseApiUrl}/contents/mark-complete`,
      payload,
      {
        headers: this.dapatkanHeaderAutentikasi(),
      },
    );
  }

  // =========================================================================
  // 🟢 LOGIKA WISHLIST ASLI (KONEKSI LIVE SERVERS CPANEL)
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
}
