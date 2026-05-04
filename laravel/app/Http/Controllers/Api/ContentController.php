<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index($course_id, Request $request)
    {
        // Cek apakah user yang login sudah membeli kursus ini
        $isEnrolled = \App\Models\Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $course_id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu harus membeli kursus ini terlebih dahulu untuk melihat materi.'
            ], 403);
        }

        $contents = \App\Models\Content::where('course_id', $course_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar materi kursus',
            'data' => $contents
        ]);
    }
}
