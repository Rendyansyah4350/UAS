import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { LoadingController, ToastController } from '@ionic/angular';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.page.html',
  styleUrls: ['./forgot-password.page.scss'],
  standalone: false,
})
export class ForgotPasswordPage {
  email: string = '';

  goToLogin() {
    this.router.navigate(['/login']);
  }

  constructor(
    private router: Router,
    private loadingCtrl: LoadingController,
    private toastCtrl: ToastController
  ) {}

  async sendOtp() {
    // Validasi sederhana: pastikan email diisi
    if (!this.email) {
      this.presentToast('Silakan masukkan email Anda', 'warning');
      return;
    }

    const loading = await this.loadingCtrl.create({ 
      message: 'Mengirim kode OTP...',
      spinner: 'crescent'
    });
    await loading.present();

    // --- Simulasi Hit API Laravel ---
    // Nanti di sini Anda akan memanggil AuthService Anda
    // this.authService.requestOtp(this.email).subscribe(...)
    
    setTimeout(async () => {
      await loading.dismiss();
      
      this.presentToast('Kode OTP telah dikirim ke Gmail Anda', 'success');

      // Navigasi ke Verify OTP dan kirim email via state
      this.router.navigate(['/verify-otp'], { 
        state: { userEmail: this.email } 
      });
    }, 2000);
  }

  async presentToast(message: string, color: 'success' | 'warning' | 'danger') {
    const toast = await this.toastCtrl.create({
      message: message,
      duration: 2000,
      color: color,
      position: 'bottom'
    });
    await toast.present();
  }
}