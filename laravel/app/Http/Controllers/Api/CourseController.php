<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;

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
        // 1. Cari data kursus berdasarkan ID
        $course = Course::find($id);

        // 2. Jika kursus tidak ditemukan, kirim respon error 404
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan'
            ], 404);
        }

        // 3. Jika ditemukan, kirim datanya
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Kursus',
            'data'    => $course
        ]);
    }
    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5'
        ]);

        $course = Course::findOrFail($id);

        // Logic sederhana: Update rating langsung (atau bisa pakai rata-rata)
        $course->update([
            'rating' => $request->rating
        ]);

        return response()->json(['message' => 'Terima kasih atas ratingnya!']);
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
