<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $courseReports = Course::withCount(['enrollments as total_sold' => function ($query)
        {
            $query->where('status', 'success');
        }])
            ->withSum(['enrollments as total_revenue' => function ($query)
            {
                $query->where('status', 'success');
            }], 'price_bought')
            ->get();

        $transactionDetails = Enrollment::with(['user', 'course'])
            ->where('status', 'success')
            ->latest()
            ->get();

        $pendingVerifications = Enrollment::with(['user', 'course'])
            ->where('status', 'Checking Admin')
            ->latest()
            ->get();

        $grandTotal = $transactionDetails->sum('price_bought');

        return view('admin.pembelian.index', compact('courseReports', 'transactionDetails', 'pendingVerifications', 'grandTotal'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:success,Fail'
        ]);

        try
        {
            $enrollment = Enrollment::findOrFail($id);

            $enrollment->update([
                'status' => $request->status
            ]);

            $msg = $request->status === 'success'
                ? 'Pembayaran berhasil dikonfirmasi! Akses kursus mahasiswa telah aktif.'
                : 'Pembayaran ditolak!';

            return redirect()->back()->with('success', $msg);
        }
        catch (\Exception $e)
        {
            return redirect()->back()->with('error', 'Gagal memproses konfirmasi: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        // Ambil semua data transaksi yang sukses untuk dicetak ke laporan PDF
        $transactions = Enrollment::with(['user', 'course'])->where('status', 'success')->latest()->get();
        $totalRevenue = Enrollment::where('status', 'success')->sum('price_bought');

        // Load view khusus untuk PDF
        $pdf = Pdf::loadView('admin.pembelian.pdf', compact('transactions', 'totalRevenue'));

        // Download file PDF-nya
        return $pdf->download('laporan-pembelian-' . date('Y-m-d') . '.pdf');
    }

    public function downloadReport($id)
    {
        // Ambil data transaksi tunggal berdasarkan ID enrollment dengan relasi user dan course
        $trans = Enrollment::with(['user', 'course'])->findOrFail($id);

        // Bungkus data untuk dikirim ke view PDF
        $data = [
            'trans' => $trans,
            'downloaded_at' => Carbon::now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB'
        ];

        // Load view cetakan item pdf kuitansi
        $pdf = Pdf::loadView('admin.pembelian.report_item_pdf', $data)
            ->setOption('isRemoteEnabled', true)
            ->setPaper('a4', 'portrait');

        // Bikin nama file otomatis, contoh: Laporan-Transaksi-Budi-Santoso-Belajar-Laravel.pdf
        $filename = 'Laporan-Transaksi-' . Str::slug($trans->user->name) . '-' . Str::slug($trans->course->title) . '.pdf';

        return $pdf->download($filename);
    }

    public function downloadCourseReport($id)
    {
        // 1. Ambil data materi berdasarkan ID beserta riwayat pendaftaran student yang sukses
        $course = Course::with(['enrollments' => function ($query)
        {
            $query->where('status', 'success')->with('user')->latest();
        }])->findOrFail($id);

        // 2. Hitung statistik internal untuk data di dalam PDF nanti
        $totalSold = $course->enrollments->count();
        $totalRevenue = $course->enrollments->sum('price_bought');

        $data = [
            'course' => $course,
            'totalSold' => $totalSold,
            'totalRevenue' => $totalRevenue,
            'downloaded_at' => \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB'
        ];

        // 3. Render layout PDF khusus rekapan materi
        $pdf = Pdf::loadView('admin.pembelian.report_course_pdf', $data)
            ->setPaper('a4', 'portrait');

        // 4. Nama file otomatis: Laporan-Materi-Belajar-Laravel.pdf
        $filename = 'Laporan-Materi-' . \Illuminate\Support\Str::slug($course->title) . '.pdf';

        return $pdf->download($filename);
    }
}
