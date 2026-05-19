import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { CoursePlayerPage } from './course-player.page';

const routes: Routes = [
  {
    path: '',
    component: CoursePlayerPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class CoursePlayerPageRoutingModule {}
