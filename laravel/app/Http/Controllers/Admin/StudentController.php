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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'student', // Pastikan role diset otomatis
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Student berhasil ditambahkan!');
    }

    public function destroy($id)
    {
    $student = User::findOrFail($id);
    
    // Hapus student (Laravel akan menghapus data terkait jika kamu menggunakan cascade delete di DB)
    $student->delete();

    return redirect()->route('admin.students.index')->with('success', 'Data student berhasil dihapus.');
    }
}