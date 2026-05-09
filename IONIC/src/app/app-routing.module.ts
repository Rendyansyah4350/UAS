import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full',
  },
  {
    path: 'login',
    loadChildren: () =>
      import('./pages/login/login.module').then((m) => m.LoginPageModule),
  },
  // --- TAMBAHKAN REGISTER DI SINI ---
  {
    path: 'register',
    loadChildren: () =>
      import('./pages/register/register.module').then(
        (m) => m.RegisterPageModule,
      ),
  },
  // ----------------------------------
  {
    path: 'tabs',
    loadChildren: () =>
      import('./pages/tabs/tabs.module').then((m) => m.TabsPageModule),
  },
  {
    path: 'wishlist',
    loadChildren: () =>
      import('./pages/wishlist/wishlist.module').then(
        (m) => m.WishlistPageModule,
      ),
  },
  {
    path: 'profil',
    loadChildren: () =>
      import('./pages/profil/profil.module').then((m) => m.ProfilPageModule),
  },
  // Wildcard '**' HARUS diletakkan paling bawah agar tidak memblokir path lain
  {
    path: '**',
    redirectTo: 'login',
  },
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules }),
  ],
  exports: [RouterModule],
})
export class AppRoutingModule {}
