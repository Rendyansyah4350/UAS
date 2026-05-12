import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';

@Component({
  selector: 'app-beranda',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
  standalone: false,
})
export class HomePage implements OnInit {

  constructor(private router: Router) { }

  ngOnInit() {
  }

  // Fungsi untuk ke halaman Notifikasi
  goToNotif() {
    this.router.navigate(['/notifications']);
  }

  // Fungsi untuk ke halaman Detail Kursus
  goToDetail() {
    this.router.navigate(['/course-detail']);
  }

}