<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\QuizResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EnrollmentController extends Controller
{
    public function store(Request $request)
    {
        try
        {
            // 1. VALIDASI: Wajib course_id dan file proof_of_payment (harus gambar)
            $request->validate([
                'course_id'        => 'required|exists:courses,id',
                'proof_of_payment' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $user = $request->user();
            if (!$user)
            {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // 2. CEK RIWAYAT PEMBELIAN: Menyesuaikan dengan status baru 'Checking Admin'
            $alreadyEnrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $request->course_id)
                ->first();

            if ($alreadyEnrolled)
            {
                if ($alreadyEnrolled->status === 'success')
                {
                    return response()->json(['message' => 'Kamu sudah memiliki kursus ini'], 400);
                }
                if ($alreadyEnrolled->status === 'Checking Admin')
                {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pembayaran kamu sedang diperiksa oleh Admin. Mohon tunggu konfirmasi.',
                        'data' => $alreadyEnrolled
                    ], 400);
                }
            }

            $course = Course::find($request->course_id);
            if (!$course)
            {
                return response()->json(['message' => 'Kursus tidak ditemukan'], 404);
            }

            $price = $course->price ?? 0;
            $fileName = null;

            // 3. PROSES SIMPAN FILE BUKTI PEMBAYARAN KE STORAGE
            if ($request->hasFile('proof_of_payment'))
            {
                $file = $request->file('proof_of_payment');

                // Penamaan unik file bukti transfer
                $fileName = 'bukti_' . $user->id . '_' . $course->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Disimpan ke direktori: storage/app/public/payment_proofs/
                $file->storeAs('public/payment_proofs', $fileName);
            }

            // 4. SIMPAN DATA TRANSAKSI BARU (Status diset 'Checking Admin')
            $enrollment = Enrollment::create([
                'user_id'          => $user->id,
                'course_id'        => $request->course_id,
                'price_bought'     => $price,
                'status'           => 'Checking Admin',
                'progress'         => 0,
                'proof_of_payment' => $fileName,
                'payment_url'      => null,
                'external_id'      => null
            ]);

            // 5. TRIGGER NOTIFIKASI EMAIL MENGGUNAKAN PHPMAILER KE ADMIN
            $this->sendEmailNotificationToAdmin($user, $course);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil dikirim. Menunggu konfirmasi status oleh Admin.',
                'data'    => $enrollment
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

    /**
     * Fungsi Helper PHPMailer untuk mengirim notifikasi ke Email Admin
     */
    private function sendEmailNotificationToAdmin($user, $course)
    {
        $mail = new PHPMailer(true);

        try
        {
            // Konfigurasi SMTP Server (Menarik nilai dari file .env)
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $mail->Port       = env('MAIL_PORT', 587);

            // Pengirim & Alamat Email Admin Penerima Notifikasi
            $mail->setFrom(env('MAIL_FROM_ADDRESS', 'noreply@eduvan.rehalivan.com'), 'EduVan Platform');
            $mail->addAddress('admin@eduvan.rehalivan.com'); // Silakan sesuaikan dengan email admin utama kamu

            // Konten Pesan Email Notifikasi (Format HTML)
            $mail->isHTML(true);
            $mail->Subject = 'NOTIFIKASI PEMBAYARAN MANUAL BARU - EduVan';

            $mail->Body    = "
                <h3>Halo Admin EduVan,</h3>
                <p>Ada pembeli baru saja mengirimkan form dan mengunggah bukti pembayaran manual. Berikut detail datanya:</p>
                <hr>
                <ul>
                    <li><strong>Nama Pembeli:</strong> {$user->name}</li>
                    <li><strong>Email Pembeli:</strong> {$user->email}</li>
                    <li><strong>Kursus yang Dibeli:</strong> {$course->title}</li>
                    <li><strong>Harga Kursus:</strong> Rp " . number_format($course->price, 0, ',', '.') . "</li>
                    <li><strong>Status Transaksi:</strong> <span style='color: #e67e22; font-weight: bold;'>Checking Admin</span></li>
                </ul>
                <hr>
                <p>Silakan segera login ke halaman <strong>Dashboard Admin Web</strong> untuk memeriksa validitas berkas foto bukti transfer dan melakukan konfirmasi status (Success / Fail).</p>
                <br>
                <p>Salam,<br>Sistem Otomatis EduVan</p>
            ";

            $mail->send();
            Log::info("Email notifikasi pembayaran manual Kursus ID {$course->id} berhasil dikirim ke Admin.");
        }
        catch (Exception $e)
        {
            // Dicatat ke log jika gagal agar alur response transaksi user di aplikasi tidak ikut terputus
            Log::error("Gagal memicu email notifikasi via PHPMailer. Error: {$mail->ErrorInfo}");
        }
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $histori = Enrollment::with(['course'])
            ->where('user_id', $userId)
            ->get()
            ->map(function ($item) use ($userId)
            {
                $totalMateri = \Illuminate\Support\Facades\DB::table('contents')
                    ->where('course_id', $item->course_id)
                    ->count();

                $materiSelesai = \Illuminate\Support\Facades\DB::table('progress')
                    ->where('user_id', $userId)
                    ->where('course_id', $item->course_id)
                    ->whereNotNull('content_id')
                    ->where('is_completed', 1)
                    ->count();

                $isQuizSelesai = \Illuminate\Support\Facades\DB::table('progress')
                    ->where('user_id', $userId)
                    ->where('course_id', $item->course_id)
                    ->whereNull('content_id')
                    ->where('is_completed', 1)
                    ->exists();

                $item->is_quiz_unlocked = ($totalMateri > 0 && $materiSelesai === $totalMateri);

                $totalItemWajib = $totalMateri + 1;
                $totalItemSelesai = $materiSelesai + ($isQuizSelesai ? 1 : 0);

                if ($totalItemWajib > 0)
                {
                    $item->progress = (int) round(($totalItemSelesai / $totalItemWajib) * 100);
                }
                else
                {
                    $item->progress = 0;
                }

                if ($item->progress > 100)
                {
                    $item->progress = 100;
                }

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

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course_id)
            ->first();

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

    // Fungsi callback Xendit dimatikan fungsinya karena beralih ke alur verifikasi manual admin
    public function handleCallback(Request $request)
    {
        return response()->json(['message' => 'Xendit integration is disabled'], 200);
    }
}
