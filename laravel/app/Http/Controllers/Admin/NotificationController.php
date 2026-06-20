<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UmumNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    // 1. Menampilkan halaman form input notifikasi
    public function index()
    {
        return view('admin.notifications.index');
    }

    // 2. Memproses tombol kirim blast dari form web admin
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:150',
            'message' => 'required|string',
            'type'    => 'required|string|in:info,pengumuman,promo,alert,success'
        ]);

        try {
            // Ambil semua user/student di localhost
            $users = User::all();

            if ($users->isEmpty()) {
                return redirect()->back()->with('error', 'Gagal blast, belum ada data student di database.');
            }

            // Kirim massal ke tabel 'notifications'
            Notification::send($users, new UmumNotification(
                $request->title,
                $request->message,
                $request->type
            ));

            return redirect()->back()->with('success', 'Notifikasi berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // 🌐 FUNGSI BARU: Khusus melayani request dari Ionic Ivan
    public function getNotifUser(Request $request)
    {
        try {
            // Mengambil daftar notifikasi khusus milik user yang sedang login di Ionic
            $notifications = $request->user()->notifications;

            return response()->json([
                'status' => 'success',
                'data'   => $notifications
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
}
