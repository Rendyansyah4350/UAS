@extends('layouts.admin')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Daftar Kursus</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola materi, harga, kategori, dan seluruh konten pembelajaran Eduvan.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.courses.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-2 shadow-lg shadow-indigo-100 w-full sm:w-auto transition-all active:scale-95">
                <i class="fas fa-plus text-[10px]"></i> Tambah Kursus Baru
            </a>
        </div>
    </div>

    <div
        class="mb-6 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="w-full sm:w-auto">
            <form action="{{ route('admin.courses.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari kursus atau kategori..."
                    class="pl-10 pr-4 py-2.5 border border-gray-200 bg-gray-50/50 rounded-xl focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition text-sm w-full sm:w-72 font-medium">
                <div class="absolute left-3.5 top-3.5 text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </div>
            </form>
        </div>
    </div>

    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-400 text-xs uppercase font-bold tracking-wider">
                <tr>
                    <th class="p-4 pl-6 border-b w-12 text-center">No</th>
                    <th class="p-4 border-b w-24">Cover</th>
                    <th class="p-4 border-b">Judul Kursus</th>
                    <th class="p-4 border-b w-40">Kategori</th>
                    <th class="p-4 border-b w-36">Harga</th>
                    <th class="p-4 border-b w-24 text-center">Rating</th>
                    <th class="p-4 border-b text-right pr-6 w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                @forelse($courses as $course)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4 pl-6 text-center font-bold text-gray-400">{{ $loop->iteration }}</td>
                        <td class="p-4">
                            @if ($course->image)
                                {{-- 🟢 PERBAIKAN DI SINI: Cek jika data mengandung Base64, langsung tampilkan tanpa asset() --}}
                                @if (str_contains($course->image, 'data:image'))
                                    <img src="{{ $course->image }}"
                                        class="w-16 h-10 object-cover rounded-xl border border-gray-100 shadow-sm">
                                @else
                                    <img src="{{ asset('storage/' . $course->image) }}"
                                        class="w-16 h-10 object-cover rounded-xl border border-gray-100 shadow-sm">
                                @endif
                            @else
                                <div
                                    class="w-16 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[10px] font-black tracking-wide shadow-sm">
                                    EDV
                                </div>
                            @endif
                        </td>
                        <td class="p-4">
                            <a href="{{ route('admin.courses.show', $course->id) }}"
                                class="font-bold text-gray-900 hover:text-indigo-600 transition-colors text-base block leading-snug">
                                {{ $course->title }}
                            </a>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide block mt-0.5">Klik
                                judul untuk kelola materi</span>
                        </td>
                        <td class="p-4">
                            <span
                                class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-black uppercase tracking-wider">
                                {{ $course->category }}
                            </span>
                        </td>
                        <td class="p-4 font-black text-emerald-600 text-base">
                            Rp {{ number_format($course->price) }}
                        </td>
                        <td class="p-4 text-center">
                            <div
                                class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-0.5 rounded-lg font-black text-xs">
                                <i class="fas fa-star text-[10px]"></i> {{ number_format($course->rating, 1) ?? '0.0' }}
                            </div>
                        </td>
                        <td class="p-4 text-right pr-6 whitespace-nowrap">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.courses.edit', $course->id) }}"
                                    class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl transition-colors"
                                    title="Edit Kursus">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl transition-colors"
                                        title="Hapus Kursus">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-gray-400 italic">
                            Belum ada kursus. Klik tombol "Tambah Kursus Baru" untuk memulai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="block md:hidden space-y-3">
        @forelse($courses as $course)
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-3">
                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        @if ($course->image)
                            {{-- 🟢 DI SINI SUDAH BENAR LANGSUNG MENEMBAK VARIABEL BASE64 --}}
                            <img src="{{ $course->image }}" alt="Sampul" style="max-width: 150px; height: auto;"
                                class="w-20 h-14 object-cover rounded-xl border border-gray-100">
                        @else
                            <div
                                class="w-20 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-black tracking-wide">
                                EDUVAN
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span
                                class="inline-block px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md text-[9px] font-black uppercase tracking-wider truncate">
                                {{ $course->category }}
                            </span>
                            <div class="inline-flex items-center gap-0.5 text-amber-600 font-black text-xs">
                                <i class="fas fa-star text-[10px]"></i> {{ number_format($course->rating, 1) ?? '0.0' }}
                            </div>
                        </div>
                        <a href="{{ route('admin.courses.show', $course->id) }}"
                            class="font-bold text-gray-900 text-sm leading-tight block hover:text-indigo-600 transition-colors truncate">
                            {{ $course->title }}
                        </a>
                        <div class="text-emerald-600 font-black text-sm mt-1">
                            Rp {{ number_format($course->price) }}
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-50 flex gap-2">
                    <a href="{{ route('admin.courses.show', $course->id) }}"
                        class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 py-2.5 rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5">
                        <i class="fas fa-folder-open text-[10px]"></i> Kelola Materi
                    </a>
                    <a href="{{ route('admin.courses.edit', $course->id) }}"
                        class="w-11 h-10 bg-blue-50 text-blue-600 flex items-center justify-center rounded-xl active:bg-blue-100 transition">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="inline-block"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-11 h-10 bg-rose-50 text-rose-600 flex items-center justify-center rounded-xl active:bg-rose-100 transition">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-2xl text-center text-gray-400 text-sm italic border border-gray-100">
                Belum ada kursus. Klik tombol "Tambah Kursus Baru" untuk memulai.
            </div>
        @endforelse
    </div>
@endsection
