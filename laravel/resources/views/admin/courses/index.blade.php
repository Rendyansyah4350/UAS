@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Kursus</h2>
        <a href="{{ route('admin.courses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Tambah Kursus Baru
        </a>
    </div>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.courses.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari kursus atau kategori..."
                    class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm w-64">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-4 border-b">No</th>
                    <th class="p-4 border-b">Gambar</th>
                    <th class="p-4 border-b">Judul Kursus</th>
                    <th class="p-4 border-b">Deskripsi</th>
                    <th class="p-4 border-b">Harga</th>
                    <th class="p-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 border-b text-center">{{ $loop->iteration }}</td>
                        <td class="p-4 border-b">
                            <img src="{{ $course->image ? asset('storage/' . $course->image) : 'https://via.placeholder.com/100' }}"
                                class="w-16 h-10 object-cover rounded">
                        </td>
                        <td class="p-4 border-b font-semibold">
                            <a href="{{ route('admin.courses.show', $course->id) }}" class="text-blue-600 hover:underline">
                                {{ $course->title }}
                            </a>
                        </td>
                        <td class="p-4 border-b">{{ $course->description }}</td>
                        <td class="p-4 border-b text-green-600 font-bold">Rp {{ number_format($course->price) }}</td>
                        <td class="p-4 border-b text-center">
                            <a href="{{ route('admin.courses.edit', $course->id) }}"
                                class="text-blue-500 hover:text-blue-700 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                                class="inline-block"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini? Semua materi di dalamnya akan ikut terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-10 text-center text-gray-500">
                            Belum ada kursus. Klik tombol "Tambah Kursus Baru" untuk memulai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
