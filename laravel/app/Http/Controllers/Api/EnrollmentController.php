<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\QuizResult;
use Illuminate\Support\Facades\Log;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class EnrollmentController extends Controller
{
    public function store(Request $request)
    {
        try
        {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
            ]);

            $user = $request->user();
            if (!$user)
            {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Cek riwayat pembelian biar ga dobel
            $alreadyEnrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $request->course_id)
                ->first();

            if ($alreadyEnrolled)
            {
                if ($alreadyEnrolled->status === 'success')
                {
                    return response()->json(['message' => 'Kamu sudah memiliki kursus ini'], 400);
                }
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
            if (!$course)
            {
                return response()->json(['message' => 'Kursus tidak ditemukan'], 404);
            }

            $price = $course->price ?? 0;
            $externalId = 'eduvan-' . $user->id . '-' . $course->id . '-' . time();
            $invoiceUrl = null;

            // 🟢 TEMBAK RAW API KE XENDIT (ANTI RIBET, GAK BUTUH VENDOR COMPOSER)
            if ($price > 0)
            {
                $secretKey = env('XENDIT_SECRET_KEY');

                // Menggunakan HTTP Client bawaan Laravel untuk nembak endpoint v2 Xendit
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
                    'Content-Type' => 'application/json'
                ])->post('https://api.xendit.co/v2/invoices', [
                    'external_id' => $externalId,
                    'amount' => (int) $price,
                    'description' => 'Pembelian Kursus: ' . $course->title,
                    'invoice_duration' => 86400,
                    'customer' => [
                        'given_names' => $user->name,
                        'email' => $user->email,
                    ],
                    'success_redirect_url' => 'http://localhost:8100/my-learning',
                ]);

                if ($response->failed())
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal terhubung ke Xendit: ' . $response->body()
                    ], 400);
                }

                $responseData = $response->json();
                $invoiceUrl = $responseData['invoice_url'] ?? null;
            }

            // Simpan data pendaftaran ke Database lokal cPanel lu
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $request->course_id,
                'price_bought' => $price,
                'status' => $price > 0 ? 'pending' : 'success',
                'progress' => 0,
                'payment_url' => $invoiceUrl,
                'external_id' => $price > 0 ? $externalId : null
            ]);

            return response()->json([
                'success' => true,
                'message' => $price > 0 ? 'Invoice berhasil dibuat, silahkan lakukan pembayaran' : 'Berhasil membeli kursus gratis',
                'data' => $enrollment
            ]);
        }
        catch (\Throwable $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal: ' . $e->getMessage()
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
        $students = Enrollment::with('user')
            ->where('course_id', $course_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    // Jalur Webhook penangkap konfirmasi pelunasan dari Xendit
    public function handleCallback(Request $request)
    {
        // 🟢 Bikin log mandiri khusus callback biar ketahuan isi kiriman Xendit
        $logFile = storage_path('logs/callback_xendit.txt');
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Webhook Masuk: " . json_encode($request->all()) . "\n", FILE_APPEND);

        try
        {
            // 1. Ambil data penting dari payload Xendit
            $externalId = $request->input('external_id');
            $xenditStatus = strtoupper($request->input('status')); // PASTIKAN PAKAI HURUF BESAR

            // 2. Cari data pendaftaran berdasarkan external_id di DB lokal
            $enrollment = Enrollment::where('external_id', $externalId)->first();

            // 💡 TRICK UNTUK BUTTON "TES DAN SIMPAN" XENDIT:
            // Jika ini cuma data dummy simulasi, external_id pasti ga ketemu di DB.
            // Kita langsung respon sukses 200 ke Xendit biar tombolnya ijo tanpa ngerusak DB.
            if (!$enrollment)
            {
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Peringatan: external_id '{$externalId}' tidak ada di DB (Abaikan jika ini tombol Tes Xendit).\n", FILE_APPEND);
                return response()->json(['message' => 'Simulasi / Data tidak ditemukan, tapi callback diterima'], 200);
            }

            // 3. Jika data pendaftaran asli ketemu, cek statusnya
            if ($xenditStatus === 'PAID' || $xenditStatus === 'SETTLED')
            {
                $enrollment->update([
                    'status' => 'success' // Ubah pending jadi success di DB lu
                ]);
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] SUKSES: Enrollment ID {$enrollment->id} berhasil diubah ke 'success'.\n", FILE_APPEND);
            }
            elseif ($xenditStatus === 'EXPIRED')
            {
                $enrollment->update([
                    'status' => 'failed'
                ]);
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] INFO: Invoice {$externalId} expired.\n", FILE_APPEND);
            }

            return response()->json(['message' => 'Callback processed successfully'], 200);
        }
        catch (\Throwable $e)
        {
            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] ERROR CALLBACK: " . $e->getMessage() . "\n", FILE_APPEND);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
