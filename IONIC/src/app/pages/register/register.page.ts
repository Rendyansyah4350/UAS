import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth';
import { Router } from '@angular/router';
import { ToastController, LoadingController } from '@ionic/angular';

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
    private router: Router,
    private toastCtrl: ToastController,
    private loadingCtrl: LoadingController
  ) {
    this.registerForm = this.fb.group({
      name: ['', [Validators.required]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      confirmPassword: ['', [Validators.required]]
    }, {
      validators: this.mustMatch('password', 'confirmPassword')
    });
  }

  ngOnInit() { }

  // Custom validator untuk mencocokkan password
  mustMatch(controlName: string, matchingControlName: string) {
    return (formGroup: FormGroup) => {
      const control = formGroup.controls[controlName];
      const matchingControl = formGroup.controls[matchingControlName];

      if (matchingControl.errors && !matchingControl.errors['mustMatch']) {
        return;
      }

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

  async onRegister() {
    if (this.registerForm.valid) {
      const loading = await this.loadingCtrl.create({ 
        message: 'Mendaftarkan akun...', 
        spinner: 'crescent' 
      });
      await loading.present();

      // Menghapus confirmPassword sebelum dikirim ke API Laravel
      const { confirmPassword, ...registerData } = this.registerForm.value;

      this.auth.register(registerData).subscribe({
        next: async (res) => {
          loading.dismiss();
          
          const toast = await this.toastCtrl.create({
            message: 'Registrasi berhasil! Silakan masuk.',
            duration: 2000,
            color: 'success',
            position: 'bottom'
          });
          await toast.present();
          
          this.router.navigateByUrl('/login');
        },
        error: async (err) => {
          loading.dismiss();
          console.error('ERROR REGISTER:', err);

          // Jika Laravel mengirim error 422 (Validation Error), biasanya email sudah ada
          let msg = 'Registrasi gagal. Silakan coba lagi.';
          if (err.status === 422) {
            msg = 'Email sudah terdaftar. Gunakan email lain.';
          } else if (err.error?.message) {
            msg = err.error.message;
          }

          const toast = await this.toastCtrl.create({
            message: msg,
            duration: 2500,
            color: 'danger',
            position: 'bottom'
          });
          toast.present();
        }
      });
    } else {
      this.registerForm.markAllAsTouched();
    }
  }
}