import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth';

@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.page.html',
  styleUrls: ['./reset-password.page.scss'],
  standalone: false
})
export class ResetPasswordPage implements OnInit {
  email: string = '';
  otpKode: string = ''; // Menampung kiriman data OTP dari page sebelah
  newPassword: string = '';
  confirmPassword: string = '';
  showPassword: boolean = false;

  constructor(private authService: AuthService, private router: Router) {}

  ngOnInit() {
    // Menangkap data lembaran dari forgot-password page
    const navigation = this.router.getCurrentNavigation();
    if (navigation?.extras.state) {
      this.email = navigation.extras.state['email'];
      this.otpKode = navigation.extras.state['otp']; // Menerima kode OTP rahasia
    }

    // Keamanan: Jika coba tembus paksa tanpa validasi OTP, usir balik
    if (!this.email || !this.otpKode) {
      this.router.navigate(['/forgot-password']);
    }
  }

updatePassword() {
    if (this.newPassword !== this.confirmPassword) {
      return alert('Konfirmasi password tidak cocok!');
    }

    const payload = {
      email: this.email,
      otp: this.otpKode,
      password: this.newPassword,
      password_confirmation: this.confirmPassword
    };

    this.authService.resetPassword(payload).subscribe(
      (res: any) => {
        alert('Mantap! ' + res.message);
        this.router.navigate(['/login']);
      },
      (error: any) => {
        // PERBAIKAN: Tangkap error spesifik dari Laravel validation
        if (error.status === 422 && error.error.errors) {
          // Menggabungkan semua pesan error dari Laravel menjadi satu teks alert
          const validationErrors = Object.values(error.error.errors).join('\n');
          alert('Validasi Gagal:\n' + validationErrors);
        } else {
          alert(error.error?.message || 'Gagal memperbarui password.');
        }
      }
    );
  }
}