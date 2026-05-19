import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ToastController } from '@ionic/angular'; // Untuk notifikasi ke user

@Component({
  selector: 'app-course-player',
  templateUrl: './course-player.page.html',
  styleUrls: ['./course-player.page.scss'],
  standalone: false
})
export class CoursePlayerPage implements OnInit {
  courseId: string | null = '';
  isCompleted: boolean = false; // Status untuk tombol

  constructor(
    private route: ActivatedRoute,
    private toastCtrl: ToastController
  ) {}

  ngOnInit() {
    this.courseId = this.route.snapshot.paramMap.get('id');
  }

  // Fungsi inilah yang dipicu saat tombol diklik
  async markAsComplete() {
    this.isCompleted = true; // Mengubah status tombol
    
    // Memberi umpan balik ke user
    const toast = await this.toastCtrl.create({
      message: 'Materi berhasil diselesaikan!',
      duration: 2000,
      color: 'success'
    });
    await toast.present();
    
    console.log("Materi ID:", this.courseId, "ditandai selesai.");
    // Nanti di sini kamu akan memanggil service HTTP untuk kirim data ke database
  }
}