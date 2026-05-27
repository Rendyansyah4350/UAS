import { Component, OnInit, ViewChild } from '@angular/core';
import { IonModal } from '@ionic/angular';
import { Network } from '@capacitor/network';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
  standalone: false,
})
export class AppComponent implements OnInit {
  // 🔥 Ambil referensi ion-modal dari HTML mbut
  @ViewChild(IonModal, { static: false }) modal!: IonModal;

  constructor() {}

  async ngOnInit() {
    // 1. Cek koneksi pertama kali pas aplikasi dibuka mbut
    const status = await Network.getStatus();
    this.handleStatusKoneksi(status.connected);

    // 2. Pantau jaringan secara real-time pas aplikasi berjalan lek
    Network.addListener('networkStatusChange', (status) => {
      this.handleStatusKoneksi(status.connected);
    });
  }

  // 🛠️ Fungsi pengontrol tampil/sembunyi modal secara aman
  private handleStatusKoneksi(isConnected: boolean) {
    if (!isConnected) {
      // Jika internet mati dan modal belum muncul, tampilkan lek!
      if (this.modal) {
        this.modal.present();
      }
    } else {
      // Jika internet nyala kembali, otomatis tutup modalnya mbut
      if (this.modal) {
        this.modal.dismiss();
      }
    }
  }

  // 🔄 Fungsi pas tombol "Coba Lagi" di dalam modal diklik
  async cekKoneksiUlang() {
    const status = await Network.getStatus();
    if (status.connected) {
      if (this.modal) {
        this.modal.dismiss(); // Internet udah aman, tutup modal!
      }
    } else {
      console.log('Masih putus lek, internet belum aktif.');
    }
  }
}
