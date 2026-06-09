import { Component, OnInit, ViewChild } from '@angular/core';
import { IonModal } from '@ionic/angular';
import { Network } from '@capacitor/network';
import { Capacitor } from '@capacitor/core';
import { Filesystem } from '@capacitor/filesystem';
import { SplashScreen } from '@capacitor/splash-screen';
import { Router } from '@angular/router'; // 🟢 Tambahkan import Router

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
  standalone: false,
})
export class AppComponent implements OnInit {
  @ViewChild(IonModal, { static: false }) modal!: IonModal;

  constructor(private router: Router) {} // 🟢 Inject Router ke dalam constructor

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
      await this.mintaPerizinanAplikasiTembakNative();
    }

    // 🚀 4. FILTER JALUR HALAMAN AWAL (PENGGUNA BARU VS LAMA)
    // Dijalankan tepat sebelum splash screen ditutup agar transisi perpindahan mulus
    this.filterHalamanAwal();

    // 🟢 5. SEMBUNYIKAN SPLASH SCREEN SECARA MANUAL SETELAH SEMUA PROSES ASYNC SIAP LEK!
    try {
      await SplashScreen.hide();
    } catch (e) {
      console.log(
        'Splash screen sudah tertutup otomatis atau berjalan di browser.',
        e
      );
    }
  }

  /**
   * 🟢 FUNGSI SAKTI FILTER NAVIGASI BYPASS
   * Menentukan halaman pembuka aplikasi secara dinamis tanpa merusak history stack HP
   */
  private filterHalamanAwal() {
    const statusLama = localStorage.getItem('eduvan_user_registered');

    if (statusLama === 'true') {
      // 🚀 PENGGUNA LAMA: Langsung didorong masuk ke login secara bersih
      // Tanpa mendaftarkan halaman Welcome ke riwayat, sehingga pas di-back langsung keluar aplikasi!
      this.router.navigate(['/login'], { replaceUrl: true });
    } else {
      // PENGGUNA BARU: Diarahkan masuk ke welcome page untuk disuruh centang syarat ketentuan
      this.router.navigate(['/welcome'], { replaceUrl: true });
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
