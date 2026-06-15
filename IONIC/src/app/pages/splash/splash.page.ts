import { Component, OnInit } from '@angular/core';
import { NavController, IonicModule } from '@ionic/angular'; // 🟢 KUNCIAN: Tambah IonicModule di sini
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-splash',
  templateUrl: './splash.page.html',
  styleUrls: ['./splash.page.scss'],
  standalone: true, // Status komponen mandiri lu
  imports: [IonicModule, CommonModule], // 🟢 KUNCIAN UTAMA: Daftarkan IonicModule di sini biar dia kenal 'ion-content' dan '[fullscreen]'
})
export class SplashPage implements OnInit {
  isFadingOut = false;

  constructor(private navCtrl: NavController) {}

  ngOnInit() {
    this.jalankanSplash();
  }

  async jalankanSplash() {
    await new Promise((resolve) => setTimeout(resolve, 2000));
    this.isFadingOut = true;
    await new Promise((resolve) => setTimeout(resolve, 400));

    // 🟢 Ambil data token login (sesuaikan nama key token di proyek kalian, misal 'token' atau 'user')
    const isLoggedIn = localStorage.getItem('token');
    const hasSeenWelcome = localStorage.getItem('hasSeenWelcome');

    if (isLoggedIn) {
      // Kalau sudah login, langsung loloskan ke halaman utama (Tabs)
      this.navCtrl.navigateRoot('/tabs', {
        animated: true,
        animationDirection: 'forward',
      });
    } else if (hasSeenWelcome === 'true') {
      // Kalau belum login tapi udah pernah liat welcome, ke Login
      this.navCtrl.navigateRoot('/login', {
        animated: true,
        animationDirection: 'forward',
      });
    } else {
      // Kalau bener-bener user baru gres setelah hapus data, ke Welcome
      localStorage.setItem('hasSeenWelcome', 'true');
      this.navCtrl.navigateRoot('/welcome', {
        animated: true,
        animationDirection: 'forward',
      });
    }
  }
}
