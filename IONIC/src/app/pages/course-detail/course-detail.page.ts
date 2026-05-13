import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'app-course-detail',
  templateUrl: './course-detail.page.html',
  styleUrls: ['./course-detail.page.scss'],
  standalone: false,
})
export class CourseDetailPage implements OnInit {
  course: any = {};

  constructor(
    private route: ActivatedRoute,
    private courseService: CourseService
  ) { }

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.getDetail(id);
    }
  }

  getDetail(id: string) {
    // Memanggil fungsi yang ada di service tadi
    this.courseService.getCourseById(id).subscribe((res: any) => {
      if (res.success) {
        this.course = res.data;
        console.log('Detail Kursus:', this.course);
      }
    }, error => {
      console.error('Gagal ambil detail:', error);
    });
  }

  // Di dalam class CourseDetailPage
enroll() {
  console.log('User menekan tombol enroll untuk kursus:', this.course.title);
  // Nanti di sini logic buat masukin data ke tabel enrollment di Laravel
  alert('Berhasil mendaftar kursus!');
}
}