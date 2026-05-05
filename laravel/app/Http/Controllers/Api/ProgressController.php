<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use Illuminate\Http\Request;
use App\Models\Content;

class ProgressController extends Controller
{
    public function markAsCompleted(Request $request)
    {
        $request->validate([
            'content_id' => 'required|exists:contents,id'
        ]);

        $progress = Progress::updateOrCreate(
            ['user_id' => $request->user()->id, 'content_id' => $request->content_id],
            ['is_completed' => true]
        );

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
            ->whereHas('content', function ($query) use ($course_id) {
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
        $completed = Progress::where('user_id', $user_id)
            ->whereHas('content', function ($q) use ($course_id) {
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
}
