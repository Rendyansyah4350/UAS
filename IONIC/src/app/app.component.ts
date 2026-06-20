import { Component, OnInit, ViewChild, NgZone } from '@angular/core';
import { IonModal } from '@ionic/angular';
import { Network } from '@capacitor/network';
import { Capacitor } from '@capacitor/core';
import { Filesystem } from '@capacitor/filesystem';
import { SplashScreen } from '@capacitor/splash-screen';
import { App } from '@capacitor/app';
import { Router } from '@angular/router';
import { AuthService } from './services/auth';

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
    private auth: AuthService
  ) {}

  async ngOnInit() {
    const status = await Network.getStatus();
    this.handleStatusKoneksi(status.connected);

    Network.addListener('networkStatusChange', (status) => {
      this.handleStatusKoneksi(status.connected);
    });

    if (Capacitor.getPlatform() === 'android') {
      await this.mintaPerizinanAplikasiTembakNative();
    }

    this.initDeepLinkGoogle();

    try {
      await SplashScreen.hide();
    } catch (e) {
      console.log(
        'Splash screen sudah tertutup otomatis atau berjalan di browser.',
        e
      );
    }
  }

  initDeepLinkGoogle() {
    App.addListener('appUrlOpen', (event: any) => {
      this.zone.run(() => {
        if (event.url.includes('eduvan://google-login')) {
          try {
            const urlObj = new URL(event.url);

            const token = urlObj.searchParams.get('token');
            const userRaw = urlObj.searchParams.get('user');

            if (token) {
              localStorage.setItem('token', decodeURIComponent(token));
            }

            if (userRaw) {
              const decodedUser = decodeURIComponent(userRaw);
              localStorage.setItem('user_data', decodedUser);
              localStorage.setItem('user', decodedUser);

              const parsedUser = JSON.parse(decodedUser);
              if (parsedUser && parsedUser.avatar) {
                localStorage.setItem('user_avatar', parsedUser.avatar);
              } else {
                localStorage.removeItem('user_avatar');
              }

              this.auth.triggerRefreshData(parsedUser);
            }

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

  async mintaPerizinanAplikasiTembakNative() {
    try {
      const statusStorage = await Filesystem.checkPermissions();
      if (statusStorage.publicStorage !== 'granted') {
        await Filesystem.requestPermissions();
      }
    } catch (error) {
      console.log('User skip popup atau ada eror', error);
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
