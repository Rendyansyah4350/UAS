import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { TabsPage } from './tabs.page';

const routes: Routes = [
  {
    path: '',
    component: TabsPage,
    children: [
      {
        path: 'beranda',
        loadChildren: () => import('../../home/home.module').then(m => m.HomePageModule)
      },
      {
        path: 'course', // Pastikan ini persis sama dengan yang ada di ion-tab-button
        loadChildren: () => import('../course/course.module').then(m => m.CoursePageModule)
      },
      {
        path: 'profil',
        loadChildren: () => import('../profil/profil.module').then(m => m.ProfilPageModule)
      },
      {
        path: 'my-learning',
        loadChildren: () => import('../learning/learning.module').then(m => m.LearningPageModule)
      },
      {
        path: 'wishlist',
        loadChildren: () => import('../wishlist/wishlist.module').then(m => m.WishlistPageModule)
      },
      // TAMBAHAN: course-player didaftarkan di sini agar Tab Bar tetap ada
      {
        path: 'course-player',
        loadChildren: () => import('../course-player/course-player.module').then(m => m.CoursePlayerPageModule)
      },
      {
        path: '',
        redirectTo: 'beranda',
        pathMatch: 'full'
      }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class TabsPageRoutingModule {}