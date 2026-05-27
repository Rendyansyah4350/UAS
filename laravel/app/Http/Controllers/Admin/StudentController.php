<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    // app/Http/Controllers/Admin/StudentController.php

    public function index(Request $request)
    {
        $query = User::where('role', 'student');

        // Filter Search Nama
        if ($request->has('search'))
        {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter Status Pembelian
        if ($request->filter == 'bought')
        {
            $query->has('enrollments');
        }
        elseif ($request->filter == 'not_bought')
        {
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
        // 1. Validasi Inputan dengan Aturan ketat namun aman
        $request->validate([
            'name'     => 'required|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        // 2. Eksekusi penyimpanan menggunakan metode manual tanpa Mass Assignment untuk bypass proteksi
        $user = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = bcrypt($request->password); // Mengunci enkripsi untuk Ionic
        $user->role     = 'student';                  // Menyesuaikan dengan filter halaman Anda
        $user->save();                                // Paksa simpan langsung ke database

        // 3. Kembalikan ke halaman indeks mahasiswa dengan aman
        return redirect()->back()->with('success', 'Student berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $student = User::findOrFail($id);

        // Hapus student (Laravel akan menghapus data terkait jika kamu menggunakan cascade delete di DB)
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Data student berhasil dihapus.');
    }

    public function showQuiz(Course $course)
    {
        // Mengambil ID user yang sedang login
        $userId = Auth::id();

        // CEK DISINI: Apakah user_id dan course_id ada di tabel enrollments?
        $isPurchased = Enrollment::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->exists();

        // Jika tidak ada datanya (belum beli), kasih error 403 (Forbidden)
        if (!$isPurchased)
        {
            return abort(403, 'Kamu harus membeli kursus ini untuk mengakses Quiz.');
        }

        // Jika sudah beli, baru ambil data quiz-nya
        $quizzes = $course->quizzes;
        return view('student.quiz.show', compact('course', 'quizzes'));
    }
}
