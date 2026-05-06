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

        // Ambil student unik yang baru saja melakukan aktivitas/pembelian
        $recentStudents = User::where('role', 'student')
            ->has('enrollments') // Pastikan hanya student yang punya kursus
            ->with(['enrollments.course']) // Eager load untuk keperluan modal
            ->latest()
            ->take(5)
            ->get();
    return view('admin.dashboard', compact('stats', 'recentStudents'));
    }
}