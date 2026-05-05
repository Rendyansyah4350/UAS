<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment; // Atau Purchase, sesuaikan nama model transaksi kamu
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_courses' => Course::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_purchases' => Enrollment::count(),
        ];

        // Ambil 5 pembelian terbaru untuk ditampilkan di tabel dashboard
        $recentEnrollments = Enrollment::with(['user', 'course'])
                            ->latest()
                            ->take(5)
                            ->get();

        return view('admin.dashboard', compact('stats', 'recentEnrollments'));
    }
}