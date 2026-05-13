import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth-guard'; // Guard tetap ada

const routes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full'
  },
  {
    path: 'login',
    loadChildren: () => import('./pages/login/login.module').then(m => m.LoginPageModule)
  },
  {
    path: 'register',
    loadChildren: () => import('./pages/register/register.module').then(m => m.RegisterPageModule)
  },
  {
    path: 'tabs',
    // canActivate: [AuthGuard], // Menggunakan AuthGuard agar tidak merah
    loadChildren: () => import('./pages/tabs/tabs.module').then(m => m.TabsPageModule)
  },
  {
    path: 'wishlist',
    canActivate: [AuthGuard],
    loadChildren: () => import('./pages/wishlist/wishlist.module').then(m => m.WishlistPageModule)
  },
  {
    path: 'profil',
    canActivate: [AuthGuard],
    loadChildren: () => import('./pages/profil/profil.module').then(m => m.ProfilPageModule)
  },
  {
    path: 'course-detail',
    loadChildren: () => import('./pages/course-detail/course-detail.module').then(m => m.CourseDetailPageModule)
  },
  {
    path: 'edit-profil',
    canActivate: [AuthGuard],
    loadChildren: () => import('./pages/edit-profil/edit-profil.module').then(m => m.EditProfilPageModule)
  },
  {
    path: 'notifications',
    // canActivate: [AuthGuard],
    loadChildren: () => import('./pages/notifications/notifications.module').then(m => m.NotificationsPageModule)
  },
  // Wildcard '**' diletakkan paling bawah agar tidak memblokir path lain
  {
    path: '**',
    redirectTo: 'login'
  },  {
    path: 'course',
    loadChildren: () => import('./pages/course/course.module').then( m => m.CoursePageModule)
  }

];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }