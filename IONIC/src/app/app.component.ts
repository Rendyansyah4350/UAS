import { Component, OnInit, ViewChild, NgZone } from '@angular/core';
import { IonModal } from '@ionic/angular';
import { Network } from '@capacitor/network';
import { Capacitor } from '@capacitor/core';
import { Filesystem } from '@capacitor/filesystem';
import { SplashScreen } from '@capacitor/splash-screen';
import { App } from '@capacitor/app';
import { Router } from '@angular/router';
import { AuthService } from './services/auth'; // 🟢 Tambahan wajib: sesuaikan path ke service auth lu lek

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
  standalone: false,
})
export class AppComponent implements OnInit {
  @ViewChild(IonModal, { static: false }) modal!: IonModal;

  constructor(
    private router: Router,
    private zone: NgZone,
    private auth: AuthService // 🟢 Inject AuthService ke constructor
  ) {}

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

    // 🟢 4. HANDLER DEEP LINK: Interseptor data login Google dari browser HP
    this.initDeepLinkGoogle();

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

  // 🟢 Fungsi Baru: Penangkap sinyal kembalian dari Laravel Callback
  initDeepLinkGoogle() {
    App.addListener('appUrlOpen', (event: any) => {
      this.zone.run(() => {
        // Cek apakah url mengandung skema eduvan://google-login
        if (event.url.includes('eduvan://google-login')) {
          try {
            const urlObj = new URL(event.url);

            // Ambil query parameter token dan data user dari url
            const token = urlObj.searchParams.get('token');
            const userRaw = urlObj.searchParams.get('user');

            if (token) {
              localStorage.setItem('token', decodeURIComponent(token));
            }

            if (userRaw) {
              const decodedUser = decodeURIComponent(userRaw);
              localStorage.setItem('user_data', decodedUser);
              localStorage.setItem('user', decodedUser);

              // 🟢 TEMBAK REFRESH STATE: Paksa BehaviorSubject di auth memperbarui data user secara global instan!
              this.auth.triggerRefreshData(JSON.parse(decodedUser));
            }

            // Jika token berhasil didapat, paksa pindah halaman ke Beranda
            if (token) {
              this.router.navigateByUrl('/tabs/beranda').then((navigated) => {
                if (!navigated) {
                  window.location.href = '/tabs/beranda';
                }
              });
            }
          } catch (err) {
            console.error('Gagal memproses data deep link Google:', err);
          }
        }
      });
    });
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
