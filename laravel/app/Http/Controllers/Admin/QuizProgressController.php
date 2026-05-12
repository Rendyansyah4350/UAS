<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Progress;

class QuizProgressController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // 1. Ambil data course dengan filter search & hitung progres asli
        $courses = Course::withCount(['users', 'progress as completed_count' => function ($query)
        {
            // Menghitung yang sudah menyelesaikan Quiz (progres final)
            $query->where('is_completed', true)->whereNull('content_id');
        }])
            ->when($search, function ($query, $search)
            {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->get();

        // 2. Statistik Ringkasan Asli
        // Menghitung total student unik yang sudah menyelesaikan minimal satu course (Quiz Done)
        $totalCompleted = Progress::where('is_completed', true)
            ->whereNull('content_id')
            ->distinct('user_id')
            ->count('user_id');

        // Rata-rata nilai dari semua quiz yang masuk
        // (Opsional: Tetap 85 kalau lo belum mau tarik data dari perhitungan nilai submit)
        $averageScore = 85;

        return view('admin.quiz.index', compact('courses', 'totalCompleted', 'averageScore'));
    }

    public function show($id)
    {
        // Mengambil course beserta student yang terdaftar di dalamnya
        $course = Course::with(['users.progress'])->findOrFail($id);

        return view('admin.quiz.show', compact('course'));
    }

    public function manage(Course $course)
    {
        // Mengambil semua quiz yang miliknya course ini
        $quizzes = $course->quizzes;
        return view('admin.quiz.manage', compact('course', 'quizzes'));
    }

    public function storeQuiz(Request $request, Course $course)
    {
        $request->validate([
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'answer'   => 'required|in:a,b,c,d',
        ]);

        // Simpan quiz lewat relasi course agar course_id terisi otomatis
        $course->quizzes()->create($request->all());

        return redirect()->back()->with('success', 'Soal berhasil ditambahkan!');
    }

    public function destroyQuiz(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->back()->with('success', 'Soal berhasil dihapus!');
    }
}
