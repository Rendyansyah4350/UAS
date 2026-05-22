import { Component, OnInit } from '@angular/core';
import { CourseService } from 'src/app/services/course.service';

@Component({
  selector: 'app-certificate',
  templateUrl: './certificate.page.html',
  styleUrls: ['./certificate.page.scss'],
  standalone: false,
})
export class CertificatePage implements OnInit {

  listSertifikat: any[] = [];
  isLoading: boolean = false;

  constructor(private courseService: CourseService) { }

  ngOnInit() {
    this.muatDaftarSertifikat();
  }

  // Dipanggil setiap kali halaman dibuka biar datanya ter-update live setelah di-ACC admin
  ionViewWillEnter() {
    this.muatDaftarSertifikat();
  }

  muatDaftarSertifikat() {
    this.isLoading = true;
    this.courseService.getMyCertificates().subscribe({
      next: (res: any) => {
        // Menangkap response JSON {'success': true, 'data': [...] } dari cPanel kamu lek
        this.listSertifikat = res.data || [];
        this.isLoading = false;
        console.log('Sertifikat kamu sukses dimuat lek:', this.listSertifikat);
      },
      error: (err: any) => {
        console.error('Gagal mengambil data sertifikat dari server:', err);
        this.isLoading = false;
      }
    });
  }

  // Fungsi untuk mengunduh berkas PDF sertifikat langsung dari server cPanel
  downloadPdf(idSertifikat: number, namaKursus: string) {
    // Menembak langsung rute download PDF yang ada di web.php Laravel kamu lek
    const urlDownload = `https://eduvan.rehalivan.com/admin/certificates/download/${idSertifikat}`;
    window.open(urlDownload, '_system'); 
  }
}