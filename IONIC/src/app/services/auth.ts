import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

  // Fungsi Login
  login(credentials: {email: string, password: string}) {
    return this.http.post(`${this.apiUrl}/login`, credentials).pipe(
      tap((res: any) => {
        // Simpan token ke localStorage agar tidak hilang saat refresh
        localStorage.setItem('auth-token', res.token);
      })
    );
  }
  // Fungsi Register
  register(userData: any) {
    return this.http.post(`${this.apiUrl}/register`, userData).pipe(
      tap((res: any) => {
        // Biasanya setelah daftar, Laravel langsung memberikan token (auto-login)
        if(res.token) {
          localStorage.setItem('auth-token', res.token);
        }
      })
    );
  }
  // Cek apakah user sudah login
  isLoggedIn() {
    return !!localStorage.getItem('auth-token');
  }

  // Logout
  logout() {
    localStorage.removeItem('auth-token');
  }
}