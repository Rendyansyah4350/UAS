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
  selector: 'app-register',
  templateUrl: './register.page.html',
  styleUrls: ['./register.page.scss'],
  standalone: false,
})
export class RegisterPage implements OnInit {
  registerForm: FormGroup;
  showPassword = false;
  showConfirmPassword = false;

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private navCtrl: NavController,
    private zone: NgZone,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController,
    private router: Router
  ) {
    this.registerForm = this.fb.group(
      {
        name: ['', [Validators.required]],
        email: ['', [Validators.required, Validators.email]],
        password: ['', [Validators.required, Validators.minLength(8)]],
        confirmPassword: ['', [Validators.required]],
      },
      {
        validators: this.mustMatch('password', 'confirmPassword'),
      }
    );
  }

  ngOnInit() {}

  mustMatch(controlName: string, matchingControlName: string) {
    return (formGroup: FormGroup) => {
      const control = formGroup.controls[controlName];
      const matchingControl = formGroup.controls[matchingControlName];
      if (matchingControl.errors && !matchingControl.errors['mustMatch'])
        return;

      if (control.value !== matchingControl.value) {
        matchingControl.setErrors({ mustMatch: true });
      } else {
        matchingControl.setErrors(null);
      }
    };
  }

  togglePassword() {
    this.showPassword = !this.showPassword;
  }
  toggleConfirmPassword() {
    this.showConfirmPassword = !this.showConfirmPassword;
  }

  goToLogin() {
    this.zone.run(() => {
      this.navCtrl.navigateBack('/login');
    });
  }

  goToVerifyOtp() {
    this.zone.run(() => {
      this.router.navigate(['/verify-otp']);
    });
  }

  async onRegister() {
    if (this.registerForm.valid) {
      const loading = await this.loadingCtrl.create({
        message: 'Mendaftarkan akun...',
        spinner: 'crescent',
      });
      await loading.present();

      const formVal = this.registerForm.value;
      const dataKeLaravel = {
        name: formVal.name,
        email: formVal.email,
        password: formVal.password,
        password_confirmation: formVal.confirmPassword,
      };

      this.auth.register(dataKeLaravel).subscribe({
        next: async (res) => {
          await loading.dismiss();
          const toast = await this.toastCtrl.create({
            message:
              'Registrasi berhasil! Silakan cek email untuk kode verifikasi.',
            duration: 3000,
            color: 'success',
            position: 'bottom',
          });
          await toast.present();

          this.zone.run(() => {
            this.router.navigate(['/verify-otp'], {
              state: { email: formVal.email },
            });
          });
        },
        error: async (err) => {
          await loading.dismiss();
          const toast = await this.toastCtrl.create({
            message: err.error?.message || 'Registrasi gagal. Coba lagi.',
            duration: 2500,
            color: 'danger',
            position: 'bottom',
          });
          await toast.present();
        },
      });
    } else {
      this.registerForm.markAllAsTouched();
    }
  }

  // 🟢 PERBAIKAN: LOGIC REGISTER GOOGLE SINKRON BEBAS ERROR COOP 🟢
  async loginWithGoogle() {
    const authUrl = 'https://eduvan.rehalivan.com/api/auth/google';

    const targetWindow = window.open(
      authUrl,
      '_blank',
      'location=yes,clearcache=yes,clearsessioncache=yes,cleartoolbar=yes'
    );

    if (!targetWindow) {
      const toast = await this.toastCtrl.create({
        message:
          'Popup diblokir browser. Jika menggunakan mode responsive inspect, matikan dulu atau izinkan pop-up di kanan atas url browser.',
        duration: 4000,
        color: 'danger',
        position: 'bottom',
      });
      await toast.present();
      return;
    }

    const loading = await this.loadingCtrl.create({
      message: 'Menghubungkan ke Google...',
      spinner: 'crescent',
    });
    await loading.present();

    let checkClosed: any;

    // Fungsi pembersihan memori dan state loading spinner
    const cleanupAuth = async () => {
      if (checkClosed) clearInterval(checkClosed);
      window.removeEventListener('message', authListener);
      await loading.dismiss();
    };

    const authListener = async (event: MessageEvent) => {
      if (event.origin !== 'https://eduvan.rehalivan.com') return;

      if (event.data && event.data.success === true) {
        // Hancurkan interval pembaca window terlebih dahulu
        await cleanupAuth();

        if (event.data.access_token) {
          localStorage.setItem('token', event.data.access_token);
        }
        if (event.data.user) {
          localStorage.setItem('user_data', JSON.stringify(event.data.user));
          localStorage.setItem('user', JSON.stringify(event.data.user));
        }

        if (
          this.auth &&
          typeof this.auth.handleGoogleLoginSuccess === 'function'
        ) {
          this.auth.handleGoogleLoginSuccess(event.data);
        }

        // Tutup jendela pop-up jembatan Google secara aman tanpa interupsi
        try {
          if (targetWindow) targetWindow.close();
        } catch (e) {
          // Abaikan cross-origin exception
        }

        const toast = await this.toastCtrl.create({
          message: 'Registrasi Google Berhasil!',
          duration: 2000,
          color: 'success',
          position: 'bottom',
        });
        await toast.present();

        this.zone.run(() => {
          this.navCtrl.navigateRoot('/tabs/beranda').then(() => {
            this.router.navigateByUrl('/tabs/beranda');
          });
        });
      }
    };

    window.addEventListener('message', authListener);

    // 🚀 OLAHAN PASIF: Pantau objek window tanpa menyentuh properti .closed yang memicu error COOP
    checkClosed = setInterval(() => {
      if (!targetWindow) {
        cleanupAuth();
      }
    }, 1500);

    // Proteksi cadangan jika user membiarkan pop-up menggantung tanpa aksi selama 35 detik
    setTimeout(() => {
      cleanupAuth();
    }, 35000);
  }
}
