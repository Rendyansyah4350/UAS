<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Mengambil daftar wishlist user
     */
    public function index(Request $request)
    {
        // Mengambil wishlist berdasarkan user yang login saat ini
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->with('course') // 🌟 Wajib membawa relasi detail kursusnya
            ->latest()
            ->get();

        // 🌟 Wajib dibungkus di dalam key 'data' agar cocok dengan res.data milik Ivan
        return response()->json([
            'status' => 'success',
            'data' => $wishlist
        ], 200);
    }

    /**
     * Tambah atau hapus dari wishlist (Toggle)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $userId = $request->user()->id;
        $courseId = $request->course_id;

        $exists = Wishlist::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if ($exists) {
            $exists->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Kursus berhasil dihapus dari wishlist.',
                'is_wishlist' => false // 🌟 Dibaca Ionic untuk mematikan warna hati
            ], 200);
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'course_id' => $courseId
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Kursus berhasil ditambahkan ke wishlist.',
                'is_wishlist' => true // 🌟 Dibaca Ionic untuk menyalakan warna hati
            ], 201);
        }
    }
}
