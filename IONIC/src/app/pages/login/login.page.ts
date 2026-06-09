import { Component, OnInit, NgZone, ViewEncapsulation } from '@angular/core';
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
  encapsulation: ViewEncapsulation.None,
  standalone: false,
})
export class LoginPage implements OnInit {
  loginForm: FormGroup;
  showPassword = false;
  isLoading = false;

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private navCtrl: NavController,
    private zone: NgZone,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController,
    private router: Router
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
    });
  }

  ngOnInit() {
    if (this.auth.isLoggedIn()) {
      this.zone.run(() => {
        this.router.navigateByUrl('/tabs/beranda');
      });
    }
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

  async onLogin() {
    if (this.loginForm.valid) {
      this.isLoading = true;

      const loading = await this.loadingCtrl.create({
        message: 'Mohon tunggu...',
        spinner: 'crescent',
      });
      await loading.present();

      this.auth.login(this.loginForm.value).subscribe({
        next: async (res: any) => {
          await loading.dismiss();
          this.isLoading = false;

          if (res.token) localStorage.setItem('token', res.token);
          if (res.user) {
            localStorage.setItem('user_data', JSON.stringify(res.user));
            localStorage.setItem('user', JSON.stringify(res.user));
          }

          this.presentToast('Selamat datang kembali!', 'primary');
          this.zone.run(() => {
            this.router.navigateByUrl('/tabs/beranda');
          });
        },
        error: async (err) => {
          await loading.dismiss();
          this.isLoading = false;

          let msg = 'Gagal masuk. Periksa kembali email dan password Anda.';

          if (err.status === 401) {
            msg = 'Email atau Password salah.';
          } else if (err.status === 403) {
            msg = 'Akun belum diverifikasi. Silakan cek email Anda.';
            this.zone.run(() => {
              this.router.navigate(['/verify-otp'], {
                state: { email: this.loginForm.value.email },
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

  async loginWithGoogle() {
    this.isLoading = true;
    const authUrl = 'https://eduvan.rehalivan.com/api/auth/google';

    const targetWindow = window.open(
      authUrl,
      '_blank',
      'location=yes,clearcache=yes,clearsessioncache=yes,cleartoolbar=yes'
    );

    if (!targetWindow) {
      this.isLoading = false;
      this.presentToast(
        'Gagal membuka browser autentikasi. Periksa izin pop-up HP Anda.',
        'danger'
      );
      return;
    }

    const loading = await this.loadingCtrl.create({
      message: 'Menghubungkan ke Google...',
      spinner: 'crescent',
    });
    await loading.present();

    // Buat referensi interval di luar scope agar bisa dihancurkan di mana saja
    let checkClosed: any;

    const cleanupAuth = async () => {
      if (checkClosed) clearInterval(checkClosed);
      window.removeEventListener('message', authListener);
      this.isLoading = false;
      await loading.dismiss();
    };

    const authListener = async (event: MessageEvent) => {
      if (event.origin !== 'https://eduvan.rehalivan.com') return;

      if (event.data && event.data.success === true) {
        // Hancurkan semua pemantau seketika demi mengamankan memori
        await cleanupAuth();

        if (event.data.access_token) {
          localStorage.setItem('token', event.data.access_token);
        }
        if (event.data.user) {
          localStorage.setItem('user_data', JSON.stringify(event.data.user));
          localStorage.setItem('user', JSON.stringify(event.data.user));
        }

        this.auth.handleGoogleLoginSuccess(event.data);

        // Tutup jendela pop-up dengan aman
        try {
          if (targetWindow) targetWindow.close();
        } catch (e) {
          // Abaikan cross-origin DOM close exception
        }

        this.presentToast('Login Google Berhasil!', 'primary');

        this.zone.run(() => {
          this.router.navigateByUrl('/tabs/beranda').then((navigated) => {
            if (!navigated) {
              window.location.href = '/tabs/beranda';
            }
          });
        });
      }
    };

    window.addEventListener('message', authListener);

    // 🟢 STRATEGI BARU: Deteksi pasif yang aman dari blokir COOP Browser 🟢
    // Kita tidak membaca properti internal targetWindow.closed secara langsung
    checkClosed = setInterval(() => {
      // Kita hanya mengecek keberadaan objek window secara berkala,
      // jika window hilang atau dihancurkan browser, kita reset loading secara aman.
      if (!targetWindow) {
        cleanupAuth();
      }
    }, 1500);

    // Backup pelindung tambahan: jika window dialihkan atau diclose paksa, hentikan spinner setelah 35 detik
    setTimeout(() => {
      if (this.isLoading) {
        cleanupAuth();
      }
    }, 35000);
  }

  async presentToast(message: string, color: string) {
    const toast = await this.toastCtrl.create({
      message: message,
      duration: 2500,
      position: 'top',
      cssClass: 'toast-eduvan-new',
      buttons: [
        {
          side: 'start',
          icon: color === 'danger' ? 'alert-circle' : 'checkmark-circle',
          role: 'cancel',
        },
      ],
    });
    await toast.present();
  }
}
