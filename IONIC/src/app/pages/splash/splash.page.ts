import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-splash',
  templateUrl: './splash.page.html',
  styleUrls: ['./splash.page.scss'],
  standalone: false,
})
export class SplashPage implements OnInit {
  constructor(private router: Router) {}

  ngOnInit() {
    // Tahan halaman splash selama 2.5 detik untuk nampilin animasi bulat
    setTimeout(() => {
      // 🟢 PAKAI LOCALSTORAGE BIAR ANTI-EROR MERAH LEK
      const hasSeenWelcome = localStorage.getItem('hasSeenWelcome');

      if (hasSeenWelcome === 'true') {
        // Kalau sudah pernah buka app, langsung oper ke Login / Home
        this.router.navigateByUrl('/login', { replaceUrl: true });
      } else {
        // Kalau pertama kali install, arahkan ke Welcome Page / Slider Tutorial
        localStorage.setItem('hasSeenWelcome', 'true');
        this.router.navigateByUrl('/welcome', { replaceUrl: true });
      }
    }, 2500);
  }
}
