import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { AuthService } from '../services/auth';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {

  constructor(private auth: AuthService, private router: Router) {}

  canActivate(): boolean {
    // Memanggil fungsi isLoggedIn dari service
    if (this.auth.isLoggedIn()) {
      return true; // Token ada, izinkan masuk
    } else {
      // Token tidak ada, tendang kembali ke login
      this.router.navigateByUrl('/login');
      return false; 
    }
  }
}