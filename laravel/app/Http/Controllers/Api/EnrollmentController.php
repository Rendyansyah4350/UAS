<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\QuizResult;
// 🟢 TAMBAHAN: Import library resmi SDK Xendit
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class EnrollmentController extends Controller
{
    // 🟢 TAMBAHAN: Set API Key Xendit setiap kali controller ini dipanggil
    public function __construct()
    {
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = $request->user();

        // Cek apakah sudah pernah beli sebelumnya
        $alreadyEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $request->course_id)
            ->first();

        if ($alreadyEnrolled)
        {
            // Jika sudah sukses beli, kunci akses
            if ($alreadyEnrolled->status === 'success')
            {
                return response()->json(['message' => 'Kamu sudah memiliki kursus ini'], 400);
            }

            // 🟢 TAMBAHAN: Jika statusnya masih pending, kembalikan invoice yang lama biar bisa dibayar
            if ($alreadyEnrolled->status === 'pending')
            {
                return response()->json([
                    'success' => true,
                    'message' => 'Silahkan selesaikan pembayaran kursus ini',
                    'data' => $alreadyEnrolled
                ]);
            }
        }

        $course = Course::find($request->course_id);
        $price = $course->price ?? 0;

        // 🟢 TAMBAHAN: Generate kode unik transaksi untuk Xendit
        $externalId = 'eduvan-' . $user->id . '-' . $course->id . '-' . time();

        try
        {
            $invoiceUrl = null;

            // 🟢 TAMBAHAN: Jika kursus berbayar (harga > 0), minta Invoice Link ke Xendit
            if ($price > 0)
            {
                $apiInstance = new InvoiceApi();
                $createInvoiceRequest = new CreateInvoiceRequest([
                    'external_id' => $externalId,
                    'amount' => $price,
                    'description' => 'Pembelian Kursus: ' . $course->title,
                    'invoice_duration' => 86400, // Aktif selama 24 jam
                    'customer' => [
                        'given_names' => $user->name,
                        'email' => $user->email,
                    ],
                    'success_redirect_url' => 'http://localhost:8100/my-learning', // URL Redirect ke Ionic setelah bayar
                ]);

                $createInvoice = $apiInstance->createInvoice($createInvoiceRequest);
                $invoiceUrl = $createInvoice->getInvoiceUrl();
            }

            // Simpan data enrollment ke database
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $request->course_id,
                'price_bought' => $price,
                'status' => $price > 0 ? 'pending' : 'success', // Dinamis: Kalau berbayar 'pending', kalau gratis langsung 'success'
                'progress' => 0,
                'payment_url' => $invoiceUrl, // Menyimpan link bayar Xendit
                'external_id' => $price > 0 ? $externalId : null // Menyimpan token external ID
            ]);

            return response()->json([
                'success' => true,
                'message' => $price > 0 ? 'Invoice berhasil dibuat, silahkan lakukan pembayaran' : 'Berhasil membeli kursus gratis',
                'data' => $enrollment
            ]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke Payment Gateway: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $histori = Enrollment::with(['course', 'user.quizResults'])
            ->where('user_id', $userId)
            ->get()
            ->map(function ($item) use ($userId)
            {
                // Cari hasil kuis untuk kursus ini
                $quizResult = $item->user->quizResults
                    ->where('course_id', $item->course_id)
                    ->first();

                // Tambahkan data kuis ke dalam objek kursus
                $item->quiz_status = $quizResult ? $quizResult->status : null;
                $item->quiz_score = $quizResult ? $quizResult->score : null;

                return $item;
            });

        return response()->json([
            'success' => true,
            'message' => 'Histori pembelian kursus',
            'data' => $histori
        ]);
    }

    public function getCertificate($course_id, Request $request)
    {
        $user = $request->user();

        // 1. Cek progress 100%
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course_id)
            ->first();

        // 2. Cek apakah ada record di QuizResult dengan status 'passed'
        $hasPassedQuiz = QuizResult::where('user_id', $user->id)
            ->where('course_id', $course_id)
            ->where('status', 'passed')
            ->exists();

        if (!$enrollment || $enrollment->progress < 100 || !$hasPassedQuiz)
        {
            return response()->json([
                'success' => false,
                'message' => 'Selesaikan materi dan kuis untuk mengklaim sertifikat.'
            ], 403);
        }

        $course = Course::find($course_id);

        return response()->json([
            'success' => true,
            'message' => 'Sertifikat tersedia',
            'data' => [
                'nama_siswa' => $user->name,
                'kursus' => $course->title ?? 'Nama Kursus Tidak Ditemukan',
                'nomor_sertifikat' => 'CERT-' . strtoupper(bin2hex(random_bytes(4))),
                'tanggal_terbit' => now()->format('Y-m-d')
            ]
        ]);
    }

    public function getEnrolledStudents($course_id)
    {
        // Mengambil data enrollment beserta detail user-nya
        $students = Enrollment::with('user')
            ->where('course_id', $course_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    // 🟢 TAMBAHAN: Jalur Webhook penangkap konfirmasi pelunasan dari Xendit
    public function handleCallback(Request $request)
    {
        $externalId = $request->input('external_id');
        $status = $request->input('status');

        // Cari data enrollment di database yang cocok dengan external_id transaksi tersebut
        $enrollment = Enrollment::where('external_id', $externalId)->first();

        if (!$enrollment)
        {
            return response()->json([
                'success' => false,
                'message' => 'Data transaksi tidak ditemukan'
            ], 404);
        }

        // Jika Xendit mengirim status 'PAID', ubah status enrollment di aplikasi kita jadi 'success'
        if ($status === 'PAID')
        {
            $enrollment->update([
                'status' => 'success'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diperbarui menjadi success'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Callback diterima, tetapi status transaksi bukan PAID'
        ], 200);
    }
}
