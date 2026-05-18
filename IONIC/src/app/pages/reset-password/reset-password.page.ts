import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth';
import { ToastController, LoadingController } from '@ionic/angular'; // TAMBAHKAN LoadingController DI SINI LEK

@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.page.html',
  styleUrls: ['./reset-password.page.scss'],
  standalone: false
})
export class ResetPasswordPage implements OnInit {
  email: string = '';
  otpKode: string = '';
  newPassword: string = '';
  confirmPassword: string = '';
  showPassword: boolean = false;
  showConfirmPassword: boolean = false;

  constructor(
    private authService: AuthService, 
    private router: Router,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController // DAFTARKAN DI SINI
  ) {}

  ngOnInit() {
    const navigation = this.router.getCurrentNavigation();
    if (navigation?.extras.state) {
      this.email = navigation.extras.state['email'];
      this.otpKode = navigation.extras.state['otp'];
    }

    if (!this.email || !this.otpKode) {
      this.router.navigate(['/forgot-password']);
    }
  }

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

  async updatePassword() {
    if (this.newPassword !== this.confirmPassword) {
      this.presentToast('Konfirmasi password tidak cocok!', 'warning');
      return;
    }

    // MEMBUAT & MEMUNCULKAN LOADING SEGERA SETELAH DIKLIK
    const loading = await this.loadingCtrl.create({
      message: 'Memperbarui password...',
      spinner: 'crescent'
    });
    await loading.present();

    const payload = {
      email: this.email,
      otp: this.otpKode,
      password: this.newPassword,
      password_confirmation: this.confirmPassword
    };

    this.authService.resetPassword(payload).subscribe({
      next: async (res: any) => {
        await loading.dismiss(); // Matikan loading saat sukses
        this.presentToast('Mantap! ' + res.message, 'success');
        this.router.navigate(['/login']);
      },
      error: async (error: any) => {
        await loading.dismiss(); // Matikan loading saat gagal
        if (error.status === 422 && error.error.errors) {
          const validationErrors = Object.values(error.error.errors).join('\n');
          this.presentToast('Validasi Gagal:\n' + validationErrors, 'danger');
        } else {
          this.presentToast(error.error?.message || 'Gagal memperbarui password.', 'danger');
        }
      }
    });
  }
}