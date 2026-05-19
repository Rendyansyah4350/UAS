@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $course->title }}</h2>
            <p class="text-gray-600">Kelola materi dan urutan pembelajaran.</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="text-gray-500 hover:text-gray-700"> Kembali</a>
    </div>

    {{-- 🟢 TAMBAHAN: Alert Box untuk menampilkan pesan eror validasi jika simpan materi ditolak --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm">
            <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle"></i> Terjadi kesalahan input materi:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 🟢 TAMBAHAN: Alert Box Sukses jika materi berhasil masuk --}}
    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-bold">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="hidden md:grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md h-fit">
            <h3 class="font-bold mb-4">Tambah Materi Baru</h3>
            <form action="{{ route('admin.courses.storeContent', $course->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Judul Materi</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            class="w-full border rounded p-2 bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">URL Video (YouTube/Vimeo)</label>
                        <input type="text" name="video_url" value="{{ old('video_url') }}" required
                            class="w-full border rounded p-2 bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Urutan (Order)</label>
                        <input type="number" name="order" value="{{ old('order', 1) }}"
                            class="w-full border rounded p-2 bg-gray-50">
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded shadow hover:bg-blue-700">Simpan Materi</button>
                </div>
            </form>
        </div>

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
                            <td class="p-4 border-b"><span
                                    class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded">VIDEO</span></td>
                            <td class="p-4 border-b">
                                <form action="{{ route('admin.courses.destroyContent', $content->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin mau menghapus materi ini, mbut?')">
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
                            <td colspan="4" class="p-10 text-center text-gray-500">Belum ada materi untuk kursus ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="block md:hidden space-y-6">
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h3 class="font-bold mb-4 text-gray-800">Tambah Materi Baru</h3>
            <form action="{{ route('admin.courses.storeContent', $course->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Judul Materi</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            class="w-full border rounded p-2 bg-gray-50 mt-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">URL Video (YouTube/Vimeo)</label>
                        <input type="text" name="video_url" value="{{ old('video_url') }}" required
                            class="w-full border rounded p-2 bg-gray-50 mt-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Urutan (Order)</label>
                        <input type="number" name="order" value="{{ old('order', 1) }}"
                            class="w-full border rounded p-2 bg-gray-50 mt-1">
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-medium shadow hover:bg-blue-700 mt-2">Simpan
                        Materi</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-bold text-gray-800">Daftar Materi</h3>
            </div>
            <div class="w-full overflow-x-auto">
                <table class="w-full text-left min-w-[500px]">
                    <thead class="bg-gray-50 text-sm text-gray-600">
                        <tr>
                            <th class="p-3 border-b w-16 text-center">Order</th>
                            <th class="p-3 border-b">Judul Materi</th>
                            <th class="p-3 border-b text-center">Tipe</th>
                            <th class="p-3 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        @forelse($course->contents as $content)
                            <tr>
                                <td class="p-3 border-b text-center">{{ $content->order }}</td>
                                <td class="p-3 border-b font-medium">{{ $content->title }}</td>
                                <td class="p-3 border-b text-center"><span
                                        class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded">VIDEO</span></td>
                                <td class="p-4 border-b">
                                    <form action="{{ route('admin.courses.destroyContent', $content->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin mau menghapus materi ini, mbut?')">
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
                                <td colspan="4" class="p-8 text-center text-gray-500">Belum ada materi untuk kursus ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
