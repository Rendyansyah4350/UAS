<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // 1. Mengambil ringkasan per materi
        // Kita hitung jumlah terjual DAN total uang dari kolom price_bought
        $courseReports = Course::withCount(['enrollments as total_sold' => function ($query) {
            $query->where('status', 'success');
        }])
            ->withSum(['enrollments as total_revenue' => function ($query) {
                $query->where('status', 'success');
            }], 'price_bought') // Mengambil total dari kolom price_bought di tabel enrollments
            ->get();

        // 2. Mengambil detail transaksi terbaru untuk tabel bawah
        $transactionDetails = Enrollment::with(['user', 'course'])
            ->where('status', 'success')
            ->latest()
            ->get();

        // 3. Menghitung Grand Total seluruh pendapatan
        $grandTotal = $transactionDetails->sum('price_bought');

        return view('admin.pembelian.index', compact('courseReports', 'transactionDetails', 'grandTotal'));
    }
}
