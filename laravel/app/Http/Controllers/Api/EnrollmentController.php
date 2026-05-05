<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
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

        $enrollment = Enrollment::create([
            'user_id' => $request->user()->id,
            'course_id' => $request->course_id,
            'status' => 'success'
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

        // Cek apakah user sudah menyelesaikan kursus (logic progress 100%)
        $totalContents = \App\Models\Content::where('course_id', $course_id)->count();
        $completedContents = \App\Models\Progress::where('user_id', $user->id)
            ->whereHas('content', function ($query) use ($course_id) {
                $query->where('course_id', $course_id);
            })
            ->count();

        if ($totalContents > 0 && $completedContents < $totalContents) {
            return response()->json([
                'success' => false,
                'message' => 'Selesaikan semua materi terlebih dahulu untuk klaim sertifikat.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sertifikat tersedia',
            'data' => [
                'nama_siswa' => $user->name,
                'kursus' => \App\Models\Course::find($course_id)->title,
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
