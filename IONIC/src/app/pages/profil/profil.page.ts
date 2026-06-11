import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import {
  NavController,
  AlertController,
  ActionSheetController,
} from '@ionic/angular';
import { AuthService } from '../../services/auth';
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'app-profil',
  templateUrl: './profil.page.html',
  styleUrls: ['./profil.page.scss'],
  standalone: false,
})
export class ProfilePage implements OnInit {
  userProfile: any = null;

  // 🟢 PERBAIKAN 1: Setel bawaan (default) awal ke gambar netral/anonim universal lek
  selectedAvatar: string = 'assets/icon/avatar-neutral.png';

  isSkModalOpen: boolean = false;
  isPrivacyModalOpen: boolean = false;

  angkaKursus: number = 0;
  angkaSertifikat: number = 0;
  isLogoutAlertOpen: boolean = false;

  logoutAlertButtons = [
    {
      text: 'Batal',
      role: 'cancel',
      cssClass: 'alert-btn-batal',
      handler: () => {
        this.isLogoutAlertOpen = false;
        this.cdr.detectChanges();
      },
    },
    {
      text: 'Ya, Keluar',
      role: 'confirm',
      cssClass: 'alert-btn-keluar',
      handler: () => {
        this.isLogoutAlertOpen = false;
        localStorage.removeItem('user_avatar');
        this.authService.logout();
        this.navCtrl.navigateRoot('/login');
        this.cdr.detectChanges();
      },
    },
  ];

  constructor(
    private navCtrl: NavController,
    private alertCtrl: AlertController,
    private authService: AuthService,
    private actionSheetCtrl: ActionSheetController,
    private cdr: ChangeDetectorRef,
    private courseService: CourseService
  ) {}

  ngOnInit() {
    this.loadSavedAvatar();
    this.authService.currentUser$.subscribe((user: any) => {
      if (user) {
        this.userProfile = user;

        // 🟢 SINKRONISASI AVATAR GOOGLE: Jika login lewat Google dan punya avatar, langsung pasang otomatis lek!
        if (user.avatar) {
          this.selectedAvatar = user.avatar;
        }

        this.cdr.detectChanges();
      }
    });
  }

  ionViewWillEnter() {
    // 🟢 ANTISIPASI DELAY: Ambil data darurat dari localStorage jika state BehaviorSubject sedang kosong pas transisi page
    const localUserData =
      localStorage.getItem('user_data') || localStorage.getItem('user');
    if (!this.userProfile && localUserData) {
      this.userProfile = JSON.parse(localUserData);
      if (this.userProfile.avatar) {
        this.selectedAvatar = this.userProfile.avatar;
      }
      this.cdr.detectChanges();
    }

    this.loadProfileFromAPI();
    this.hitungStatistikMandiri();
  }

  loadSavedAvatar() {
    const savedAvatar = localStorage.getItem('user_avatar');
    // Jika ada di storage pake yang lama, jika tidak ada tetep stay di avatar-neutral.png
    if (savedAvatar) {
      this.selectedAvatar = savedAvatar;
    } else {
      this.selectedAvatar = 'assets/icon/avatar-neutral.png';
    }
  }

  /**
   * 🟢 PERBAIKAN 2: Action Sheet dengan opsi Laki-laki, Perempuan, dan Pilihan Netral baku
   */
  async changeAvatar() {
    const actionSheet = await this.actionSheetCtrl.create({
      header: 'Pilih Karakter Avatar',
      cssClass: 'premium-avatar-sheet',
      mode: 'ios', // Paksa mode iOS biar tampilannya clean melengkung rapi
      buttons: [
        {
          text: 'Karakter Laki-laki 👦',
          icon: 'man-outline',
          handler: () => {
            this.updateAvatar('assets/icon/avatar-male.png');
          },
        },
        {
          text: 'Karakter Perempuan 👧',
          icon: 'woman-outline',
          handler: () => {
            this.updateAvatar('assets/icon/avatar-female.png');
          },
        },
        {
          text: 'Gunakan Gambar Netral 👤',
          icon: 'person-circle-outline',
          handler: () => {
            this.updateAvatar('assets/icon/avatar-neutral.png');
          },
        },
        {
          text: 'Batal',
          role: 'cancel',
          icon: 'close',
        },
      ],
    });
    await actionSheet.present();
  }

