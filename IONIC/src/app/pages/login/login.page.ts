import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth';
import { Router } from '@angular/router';
import { ToastController, LoadingController } from '@ionic/angular';

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
    private router: Router,
    private loadingCtrl: LoadingController,
    private toastCtrl: ToastController
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required]]
    });
  }

  ngOnInit() { }

  togglePassword() {
    this.showPassword = !this.showPassword;
  }

  async onLogin() {
    if (this.loginForm.valid) {
      const loading = await this.loadingCtrl.create({ 
        message: 'Sedang masuk...', 
        spinner: 'crescent' 
      });
      await loading.present();

      this.auth.login(this.loginForm.value).subscribe({
        next: (res) => {
          loading.dismiss();
          this.router.navigateByUrl('/login');
        },
        error: async (err) => {
          loading.dismiss();
          console.error('ERROR LOGIN:', err);
          
          // Mengambil pesan error dari Laravel (misal: "Kredensial salah")
          const errorMessage = err.error?.message || 'Login Gagal. Cek kembali email & password.';
          
          const toast = await this.toastCtrl.create({
            message: errorMessage,
            duration: 2500,
            color: 'danger',
            position: 'bottom'
          });
          toast.present();
        }
      });
    } else {
      this.loginForm.markAllAsTouched();
    }
  }
}