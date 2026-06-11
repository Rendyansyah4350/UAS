import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth-guard';

const routes: Routes = [
  // 1. Rute Awal: Langsung arahkan default-nya ke welcome
  {
    path: '',
    redirectTo: 'welcome',
    pathMatch: 'full',
  },

  // 2. RUTE HALAMAN LUAR (Dipastikan aman di atas bintang ganda!)
  {
    path: 'welcome',
    loadChildren: () =>
      import('./pages/welcome/welcome.module').then((m) => m.WelcomePageModule),
  },

  {
    path: 'login',
    loadChildren: () =>
      import('./pages/login/login.module').then((m) => m.LoginPageModule),
  },

  {
    path: 'register',
    loadChildren: () =>
      import('./pages/register/register.module').then(
        (m) => m.RegisterPageModule
      ),
  },

  {
    path: 'verify-otp',
    loadChildren: () =>
      import('./pages/verify-otp/verify-otp.module').then(
        (m) => m.VerifyOtpPageModule
      ),
  },

  {
    path: 'forgot-password',
    loadChildren: () =>
      import('./pages/forgot-password/forgot-password.module').then(
        (m) => m.ForgotPasswordPageModule
      ),
  },

  {
    path: 'reset-password',
    loadChildren: () =>
      import('./pages/reset-password/reset-password.module').then(
        (m) => m.ResetPasswordPageModule
      ),
  },

  // 3. Rute Utama & Fitur Aplikasi: Kunci dengan AuthGuard
  {
    path: 'tabs',
    loadChildren: () =>
      import('./pages/tabs/tabs.module').then((m) => m.TabsPageModule),
    canActivate: [AuthGuard],
  },

  // 4. Rute Detail Dalam Aplikasi: Kunci dengan AuthGuard
  {
    path: 'course-player/:id',
    loadChildren: () =>
      import('./pages/course-player/course-player.module').then(
        (m) => m.CoursePlayerPageModule
      ),
    canActivate: [AuthGuard],
  },

  {
    path: 'course-detail/:id',
    loadChildren: () =>
      import('./pages/course-detail/course-detail.module').then(
        (m) => m.CourseDetailPageModule
      ),
    canActivate: [AuthGuard],
  },

  {
    path: 'quiz/:id',
    loadChildren: () =>
      import('./pages/quiz/quiz.module').then((m) => m.QuizPageModule),
    canActivate: [AuthGuard],
  },

  {
    path: 'edit-profil',
    loadChildren: () =>
      import('./pages/edit-profil/edit-profil.module').then(
        (m) => m.EditProfilPageModule
      ),
    canActivate: [AuthGuard],
  },

  {
    path: 'certificate',
    loadChildren: () =>
      import('./pages/certificate/certificate.module').then(
        (m) => m.CertificatePageModule
      ),
    canActivate: [AuthGuard],
  },

  {
    path: 'riwayat-transaksi',
    loadChildren: () =>
      import('./pages/riwayat-transaksi/riwayat-transaksi.module').then(
        (m) => m.RiwayatTransaksiPageModule
      ),
    canActivate: [AuthGuard],
  },

  {
    path: 'notifications',
    loadChildren: () =>
      import('./pages/notifications/notifications.module').then(
        (m) => m.NotificationsPageModule
      ),
    canActivate: [AuthGuard],
  },

  // 5. Rute fallback otomatis: WAJIB DI PALING BAWAH DEH LEK!
  { path: '**', redirectTo: 'welcome' },
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules }),
  ],
  exports: [RouterModule],
})
export class AppRoutingModule {}
