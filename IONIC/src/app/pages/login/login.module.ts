import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms'; // Pastikan kedua ini ada
import { IonicModule } from '@ionic/angular';

import { LoginPageRoutingModule } from './login-routing.module';
import { LoginPage } from './login.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule, // Diperlukan untuk [(ngModel)] pada input OTP
    ReactiveFormsModule, // Diperlukan untuk [formGroup] pada form Login
    IonicModule,
    LoginPageRoutingModule,
  ],
  declarations: [LoginPage], // LoginPage harus terdaftar di sini karena standalone: false
})
export class LoginPageModule {}
