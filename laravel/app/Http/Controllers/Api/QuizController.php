<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Progress;
use Illuminate\Support\Facades\Auth;
use App\Models\Content;

class QuizController extends Controller
{
    public function index($course_id)
    {
        $userId = Auth::id();

        // 1. Hitung total materi (content) yang ada di course ini
        $totalContent = Content::where('course_id', $course_id)->count();

        // 2. Hitung berapa materi yang SUDAH diselesaikan user ini
        $completedContent = Progress::where('user_id', $userId)
            ->whereIn('content_id', Content::where('course_id', $course_id)->pluck('id'))
            ->where('is_completed', true)
            ->count();

        // 3. LOGIKA GERBANG: Cek apakah jumlah yang ditonton sudah sama dengan total materi
        if ($completedContent < $totalContent)
        {
            return response()->json([
                'success' => false,
                'message' => 'Eitss! Selesaikan semua video materi dulu baru bisa buka Quiz.',
                'debug' => [
                    'total_materi' => $totalContent,
                    'materi_selesai' => $completedContent
                ]
            ], 403); // Kita kasih status 403 (Forbidden) biar Ionic tau ini akses ditolak
        }

        // 4. Kalau lolos (sudah nonton semua), baru ambil soalnya
        $quizzes = Quiz::where('course_id', $course_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar soal quiz terbuka!',
            'data' => $quizzes
        ]);
    }
    public function submit(Request $request, $course_id)
    {
        $userId = Auth::id();
        $userAnswers = $request->input('answers');
        $correctCount = 0;
        $quizzes = Quiz::where('course_id', $course_id)->get();
        $totalQuestions = $quizzes->count();

        if ($totalQuestions === 0)
        {
            return response()->json(['message' => 'Belum ada soal untuk kursus ini'], 404);
        }
        // Hitung jawaban (tetap dihitung buat ditampilin di Ionic, tapi nggak nentuin lulus/nggak)
        foreach ($userAnswers as $userAns)
        {
            $quiz = $quizzes->where('id', $userAns['quiz_id'])->first();
            if ($quiz && $quiz->answer === $userAns['answer'])
            {
                $correctCount++;
            }
        }

        // SIMPAN PROGRES
        Progress::updateOrCreate(
            [
                'user_id'    => $userId,
                'course_id'  => $course_id,
                'content_id' => null, // PAKSA NULL: Menandakan ini progres QUIZ, bukan VIDEO
            ],
            [
                'is_completed' => true
            ]
        );

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
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'answer' => 'required|in:a,b,c,d'
        ]);

        $quiz = Quiz::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Soal quiz berhasil ditambahkan!',
            'data' => $quiz
        ], 201);
    }
}
