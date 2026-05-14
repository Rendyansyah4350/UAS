import { Component } from '@angular/core';
import { Router } from '@angular/router';
import {
  LoadingController,
  ToastController,
  NavController,
} from '@ionic/angular';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.page.html',
  styleUrls: ['./forgot-password.page.scss'],
  standalone: false,
})
export class ForgotPasswordPage {
  // Variabel Kontrol Alur
  step: number = 1; // 1: Email, 2: OTP, 3: Password Baru

  // Variabel Data
  email: string = '';
  otpCode: string = '';
  newPassword: string = '';
  confirmPassword: string = '';

  // Variabel UI
  showPassword = false;

  constructor(
    private router: Router,
    private navCtrl: NavController,
    private loadingCtrl: LoadingController,
    private toastCtrl: ToastController,
  ) {}

  // --- STEP 1: KIRIM OTP ---
  async sendOtp() {
    if (!this.email || !this.email.includes('@')) {
      this.presentToast('Silakan masukkan format email yang benar', 'warning');
      return;
    }

    const loading = await this.loadingCtrl.create({
      message: 'Mengirim kode OTP...',
      spinner: 'crescent',
    });
    await loading.present();

    // Simulasi API Laravel
    setTimeout(async () => {
      await loading.dismiss();
      this.presentToast('Kode OTP telah dikirim ke Gmail Anda', 'success');
      this.step = 2; // Pindah ke tampilan input OTP
    }, 2000);
  }

  // --- STEP 2: VERIFIKASI OTP ---
  async verifyOtp() {
    if (!this.otpCode || this.otpCode.toString().length < 4) {
      this.presentToast('Masukkan kode OTP yang valid', 'warning');
      return;
    }

    const loading = await this.loadingCtrl.create({
      message: 'Memverifikasi kode...',
      spinner: 'crescent',
    });
    await loading.present();

    // Simulasi Cek OTP ke Backend
    setTimeout(async () => {
      await loading.dismiss();
      this.presentToast('OTP Valid. Silakan buat password baru', 'success');
      this.step = 3; // Pindah ke tampilan ganti password
    }, 2000);
  }

  // --- STEP 3: UPDATE PASSWORD ---
  async updatePassword() {
    if (this.newPassword.length < 8) {
      this.presentToast('Password minimal 8 karakter', 'warning');
      return;
    }

    if (this.newPassword !== this.confirmPassword) {
      this.presentToast('Konfirmasi password tidak cocok', 'danger');
      return;
    }

    const loading = await this.loadingCtrl.create({
      message: 'Memperbarui password...',
      spinner: 'crescent',
    });
    await loading.present();

    // Simulasi Update Database Laravel
    setTimeout(async () => {
      await loading.dismiss();
      this.presentToast('Password berhasil diperbarui!', 'success');

      // Kembali ke Login setelah sukses
      this.navCtrl.navigateRoot('/login');
    }, 2000);
  }

  // Navigasi & UI Helpers
  goToLogin() {
    this.navCtrl.back();
  }

  async presentToast(message: string, color: 'success' | 'warning' | 'danger') {
    const toast = await this.toastCtrl.create({
      message: message,
      duration: 2500,
      color: color,
      position: 'bottom',
      buttons: [{ text: 'OK', role: 'cancel' }],
    });
    await toast.present();
  }
}