import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router'; // Pastikan ini tidak merah
import { TabsPage } from './tabs.page'; // Cek apakah file tabs.page.ts ada di folder yang sama

const routes: Routes = [
  {
    path: '',
    component: TabsPage,
    children: [
      {
        path: 'beranda',
        // Jalur ini mengarah ke src/app/home
        loadChildren: () => import('../../home/home.module').then(m => m.HomePageModule)
      },
      {
        path: 'profil', // Pastikan profil ada di sini
        loadChildren: () => import('../profil/profil.module').then(m => m.ProfilPageModule)
      },
      {
        path: 'course',
        loadChildren: () => import('../course/course.module').then(m => m.CoursePageModule)
      },
      {
        path: 'my-learning',
        loadChildren: () => import('../learning/learning.module').then(m => m.LearningPageModule)
      },
      {
        path: 'wishlist',
        loadChildren: () => import('../wishlist/wishlist.module').then(m => m.WishlistPageModule)
      },
      {
        path: 'profil',
        loadChildren: () => import('../profil/profil.module').then(m => m.ProfilPageModule)
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