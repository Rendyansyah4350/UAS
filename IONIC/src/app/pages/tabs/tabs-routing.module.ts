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
      { path: 'profil', loadChildren: () => import('../profil/profil.module').then(m => m.ProfilPageModule) },
      { path: 'my-learning', loadChildren: () => import('../learning/learning.module').then(m => m.LearningPageModule) },
      { path: 'wishlist', loadChildren: () => import('../wishlist/wishlist.module').then(m => m.WishlistPageModule) },
      { path: 'edit-profil', loadChildren: () => import('../edit-profil/edit-profil.module').then(m => m.EditProfilPageModule) },
      { path: 'certificate', loadChildren: () => import('../certificate/certificate.module').then(m => m.CertificatePageModule) },
      { path: 'riwayat-transaksi', loadChildren: () => import('../riwayat-transaksi/riwayat-transaksi.module').then(m => m.RiwayatTransaksiPageModule) },
      { path: 'notifications', loadChildren: () => import('../notifications/notifications.module').then(m => m.NotificationsPageModule) },
      { path: '', redirectTo: 'beranda', pathMatch: 'full' }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class TabsPageRoutingModule {}