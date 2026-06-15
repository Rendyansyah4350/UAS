import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth-guard';
import { WelcomeGuard } from './guards/welcome.guard';

const routes: Routes = [
  // 🟢 1. RUTE AWAL: Paksa murni langsung lempar ke splash tanpa ada gembok Guard apa pun!
  {
    path: '',
    pathMatch: 'full',
    redirectTo: 'splash',
  },

  // 🟢 2. HALAMAN SPLASH CUSTOM (Pintu gerbang tunggal aplikasi)
  {
    path: 'splash',
    loadChildren: () =>
      import('./pages/splash/splash.module').then((m) => m.SplashPageModule),
    data: { preload: false }, // Matikan preloading biar gak nyolong start render
  },

  // 🟢 3. RUTE WELCOME & LOGIN: Biarkan Guard dipasang di halamannya masing-masing, bukan di rute awal aplikasi
  {
    path: 'welcome',
    loadChildren: () =>
      import('./pages/welcome/welcome.module').then((m) => m.WelcomePageModule),
    canActivate: [WelcomeGuard], // 🔒 Gembok WelcomeGuard aman di sini
  },

  {
    path: 'login',
    loadChildren: () =>
      import('./pages/login/login.module').then((m) => m.LoginPageModule),
    data: { preload: false },
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

  // 4. Rute Utama & Fitur Aplikasi: Kunci dengan AuthGuard (TIDAK ADA YANG DIUBAH)
  {
    path: 'tabs',
    loadChildren: () =>
      import('./pages/tabs/tabs.module').then((m) => m.TabsPageModule),
    canActivate: [AuthGuard],
  },

  // 5. Rute Detail Dalam Aplikasi: Kunci dengan AuthGuard (TIDAK ADA YANG DIUBAH)
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

  // 🟢 6. Rute fallback otomatis: WAJIB PALING BAWAH DAN DILEMPAR KE SPLASH!
  { path: '**', redirectTo: 'splash' },
];

@NgModule({
  imports: [
    // Menggunakan strategi preloading bawaan kalian, namun sudah kita jinakkan lewat 'data' config
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules }),
  ],
  exports: [RouterModule],
})
export class AppRoutingModule {}
