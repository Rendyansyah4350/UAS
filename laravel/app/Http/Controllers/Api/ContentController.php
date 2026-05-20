<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // 🟢 TAMBAHAN: Biar VS Code Intelephense adem gak merah lagi

class ContentController extends Controller
{
    public function index($course_id, Request $request)
    {
        // 1. Cek apakah user membawa token login (Sanctum)
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

        // 2. Ambil semua materi asli dari database berdasarkan id kursus
        $contents = \App\Models\Content::where('course_id', $course_id)->get();

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
                    'content_url' => null, // Gembok aman
                    'type' => $materi->type,
                    'is_completed' => 0, // Belum beli otomatis 0
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

        // 4. JIKA SUDAH LUNAS: Inject status centang hijau 'is_completed' asli dari DB progress
        $userId = $user->id;
        $formattedContents = $contents->map(function ($materi) use ($userId, $course_id)
        {
            // Cek ke tabel progress apakah user sudah menyelesaikan materi ini
            $hasProgress = DB::table('progress')
                ->where('user_id', $userId)
                ->where('course_id', $course_id)
                ->where('content_id', $materi->id)
                ->where('is_completed', 1)
                ->exists();

            return [
                'id' => $materi->id,
                'course_id' => $materi->course_id,
                'title' => $materi->title,
                'duration' => $materi->duration ?? '15',
                'content_url' => $materi->content_url, // Akses penuh video youtube
                'type' => $materi->type,
                'is_completed' => $hasProgress ? 1 : 0, // 🟢 SAKTI: Balikin nilai 1 atau 0 ke Angular
                'created_at' => $materi->created_at,
                'updated_at' => $materi->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar materi kursus (Akses Penuh)',
            'data' => $formattedContents
        ]);
    }

    // 🟢 FITUR BARU: Dipicu saat student klik tombol "TANDAI SELESAI" di Angular
    public function markComplete(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'content_id' => 'required|exists:contents,id',
        ]);

        $user = auth('sanctum')->user();
        if (!$user)
        {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // 1. Simpan atau update log selesai ke tabel progress mbut
        DB::table('progress')->updateOrInsert(
            [
                'user_id' => $user->id,
                'course_id' => $request->course_id,
                'content_id' => $request->content_id,
            ],
            [
                'is_completed' => 1, // Set selesai
                'updated_at' => now(),
                'created_at' => now(), // Dipakai jika insert data baru
            ]
        );

        // 2. HITUNG PROGRESS TOTAL: Ambil total semua video vs yang udah selesai di-centang
        $totalMateri = \App\Models\Content::where('course_id', $request->course_id)->count();

        $materiSelesai = DB::table('progress')
            ->where('user_id', $user->id)
            ->where('course_id', $request->course_id)
            ->where('is_completed', 1)
            ->count();

        // Rumus matematika persentase progress EduVan
        $persentase = $totalMateri > 0 ? round(($materiSelesai / $totalMateri) * 100) : 0;

        // 3. UPDATE TABEL ENROLLMENTS: Biar progress bar di halaman "Kursus Saya" ikut nambah naik
        \App\Models\Enrollment::where('user_id', $user->id)
            ->where('course_id', $request->course_id)
            ->update([
                'progress' => $persentase
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil diselesaikan!',
            'data' => [
                'current_materi_progress' => 1,
                'total_course_progress' => $persentase
            ]
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
