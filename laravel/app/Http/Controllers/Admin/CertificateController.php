<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Support\Str;
use App\Models\Progress;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Content;
use App\Models\QuizResult;


class CertificateController extends Controller
{
    public function index()
    {
        // 1. Ambil semua kursus beserta konten materi dan user yang terdaftar via enrollments
        $courses = Course::with(['users', 'contents'])->get();

        $pendingCertificates = collect();

        foreach ($courses as $course)
        {
            // Hitung total materi yang ada di dalam kursus ini
            $totalMateri = $course->contents->count();

            if ($totalMateri === 0) continue;

            foreach ($course->users as $user)
            {
                // ==========================================================================
                // ðŸŸ¢ FIX SAKTI: Tembak Langsung ke Model Progress Berdasarkan ID User & Course
                // ==========================================================================
                $userProgress = Progress::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->get();

                // Hitung jumlah materi video yang berstatus selesai (content_id TIDAK NULL)
                $userCompleted = $userProgress
                    ->where('is_completed', true)
                    ->whereNotNull('content_id')
                    ->count();

                // Cek status kuis dari tabel progress (content_id NYA NULL)
                $isQuizDone = $userProgress
                    ->where('is_completed', true)
                    ->whereNull('content_id')
                    ->isNotEmpty();

                // Syarat mutlak kelulusan: Video ditonton semua DAN kuis sudah selesai dikerjakan
                if ($userCompleted >= $totalMateri && $isQuizDone)
                {
                    // Cek apakah sertifikat sudah pernah diterbitkan sebelumnya
                    $user->already_has_certificate = Certificate::where('user_id', $user->id)
                        ->where('course_id', $course->id)
                        ->exists();

                    $pendingCertificates->push((object)[
                        'user' => $user,
                        'course' => $course,
                        'user_id' => $user->id,
                        'course_id' => $course->id
                    ]);
                }
            }
        }

        // Memastikan satu student di satu kelas tidak muncul dobel di tabel admin
        $pendingCertificates = $pendingCertificates->unique(function ($item)
        {
            return $item->user_id . $item->course_id;
        });

        return view('admin.certificates.index', compact('pendingCertificates'));
    }

    public function show($id)
    {
        $course = Course::with(['users.progress' => function ($query) use ($id)
        {
            $query->where('course_id', $id);
        }])->findOrFail($id);

        return view('admin.quiz.show', compact('course'));
    }

    public function issue($userId, $courseId)
    {
        $existing = Certificate::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$existing)
        {
            Certificate::create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'certificate_number' => 'EV-' . date('Y') . '-' . strtoupper(Str::random(8)),
                'issued_at' => now(),
            ]);

            return back()->with('success', 'Sertifikat berhasil divalidasi!');
        }

        // Jika sudah ada, kasih tahu admin biar nggak bingung
        return back()->with('info', 'Sertifikat untuk student ini sudah pernah diterbitkan.');
    }

    public function preview($id)
    {
        $certificate = Certificate::with(['user', 'course'])->findOrFail($id);
        return view('admin.certificates.preview', compact('certificate'));
    }

    public function download($id)
    {
        $certificate = Certificate::with(['user', 'course'])->findOrFail($id);

        // Load view khusus untuk PDF
        $pdf = Pdf::loadView('admin.certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape') // Set kertas A4 Landscape
            ->setWarnings(false); // Mematikan warning agar proses lebih bersih

        // Nama file saat didownload
        return $pdf->download('Sertifikat-' . $certificate->user->name . '.pdf');
    }
}
