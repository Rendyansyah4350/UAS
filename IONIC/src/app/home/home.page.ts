import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth';

@Component({
  selector: 'app-beranda',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
  standalone: false,
})
export class HomePage implements OnInit {
  namaUser: string = 'User';
  keywordPencarian: string = '';
  isLoading: boolean = true;
  
  // 🟢 KITA KEMBALIKAN NAMA INI AGAR SESUAI DENGAN HTML KAMU
  kursusTersaring: any[] = []; 

  constructor(
    private router: Router,
    private authService: AuthService,
  ) {}

  ngOnInit() {
    this.ambilNamaUserLive();
    this.muatKursusDariHosting();
  }

  ambilNamaUserLive() {
    this.authService.currentUser$.subscribe((user: any) => {
      if (user) {
        const namaLengkap = user.name || user.nama || user.fullname || 'User';
        this.namaUser = namaLengkap.split(' ')[0];
      } else {
        this.namaUser = 'User';
      }
    });
  }

  muatKursusDariHosting() {
    this.isLoading = true;
    this.authService.getCoursesFromServer().subscribe({
      next: (res: any) => {
        const dataMentah = res.data || [];
        // 🟢 Kita simpan ke kursusTersaring agar HTML tidak error
        this.kursusTersaring = dataMentah
          .filter((k: any) => Number(k.rating || 0) > 0)
          .sort((a: any, b: any) => Number(b.rating || 0) - Number(a.rating || 0))
          .slice(0, 3);
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Gagal:', err);
        this.isLoading = false;
      },
    });
  }

  // 🟢 FUNGSI INI WAJIB ADA AGAR ERROR goToDetail() HILANG
  goToDetail(id?: any) {
    if (id) {
      this.router.navigate(['/course-detail', id]);
    } else {
      this.router.navigate(['/course-detail']);
    }
  }

  fungsiCariKursus() {
    const keyword = this.keywordPencarian.trim();
    if (keyword) {
      this.router.navigate(['/tabs/course'], { state: { keyword: keyword } });
    } else {
      this.router.navigate(['/tabs/course']);
    }
    this.keywordPencarian = '';
  }

  goToNotif() { this.router.navigate(['/notifications']); }
  goToCourse() { this.router.navigateByUrl('/tabs/course'); }
}