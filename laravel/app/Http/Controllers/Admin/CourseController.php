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
            'category' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Maksimal 2MB
        ]);

        // 2. Olah upload gambar murni ke Base64 (Tanpa Folder Storage Link)
        $base64Image = null;
        if ($request->hasFile('image'))
        {
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
            'image' => $base64Image,
        ]);

        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil ditambahkan dengan gambar Base64!');
    }

    /**
     * Menampilkan detail kursus beserta daftar materinya
     */
    public function show($id)
    {
        // Mengambil data kursus beserta semua materinya (relationship contents)
        $course = Course::with('contents')->findOrFail($id);
        return view('admin.courses.show', compact('course'));
    }

    // 🟢 TAMBAHAN BARU: Fungsi untuk menampung data form simpan materi lo
    public function storeContent(Request $request, $course_id)
    {
        // 1. Validasi input dari form admin lo
        $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'required|string', // Menyesuaikan name="video_url" di form lo
            'order' => 'required|integer'      // Menyesuaikan name="order" di form lo
        ]);

        // 2. Simpan data ke tabel 'contents' dan sesuaikan dengan struktur database baru
        Content::create([
            'course_id'   => $course_id,
            'title'       => $request->title,
            'content_url' => $request->video_url, // Kita petakan video_url ke kolom content_url DB lo
            'type'        => 'video',             // Auto set ke 'video' biar DB gak protes kosong/null
            'order'       => $request->order,     // Menyimpan urutan materi
        ]);

        // 3. Kembalikan ke halaman detail kursus dengan pesan sukses
        return redirect()->route('admin.courses.show', $course_id)
            ->with('success', 'Materi pembelajaran berhasil ditambahkan, bre!');
    }

    /**
     * Menghapus materi dari dalam kursus
     */
    public function destroyContent($content_id)
    {
        // 1. Cari data materi berdasarkan ID-nya
        $content = Content::findOrFail($content_id);

        // Simpan course_id-nya dulu sebelum dihapus buat rute redirect balik
        $course_id = $content->course_id;

        // 2. Eksekusi hapus dari database
        $content->delete();

        // 3. Kembalikan ke halaman detail kursus dengan pesan sukses
        return redirect()->route('admin.courses.show', $course_id)
            ->with('success', 'Materi pembelajaran berhasil dihapus, bre!');
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240' // 10MB
        ]);

        $data = [
            'title' => $request->title,
            'category' => $request->category, // Pastikan nama kolom di DB lo emang 'category' ya mbut, bukan 'category_id'
            'description' => $request->description,
            'price' => $request->price,
        ];

        // 🟢 DISAMAKAN: Olah upload gambar murni ke Base64 saat update biar gak crash di cPanel
        if ($request->hasFile('image'))
        {
            $image = $request->file('image');
            $imageData = base64_encode(file_get_contents($image));
            $imageMime = $image->getClientMimeType();

            // Satukan jadi format data URL Base64
            $data['image'] = 'data:' . $imageMime . ';base64,' . $imageData;
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil diperbarui dengan gambar Base64!');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil dihapus!');
    }
}
