import { Component, OnInit, ChangeDetectorRef } from '@angular/core'; // 🟢 TAMBAHAN: Import ChangeDetectorRef
import { CourseService } from '../../services/course.service';

@Component({
  selector: 'app-notifications',
  templateUrl: './notifications.page.html',
  styleUrls: ['./notifications.page.scss'],
  standalone: false,
})
export class NotificationsPage implements OnInit {
  // Array untuk menampung data dari Laravel backend
  listNotifikasi: any[] = [];
  isLoading: boolean = false;

  // 🟢 SUNTIKKAN ChangeDetectorRef ke dalam constructor
  constructor(
    private courseService: CourseService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.getNotificationData();
  }

  // 🟢 TAMBAHAN SAKTI: Memastikan notifikasi di-refresh murni SETIAP KALI user buka tab notif tanpa perlu restart apps
  ionViewWillEnter() {
    this.getNotificationData();
  }

  getNotificationData() {
    this.isLoading = true;
    this.cdr.detectChanges(); // Paksa spinner loading langsung muncul
    this.courseService.ambilDaftarNotifikasi().subscribe({
      next: (res: any) => {
        this.isLoading = false;
        console.log('Notifikasi sukses diambil:', res);

        // Mengantisipasi fleksibilitas respons API (res.data atau langsung res)
        const dataMentah = res.data ? res.data : res;

        if (Array.isArray(dataMentah)) {
          // 🟢 KUNCI BARU: Pakai .reverse() agar notifikasi transaksi yang paling baru otomatis nangkring di posisi paling atas
          this.listNotifikasi = dataMentah.reverse();
        } else {
          this.listNotifikasi = [];
        }

        this.cdr.detectChanges(); // Paksa HTML ngerender ulang kartu ijo sukses transaksi terbarumu!
        console.log(
          'Hasil manipulasi array setelah dibalik:',
          this.listNotifikasi
        );
      },
      error: (err: any) => {
        console.error('Gagal mengambil data notifikasi:', err);
        this.isLoading = false;
        this.cdr.detectChanges();
      },
    });
  }

  // 🔥 FUNGSI BARU: Menandai notifikasi sebagai terbaca ketika item diklik di HTML
  bukaPesanNotifikasi(notifId: string) {
    if (!notifId) return;

    this.courseService.tandaiNotifikasiTerbaca(notifId).subscribe({
      next: (res: any) => {
        if (res && res.status === 'success') {
          console.log('Notifikasi sukses ditandai terbaca:', notifId);

          // Refresh data list notifikasi agar tanda status terbaca di UI langsung sinkron
          this.getNotificationData();
        }
      },
      error: (err: any) => {
        console.error('Gagal memperbarui status terbaca notifikasi:', err);
      },
    });
  }
}
