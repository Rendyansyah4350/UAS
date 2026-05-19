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
        // 1. Ambil semua kursus beserta student-nya, progress, DAN quizResults sekaligus (Eager Loading)
        $courses = Course::with(['users.progress', 'users.quizResults', 'contents'])->get();

        $pendingCertificates = collect();

        foreach ($courses as $course)
        {
            $totalMateri = $course->contents->count();

            if ($totalMateri === 0) continue;

            foreach ($course->users as $user)
            {
                // Hitung materi yang selesai
                $userCompleted = $user->progress
                    ->where('course_id', $course->id)
                    ->where('is_completed', true)
                    ->whereNotNull('content_id')
                    ->count();

                // 2. TAMBAHAN: Cek apakah user sudah mengerjakan kuis (status passed/failed gak masalah)
                $isQuizDone = $user->quizResults
                    ->where('course_id', $course->id)
                    ->isNotEmpty();

                // Syarat: Materi Selesai DAN Kuis Sudah Dikerjakan
                if ($userCompleted >= $totalMateri && $isQuizDone)
                {
                    // Cek apakah sertifikat sudah ada
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

        // Menggunakan unique untuk memastikan satu student-satu kursus tidak muncul dobel
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
