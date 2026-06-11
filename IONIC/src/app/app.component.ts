import { Component, OnInit, ViewChild } from '@angular/core';
import { IonModal } from '@ionic/angular';
import { Network } from '@capacitor/network';
import { Capacitor } from '@capacitor/core';
import { Filesystem } from '@capacitor/filesystem';
import { SplashScreen } from '@capacitor/splash-screen';
import { Router } from '@angular/router';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
  standalone: false,
})
export class AppComponent implements OnInit {
  @ViewChild(IonModal, { static: false }) modal!: IonModal;

  constructor(private router: Router) {}

  async ngOnInit() {
    // 1. Jalankan fitur native hanya jika berjalan di HP
    if (Capacitor.isNativePlatform()) {
      const status = await Network.getStatus();
      this.handleStatusKoneksi(status.connected);

      Network.addListener('networkStatusChange', (status) => {
        this.handleStatusKoneksi(status.connected);
      });

      if (Capacitor.getPlatform() === 'android') {
        await this.mintaPerizinanAplikasiTembakNative();
      }
    }

    // 🚀 2. JALANKAN FILTER JALUR AWAL DAN TUNGGU SAMPAI SELESAI SAKALIGUS!
    // Kita simpan status sukses navigasinya ke dalam variabel
    const navigasiSukses = await this.filterHalamanAwal();

    // 🟢 3. SEKARANG KITA KUNCI: Splash screen HANYA BOLEH menutup jika Angular sudah sukses mendarat di halaman tujuan!
    if (navigasiSukses && Capacitor.isNativePlatform()) {
      try {
        // Kasih jeda super kecil 100 milidetik biar transisi native Android-nya gak kaget
        setTimeout(async () => {
          await SplashScreen.hide();
        }, 100);
      } catch (e) {
        console.log('Splash screen ditutup otomatis.', e);
      }
    }
  }

  /**
   * 🟢 FUNGSI SAKTI FILTER NAVIGASI BYPASS (DIUBAH JADI ASYNC PROMISE)
   */
  private async filterHalamanAwal(): Promise<boolean> {
    const sudahLogin = localStorage.getItem('user_data');
    const statusLama = localStorage.getItem('eduvan_user_registered');

    if (sudahLogin) {
      // 1. Jika sudah login dan punya token/data, langsung lolos ke dalam beranda
      return await this.router.navigate(['/tabs/beranda'], {
        replaceUrl: true,
      });
    } else if (statusLama === 'true') {
      // 2. Jika sudah pernah daftar/buka Welcome tapi belum login, arahkan ke Login
      return await this.router.navigate(['/login'], { replaceUrl: true });
    } else {
      // 3. PENGGUNA BARU GRES: Wajib masuk welcome page dulu!
      return await this.router.navigate(['/welcome'], { replaceUrl: true });
    }
  }

  // Fungsi Sakti Paksa Muncul Popup Izin Native Android
  async mintaPerizinanAplikasiTembakNative() {
    try {
      const statusStorage = await Filesystem.checkPermissions();
      if (statusStorage.publicStorage !== 'granted') {
        await Filesystem.requestPermissions();
      }
    } catch (error) {
      console.log('User skip popup atau ada eror mbut:', error);
    }
  }

  private handleStatusKoneksi(isConnected: boolean) {
    if (!isConnected) {
      if (this.modal) this.modal.present();
    } else {
      if (this.modal) this.modal.dismiss();
    }
  }

  async cekKoneksiUlang() {
    const status = await Network.getStatus();
    if (status.connected) {
      if (this.modal) this.modal.dismiss();
    }
  }
}
