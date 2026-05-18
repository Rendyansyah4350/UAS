import { Component, OnInit, NgZone } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth';
import {
  NavController,
  ToastController,
  LoadingController,
} from '@ionic/angular';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
  standalone: false,
})
export class LoginPage implements OnInit {
  loginForm: FormGroup;
  showPassword = false;

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private navCtrl: NavController,
    private zone: NgZone,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController,
    private router: Router,
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
    });
  }

  ngOnInit() {
    // Bersih dari pengecekan queryParams OTP register
  }

  togglePassword() {
    this.showPassword = !this.showPassword;
  }

  goToRegister() {
    this.zone.run(() => {
      this.router.navigate(['/register']);
    });
  }

  goToForgotPassword() {
    this.router.navigate(['/forgot-password']);
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
        next: async (res: any) => {
          await loading.dismiss();

          // Simpan token dan data user agar tidak ditendang AuthGuard
          if (res.token) {
            localStorage.setItem('token', res.token);
          }
          if (res.user) {
            localStorage.setItem('user_data', JSON.stringify(res.user));
            localStorage.setItem('user', JSON.stringify(res.user));
          }

          this.presentToast('Selamat datang kembali!', 'success');
          this.zone.run(() => {
            this.navCtrl.navigateRoot('/tabs');
          });
        },
        error: async (err) => {
          await loading.dismiss();
          let msg = 'Gagal masuk. Periksa kembali email dan password Anda.';

          if (err.status === 401) {
            msg = 'Email atau Password salah.';
          } else if (err.status === 403) {
            msg = 'Akun belum diverifikasi. Silakan cek email Anda.';

            // OPSIONAL: Jika backend mengembalikan status 403 (belum verifikasi),
            // kamu bisa otomatis melempar user ke halaman verify-otp dengan membawa email mereka.
            this.zone.run(() => {
              this.router.navigate(['/verify-otp'], {
                state: { email: this.loginForm.value.email }
              });
            });
          }

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
}