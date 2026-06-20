import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth'; 
import { ToastController, LoadingController } from '@ionic/angular'; // TAMBAHKAN LoadingController DI SINI

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.page.html',
  styleUrls: ['./forgot-password.page.scss'],
  standalone: false
})
export class ForgotPasswordPage implements OnInit {
  email: string = '';
  otpCode: string = '';
  step: number = 1;

  constructor(
    private authService: AuthService, 
    private router: Router,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController // DAFTARKAN DI SINI
  ) {}

  ngOnInit() {}

  async presentToast(message: string, color: 'success' | 'danger' | 'warning') {
    const toast = await this.toastCtrl.create({
      message: message,
      duration: 3000,
      color: color,
      position: 'bottom',
      cssClass: 'premium-toast'
    });
    await toast.present();
  }

  async sendOtp() {
    if (!this.email) {
      this.presentToast('Email wajib diisi!', 'warning');
      return;
    }

    // MEMBUAT & MEMUNCULKAN LOADING SEGERA SETELAH DIKLIK
    const loading = await this.loadingCtrl.create({
      message: 'Mengirim kode OTP...',
      spinner: 'crescent'
    });
    await loading.present();

    this.authService.sendResetOtp(this.email).subscribe({
      next: async (res: any) => {
        await loading.dismiss(); // Matikan loading saat sukses
        this.presentToast(res.message, 'success');
        this.step = 2; 
      },
      error: async (error: any) => {
        await loading.dismiss(); // Matikan loading saat gagal
        this.presentToast(error.error?.message || 'Gagal mengirim OTP', 'danger');
      }
    });
  }

  async verifyOtp() {
    if (!this.otpCode) {
      this.presentToast('Kode OTP wajib diisi!', 'warning');
      return;
    }

    // MEMBUAT & MEMUNCULKAN LOADING SEGERA SETELAH DIKLIK
    const loading = await this.loadingCtrl.create({
      message: 'Memverifikasi kode...',
      spinner: 'crescent'
    });
    await loading.present();

    this.authService.verifyResetOtp(this.email, this.otpCode).subscribe({
      next: async (res: any) => {
        await loading.dismiss(); // Matikan loading saat sukses
        this.presentToast(res.message, 'success');
        this.router.navigate(['/reset-password'], {
          state: { email: this.email, otp: this.otpCode }
        });
      },
      error: async (error: any) => {
        await loading.dismiss(); // Matikan loading saat gagal
        this.presentToast(error.error?.message || 'Kode OTP salah atau expired!', 'danger');
      }
    });
  }

  goToLogin() {
    this.router.navigate(['/login']);
  }
}