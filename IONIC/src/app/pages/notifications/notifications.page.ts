import { Component, OnInit } from '@angular/core';
import { CourseService } from '../../services/course.service'; // 🚨 Pastikan path folder service Ivan sudah pas

@Component({
  selector: 'app-notifications',
  templateUrl: './notifications.page.html',
  styleUrls: ['./notifications.page.scss'],
  standalone: false,
})
export class NotificationsPage implements OnInit {

  // Array untuk menampung data dari Laravel backend
  listNotifikasi: any[] = [];
  isLoading: boolean = false;

  constructor(private courseService: CourseService) { }

  ngOnInit() {
    this.getNotificationData();
  }

  getNotificationData() {
    this.isLoading = true;
    this.courseService.ambilDaftarNotifikasi().subscribe({
      next: (res: any) => {
        // Menangkap data array dari response API
        this.listNotifikasi = res.data || [];
        this.isLoading = false;
        console.log('Notifikasi sukses diambil lek:', this.listNotifikasi);
      },
      error: (err: any) => {
        console.error('Gagal mengambil data notifikasi:', err);
        this.isLoading = false;
      }
    });
  }

}