import { Component, OnInit } from '@angular/core';
import { NavController, ToastController } from '@ionic/angular';
import { AuthService } from '../../services/auth'; // Pastikan path ke auth service sudah benar lek

@Component({
  selector: 'app-edit-profil',
  templateUrl: './edit-profil.page.html',
  styleUrls: ['./edit-profil.page.scss'],
  standalone: false
})
export class EditProfilPage implements OnInit {
  
  // 🟢 KUNCI PENYENTER: Inisialisasi properti formData agar dikenal oleh HTML
  formData: any = {
    name: '',
    email: '',
    instansi: ''
  };

  isLoading: boolean = false;

  constructor(
    private navCtrl: NavController,
    private authService: AuthService,
    private toastCtrl: ToastController
  ) {}

  ngOnInit() {
    // Ambil data user yang sedang login dari stasiun radio AuthService lek
    this.authService.currentUser$.subscribe({
      next: (user: any) => {
        if (user) {
          // Masukkan data lama ke dalam form biar user tinggal edit teksnya lek
          this.formData.name = user.name || user.nama || '';
          this.formData.email = user.email || '';
          this.formData.instansi = user.instansi || user.university || 'Mahasiswa Universitas';
        }
      }
    });
  }

  simpanPerubahan() {
    // Validasi super simpel biar gak kosong
    if (!this.formData.name || !this.formData.email) {
      this.tampilkanToast('Nama dan Email tidak boleh kosong lek!', 'danger');
      return;
    }

    this.isLoading = true;

    // Tembak langsung ke fungsi updateProfile di auth.service.ts
    this.authService.updateProfile(this.formData).subscribe({
      next: (res: any) => {
        this.isLoading = false;
        this.tampilkanToast('Profil kamu berhasil diperbarui lek!', 'success');
        
        // Kembalikan user ke halaman profil utama (Home & Profil otomatis berubah live!)
        this.navCtrl.back();
      },
      error: (err) => {
        this.isLoading = false;
        console.error('Gagal update ke API Laravel:', err);
        this.tampilkanToast('Gagal menyinkronkan data ke server.', 'danger');
      }
    });
  }

  // Fungsi pembantu untuk memunculkan notifikasi toast di layar
  async tampilkanToast(pesan: string, warna: string) {
    const toast = await this.toastCtrl.create({
      message: pesan,
      duration: 2000,
      color: warna,
      position: 'bottom'
    });
    await toast.present();
  }
}