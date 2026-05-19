<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;

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

        if ($alreadyEnrolled) {
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
        // Mengambil semua data enrollment milik user yang sedang login
        // Kita gunakan 'with' agar data detail kursusnya juga ikut terbawa
        $histori = Enrollment::with('course')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Histori pembelian kursus',
            'data' => $histori
        ]);
    }

    public function getCertificate($course_id, Request $request)
    {
        $user = $request->user();

        // 🟢 DIOPTIMALKAN: Cari data enrollment dan langsung cek kolom progress-nya
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course_id)
            ->first();

        // Jika data beli gak ada atau progress belum 100%, blokir klaim sertifikat
        if (!$enrollment || $enrollment->progress < 100)
        {
            return response()->json([
                'success' => false,
                'message' => 'Selesaikan semua materi terlebih dahulu untuk mendapatkan sertifikat.'
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
