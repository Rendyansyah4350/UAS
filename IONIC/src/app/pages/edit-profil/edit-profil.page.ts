import { Component, OnInit } from '@angular/core';
import { NavController, ToastController } from '@ionic/angular';
import { AuthService } from '../../services/auth'; // Sesuaikan jalurnya

@Component({
  selector: 'app-edit-profil',
  templateUrl: './edit-profil.page.html',
  styleUrls: ['./edit-profil.page.scss'],
  standalone: false,
})
export class EditProfilPage implements OnInit {
  userProfile: any = { name: '', email: '' };

  constructor(
    private authService: AuthService,
    private navCtrl: NavController,
    private toastCtrl: ToastController
  ) { }

  ngOnInit() {
    this.loadCurrentProfile();
  }

  loadCurrentProfile() {
    // Ambil profil live dari server biar inputan form langsung terisi nama asli
    this.authService.getProfileFromServer().subscribe((res: any) => {
      if (res) {
        this.userProfile = { name: res.name, email: res.email };
      }
    });
  }

  async simpanPerubahan() {
    const bodyData = {
      name: this.userProfile.name,
      email: this.userProfile.email
    };

    // Tembak langsung ke server hosting online kamu
    this.authService.updateProfile(bodyData).subscribe(async (res: any) => {
      const toast = await this.toastCtrl.create({
        message: 'Profil berhasil diperbarui di database!',
        duration: 2000,
        color: 'success',
        position: 'bottom'
      });
      await toast.present();
      
      this.navCtrl.back(); // Balik ke profil, otomatis profil ngeload API me yang baru
    }, async (error) => {
      console.error(error);
      const toast = await this.toastCtrl.create({
        message: 'Gagal memperbarui profil di server.',
        duration: 2000,
        color: 'danger'
      });
      await toast.present();
    });
  }
}