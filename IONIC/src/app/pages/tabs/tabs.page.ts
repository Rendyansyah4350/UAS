import { Component } from '@angular/core';
import { Platform, ToastController } from '@ionic/angular';
import { App } from '@capacitor/app';

@Component({
  selector: 'app-tabs',
  templateUrl: './tabs.page.html',
  styleUrls: ['./tabs.page.scss'],
  standalone: false,
})
export class TabsPage {
  private backButtonSubscription: any;
  private waktuKlikTerakhir = 0;
  private jedaWaktuKeluar = 2000;

  constructor(private platform: Platform, private toastCtrl: ToastController) {}

  ionViewDidEnter() {
    if (this.platform.is('cordova') || this.platform.is('capacitor')) {
      this.backButtonSubscription =
        this.platform.backButton.subscribeWithPriority(10, () => {
          const waktuSekarang = new Date().getTime();

          if (waktuSekarang - this.waktuKlikTerakhir < this.jedaWaktuKeluar) {
            App.exitApp();
          } else {
            this.tampilkanToastKonfirmasi();
            this.waktuKlikTerakhir = waktuSekarang;
          }
        });
    }
  }

  ionViewDidLeave() {
    if (this.backButtonSubscription) {
      this.backButtonSubscription.unsubscribe();
    }
  }

  async tampilkanToastKonfirmasi() {
    const toast = await this.toastCtrl.create({
      message: 'Tekan sekali lagi untuk keluar',
      duration: 2000,
      position: 'bottom',
      icon: 'log-out-outline',
      cssClass: 'toast-keluar-premium',
      layout: 'baseline',
    });
    await toast.present();
  }
}
