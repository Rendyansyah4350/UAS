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
    private router: Router, 
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
      },
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

  // ======================================================================
  // PERBAIKAN: Menambahkan fungsi dengan penamaan camelCase yang valid
  // untuk mengatasi error compile "Property 'otp' does not exist" di HTML
  // ======================================================================
  goToVerifyOtp() {
    this.zone.run(() => {
      this.router.navigate(['/verify-otp']);
    });
  }
  // ======================================================================

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
            message: 'Registrasi berhasil! Silakan cek email untuk kode verifikasi.',
            duration: 3000,
            color: 'success',
            position: 'bottom',
          });
          await toast.present();

          // Alihkan navigasi langsung ke halaman /verify-otp dengan membawa state data email
          this.zone.run(() => {
            this.router.navigate(['/verify-otp'], {
              state: { email: formVal.email }
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
}