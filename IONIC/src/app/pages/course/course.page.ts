import { Component, OnInit } from '@angular/core';
import { NavController } from '@ionic/angular';

@Component({
  selector: 'app-course',
  templateUrl: './course.page.html',
  styleUrls: ['./course.page.scss'],
  standalone: false,
})
export class CoursePage implements OnInit {

  // Inisialisasi variabel courses dengan data dummy
  courses: any[] = [
    {
      id: 1,
      title: 'Mastering Laravel 11',
      price: '300,000',
      image: 'https://cdn.worldvectorlogo.com/logos/laravel-2.svg'
    },
    {
      id: 2,
      title: 'HTML & CSS Dasar',
      price: '200,000',
      image: 'https://cdn.worldvectorlogo.com/logos/html-1.svg'
    },
    {
      id: 3,
      title: 'UI/UX Design with Figma',
      price: '450,000',
      image: 'https://cdn.worldvectorlogo.com/logos/figma-1.svg'
    },
    {
      id: 4,
      title: 'Ionic Framework Expert',
      price: '500,000',
      image: 'https://cdn.worldvectorlogo.com/logos/ionic-icon.svg'
    }
  ];

  constructor(private navCtrl: NavController) { }

  ngOnInit() {
  }

  goToDetail(id: number) {
    this.navCtrl.navigateForward(`/course-detail/${id}`);
  }

}