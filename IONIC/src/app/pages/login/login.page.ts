import { Component, OnInit, NgZone } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth';
import { NavController, ToastController, LoadingController } from '@ionic/angular';
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
    private router: Router
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]]
    });
  }

  ngOnInit() {}

  togglePassword() {
    this.showPassword = !this.showPassword;
  }

  // Navigasi ke halaman daftar
  goToRegister() {
    this.zone.run(() => {
      this.router.navigate(['/register']);
    });
  }

  async onLogin() {
    if (this.loginForm.valid) {
      const loading = await this.loadingCtrl.create({
        message: 'Mohon tunggu...',
        spinner: 'crescent'
      });
      await loading.present();

      this.auth.login(this.loginForm.value).subscribe({
        next: async (res) => {
          await loading.dismiss();
          
          const toast = await this.toastCtrl.create({
            message: 'Selamat datang kembali!',
            duration: 2000,
            color: 'success',
            position: 'bottom'
          });
          await toast.present();

          this.zone.run(() => {
            this.navCtrl.navigateRoot('/home');
          });
        },
        error: async (err) => {
          await loading.dismiss();
          console.error('ERROR LOGIN:', err);

          let msg = 'Gagal masuk. Periksa kembali email dan password Anda.';
          if (err.status === 401) {
            msg = 'Email atau Password salah.';
          }

          const toast = await this.toastCtrl.create({
            message: msg,
            duration: 3000,
            color: 'danger',
            position: 'bottom'
          });
          await toast.present();
        }
      });
    } else {
      this.loginForm.markAllAsTouched();
    }
  }
}