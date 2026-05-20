import { Component, OnInit } from '@angular/core';
import { NavController, AlertController, ActionSheetController } from '@ionic/angular';
import { AuthService } from '../../services/auth';

@Component({
  selector: 'app-profil',
  templateUrl: './profil.page.html',
  styleUrls: ['./profil.page.scss'],
  standalone: false,
})
export class ProfilePage implements OnInit {
  userProfile: any = null;
  selectedAvatar: string = 'assets/icon/avatar-male.png'; 

  constructor(
    private navCtrl: NavController, 
    private alertCtrl: AlertController,
    private authService: AuthService,
    private actionSheetCtrl: ActionSheetController
  ) {}

  ngOnInit() {
    this.loadSavedAvatar();
    this.authService.currentUser$.subscribe((user: any) => {
      if (user) this.userProfile = user;
    });
    this.loadProfileFromAPI();
  }

  ionViewWillEnter() {
    this.loadProfileFromAPI();
  }

  loadSavedAvatar() {
    const savedAvatar = localStorage.getItem('user_avatar');
    if (savedAvatar) this.selectedAvatar = savedAvatar;
  }

async changeAvatar() {
  const actionSheet = await this.actionSheetCtrl.create({
    header: 'Pilih Karakter Avatar',
    buttons: [
      {
        text: 'Laki-laki',
        icon: 'man-outline',
        handler: () => { this.updateAvatar('assets/icon/avatar-male.png'); } // Ganti icons jadi icon
      },
      {
        text: 'Perempuan',
        icon: 'woman-outline',
        handler: () => { this.updateAvatar('assets/icon/avatar-female.png'); } // Ganti icons jadi icon
      },
      { text: 'Batal', role: 'cancel', icon: 'close' }
    ]
  });
  await actionSheet.present();
}

  updateAvatar(path: string) {
    this.selectedAvatar = path;
    localStorage.setItem('user_avatar', path);
  }

  loadProfileFromAPI() {
    this.authService.getProfileFromServer().subscribe({
      next: (res: any) => { console.log('Profil dimuat'); },
      error: (err) => {
        if (err.status === 401) {
          this.authService.logout();
          this.navCtrl.navigateRoot('/login');
        }
      }
    });
  }

  goToEdit() { this.navCtrl.navigateForward('/edit-profil'); }
  goToCertificate() { this.navCtrl.navigateForward('/certificate'); } 
  goToHistory() { this.navCtrl.navigateForward('/riwayat-transaksi'); }
  goToNotif() { this.navCtrl.navigateForward('/notifications'); }

  async logout() {
    const alert = await this.alertCtrl.create({
      header: 'Konfirmasi Keluar',
      message: 'Apakah kamu yakin ingin keluar?',
      buttons: [
        { text: 'Batal', role: 'cancel' },
        {
          text: 'Ya, Keluar',
          handler: () => {
            // Hapus data avatar saat logout agar tidak tertukar akun lain
            localStorage.removeItem('user_avatar');
            this.authService.logout(); 
            this.navCtrl.navigateRoot('/login'); 
          }
        }
      ]
    });
    await alert.present();
  }
}