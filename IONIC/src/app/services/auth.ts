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
        if (res?.access_token) localStorage.setItem('token', res.access_token);
        if (res?.user || res?.data) this.updateCurrentUserState(res.user || res.data);
      })
    );
  }

  verifyOTP(email: string, otp: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/verify-otp`, { email, otp }).pipe(
      tap((res: any) => {
        if (res?.token) localStorage.setItem('token', res.token);
        if (res?.user || res?.data) this.updateCurrentUserState(res.user || res.data);
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
    const headers = new HttpHeaders({
      Authorization: `Bearer ${localStorage.getItem('token')}`,
      Accept: 'application/json'
    });
    return this.http.get(`${this.apiUrl}/user`, { headers }).pipe(
      tap((res: any) => { if (res) this.updateCurrentUserState(res.user || res.data || res); })
    );
  }

  updateProfile(data: any): Observable<any> {
    const headers = new HttpHeaders({
      Authorization: `Bearer ${localStorage.getItem('token')}`,
      Accept: 'application/json'
    });
    return this.http.put(`${this.apiUrl}/user/update`, data, { headers }).pipe(
      tap((res: any) => { if (res) this.updateCurrentUserState(res.user || res.data || res); })
    );
  }

  getCoursesFromServer(): Observable<any> {
    const headers = new HttpHeaders({
      Authorization: `Bearer ${localStorage.getItem('token')}`,
      Accept: 'application/json'
    });
    return this.http.get(`${this.apiUrl}/courses`, { headers });
  }
}
