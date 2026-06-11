import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';

@Injectable({
  providedIn: 'root',
})
export class WelcomeGuard implements CanActivate {
  constructor(private router: Router) {}

  canActivate(): boolean {
    const sudahLogin = localStorage.getItem('user_data');
    const statusLama = localStorage.getItem('eduvan_user_registered');

    // 1. Kalau di memori ternyata SUDAH LOGIN, tendang langsung ke Beranda
    if (sudahLogin) {
      this.router.navigate(['/tabs/beranda'], { replaceUrl: true });
      return false; // Blokir rute awal, jangan kasih render Welcome!
    }

    // 2. Kalau SUDAH PERNAH DAFTAR tapi belum login, tendang ke Login
    if (statusLama === 'true') {
      this.router.navigate(['/login'], { replaceUrl: true });
      return false; // Blokir rute awal, jangan kasih render Welcome!
    }

    // 3. PENGGUNA BARU GRES: Baru boleh lolos ngerender halaman Welcome
    return true;
  }
}
