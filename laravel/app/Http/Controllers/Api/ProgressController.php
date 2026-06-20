<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use App\Models\Content;
use App\Models\QuizResult;
use App\Models\Enrollment; // Ã°Å¸Å¸Â¢ Tambahkan model Enrollment untuk update otomatis
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function submitQuiz(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'score' => 'required|integer'
        ]);

        $user_id = $request->user()->id;
        $course_id = $request->course_id;

        // 1. Hitung total materi video di kursus ini
        $totalVideo = Content::where('course_id', $course_id)->count();

        // 2. Hitung video yang sudah diselesaikan user
        $videoWatched = Progress::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->whereNotNull('content_id')
            ->where('is_completed', true)
            ->count();

        // 3. VALIDASI: Jika video belum ditonton semua, TOLAK submit quiz
        if ($videoWatched < $totalVideo)
        {
            return response()->json([
                'success' => false,
                'message' => 'Selesaikan semua video terlebih dahulu sebelum mengerjakan quiz!'
            ], 403);
        }

        // 4. Jika lolos validasi, simpan progres quiz ke tabel progress (Jalur Ionic Ivan)
        $progress = Progress::updateOrCreate(
            [
                'user_id' => $user_id,
                'course_id' => $course_id,
                'content_id' => null
            ],
            [
                'is_completed' => true,
                'score' => $request->score
            ]
        );

        // ==========================================================================
        // ðŸŒŸ KUNCI FIX SAKTI: SIMPAN JURUS KEDUA KE TABEL QUIZ_RESULTS (Jalur Admin)
        // ==========================================================================
        QuizResult::updateOrCreate(
            [
                'user_id'   => $user_id,
                'course_id' => $course_id,
            ],
            [
                'score'     => $request->score,
            ]
        );

        $this->calculateAndSaveEnrollmentProgress($user_id, $course_id);

        return response()->json([
            'success' => true,
            'message' => 'Quiz berhasil disubmit dan disinkronkan ke Admin!',
            'data' => $progress
        ]);
    }

    public function markAsCompleted(Request $request)
    {
        $request->validate([
            'content_id' => 'required|exists:contents,id'
        ]);

        // Mencari data content untuk mendapatkan course_id asli
        $content = Content::findOrFail($request->content_id);
        $user_id = $request->user()->id;
        $course_id = $content->course_id;

        // Update atau create dengan menyertakan course_id agar sinkron dengan tabel progress
        $progress = Progress::updateOrCreate(
            [
                'user_id' => $user_id,
                'content_id' => $request->content_id
            ],
            [
                'course_id' => $course_id, // Perbaikan: Memastikan course_id terisi
                'is_completed' => true
            ]
        );

        // Ã°Å¸Å¸Â¢ OTOMATISASI: Hitung ulang dan update kolom progress di tabel enrollments
        $this->calculateAndSaveEnrollmentProgress($user_id, $course_id);

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil diselesaikan!',
            'data' => $progress
        ]);
    }

    public function getProgress($course_id, Request $request)
    {
        $user_id = $request->user()->id;

        // 1. Hitung total materi yang ada di kursus ini
        $totalContents = Content::where('course_id', $course_id)->count();

        // 2. Hitung berapa materi yang sudah diselesaikan oleh user ini
        $completedContents = Progress::where('user_id', $user_id)
            ->whereHas('content', function ($query) use ($course_id)
            {
                $query->where('course_id', $course_id);
            })
            ->where('is_completed', true)
            ->count();

        // 3. Hitung presentase (biar tidak error division by zero jika materi kosong)
        $percentage = $totalContents > 0 ? round(($completedContents / $totalContents) * 100) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'course_id' => $course_id,
                'total_materi' => $totalContents,
                'materi_selesai' => $completedContents,
                'persentase' => $percentage . '%'
            ]
        ]);
    }

    public function getStudentProgress($course_id, $user_id)
    {
        $total = Content::where('course_id', $course_id)->count();

        // Perbaikan: Menambahkan filter is_completed agar data akurat
        $completed = Progress::where('user_id', $user_id)
            ->where('is_completed', true)
            ->whereHas('content', function ($q) use ($course_id)
            {
                $q->where('course_id', $course_id);
            })->count();

        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user_id,
                'persentase_selesai' => $percentage . '%'
            ]
        ]);
    }

    /**
     * Ã°Å¸Å¸Â¢ FUNGSI OTOMATIS: Menghitung akumulasi progress materi + quiz,
     * lalu menyimpannya langsung ke kolom 'progress' milik tabel enrollments.
     */
/**
     * ðŸŸ¢ PERBAIKAN SAKTI: Menghitung akumulasi progress materi + quiz secara presisi,
     * memastikan tidak ada kebocoran data ganda antar course di database MySQL.
     */
    private function calculateAndSaveEnrollmentProgress($user_id, $course_id)
    {
        // 1. Hitung total item yang harus dikerjakan (Semua Konten + 1 Quiz)
        $totalMateri = Content::where('course_id', $course_id)->count();
        $totalItemWajib = $totalMateri + 1; // Ditambah 1 karena ada Quiz di akhir course

        // 2. KUNCI QUERY: Hitung materi selesai dengan memastikan content_id benar-owned oleh course ini
        $materiSelesai = Progress::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->whereNotNull('content_id')
            ->where('is_completed', true)
            ->whereHas('content', function ($query) use ($course_id) {
                $query->where('course_id', $course_id);
            })
            ->count();

        // 3. Cek apakah user sudah menyelesaikan Quiz (content_id bernilai null di tabel progress)
        $quizSelesai = Progress::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->whereNull('content_id')
            ->where('is_completed', true)
            ->count();

        // Amankan nilai quizSelesai maksimal 1 agar tidak duplikat jika user submit berkali-kali
        if ($quizSelesai > 1) {
            $quizSelesai = 1;
        }

        $totalSelesai = $materiSelesai + $quizSelesai;

        // 4. Hitung persentase murni angka bulat (0 - 100)
        $finalPercentage = $totalItemWajib > 0 ? (int) round(($totalSelesai / $totalItemWajib) * 100) : 0;

        // Paksa potong di angka maksimal 100% jika kalkulasi database meluber
        if ($finalPercentage > 100) {
            $finalPercentage = 100;
        }

        // 5. Eksekusi update langsung ke tabel enrollments database cPanel
        Enrollment::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->update([
                'progress' => $finalPercentage
            ]);
    }
}
