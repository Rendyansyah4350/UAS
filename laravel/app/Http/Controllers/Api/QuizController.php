<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index($course_id)
    {
        // Mengambil semua soal quiz untuk kursus tertentu
        $quizzes = \App\Models\Quiz::where('course_id', $course_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar soal quiz',
            'data' => $quizzes
        ]);
    }
    public function submit(Request $request, $course_id)
    {
        // 1. Ambil jawaban dari user melalui Body (format: array of objects)
        $userAnswers = $request->input('answers'); // Contoh: [{"quiz_id": 1, "answer": "a"}, ...]

        $correctCount = 0;

        // 2. Ambil semua soal yang asli dari database untuk kursus ini
        $quizzes = \App\Models\Quiz::where('course_id', $course_id)->get();
        $totalQuestions = $quizzes->count();

        if ($totalQuestions === 0) {
            return response()->json(['message' => 'Belum ada soal untuk kursus ini'], 404);
        }

        // 3. Cocokkan jawaban user dengan jawaban di database
        foreach ($userAnswers as $userAns) {
            $quiz = $quizzes->where('id', $userAns['quiz_id'])->first();

            if ($quiz && $quiz->answer === $userAns['answer']) {
                $correctCount++;
            }
        }

        // 4. Hitung nilai akhir (skala 0-100)
        $finalScore = ($correctCount / $totalQuestions) * 100;

        return response()->json([
            'success' => true,
            'message' => 'Quiz berhasil dikirim!',
            'data' => [
                'total_soal' => $totalQuestions,
                'jawaban_benar' => $correctCount,
                'nilai' => round($finalScore, 2)
            ]
        ]);
    }
}
