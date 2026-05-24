<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return response()->json([
            'success' => true,
            'data'    => $courses
        ]);
    }
    public function show($id)
    {
        // ðŸŸ¢ 1. Cari data kursus SEKALIGUS hitung jumlah relasi user yang mendaftar (withCount)
        $course = Course::withCount('users')->find($id);

        // 2. Jika kursus tidak ditemukan, kirim respon error 404
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan lek!'
            ], 404);
        }

        // 3. Jika ditemukan, kirim datanya (sekarang di dalam $course sudah ada field 'users_count')
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Kursus Berhasil Dimuat',
            'data'    => $course
        ]);
    }
    public function rate(Request $request, $id)
    {
        // 1. Validasi input bintang wajib angka 1 sampai 5
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5'
        ]);

        // 2. Pastikan kursusnya emang ada di database cPanel
        $course = Course::findOrFail($id);
        $user = $request->user(); // Ambil data student yang sedang login

        // 3. Cek apakah student ini beneran udah beli kelasnya (Biar ga di-spam orang luar)
        $hasEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $id)
            ->where('status', 'success')
            ->exists();

        if (!$hasEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Lu belum beli atau melunasi kursus ini mbut, ga bisa ngasih rating!'
            ], 403);
        }

        // 4. Gunakan DB table builder bawaan Laravel untuk menghemat space tanpa bikin Model baru
        // Simpan atau update bintang dari student ini di tabel transaksional 'course_ratings'
        DB::table('course_ratings')->updateOrInsert(
            ['user_id' => $user->id, 'course_id' => $id], // Kondisi cek unik
            [
                'rating' => $request->rating,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // 5. HITUNG RATA-RATA: Tarik semua total rating masuk rumus AVG murni MySQL
        $rataRataBintang = DB::table('course_ratings')
            ->where('course_id', $id)
            ->avg('rating');

        // 6. UPDATE LIVE: Masukkan nilai pecahan desimal ke kolom rating di tabel courses lu mbut
        $course->update([
            'rating' => round($rataRataBintang, 1) // otomatis dibulatkan 1 angka di belakang koma (ex: 4.8)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas rating bintang ' . $request->rating . ' yang Anda berikan.',
            'current_average' => $course->rating
        ]);
    }
    public function dashboard()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_courses' => Course::count(),
                'total_students' => User::where('role', 'student')->count(), // Pastikan ada kolom role
                'total_revenue' => Enrollment::join('courses', 'enrollments.course_id', '=', 'courses.id')->sum('courses.price')
            ]
        ]);
    }
}
