import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http'; // Tambahkan HttpHeaders di sini
import { environment } from '../../environments/environment';
import { Observable, tap } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  login(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, data).pipe(
      tap((res: any) => {
        if (res && res.access_token) {
          localStorage.setItem('token', res.access_token);
        }
      }),
    );
  }

  verifyOTP(email: string, otp: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/verify-otp`, { email, otp }).pipe(
      tap((res: any) => {
        if (res && res.token) {
          localStorage.setItem('token', res.token);
        }
      }),
    );
  }
  sendResetOtp(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password/send-otp`, { email });
  }

  verifyResetOtp(email: string, otp: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password/verify-otp`, {
      email,
      otp,
    });
  }

  resetPassword(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password/reset`, data);
  }

  register(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, data);
  }

  isLoggedIn(): boolean {
    return !!localStorage.getItem('token');
  }

  logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user_data');
  }

  // --- TAMBAHAN BARU: Taruh di sini ya ---
  getProfileFromServer(): Observable<any> {
    // Ambil token secara dinamis dari localStorage saat fungsi dieksekusi
    const token = localStorage.getItem('token');

    console.log('Token yang dikirim ke server live:', token); // Cek di console untuk memastikan tidak null

    const headers = new HttpHeaders({
      Authorization: `Bearer ${token}`,
      Accept: 'application/json',
    });

    return this.http.get(`${this.apiUrl}/user`, { headers });
  }

  updateProfile(data: any): Observable<any> {
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      Authorization: `Bearer ${token}`,
      Accept: 'application/json',
    });
    return this.http.put(`${this.apiUrl}/user/update`, data, { headers });
  }

  // --- FUNGSI AMBIL DAFTAR KURSUS DARI HOSTING CPANEL ---
  getCoursesFromServer(): Observable<any> {
    const token = localStorage.getItem('token');

    // Siapkan header jika backend Laravel lo mewajibkan login (auth:api / auth:sanctum)
    const headers = new HttpHeaders({
      Authorization: `Bearer ${token}`,
      Accept: 'application/json',
    });

    // Gantilah '/courses' di bawah ini kalau ternyata nama endpoint dari temen lo berbeda!
    return this.http.get(`${this.apiUrl}/courses`, { headers });
  }

  
}
