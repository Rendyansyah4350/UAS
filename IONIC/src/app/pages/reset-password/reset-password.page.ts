import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController, LoadingController } from '@ionic/angular';

@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.page.html',
  styleUrls: ['./reset-password.page.scss'],
  standalone: false,
})
export class ResetPasswordPage implements OnInit {
  newPassword: string = '';
  confirmPassword: string = '';
  showPassword: boolean = false;
  email: string = '';

  constructor(
    private router: Router,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController
  ) {
    // Mengambil email dari state navigasi (dikirim dari page sebelumnya)
    const nav = this.router.getCurrentNavigation();
    if (nav?.extras.state) {
      this.email = nav.extras.state['userEmail'];
    }
  }

  ngOnInit() {}

  togglePassword() {
    this.showPassword = !this.showPassword;
  }

  async onResetPassword() {
    // 1. Validasi Input Kosong
    if (!this.newPassword || !this.confirmPassword) {
      this.presentToast('Please fill in all fields', 'warning');
      return;
    }

    // 2. Validasi Kesamaan Password
    if (this.newPassword !== this.confirmPassword) {
      this.presentToast('Passwords do not match', 'danger');
      return;
    }

    // 3. Validasi Panjang Password (Contoh: min 8 karakter)
    if (this.newPassword.length < 8) {
      this.presentToast('Password must be at least 8 characters', 'warning');
      return;
    }

    const loading = await this.loadingCtrl.create({
      message: 'Updating password...',
      spinner: 'crescent'
    });
    await loading.present();

    // Simulasi Response dari Laravel
    setTimeout(async () => {
      await loading.dismiss();
      this.presentToast('Password updated successfully!', 'success');
      
      // Kembali ke halaman login
      this.router.navigate(['/login']);
    }, 2000);
  }

  async presentToast(message: string, color: string) {
    const toast = await this.toastCtrl.create({
      message: message,
      duration: 2000,
      color: color,
      position: 'bottom'
    });
    await toast.present();
  }

  goToLogin() {
    this.router.navigate(['/login']);
  }
}