// src/app/guards/auth-guard.ts
import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {
  constructor(private router: Router) {}

  canActivate(): boolean {
    // Cek apakah ada data user di storage
    const user = localStorage.getItem('user_data'); 
    
    if (user) {
      return true; // Izinkan masuk
    } else {
      this.router.navigate(['/login']); // Tendang ke login
      return false;
    }
  }
}