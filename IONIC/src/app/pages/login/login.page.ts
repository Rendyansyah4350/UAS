import { Component, OnInit, NgZone } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth';
import {
  NavController,
  ToastController,
  LoadingController,
} from '@ionic/angular';
import { Router, ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
  standalone: false,
})
export class LoginPage implements OnInit {
  loginForm: FormGroup;
  showPassword = false;

  // Variabel Kontrol Verifikasi (OTP)
  otpSent = false;
  otpCode: string = '';
  emailForVerify: string = '';

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private navCtrl: NavController,
    private zone: NgZone,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController,
    private router: Router,
    private route: ActivatedRoute,
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
    });
  }

  ngOnInit() {
    // Menangkap email dari halaman Register (misal: /login?email=xxx&verify=true)
    this.route.queryParams.subscribe((params) => {
      if (params['email'] && params['verify'] === 'true') {
        this.emailForVerify = params['email'];
        this.otpSent = true;
        this.presentToast(
          'Silakan cek email Anda untuk kode verifikasi.',
          'primary',
        );
      }
    });
  }

  togglePassword() {
    this.showPassword = !this.showPassword;
  }

  goToRegister() {
    this.zone.run(() => {
      this.router.navigate(['/register']);
    });
  }

  // LOGIKA VERIFIKASI OTP
  async onVerifyOTP() {
    if (!this.otpCode || this.otpCode.toString().length < 4) {
      this.presentToast('Masukkan kode verifikasi yang valid.', 'warning');
      return;
    }

    const loading = await this.loadingCtrl.create({
      message: 'Memverifikasi akun...',
      spinner: 'crescent',
    });
    await loading.present();

    this.auth.verifyOTP(this.emailForVerify, this.otpCode).subscribe({
      next: async (res) => {
        await loading.dismiss();
        this.otpSent = false;
        this.presentToast(
          'Akun berhasil diverifikasi! Silakan masuk.',
          'success',
        );
      },
      error: async (err) => {
        await loading.dismiss();
        this.presentToast(
          'Kode verifikasi salah atau sudah kadaluwarsa.',
          'danger',
        );
      },
    });
  }

  // LOGIKA LOGIN UTAMA
  async onLogin() {
    if (this.loginForm.valid) {
      const loading = await this.loadingCtrl.create({
        message: 'Mohon tunggu...',
        spinner: 'crescent',
      });
      await loading.present();

      this.auth.login(this.loginForm.value).subscribe({
        next: async (res) => {
          await loading.dismiss();
          this.presentToast('Selamat datang kembali!', 'success');
          this.zone.run(() => {
            this.navCtrl.navigateRoot('/home');
          });
        },
        error: async (err) => {
          await loading.dismiss();
          let msg = 'Gagal masuk. Periksa kembali email dan password Anda.';
          if (err.status === 401) msg = 'Email atau Password salah.';
          this.presentToast(msg, 'danger');
        },
      });
    } else {
      this.loginForm.markAllAsTouched();
    }
  }

  async presentToast(message: string, color: string) {
    const toast = await this.toastCtrl.create({
      message: message,
      duration: 3000,
      color: color,
      position: 'bottom',
    });
    await toast.present();
  }

  goToForgotPassword() {
    this.router.navigate(['/forgot-password']);
  }
}
