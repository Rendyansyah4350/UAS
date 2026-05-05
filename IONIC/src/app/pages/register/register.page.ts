import { Component, OnInit, NgZone } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth';
import { NavController, ToastController, LoadingController } from '@ionic/angular';

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
    private zone: NgZone, // Memastikan sinkronisasi URL di browser
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

      // Menyesuaikan data agar sesuai dengan validasi Laravel
      const formVal = this.registerForm.value;
      const dataKeLaravel = {
        name: formVal.name,
        email: formVal.email,
        password: formVal.password,
        password_confirmation: formVal.confirmPassword 
      };

      this.auth.register(dataKeLaravel).subscribe({
        next: async (res) => {
          await loading.dismiss();
          
          const toast = await this.toastCtrl.create({
            message: 'Registrasi berhasil! Silakan masuk.',
            duration: 2000,
            color: 'success',
            position: 'bottom'
          });
          await toast.present();
          
          // Memaksa navigasi berjalan di dalam Zone Angular agar URL terupdate
          this.zone.run(() => {
            this.navCtrl.navigateForward('/login');
          });
        },
        error: async (err) => {
          await loading.dismiss();
          console.error('ERROR REGISTER:', err);

          let msg = 'Registrasi gagal. Silakan coba lagi.';
          if (err.status === 422) {
            msg = err.error.message || 'Data tidak valid atau email sudah terdaftar.';
          }

          const toast = await this.toastCtrl.create({
            message: msg,
            duration: 2500,
            color: 'danger',
            position: 'bottom'
          });
          await toast.present();
        }
      });
    } else {
      this.registerForm.markAllAsTouched();
    }
  }
}