import { Component, OnInit, ViewChild } from '@angular/core';
import { IonModal } from '@ionic/angular';
import { Network } from '@capacitor/network';
import { Capacitor } from '@capacitor/core';
import { Filesystem } from '@capacitor/filesystem'; 

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
  standalone: false,
})
export class AppComponent implements OnInit {
  @ViewChild(IonModal, { static: false }) modal!: IonModal;

  constructor() {}

  async ngOnInit() {
    // 1. Cek koneksi internet pertama kali pas aplikasi dibuka
    const status = await Network.getStatus();
    this.handleStatusKoneksi(status.connected);

    // 2. Pantau jaringan secara real-time
    Network.addListener('networkStatusChange', (status) => {
      this.handleStatusKoneksi(status.connected);
    });

    // 3. 🔥 Tembak Popup Perizinan Android Pas Pertama Kali Dibuka!
    if (Capacitor.getPlatform() === 'android') {
      this.mintaPerizinanAplikasiTembakNative();
    }
  }

  // 🛠️ Fungsi Sakti Paksa Muncul Popup Izin Native Android (Anti-Manual)
  async mintaPerizinanAplikasiTembakNative() {
    try {
      // 📄 SEKARANG CUMA MINTA IZIN FILE/STORAGE BUAT DOWNLOAD PDF SERTIFIKAT LEK!
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
