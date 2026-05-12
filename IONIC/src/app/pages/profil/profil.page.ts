import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule, AlertController } from '@ionic/angular';
import { Router } from '@angular/router';

@Component({
  selector: 'app-profil',
  templateUrl: './profil.page.html',
  styleUrls: ['./profil.page.scss'],
  standalone: true,
  imports: [IonicModule, CommonModule, FormsModule]
})
export class ProfilPage implements OnInit {

  constructor(
    private router: Router,
    private alertController: AlertController
  ) { }

  ngOnInit() { }

  // Fungsi pindah ke halaman Edit Profil
  goToEdit() {
    this.router.navigate(['/edit-profil']);
  }

  // Fungsi pindah ke halaman Notifikasi
  goToNotif() {
    this.router.navigate(['/notifications']);
  }

  // Placeholder untuk Keamanan
  goToSecurity() {
    console.log('Fitur keamanan diklik');
  }

  // Fungsi Keluar dengan Konfirmasi
  async logout() {
    const alert = await this.alertController.create({
      header: 'Konfirmasi',
      message: 'Apakah Anda yakin ingin keluar dari EduVan?',
      buttons: [
        {
          text: 'Batal',
          role: 'cancel'
        },
        {
          text: 'Keluar',
          handler: () => {
            this.router.navigate(['/login']);
          }
        }
      ]
    });

    await alert.present();
  }
}