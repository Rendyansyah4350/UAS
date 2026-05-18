import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth-guard'; // Memastikan Guard terhubung dengan benar

const routes: Routes = [
  {
    path: '',
    redirectTo: 'tabs', // Diarahkan ke tabs agar user bisa langsung melihat katalog
    pathMatch: 'full'
  },
  
  // ==========================================
  // RUTE UTAN AUTENTIKASI & KEAMANAN AKUN
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
    path: 'verify-otp', // <-- PERBAIKAN: Menambahkan rute verifikasi OTP yang baru dipisah
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
  // RUTE UTAMA APLIKASI EDUVAN
  // ==========================================
  {
    path: 'tabs',
    // canActivate: [AuthGuard], // Dibuka agar pengunjung anonim bisa melihat katalog kursus
    loadChildren: () => import('./pages/tabs/tabs.module').then(m => m.TabsPageModule)
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
    canActivate: [AuthGuard], // Wajib login untuk melihat daftar keinginan
    loadChildren: () => import('./pages/wishlist/wishlist.module').then(m => m.WishlistPageModule)
  },
  {
    path: 'edit-profil',
    // canActivate: [AuthGuard], // Wajib login untuk mengubah data diri
    loadChildren: () => import('./pages/edit-profil/edit-profil.module').then(m => m.EditProfilPageModule)
  },
  {
    path: 'certificate', // <-- Pastikan namanya sama persis 'certificate'
    loadChildren: () => import('./pages/certificate/certificate.module').then( m => m.CertificatePageModule)
  },
  {
    path: 'notifications',
    // canActivate: [AuthGuard], // Wajib login karena notifikasi bersifat personal
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
    path: '**', // Menangani jika user mengetik alamat asal-asalan, langsung oper ke tabs
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