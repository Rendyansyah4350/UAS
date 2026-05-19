<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use App\Models\Content;
use App\Models\Enrollment; // 🟢 Tambahkan model Enrollment untuk update otomatis
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

        // 4. Jika lolos validasi, simpan progres quiz
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

        // 🟢 OTOMATISASI: Hitung ulang dan update kolom progress di tabel enrollments
        $this->calculateAndSaveEnrollmentProgress($user_id, $course_id);

        return response()->json([
            'success' => true,
            'message' => 'Quiz berhasil disubmit!',
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

        // 🟢 OTOMATISASI: Hitung ulang dan update kolom progress di tabel enrollments
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
     * 🟢 FUNGSI OTOMATIS: Menghitung akumulasi progress materi + quiz,
     * lalu menyimpannya langsung ke kolom 'progress' milik tabel enrollments.
     */
    private function calculateAndSaveEnrollmentProgress($user_id, $course_id)
    {
        // 1. Hitung total item yang harus dikerjakan (Semua Konten + 1 Quiz)
        $totalMateri = Content::where('course_id', $course_id)->count();
        $totalItemWajib = $totalMateri + 1; // Ditambah 1 karena ada Quiz di akhir course

        // 2. Hitung berapa video yang sudah beres ditonton user
        $materiSelesai = Progress::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->whereNotNull('content_id')
            ->where('is_completed', true)
            ->count();

        // 3. Cek apakah user sudah menyelesaikan Quiz (content_id bernilai null di tabel progress)
        $quizSelesai = Progress::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->whereNull('content_id')
            ->where('is_completed', true)
            ->count();

        $totalSelesai = $materiSelesai + $quizSelesai;

        // 4. Hitung persentase murni angka bulat (0 - 100)
        $finalPercentage = $totalItemWajib > 0 ? round(($totalSelesai / $totalItemWajib) * 100) : 0;

        // Batasi angka maksimal 100% biar tidak luber jika ada anomali data
        if ($finalPercentage > 100)
        {
            $finalPercentage = 100;
        }

        // 5. Eksekusi update langsung ke tabel enrollments database cPanel kamu
        Enrollment::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->update([
                'progress' => $finalPercentage
            ]);
    }
}
