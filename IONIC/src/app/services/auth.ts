import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { Observable, tap, BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private apiUrl = environment.apiUrl;

  private currentUserSubject = new BehaviorSubject<any>(null);
  currentUser$ = this.currentUserSubject.asObservable();

  constructor(private http: HttpClient) {
    const savedUser = localStorage.getItem('user_data');
    if (savedUser) {
      this.currentUserSubject.next(JSON.parse(savedUser));
    }
  }

  updateCurrentUserState(userData: any) {
    localStorage.setItem('user_data', JSON.stringify(userData));
    this.currentUserSubject.next(userData);
  }

  login(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, data).pipe(
      tap((res: any) => {
        if (res && res.access_token) localStorage.setItem('token', res.access_token);
        if (res && (res.user || res.data)) this.updateCurrentUserState(res.user || res.data);
      }),
    );
  }

  verifyOTP(email: string, otp: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/verify-otp`, { email, otp }).pipe(
      tap((res: any) => {
        if (res && res.token) localStorage.setItem('token', res.token);
        if (res && (res.user || res.data)) this.updateCurrentUserState(res.user || res.data);
      }),
    );
  }
  
  sendResetOtp(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password/send-otp`, { email });
  }

  // 🟢 FUNGSI RESEND: Coba pakai ini dulu. 
  // Jika masih 404, ganti URL-nya ke '/forgot-password/send-otp'
  sendRegisterOtp(email: string): Observable<any> {
    // Jalur ini yang tadi error 404
    // Silakan ganti ke: return this.http.post(`${this.apiUrl}/forgot-password/send-otp`, { email }); 
    // jika jalur di bawah ini tetap tidak ditemukan oleh server.
    return this.http.post(`${this.apiUrl}/register/send-otp`, { email });
  }

  verifyResetOtp(email: string, otp: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/forgot-password/verify-otp`, { email, otp });
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
    this.currentUserSubject.next(null);
  }

  getProfileFromServer(): Observable<any> {
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({ Authorization: `Bearer ${token}`, Accept: 'application/json' });
    return this.http.get(`${this.apiUrl}/user`, { headers }).pipe(
      tap((res: any) => { if (res) this.updateCurrentUserState(res.user || res.data || res); })
    );
  }

  updateProfile(data: any): Observable<any> {
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({ Authorization: `Bearer ${token}`, Accept: 'application/json' });
    return this.http.put(`${this.apiUrl}/user/update`, data, { headers }).pipe(
      tap((res: any) => { if (res) this.updateCurrentUserState(res.user || res.data || res); })
    );
  }

  getCoursesFromServer(): Observable<any> {
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({ Authorization: `Bearer ${token}`, Accept: 'application/json' });
    return this.http.get(`${this.apiUrl}/courses`, { headers });
  }
}
