import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';
import { SplashPageRoutingModule } from './splash-routing.module';
import { SplashPage } from './splash.page'; // Tetap import gambarnya

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    SplashPageRoutingModule,
    SplashPage, // 🟢 KUNCIANNYA: Pindahkan SplashPage ke dalam array IMPORTS sini!
  ],
  // 🔴 BARIS DECLARATIONS YANG EROR TADI SUDAH DIHAPUS TOTAL / DIKOSONGKAN
  declarations: [],
})
export class SplashPageModule {}
