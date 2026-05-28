<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    // 1. API mengambil daftar notif + menghitung jumlah unread khusus student yang login
    public function getNotifUser(Request $request)
    {
        try
        {
            $user = $request->user(); // Mendeteksi student yang sedang login di Ionic

            // Ambil semua notifikasi milik user ini
            $notifications = $user->notifications;

            // Hitung jumlah notifikasi yang BELUM DIBACA (read_at masih NULL)
            $unreadCount = $user->unreadNotifications()->count();

            return response()->json([
                'status'       => 'success',
                'unread_count' => $unreadCount, // Ini untuk angka merah di lonceng Ionic
                'data'         => $notifications
            ], 200);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    // 2. API mengubah status menjadi SUDAH DIBACA pas diklik di Ionic
    public function markAsRead(Request $request, $id)
    {
        try
        {
            // Cari notif berdasarkan ID milik user yang login
            $notification = $request->user()->notifications()->where('id', $id)->first();

            if ($notification)
            {
                $notification->markAsRead(); // Otomatis mengisi kolom read_at di database
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Notifikasi berhasil dibaca'
            ], 200);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }
}
