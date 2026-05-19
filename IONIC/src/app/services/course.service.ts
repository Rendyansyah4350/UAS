// src/app/services/course.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http'; // TAMBAHKAN HttpHeaders DI SINI LEK
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CourseService {
  // Ganti dengan URL API backend marketplace kamu
  private apiUrl = 'https://eduvan.rehalivan.com/api/courses';

  // URL dasar endpoint API Laravel kamu (tanpa embel-embel /courses di buntutnya)
  private baseApiUrl = 'https://eduvan.rehalivan.com/api';

  constructor(private http: HttpClient) { }
  
  // Fungsi pembantu untuk menyisipkan Token Login JWT/Sanctum Laravel ke Header API
  private dapatkanHeaderAutentikasi() {
    const tokenUser = localStorage.getItem('token') || localStorage.getItem('userData');
    return new HttpHeaders({
      'Authorization': `Bearer ${tokenUser}`,
      'Content-Type': 'application/json'
    });
  }

  getCourses(): Observable<any> {
    return this.http.get(this.apiUrl);
  }

  getCourseById(id: string): Observable<any> {
    // Pastikan pakai backticks (``) bukan petik biasa ('')
    return this.http.get(`${this.apiUrl}/${id}`); 
  }

  // =========================================================================
  // 🟢 LOGIKA WISHLIST ASLI (KONEKSI LIVE SERVERS CPANEL)
  // =========================================================================

  // 1. Fungsi mengambil daftar semua kursus yang di-wishlist oleh user yang sedang login
  ambilDaftarWishlist(): Observable<any> {
    return this.http.get(`${this.baseApiUrl}/wishlist`, { 
      headers: this.dapatkanHeaderAutentikasi() 
    });
  }

  // 2. Fungsi untuk pasang/lepas status wishlist (Toggle klik ikon hati)
  toggleWishlistServer(courseId: number): Observable<any> {
    const payload = { course_id: courseId };
    return this.http.post(`${this.baseApiUrl}/wishlist/toggle`, payload, { 
      headers: this.dapatkanHeaderAutentikasi() 
    });
  }
}