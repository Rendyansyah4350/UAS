<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request; 
class StudentController extends Controller
{
    // app/Http/Controllers/Admin/StudentController.php

    public function index(Request $request)
    {
        $query = User::where('role', 'student');

        // Filter Search Nama
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter Status Pembelian
        if ($request->filter == 'bought') {
            $query->has('enrollments');
        } elseif ($request->filter == 'not_bought') {
            $query->doesntHave('enrollments');
        }

        $students = $query->latest()->paginate(10);
        return view('admin.students.index', compact('students'));
    }

    public function show($id)
    {
        // Mengambil student dengan relasi pembelian, course, dan progress konten
        $student = User::with(['enrollments.course.contents', 'progress'])->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    public function apiShow($id)
    {
    $student = User::with('enrollments.course')->findOrFail($id);
    
    return response()->json([
        'name' => $student->name,
        'courses' => $student->enrollments->map(fn($e) => $e->course->title),
        'progress' => $student->enrollments->map(fn($e) => $e->calculateProgress()),
    ]);
    }
}