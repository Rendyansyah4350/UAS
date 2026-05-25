import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth';
import { SearchService } from '../services/search'; // Pastikan path ini benar

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
  kursusTersaring: any[] = []; 

  constructor(
    private router: Router,
    private authService: AuthService,
    private searchService: SearchService
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

  goToDetail(id?: any) {
    if (id) {
      this.router.navigate(['/course-detail', id]);
    } else {
      this.router.navigate(['/course-detail']);
    }
  }

  // 🔥 FUNGSI UNTUK IKLAN (Menghilangkan error Anda)
  goToBannerDetail() {
    this.router.navigate(['/tabs/course']);
  }

  fungsiCariKursus() {
    const keyword = this.keywordPencarian.trim();
    // Kirim keyword ke Service
    this.searchService.changeKeyword(keyword);
    
    this.router.navigate(['/tabs/course']);
    this.keywordPencarian = '';
  }

  goToNotif() { this.router.navigate(['/notifications']); }
  goToCourse() { this.router.navigateByUrl('/tabs/course'); }
}