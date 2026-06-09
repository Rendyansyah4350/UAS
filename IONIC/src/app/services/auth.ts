import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { Observable, tap, BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private apiUrl = 'https://eduvan.rehalivan.com/api';

  private currentUserSubject = new BehaviorSubject<any>(null);
  currentUser$ = this.currentUserSubject.asObservable();

  constructor(private http: HttpClient) {
    // 🟢 PERBAIKAN SINKRONISASI AUTO-LOGIN: Cek token & data user sekaligus saat aplikasi pertama dibuka
    const token = localStorage.getItem('token');
    const savedUser = localStorage.getItem('user_data');

    if (token && savedUser) {
      this.currentUserSubject.next(JSON.parse(savedUser));
    } else {
      // Jika salah satu tidak ada, bersihkan sekalian agar aman
      this.clearStorageState();
    }
  }

  updateCurrentUserState(userData: any) {
    localStorage.setItem('user_data', JSON.stringify(userData));
    this.currentUserSubject.next(userData);
  }

  // Fungsi pembantu internal untuk membersihkan state memori
  private clearStorageState() {
    localStorage.removeItem('token');
    localStorage.removeItem('user_data');
    this.currentUserSubject.next(null);
  }

  login(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, data).pipe(
      tap((res: any) => {
        if (res?.access_token) localStorage.setItem('token', res.access_token);
        if (res?.user || res?.data)
          this.updateCurrentUserState(res.user || res.data);
      })
    );
  }

  handleGoogleLoginSuccess(res: any): boolean {
    if (res?.access_token) {
      localStorage.setItem('token', res.access_token);
    }
    if (res?.user) {
      this.updateCurrentUserState(res.user);
    }
    // Kembalikan status true jika token berhasil disuntikkan ke HP
    return !!localStorage.getItem('token');
  }

  verifyOTP(email: string, otp: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/verify-otp`, { email, otp }).pipe(
      tap((res: any) => {
        // 🟢 DIKONDISIKAN: Menangkap token akses jika backend memberikan token otomatis setelah verifikasi sukses
        if (res?.access_token || res?.token) {
          localStorage.setItem('token', res.access_token || res.token);
        }
        if (res?.user || res?.data)
          this.updateCurrentUserState(res.user || res.data);
      })
    );
  }

  sendResetOtp(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password/send-otp`, { email });
  }

  sendRegisterOtp(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/resend-otp`, { email });
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

  // 🟢 CEK STATUS LOGIN: Mengembalikan nilai true hanya jika token fisik benar-benar tersimpan di HP
  isLoggedIn(): boolean {
    return !!localStorage.getItem('token');
  }

  logout() {
    this.clearStorageState();
  }

  getProfileFromServer(): Observable<any> {
    const headers = new HttpHeaders({
      Authorization: `Bearer ${localStorage.getItem('token')}`,
      Accept: 'application/json',
    });
    return this.http.get(`${this.apiUrl}/user`, { headers }).pipe(
      tap((res: any) => {
        if (res) this.updateCurrentUserState(res.user || res.data || res);
      })
    );
  }

  updateProfile(data: any): Observable<any> {
    const headers = new HttpHeaders({
      Authorization: `Bearer ${localStorage.getItem('token')}`,
      Accept: 'application/json',
    });
    return this.http.put(`${this.apiUrl}/user/update`, data, { headers }).pipe(
      tap((res: any) => {
        if (res) this.updateCurrentUserState(res.user || res.data || res);
      })
    );
  }

  getCoursesFromServer(): Observable<any> {
    const headers = new HttpHeaders({
      Authorization: `Bearer ${localStorage.getItem('token')}`,
      Accept: 'application/json',
    });
    return this.http.get(`${this.apiUrl}/courses`, { headers });
  }
}
