import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'app-course',
  templateUrl: './course.page.html',
  styleUrls: ['./course.page.scss'],
  standalone: false,
})
export class CoursePage implements OnInit {
  listCourses: any[] = [];

  constructor(private navCtrl: NavController, private courseService: CourseService) { } // Tambahkan di constructor

  ngOnInit() {
    this.loadData();
  }

  loadData() {
    this.courseService.getCourses().subscribe((res: any) => {
      this.listCourses = res.data; // Sesuaikan dengan struktur JSON API kamu
      console.log('Data API:', this.listCourses);
    }, error => {
      console.error('Gagal ambil data', error);
    });
  }

  // TAMBAHKAN FUNGSI INI
goToDetail(id: any) {
  this.navCtrl.navigateForward(['/course-detail', id]);
}

}