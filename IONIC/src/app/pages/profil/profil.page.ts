import { Component, OnInit } from '@angular/core';
import { NavController, AlertController, LoadingController } from '@ionic/angular';
import { AuthService } from '../../services/auth'; // Pastikan jalur benar

@Component({
  selector: 'app-profil',
  templateUrl: './profil.page.html',
  styleUrls: ['./profil.page.scss'],
  standalone: false,
})
export class ProfilePage implements OnInit {
  userProfile: any = null;

  constructor(
    private navCtrl: NavController, 
    private alertCtrl: AlertController,
    private loadingCtrl: LoadingController,
    private authService: AuthService
  ) {}

  ngOnInit() {
    // Jalankan saat pertama kali load
    this.loadProfileFromAPI();
  }

  ionViewWillEnter() {
    // Dipanggil ulang otomatis tiap kali Ivan balik ke halaman ini
    this.loadProfileFromAPI();
  }

loadProfileFromAPI() {
    this.authService.getProfileFromServer().subscribe(
      (res: any) => {
        // Data sukses diambil live dari hosting eduvan.rehalivan.com
        this.userProfile = res;
        console.log('Data profil live berhasil dimuat:', this.userProfile);
      },
      (error) => {
        console.error('Gagal mengambil profil dari API:', error);
        
        // Skenario jika token mati / 401: Tendang ke login agar dapet token baru
        if (error.status === 401) {
          console.warn('Token tidak valid atau expired. Mengarahkan kembali ke login...');
          this.authService.logout();
          this.navCtrl.navigateRoot('/login');
        }
      }
    );
  }

  goToEdit() { 
    this.navCtrl.navigateForward('/edit-profil'); 
  }

  goToCertificate() { 
    this.navCtrl.navigateForward('/certificate'); 
  } 

  goToHistory() { 
    this.navCtrl.navigateForward('/riwayat-transaksi'); 
  }

  goToNotif() { 
    this.navCtrl.navigateForward('/notifications'); 
  }

  async logout() {
    const alert = await this.alertCtrl.create({
      header: 'Keluar',
      message: 'Apakah Anda yakin ingin keluar dari aplikasi Eduvan?',
      buttons: [
        { text: 'Batal', role: 'cancel' },
        {
          text: 'Ya, Keluar',
          handler: () => {
            this.authService.logout(); 
            this.navCtrl.navigateRoot('/login'); 
          }
        }
      ]
    });
    await alert.present();
  }
}