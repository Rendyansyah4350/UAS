import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-course-player',
  templateUrl: './course-player.page.html',
  styleUrls: ['./course-player.page.scss'],
  standalone: false, // Wajib false sesuai permintaanmu
})
export class CoursePlayerPage implements OnInit {
  materiAktif: any;
  daftarMateri = [
    { id: 1, judul: 'Pendahuluan Laravel', durasi: '5m', video_url: 'url_video_1', is_completed: true },
    { id: 2, judul: 'Instalasi Database', durasi: '10m', video_url: 'url_video_2', is_completed: false }
  ];

  ngOnInit() {
    this.materiAktif = this.daftarMateri[0];
  }

  pilihMateri(m: any) {
    this.materiAktif = m;
  }
}