<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Content;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input dari search bar (jika ada)
        $search = $request->input('search');

        // 2. Query ke database
        // "when" akan menjalankan filter query HANYA jika variable $search ada isinya
        $courses = Course::when($search, function ($query, $search) {
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
            'category' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Maksimal 2MB
        ]);

        // 2. Olah upload gambar murni ke Base64 (Tanpa Folder Storage Link)
        $base64Image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageData = base64_encode(file_get_contents($image));
            $imageMime = $image->getClientMimeType();

            // Satukan jadi format data URL yang bisa langsung dibaca tag <img> HTML
            $base64Image = 'data:' . $imageMime . ';base64,' . $imageData;
        }

        // 3. Simpan ke database
        Course::create([
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,
            'rating' => 0,
            'image' => $base64Image, // <--- Menyimpan string teks panjang
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil ditambahkan dengan gambar Base64!');
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
            'category' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240' // <--- Ubah dari 2048 ke 10240 (10MB)
        ]);


        $data = [
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,

        ];

        if ($request->hasFile('image')) {
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
