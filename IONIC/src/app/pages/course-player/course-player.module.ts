import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';
import { CoursePlayerPageRoutingModule } from './course-player-routing.module';
import { CoursePlayerPage } from './course-player.page'; // Import komponennya

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule, // IonicModule ini wajib ada agar tag <ion-header> dll dikenali
    CoursePlayerPageRoutingModule
  ],
  declarations: [CoursePlayerPage] // <--- Didaftarkan di sini, BUKAN di imports!
})
export class CoursePlayerPageModule {}