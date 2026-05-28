import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { TabsPage } from './tabs.page';

const routes: Routes = [
{
  path: '',
  component: TabsPage,
  children: [
    { path: 'beranda', loadChildren: () => import('../../home/home.module').then(m => m.HomePageModule) },
    { path: 'course', loadChildren: () => import('../course/course.module').then(m => m.CoursePageModule) },
    { path: 'my-learning', loadChildren: () => import('../learning/learning.module').then(m => m.LearningPageModule) },
    { path: 'wishlist', loadChildren: () => import('../wishlist/wishlist.module').then(m => m.WishlistPageModule) },
    { path: 'profil', loadChildren: () => import('../profil/profil.module').then(m => m.ProfilPageModule) },
    
    // 🟢 Hanya menyisakan menu yang emang mau ditempelin tabs bawah lek
    
    { path: '', redirectTo: 'profil', pathMatch: 'full' }
  ]
}
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class TabsPageRoutingModule {} 