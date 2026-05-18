import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth'; // Sesuaikan folder service kalian

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.page.html',
  styleUrls: ['./forgot-password.page.scss'],
  standalone: false
})
export class ForgotPasswordPage implements OnInit {
  email: string = '';
  otpCode: string = '';
  step: number = 1;

  constructor(private authService: AuthService, private router: Router) {}

  ngOnInit() {}

  sendOtp() {
    if (!this.email) return alert('Email wajib diisi!');

    this.authService.sendResetOtp(this.email).subscribe(
      (res: any) => {
        alert(res.message);
        this.step = 2; // Naik ke step 2 (buka form input OTP)
      },
      (error: any) => {
        alert(error.error?.message || 'Gagal mengirim OTP');
      }
    );
  }

  verifyOtp() {
    if (!this.otpCode) return alert('Kode OTP wajib diisi!');

    this.authService.verifyResetOtp(this.email, this.otpCode).subscribe(
      (res: any) => {
        alert(res.message);
        // OTP Valid! Lempar data email dan otpCode ke page reset-password
        this.router.navigate(['/reset-password'], {
          state: { email: this.email, otp: this.otpCode }
        });
      },
      (error: any) => {
        alert(error.error?.message || 'Kode OTP salah atau expired!');
      }
    );
  }

  goToLogin() {
    this.router.navigate(['/login']);
  }
}