import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';
import { RouterModule } from '@angular/router'; // Import RouterModule
import { CoursePlayerPage } from './course-player.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    // Rute didefinisikan di sini agar modul tahu komponen mana yang dimuat
    RouterModule.forChild([{ path: '', component: CoursePlayerPage }])
  ],
  declarations: [CoursePlayerPage]
})
export class CoursePlayerPageModule {}