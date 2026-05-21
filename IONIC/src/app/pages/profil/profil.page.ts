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
      cssClass: 'premium-avatar-sheet', // Menghubungkan ke class di global.scss
      buttons: [
        {
          text: 'Laki-laki',
          icon: 'man-outline',
          handler: () => { this.updateAvatar('assets/icon/avatar-male.png'); }
        },
        {
          text: 'Perempuan',
          icon: 'woman-outline',
          handler: () => { this.updateAvatar('assets/icon/avatar-female.png'); }
        },
        { 
          text: 'Batal', 
          role: 'cancel', 
          icon: 'close' 
        }
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
      next: (res: any) => { 
        if (res) this.userProfile = res;
      },
      error: (err) => {
        console.error('Error saat load profile:', err);
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