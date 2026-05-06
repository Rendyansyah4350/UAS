import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth-guard';

const routes: Routes = [
  // 1. Root: Arahkan ke login saat pertama kali aplikasi dibuka
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full'
  },
  
  // 2. Auth Page
  {
    path: 'login',
    loadChildren: () => import('./pages/login/login.module').then( m => m.LoginPageModule)
  },
    {
    path: 'register',
    loadChildren: () => import('./pages/register/register.module').then( m => m.RegisterPageModule)
  },
  // 3. Home (Daftar Course)
  {
    path: 'home',
    loadChildren: () => import('./home/home.module').then( m => m.HomePageModule),
    canActivate: [AuthGuard]
  },

  // 4. Detail Course (Wajib pakai :id agar bisa ambil data spesifik)
  {
    path: 'course-detail/:id',
    loadChildren: () => import('./pages/course-detail/course-detail.module').then( m => m.CourseDetailPageModule),
    canActivate: [AuthGuard]
  },

  // 5. Learning Page (Tempat menonton video/baca materi)
  {
    path: 'learning/:id',
    loadChildren: () => import('./pages/learning/learning.module').then( m => m.LearningPageModule),
    canActivate: [AuthGuard]
  },

  // 6. Quiz Page (Pengerjaan soal)
  {
    path: 'quiz/:id',
    loadChildren: () => import('./pages/quiz/quiz.module').then( m => m.QuizPageModule),
    canActivate: [AuthGuard]
  },

  // 7. Certificate Page
  {
    path: 'certificate',
    loadChildren: () => import('./pages/certificate/certificate.module').then( m => m.CertificatePageModule),
    canActivate: [AuthGuard]
  },

  // 8. Fallback (Jika user mengetik url ngawur, arahkan ke login atau home)
  {
    path: '**',
    redirectTo: 'login'
  },  {
    path: 'forgot-password',
    loadChildren: () => import('./pages/forgot-password/forgot-password.module').then( m => m.ForgotPasswordPageModule)
  },
  {
    path: 'verify-otp',
    loadChildren: () => import('./pages/verify-otp/verify-otp.module').then( m => m.VerifyOtpPageModule)
  },
  {
    path: 'reset-password',
    loadChildren: () => import('./pages/reset-password/reset-password.module').then( m => m.ResetPasswordPageModule)
  },

];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }