import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CourseService } from '../../services/course.service'; // 🚨 1. PASTIKAN SERVICE DI-IMPORT LEK

@Component({
  selector: 'app-riwayat-transaksi',
  templateUrl: './riwayat-transaksi.page.html',
  styleUrls: ['./riwayat-transaksi.page.scss'],
  standalone: false,
})
export class RiwayatTransaksiPage implements OnInit {

  // 🟢 2. SIAPKAN VARIABEL KONTROL DATA DAN LOADING UI LEK
  listTransaksi: any[] = [];
  isLoading: boolean = false;

  constructor(
    private courseService: CourseService,
    private cdr: ChangeDetectorRef
  ) { }

  ngOnInit() {
  }

  // 🟢 3. SINKRONISASI AKTIF: TARIK DATA LIVE TIAP KALI HALAMAN DIBUKA
  ionViewWillEnter() {
    this.ambilRiwayatTransaksiStudent();
  }

  ambilRiwayatTransaksiStudent() {
    this.isLoading = true;
    this.cdr.detectChanges();

    this.courseService.getMyEnrollments().subscribe({
      next: (res: any) => {
        this.isLoading = false;
        console.log('Isi mentah data riwayat transaksi:', res);

        // Mengantisipasi fleksibilitas respons API (res.data atau langsung res)
        const dataMentah = res.data ? res.data : res;

        if (Array.isArray(dataMentah)) {
          // Masukkan data pendaftaran kursus ke dalam array lokal lek
          this.listTransaksi = dataMentah;
        }
        
        this.cdr.detectChanges(); // Paksa HTML render ulang data terbaru
      },
      error: (err) => {
        this.isLoading = false;
        console.error('Gagal mengambil riwayat transaksi di frontend:', err);
        this.cdr.detectChanges();
      }
    });
  }
}