@extends('layouts.admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">{{ $course->title }}</h2>
        <p class="text-gray-600">Kelola materi dan urutan pembelajaran.</p>
    </div>
    <a href="{{ route('admin.courses.index') }}" class="text-gray-500 hover:text-gray-700"> Kembali</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Tambah Materi -->
    <div class="bg-white p-6 rounded-lg shadow-md h-fit">
        <h3 class="font-bold mb-4">Tambah Materi Baru</h3>
        <form action="{{ route('admin.courses.storeContent', $course->id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Judul Materi</label>
                    <input type="text" name="title" required class="w-full border rounded p-2 bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-medium">URL Video (YouTube/Vimeo)</label>
                    <input type="text" name="content_url" required class="w-full border rounded p-2 bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-medium">Urutan (Order)</label>
                    <input type="number" name="order" value="1" class="w-full border rounded p-2 bg-gray-50">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded shadow hover:bg-blue-700">Simpan Materi</button>
            </div>
        </form>
    </div>

    <!-- Daftar Materi -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-4 border-b w-16">Order</th>
                    <th class="p-4 border-b">Judul Materi</th>
                    <th class="p-4 border-b">Tipe</th>
                    <th class="p-4 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($course->contents as $content)
                <tr>
                    <td class="p-4 border-b text-center">{{ $content->order }}</td>
                    <td class="p-4 border-b">{{ $content->title }}</td>
                    <td class="p-4 border-b"><span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">VIDEO</span></td>
                    <td class="p-4 border-b">
                        <button class="text-red-500"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center text-gray-500">Belum ada materi untuk kursus ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection