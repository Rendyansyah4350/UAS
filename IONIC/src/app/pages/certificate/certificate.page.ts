import { Component, OnInit } from '@angular/core';
import { CourseService } from 'src/app/services/course.service';
import { HttpClient, HttpHeaders } from '@angular/common/http';

@Component({
  selector: 'app-certificate',
  templateUrl: './certificate.page.html',
  styleUrls: ['./certificate.page.scss'],
  standalone: false,
})
export class CertificatePage implements OnInit {

  listSertifikat: any[] = [];
  isLoading: boolean = false;

  constructor(private courseService: CourseService, private http: HttpClient) { }

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
    // 1. Ambil token bearer milik student dari local storage lewat fungsi pembantu Ivan
    let tokenUser = localStorage.getItem('token');
    if (tokenUser) {
      tokenUser = String(tokenUser).replace(/"/g, '').trim();
    }

    const headers = new HttpHeaders({
      'Authorization': `Bearer ${tokenUser}`
    });

    // 2. Tembak rute API download baru yang sudah kita buat di api.php
    const urlApiDownload = `https://eduvan.rehalivan.com/api/certificates/${idSertifikat}/download`;

    // 3. Tarik data file PDF-nya sebagai Blob (Binary Large Object) langsung di dalam HP
    this.http.get(urlApiDownload, { headers, responseType: 'blob' }).subscribe({
      next: (blobData: Blob) => {
        // 4. Ubah berkas biner blob menjadi link unduhan lokal instan di sistem HP student
        const linkLokal = document.createElement('a');
        linkLokal.href = window.URL.createObjectURL(blobData);
        linkLokal.download = `Sertifikat-${namaKursus.replace(/\s+/g, '_')}.pdf`;
        linkLokal.click();
        console.log('Sertifikat resmi berhasil terunduh');
      },
      error: (err) => {
        console.error('Gagal memproses unduhan sertifikat via API:', err);
        alert('Gagal mendownload sertifikat, pastikan jaringan aman.');
      }
    });
  }
}