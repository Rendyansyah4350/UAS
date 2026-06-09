import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';

@Component({
  selector: 'app-welcome',
  templateUrl: './welcome.page.html',
  styleUrls: ['./welcome.page.scss'],
  standalone: false,
})
export class WelcomePage {
  isPenggunaBaru: boolean = true;
  currentStep: number = 1;

  setujuTerms: boolean = false;
  setujuPrivasi: boolean = false;

  // 🟢 State Pengunci: bernilai true jika user sukses men-scroll text box sampai ujung bawah
  sudahScrollTerms: boolean = false;
  sudahScrollPrivasi: boolean = false;

  constructor(private router: Router, private toastCtrl: ToastController) {}

  ionViewWillEnter() {
    this.cekStatusPengguna();
  }

  cekStatusPengguna() {
    const statusLama = localStorage.getItem('eduvan_user_registered');
    if (statusLama === 'true') {
      this.isPenggunaBaru = false;
      this.router.navigate(['/login'], { replaceUrl: true });
    } else {
      this.isPenggunaBaru = true;
    }
  }

  /**
   * 🟢 FUNGSI UTAMA: Menghitung posisi scroll box text secara realtime
   */
  cekPosisiScroll(event: any) {
    const targetEl = event.target;

    // Perhitungan matematika: Posisi Scroll Saat Ini + Tinggi Kotak Box >= Total Tinggi Konten Dokumen
    // Angka -5 diberikan sebagai batas toleransi padding bawah piksel HP
    if (
      targetEl.scrollTop + targetEl.clientHeight >=
      targetEl.scrollHeight - 5
    ) {
      if (this.currentStep === 2) {
        this.sudahScrollTerms = true;
      } else if (this.currentStep === 3) {
        this.sudahScrollPrivasi = true;
      }
    }
  }

  judulToolbarDinamis(): string {
    if (this.currentStep === 2) return 'Syarat & Ketentuan';
    if (this.currentStep === 3) return 'Kebijakan Privasi';
    return '';
  }

  /**
   * 🟢 TOMBOL LANJUT MATI JIKA: Belum di-scroll ATAU kotak ceklis belum dicentang
   */
  tombolIsDisabled(): boolean {
    if (this.currentStep === 1) return false;
    if (this.currentStep === 2)
      return !this.sudahScrollTerms || !this.setujuTerms;
    if (this.currentStep === 3)
      return !this.sudahScrollPrivasi || !this.setujuPrivasi;
    return true;
  }

  langkahLanjut() {
    if (this.currentStep < 3) {
      this.currentStep++;
    }
  }

  langkahKembali() {
    if (this.currentStep > 1) {
      this.currentStep--;
      // Reset state ketika user balik arah biar adil wajib baca ulang
      if (this.currentStep === 2) {
        this.sudahScrollPrivasi = false;
        this.setujuPrivasi = false;
      } else if (this.currentStep === 1) {
        this.sudahScrollTerms = false;
        this.setujuTerms = false;
      }
    }
  }

  async eksekusiDaftar() {
    if (!this.setujuPrivasi || !this.sudahScrollPrivasi) {
      const toast = await this.toastCtrl.create({
        message: 'Anda harus men-scroll dan menyetujui Kebijakan Privasi.',
        duration: 2000,
        position: 'bottom',
        color: 'danger',
      });
      await toast.present();
      return;
    }
    localStorage.setItem('eduvan_user_registered', 'true');
    this.router.navigate(['/register'], { replaceUrl: true });
  }

  goToLogin() {
    localStorage.setItem('eduvan_user_registered', 'true');
    this.router.navigate(['/login'], { replaceUrl: true });
  }

  handleImageError(event: any) {
    event.target.src = 'assets/icon/computer-science.jpeg';
  }
}
