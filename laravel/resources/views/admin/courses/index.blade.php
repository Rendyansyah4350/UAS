@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Kursus</h2>
        <a href="{{ route('admin.courses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Tambah Kursus Baru
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-4 border-b">Gambar</th>
                    <th class="p-4 border-b">Judul Kursus</th>
                    <th class="p-4 border-b">Harga</th>
                    <th class="p-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 border-b">
                            <img src="{{ $course->image ? asset('storage/' . $course->image) : 'https://via.placeholder.com/100' }}"
                                class="w-16 h-10 object-cover rounded">
                        </td>
                        <td class="p-4 border-b font-semibold">
                            <a href="{{ route('admin.courses.show', $course->id) }}" class="text-blue-600 hover:underline">
                                {{ $course->title }}
                            </a>
                        </td>
                        <td class="p-4 border-b text-green-600 font-bold">Rp {{ number_format($course->price) }}</td>
                        <td class="p-4 border-b text-center">
                            <a href="{{ route('admin.courses.edit', $course->id) }}"
                                class="text-blue-500 hover:text-blue-700 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
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
