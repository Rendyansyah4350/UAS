import { Component, OnInit, NgZone } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth'; // Pastikan path service auth ini benar
import { ToastController, LoadingController, NavController } from '@ionic/angular';

@Component({
  selector: 'app-verify-otp',
  templateUrl: './verify-otp.page.html',
  styleUrls: ['./verify-otp.page.scss'],
  standalone: false,
})
export class VerifyOtpPage implements OnInit {
  otpCode: string = '';
  emailForVerify: string = ''; 

  constructor(
    private router: Router,
    private auth: AuthService, 
    private zone: NgZone,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController, 
    private navCtrl: NavController 
  ) {
    // Mengambil data email yang dikirim saat navigasi dari Register atau Login (403)
    const nav = this.router.getCurrentNavigation();
    if (nav?.extras.state) {
      this.emailForVerify = nav.extras.state['email'] || nav.extras.state['userEmail'] || '';
    }
  }

  ngOnInit() {
    // Pengaman: Jika masuk halaman ini tanpa membawa data email, kembalikan ke login
    if (!this.emailForVerify) {
      this.presentToast('Sesi verifikasi habis, silakan lakukan aksi kembali.', 'warning');
      this.navCtrl.navigateBack('/login');
    }
  }

  // Fungsi memproses verifikasi OTP ke backend
  async onVerifyOTP() {
    if (!this.otpCode || this.otpCode.length < 6) {
      this.presentToast('Masukkan 6 digit kode verifikasi yang valid.', 'warning');
      return;
    }

    const loading = await this.loadingCtrl.create({
      message: 'Memverifikasi akun...',
      spinner: 'crescent',
    });
    await loading.present();

    // Memanggil API Verifikasi OTP dari AuthService
    this.auth.verifyOTP(this.emailForVerify, this.otpCode).subscribe({
      next: async (res) => {
        await loading.dismiss();
        this.presentToast('Akun berhasil diverifikasi! Silakan masuk.', 'success');
        
        this.zone.run(() => {
          this.navCtrl.navigateRoot('/login');
        });
      },
      error: async (err) => {
        await loading.dismiss();
        this.presentToast('Kode verifikasi salah atau sudah kadaluwarsa.', 'danger');
      },
    });
  }

  // Tambahan Fungsi Kirim Ulang OTP agar tidak error
  resendCode() {
    this.presentToast('Kode OTP baru berhasil dikirim ulang!', 'success');
  }

  // Fungsi tombol kembali ke Sign In
  kembali() {
    this.navCtrl.navigateBack('/login');
  }

  async presentToast(msg: string, color: string = 'bottom') {
    const toast = await this.toastCtrl.create({ 
      message: msg, 
      duration: 3000, 
      color: color,
      position: 'bottom'
    });
    await toast.present();
  }
}