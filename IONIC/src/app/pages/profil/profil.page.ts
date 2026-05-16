import { Component } from '@angular/core';
import { NavController, AlertController } from '@ionic/angular';

@Component({
  selector: 'app-profil',
  templateUrl: './profil.page.html',
  styleUrls: ['./profil.page.scss'],
  standalone: false,
})
export class ProfilePage {
  constructor(private navCtrl: NavController, private alertCtrl: AlertController) {}

  goToEdit() { this.navCtrl.navigateForward('/edit-profil'); }
  
  goToHistory() { this.navCtrl.navigateForward('/riwayat-transaksi'); }
  
  goToNotif() { this.navCtrl.navigateForward('/notifications'); }

  async logout() {
    const alert = await this.alertCtrl.create({
      header: 'Keluar',
      message: 'Apakah Anda yakin ingin keluar?',
      buttons: [
        { text: 'Batal', role: 'cancel' },
        {
          text: 'Ya',
          handler: () => {
            localStorage.clear();
            this.navCtrl.navigateRoot('/login');
          }
        }
      ]
    });
    await alert.present();
  }
}