import { Component, OnInit } from '@angular/core';
import { CourseService } from 'src/app/services/course.service';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { LoadingController, ToastController } from '@ionic/angular';
import { Browser } from '@capacitor/browser';

@Component({
  selector: 'app-certificate',
  templateUrl: './certificate.page.html',
  styleUrls: ['./certificate.page.scss'],
  standalone: false,
})
export class CertificatePage implements OnInit {
  listSertifikat: any[] = [];
  isLoading: boolean = false;
  isDownloading: boolean = false;
  activeCertId: number | null = null;

  constructor(
    private courseService: CourseService,
    private http: HttpClient,
    private loadingCtrl: LoadingController,
    private toastCtrl: ToastController,
  ) {}

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
        // Menangkap response JSON {'success': true, 'data': [...] } dari cPanel kamu
        this.listSertifikat = res.data || [];
        this.isLoading = false;
        console.log('Sertifikat kamu sukses dimuat:', this.listSertifikat);
      },
      error: (err: any) => {
        console.error('Gagal mengambil data sertifikat dari server:', err);
        this.isLoading = false;
      },
    });
  }

  // Fungsi untuk mengunduh berkas PDF sertifikat langsung dari server cPanel
  // 🟢 Ubah menjadi 'async' agar bisa mengontrol animasi loading Ionic
  async downloadPdf(idSertifikat: number, namaKursus: string) {
    // Kunci tombol agar tidak di-spam klik
    this.isDownloading = true;
    this.activeCertId = idSertifikat;

    // 1. TAMPILKAN LOADING MEMUTAR DI TENGAH LAYAR
    const loadingSertifikat = await this.loadingCtrl.create({
      message: 'Sedang memproses sertifikat...',
      spinner: 'crescent',
      backdropDismiss: false,
    });
    await loadingSertifikat.present();

    // 2. Ambil token bearer milik student dari local storage lewat fungsi pembantu Ivan
    let tokenUser = localStorage.getItem('token');
    if (tokenUser) {
      tokenUser = String(tokenUser).replace(/"/g, '').trim();
    }

    const headers = new HttpHeaders({
      Authorization: `Bearer ${tokenUser}`,
    });

    // 3. Tembak rute API download asli kalian (Pakai S)
    const urlApiDownload = `https://eduvan.rehalivan.com/api/certificates/${idSertifikat}/download`;

    // 4. Tarik data file PDF sebagai Blob dengan Header tetap terjaga aman (Gak bakal nyasar ke Web Admin)
    this.http.get(urlApiDownload, { headers, responseType: 'blob' }).subscribe({
      next: async (blobData: Blob) => {
        // 5. 🔑 TRICK KHUSUS APK: Menggunakan FileReader untuk membaca Blob menjadi Data URL lokal
        const pembacaFile = new FileReader();
        pembacaFile.readAsDataURL(blobData);
        pembacaFile.onloadend = () => {
          const base64Data = pembacaFile.result as string;

          // Buat trigger download lokal yang diizinkan oleh WebView Android APK
          const linkLokal = document.createElement('a');
          linkLokal.href = base64Data;
          linkLokal.download = `Sertifikat-${namaKursus.replace(/\s+/g, '_')}.pdf`;

          document.body.appendChild(linkLokal);
          linkLokal.click();
          document.body.removeChild(linkLokal);
        };

        console.log('Sertifikat resmi berhasil terunduh');

        // Matikan animasi loading
        await loadingSertifikat.dismiss();
        this.isDownloading = false;
        this.activeCertId = null;
      },
      error: async (err) => {
        console.error('Gagal memproses unduhan sertifikat via API:', err);

        // Matikan animasi loading jika gagal
        await loadingSertifikat.dismiss();
        this.isDownloading = false;
        this.activeCertId = null;

        // Tampilkan pesan error toast premium
        const toast = await this.toastCtrl.create({
          message: 'Gagal mendownload sertifikat, pastikan jaringan aman.',
          duration: 3000,
          color: 'danger',
        });
        await toast.present();
      },
    });
  }

  async downloadPdfViaBrowser(idSertifikat: number) {
    if (!idSertifikat) return;

    // Ambil token bearer murni dari local storage
    let tokenUser = localStorage.getItem('token') || '';

    // KUNCI UTAMA: Hapus semua tanda kutip dua dan spasi gaib agar dibaca valid oleh Laravel Sanctum
    if (tokenUser) {
      tokenUser = String(tokenUser).replace(/"/g, '').trim();
    }

    // Buat URL direct download dengan token yang sudah bersih total
    const urlDirectDownload = `https://eduvan.rehalivan.com/api/certificates/${idSertifikat}/download?token=${tokenUser}`;

    try {
      // Melempar proses download ke engine browser eksternal bawaan perangkat
      await Browser.open({ url: urlDirectDownload });
      console.log(
        'Membuka browser eksternal dengan token bersih:',
        tokenUser,
      );
    } catch (error) {
      console.error('Gagal membuka browser eksternal:', error);
      window.open(urlDirectDownload, '_blank');
    }
  }
}
