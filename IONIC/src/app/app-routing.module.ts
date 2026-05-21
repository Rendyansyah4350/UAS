import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth-guard';

const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', loadChildren: () => import('./pages/login/login.module').then(m => m.LoginPageModule) },
  { path: 'register', loadChildren: () => import('./pages/register/register.module').then(m => m.RegisterPageModule) },
  { path: 'verify-otp', loadChildren: () => import('./pages/verify-otp/verify-otp.module').then(m => m.VerifyOtpPageModule) },
  { path: 'forgot-password', loadChildren: () => import('./pages/forgot-password/forgot-password.module').then(m => m.ForgotPasswordPageModule) },
  { path: 'reset-password', loadChildren: () => import('./pages/reset-password/reset-password.module').then(m => m.ResetPasswordPageModule) },

  // Rute Utama
  { path: 'tabs', loadChildren: () => import('./pages/tabs/tabs.module').then(m => m.TabsPageModule) },

  // Rute Detail (di luar tabs)
  { path: 'course-player/:id', loadChildren: () => import('./pages/course-player/course-player.module').then(m => m.CoursePlayerPageModule) },
  { path: 'course-detail/:id', loadChildren: () => import('./pages/course-detail/course-detail.module').then(m => m.CourseDetailPageModule)},
  { path: 'quiz/:id', loadChildren: () => import('./pages/quiz/quiz.module').then(m => m.QuizPageModule) },

  { path: '**', redirectTo: 'tabs' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })],
  exports: [RouterModule]
})
export class AppRoutingModule { }