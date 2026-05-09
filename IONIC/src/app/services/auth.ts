import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  // 1. Fungsi Login Email/Password Dasar
  login(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, data);
  }

  // 2. Fungsi Kirim OTP (Baru)
  // Dipanggil setelah login email/pass berhasil atau untuk login via email saja
  sendOTP(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/send-otp`, { email });
  }

  // 3. Fungsi Verifikasi OTP (Baru)
  verifyOTP(email: string, otp: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/verify-otp`, { email, otp });
  }

  // 4. Fungsi Register
  register(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, data);
  }

  // 5. Fungsi Cek Status Login
  isLoggedIn(): boolean {
    // Cek apakah ada token di localStorage
    return !!localStorage.getItem('token');
  }

  // 6. Fungsi Logout (Disederhanakan tanpa GoogleAuth)
  logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user_data');
    // Opsional: arahkan ke halaman login setelah logout
  }
}
