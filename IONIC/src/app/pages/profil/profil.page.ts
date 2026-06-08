import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import {
  NavController,
  AlertController,
  ActionSheetController,
} from '@ionic/angular';
import { AuthService } from '../../services/auth';
import { CourseService } from '../../services/course.service'; // 🚨 1. IMPORT COURSESERVICE LEK!

@Component({
  selector: 'app-profil',
  templateUrl: './profil.page.html',
  styleUrls: ['./profil.page.scss'],
  standalone: false,
})
export class ProfilePage implements OnInit {
  userProfile: any = null;
  selectedAvatar: string = 'assets/icon/avatar-male.png';
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
        this.cdr.detectChanges();
      }
    });
  }

  ionViewWillEnter() {
    this.loadProfileFromAPI();
    this.hitungStatistikMandiri();
  }

  loadSavedAvatar() {
    const savedAvatar = localStorage.getItem('user_avatar');
    if (savedAvatar) this.selectedAvatar = savedAvatar;
  }

  async changeAvatar() {
    const actionSheet = await this.actionSheetCtrl.create({
      header: 'Pilih Karakter Avatar',
      cssClass: 'premium-avatar-sheet',
      buttons: [
        {
          text: 'Laki-laki',
          icon: 'man-outline',
          handler: () => {
            this.updateAvatar('assets/icon/avatar-male.png');
          },
        },
        {
          text: 'Perempuan',
          icon: 'woman-outline',
          handler: () => {
            this.updateAvatar('assets/icon/avatar-female.png');
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
  }

  hitungStatistikMandiri() {
    // A. Hitung Jumlah Kursus Aktif Langsung Tanpa Nunggu API Profil
    this.courseService.getMyEnrollments().subscribe({
      next: (enrollRes: any) => {
        console.log('Jalur Bypass Enrollments Sukses:', enrollRes);
        const dataKursus = enrollRes.data ? enrollRes.data : enrollRes;
        if (Array.isArray(dataKursus)) {
          this.angkaKursus = dataKursus.length;
          this.cdr.detectChanges(); // Paksa angka kursus langsung berubah di HTML lek!
        }
      },
      error: (err) => console.error('Bypass Kursus Gagal:', err),
    });

    // B. Hitung Jumlah Sertifikat Langsung Tanpa Nunggu API Profil
    this.courseService.getMyCertificates().subscribe({
      next: (certRes: any) => {
        console.log('Jalur Bypass Certificates Sukses:', certRes);
        const dataSertifikat = certRes.data ? certRes.data : certRes;
        if (Array.isArray(dataSertifikat)) {
          this.angkaSertifikat = dataSertifikat.length;
          this.cdr.detectChanges(); // Paksa angka sertifikat langsung berubah di HTML lek!
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

          // ==========================================================================
          // 🟢 HITUNG KURSUS & MASUKKAN KE VARIABEL MANDIRI
          // ==========================================================================
          this.courseService.getMyEnrollments().subscribe({
            next: (enrollRes: any) => {
              const dataKursus = enrollRes.data ? enrollRes.data : enrollRes;
              if (Array.isArray(dataKursus)) {
                this.angkaKursus = dataKursus.length; // <-- Masuk ke variabel mandiri lek
                this.cdr.detectChanges();
              }
            },
          });

          // ==========================================================================
          // 🟢 HITUNG SERTIFIKAT & MASUKKAN KE VARIABEL MANDIRI
          // ==========================================================================
          this.courseService.getMyCertificates().subscribe({
            next: (certRes: any) => {
              const dataSertifikat = certRes.data ? certRes.data : certRes;
              if (Array.isArray(dataSertifikat)) {
                this.angkaSertifikat = dataSertifikat.length; // <-- Masuk ke variabel mandiri lek
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

  // 🟢 FUNGSI BARU UNTUK MEMBUKA OVERLAY POPUP LOGOUT KUSTOM DARI HTML LEK
  bukaKonfirmasiKeluar() {
    this.isLogoutAlertOpen = true;
    this.cdr.detectChanges();
  }

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
          },
        },
      ],
    });
    await alert.present();
  }
  setSkModal(isOpen: boolean) {
    this.isSkModalOpen = isOpen;
  }
  setPrivacyModal(isOpen: boolean) {
    this.isPrivacyModalOpen = isOpen;
  }
}
