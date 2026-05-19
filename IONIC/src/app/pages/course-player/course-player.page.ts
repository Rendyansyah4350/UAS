import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-course-player',
  templateUrl: './course-player.page.html',
  styleUrls: ['./course-player.page.scss'],
  standalone: false
})
// WAJIB ADA KATA 'export' DI SINI
export class CoursePlayerPage implements OnInit { 
  courseId: string | null = '';

  constructor(private route: ActivatedRoute) {}

  ngOnInit() {
    this.courseId = this.route.snapshot.paramMap.get('id');
  }
}