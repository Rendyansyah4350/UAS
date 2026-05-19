import { Component, OnInit } from '@angular/core';
import { NavController, AlertController } from '@ionic/angular';

@Component({
  selector: 'app-learning',
  templateUrl: './learning.page.html',
  styleUrls: ['./learning.page.scss'],
  standalone: false
})
export class LearningPage implements OnInit {
  activeTab: string = 'ongoing';
  allEnrollments: any[] = [];
  filteredEnrollments: any[] = [];

  constructor(private navCtrl: NavController, private alertCtrl: AlertController) {}

  ngOnInit() { this.loadData(); }

  loadData() {
    this.allEnrollments = [
      { id: 1, course_id: 101, progress: 45, course: { title: 'Ionic & Angular' } },
      { id: 2, course_id: 102, progress: 100, course: { title: 'React JS' } }
    ];
    this.filterData();
  }

  filterData() {
    this.filteredEnrollments = this.allEnrollments.filter(item => 
      this.activeTab === 'ongoing' ? item.progress < 100 : item.progress === 100
    );
  }

  goToPlayer(id: any) { this.navCtrl.navigateForward(['/course-player', id]); }

async openQuiz(item: any) {
  // Langsung navigasi tanpa cek 100%
  this.navCtrl.navigateForward(['/quiz', item.course_id]);
}
}