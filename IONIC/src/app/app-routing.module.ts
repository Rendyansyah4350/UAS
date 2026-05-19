import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth-guard';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full'
  },

  // ==========================================
  // RUTE OTENTIKASI & KEAMANAN
  // ==========================================
  {
    path: 'login',
    loadChildren: () => import('./pages/login/login.module').then(m => m.LoginPageModule)
  },
  {
    path: 'register',
    loadChildren: () => import('./pages/register/register.module').then(m => m.RegisterPageModule)
  },
  {
    path: 'verify-otp',
    loadChildren: () => import('./pages/verify-otp/verify-otp.module').then(m => m.VerifyOtpPageModule)
  },
  {
    path: 'forgot-password',
    loadChildren: () => import('./pages/forgot-password/forgot-password.module').then(m => m.ForgotPasswordPageModule)
  },
  {
    path: 'reset-password',
    loadChildren: () => import('./pages/reset-password/reset-password.module').then(m => m.ResetPasswordPageModule)
  },

  // ==========================================
  // RUTE UTAMA APLIKASI
  // ==========================================
  {
    path: 'tabs',
    loadChildren: () => import('./pages/tabs/tabs.module').then(m => m.TabsPageModule)
  },
  // Rute Fullscreen Course Player (Penting: tambahkan /:id)
  {
    path: 'course-player/:id',
    loadChildren: () => import('./pages/course-player/course-player.module').then(m => m.CoursePlayerPageModule)
  },
  {
    path: 'course',
    loadChildren: () => import('./pages/course/course.module').then(m => m.CoursePageModule)
  },
 {
  path: 'course-detail/:id',
  loadChildren: () => import('./pages/course-detail/course-detail.module').then(m => m.CourseDetailPageModule)
},
  {
    path: 'wishlist',
    // canActivate: [AuthGuard],
    loadChildren: () => import('./pages/wishlist/wishlist.module').then(m => m.WishlistPageModule)
  },
  {
    path: 'edit-profil',
    loadChildren: () => import('./pages/edit-profil/edit-profil.module').then(m => m.EditProfilPageModule)
  },
  {
    path: 'certificate',
    loadChildren: () => import('./pages/certificate/certificate.module').then( m => m.CertificatePageModule)
  },
  {
    path: 'notifications',
    loadChildren: () => import('./pages/notifications/notifications.module').then(m => m.NotificationsPageModule)
  },
  {
    path: 'riwayat-transaksi',
    loadChildren: () => import('./pages/riwayat-transaksi/riwayat-transaksi.module').then( m => m.RiwayatTransaksiPageModule)
  },

  // ==========================================
  // WILDCARD ROUTE (HARUS PALING BAWAH)
  // ==========================================
  {
    path: '**',
    redirectTo: 'tabs'
  },
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }