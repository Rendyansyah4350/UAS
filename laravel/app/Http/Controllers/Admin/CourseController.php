<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Content; // Tambahkan ini
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input dari search bar (jika ada)
        $search = $request->input('search');

        // 2. Query ke database
        // "when" akan menjalankan filter query HANYA jika variable $search ada isinya
        $courses = Course::when($search, function ($query, $search)
        {
            return $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        })
            ->latest()
            ->get();

        // 3. Kirim ke view
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'price' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // 2. Olah upload gambar jika ada
        $imagePath = null;
        if ($request->hasFile('image'))
        {
            $imagePath = $request->file('image')->store('course_covers', 'public');
        }

        // 3. Simpan ke database
        Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            // 'user_id' => auth()->id(), // Nanti aktifkan ini setelah kita set role & login admin
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil ditambahkan!');
    }

    // --- TAMBAHAN BARU ---

    /**
     * Menampilkan detail kursus beserta daftar materinya
     */
    public function show($id)
    {
        // Mengambil data kursus beserta semua materinya (relationship contents)
        $course = Course::with('contents')->findOrFail($id);
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Menyimpan materi video baru ke dalam kursus
     */
    public function storeContent(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content_url' => 'required',
            'order' => 'required|integer'
        ]);

        Content::create([
            'course_id' => $courseId,
            'title' => $request->title,
            'content_url' => $request->content_url,
            'type' => 'video', // Sesuai enum di migration kamu
            'order' => $request->order,
        ]);

        return back()->with('success', 'Materi berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'price' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
        ];

        if ($request->hasFile('image'))
        {
            $data['image'] = $request->file('image')->store('course_covers', 'public');
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil dihapus!');
    }
}
