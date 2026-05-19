<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\QuizResult;

class EnrollmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        // Cek apakah sudah pernah beli sebelumnya
        $alreadyEnrolled = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $request->course_id)
            ->first();

        if ($alreadyEnrolled)
        {
            return response()->json(['message' => 'Kamu sudah memiliki kursus ini'], 400);
        }


        $course = Course::find($request->course_id);

        $enrollment = Enrollment::create([
            'user_id' => $request->user()->id,
            'course_id' => $request->course_id,
            'price_bought' => $course->price ?? 0,
            'status' => 'success',
            'progress' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil membeli kursus',
            'data' => $enrollment
        ]);
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Tambahkan with(['course', 'user.quizResults'])
        // Kita panggil relasi quizResults milik user, lalu kita filter di collection nanti
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
}
