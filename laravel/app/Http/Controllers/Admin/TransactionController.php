<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment; // Pastikan model ini yang merekam pembelian
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // 1. Data untuk ringkasan total per materi
        $courseReports = Course::withCount(['enrollments as total_sold' => function ($query) {
            $query->where('status', 'success');
        }])->get();

        // 2. Data untuk detail transaksi (Siapa, Apa, Kapan)
        $transactionDetails = Enrollment::with(['user', 'course'])
            ->where('status', 'success')
            ->latest()
            ->get();

        // 3. Hitung Grand Total uang masuk
        $grandTotal = $courseReports->sum(function ($course) {
            return $course->total_sold * $course->price;
        });

        return view('admin.pembelian.index', compact('courseReports', 'transactionDetails', 'grandTotal'));
    }
}
