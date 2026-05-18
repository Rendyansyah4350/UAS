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
  emailForVerify: string = ''; // Disamakan dengan {{ emailForVerify }} di HTML

  constructor(
    private router: Router,
    private auth: AuthService, // Ditambahkan untuk memproses verifikasi ke backend
    private zone: NgZone,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController, // Ditambahkan untuk efek loading spinner
    private navCtrl: NavController // Ditambahkan untuk navigasi back aman khas Ionic
  ) {
    // Mengambil data email yang dikirim saat navigasi dari Register atau Login (403)
    const nav = this.router.getCurrentNavigation();
    if (nav?.extras.state) {
      // Mengantisipasi jika dikirim dengan key 'email' atau 'userEmail'
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

  // Nama fungsi disamakan dengan (click)="onVerifyOTP()" di HTML
  async onVerifyOTP() {
    // Validasi panjang OTP (di HTML kita set maksimal 6 karakter)
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
          // Setelah sukses, lempar ke halaman login
          this.navCtrl.navigateRoot('/login');
        });
      },
      error: async (err) => {
        await loading.dismiss();
        this.presentToast('Kode verifikasi salah atau sudah kadaluwarsa.', 'danger');
      },
    });
  }

  // Fungsi untuk tombol "Go Back" di HTML
  goBack() {
    this.navCtrl.navigateBack('/login');
  }

  async presentToast(msg: string, color: string) {
    const toast = await this.toastCtrl.create({ 
      message: msg, 
      duration: 3000, 
      color: color,
      position: 'bottom'
    });
    await toast.present();
  }
}