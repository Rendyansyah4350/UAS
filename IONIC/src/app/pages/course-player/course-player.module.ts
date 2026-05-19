import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';
import { RouterModule } from '@angular/router'; // Penting
import { CoursePlayerPage } from './course-player.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    // Definisi rute langsung di sini
    RouterModule.forChild([{ path: '', component: CoursePlayerPage }])
  ],
  declarations: [CoursePlayerPage]
})
export class CoursePlayerPageModule {}