  updateAvatar(path: string) {
    this.selectedAvatar = path;
    localStorage.setItem('user_avatar', path);
    this.cdr.detectChanges(); // Paksa view update gambar baru saat diklik lek
  }

  hitungStatistikMandiri() {
    // A. Hitung Jumlah Kursus Aktif
    this.courseService.getMyEnrollments().subscribe({
      next: (enrollRes: any) => {
        console.log('Jalur Bypass Enrollments Sukses:', enrollRes);
        const dataKursus = enrollRes.data ? enrollRes.data : enrollRes;
        if (Array.isArray(dataKursus)) {
          this.angkaKursus = dataKursus.length;
          this.cdr.detectChanges();
        }
      },
      error: (err) => console.error('Bypass Kursus Gagal:', err),
    });

    // B. Hitung Jumlah Sertifikat
    this.courseService.getMyCertificates().subscribe({
      next: (certRes: any) => {
        console.log('Jalur Bypass Certificates Sukses:', certRes);
        const dataSertifikat = certRes.data ? certRes.data : certRes;
        if (Array.isArray(dataSertifikat)) {
          this.angkaSertifikat = dataSertifikat.length;
          this.cdr.detectChanges();
        }
      },
      error: (err) => console.error('Bypass Sertifikat Gagal:', err),
    });
  }

  loadProfileFromAPI() {
    this.authService.getProfileFromServer().subscribe({
      next: (res: any) => {
        if (res) {
          this.userProfile = res.data ? res.data : res;

          // Jika API server mengembalikan avatar Google, ikuti pasang otomatis
          if (this.userProfile.avatar) {
            this.selectedAvatar = this.userProfile.avatar;
          }

          // Hitung Ulang Kursus
          this.courseService.getMyEnrollments().subscribe({
            next: (enrollRes: any) => {
              const dataKursus = enrollRes.data ? enrollRes.data : enrollRes;
              if (Array.isArray(dataKursus)) {
                this.angkaKursus = dataKursus.length;
                this.cdr.detectChanges();
              }
            },
          });

          // Hitung Ulang Sertifikat
          this.courseService.getMyCertificates().subscribe({
            next: (certRes: any) => {
              const dataSertifikat = certRes.data ? certRes.data : certRes;
              if (Array.isArray(dataSertifikat)) {
                this.angkaSertifikat = dataSertifikat.length;
                this.cdr.detectChanges();
              }
            },
          });

          this.cdr.detectChanges();
        }
      },
      error: (err) => {
        console.error('Error saat load profile:', err);
      },
    });
  }

  goToEdit() {
    this.navCtrl.navigateForward(['/tabs/edit-profil']);
  }
  goToCertificate() {
    this.navCtrl.navigateForward(['/tabs/certificate']);
  }
  goToHistory() {
    this.navCtrl.navigateForward(['/tabs/riwayat-transaksi']);
  }
  goToNotif() {
    this.navCtrl.navigateForward(['/tabs/notifications']);
  }

  bukaKonfirmasiKeluar() {
    this.isLogoutAlertOpen = true;
    this.cdr.detectChanges();
  }

  async logout() {
    const alert = await this.alertCtrl.create({
      header: 'Konfirmasi Keluar',
      message: 'Apakah kamu yakin ingin keluar?',
      mode: 'ios',
      buttons: [
        { text: 'Batal', role: 'cancel' },
        {
          text: 'Ya, Keluar',
          handler: () => {
            localStorage.removeItem('user_avatar');
            this.authService.logout();
            this.navCtrl.navigateRoot('/login');
          },
        },
      ],
    });
    await alert.present();
  }

  // 🟢 PERBAIKAN 3: Ditambahkan deteksi deteksi paksa perubahan komponen modal kustom
  setSkModal(isOpen: boolean) {
    this.isSkModalOpen = isOpen;
    this.cdr.detectChanges();
  }

  setPrivacyModal(isOpen: boolean) {
    this.isPrivacyModalOpen = isOpen;
    this.cdr.detectChanges();
  }
}
