import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';

@Component({
  selector: 'app-verify-otp',
  templateUrl: './verify-otp.page.html',
  styleUrls: ['./verify-otp.page.scss'],
  standalone: false,
})
export class VerifyOtpPage implements OnInit {
  email: string = '';
  otpCode: string = '';

  constructor(private router: Router, private toastCtrl: ToastController) {
    // Mengambil data email yang dikirim saat navigasi
    const nav = this.router.getCurrentNavigation();
    if (nav?.extras.state) {
      this.email = nav.extras.state['userEmail'];
    }
  }

  ngOnInit() {}

  async verifyOtp() {
    if (this.otpCode.length < 4) {
      this.presentToast('Masukkan kode OTP yang valid', 'warning');
      return;
    }

    // Simulasi jika kode benar
    this.presentToast('OTP Berhasil diverifikasi', 'success');
    this.router.navigate(['/reset-password'], { state: { userEmail: this.email } });
  }

  async presentToast(msg: string, color: string) {
    const toast = await this.toastCtrl.create({ message: msg, duration: 2000, color: color });
    await toast.present();
  }
}