<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index($course_id, Request $request)
    {
        // 1. Ambil semua materi asli dari database berdasarkan id kursus
        $contents = \App\Models\Content::where('course_id', $course_id)->get();

        // 2. Cek apakah user bawa token login (Sanctum)
        $user = auth('sanctum')->user();

        $isLunas = false;

        if ($user)
        {
            // Cek data enrollment milik user ini
            $enrollment = \App\Models\Enrollment::where('user_id', $user->id)
                ->where('course_id', $course_id)
                ->first();

            // Jika ada riwayat transaksi dan statusnya lunas (success)
            if ($enrollment && $enrollment->status === 'success')
            {
                $isLunas = true;
            }
        }

        // 3. JIKA BELUM LUNAS / BELUM BELI: Sembunyikan link video sensitifnya, tapi kirim judulnya buat di-gembok
        if (!$isLunas)
        {
            $formattedContents = $contents->map(function ($materi)
            {
                return [
                    'id' => $materi->id,
                    'course_id' => $materi->course_id,
                    'title' => $materi->title,
                    'duration' => $materi->duration ?? '15',
                    'content_url' => null,
                    'type' => $materi->type,
                    'created_at' => $materi->created_at,
                    'updated_at' => $materi->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Daftar materi (Terkunci, silakan lakukan pembayaran)',
                'data' => $formattedContents
            ]);
        }

        // 4. JIKA SUDAH LUNAS: Kirim semua data utuh beserta link videonya (`content_url`)
        return response()->json([
            'success' => true,
            'message' => 'Daftar materi kursus (Akses Penuh)',
            'data' => $contents
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string',
            'content_url' => 'required|url',
            'type' => 'required|in:video,pdf,text'
        ]);

        $content = \App\Models\Content::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil ditambahkan!',
            'data' => $content
        ], 201);
    }
}
