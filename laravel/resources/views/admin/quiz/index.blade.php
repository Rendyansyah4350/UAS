@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Quiz & Student Progress</h2>
            <p class="text-gray-600 text-sm">Pantau efektivitas materi dan pencapaian skor student.</p>
        </div>
    </div>

    <div
        class="mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('admin.quiz.index') }}" method="GET" class="relative w-full md:w-80">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kursus..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
            <div class="absolute left-3 top-3.5 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </form>
        @if (request('search'))
            <a href="{{ route('admin.quiz.index') }}"
                class="text-sm text-gray-500 hover:text-indigo-600 hover:underline">Reset Pencarian</a>
        @endif
    </div>

    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50/50">
            <h4 class="font-bold text-gray-800">Progres Per Kursus</h4>
        </div>
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-400 text-xs uppercase font-bold tracking-wider">
                <tr>
                    <th class="p-4 border-b pl-6">Nama Kursus</th>
                    <th class="p-4 border-b text-center w-40">Total Student</th>
                    <th class="p-4 border-b text-right pr-6 w-72">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                @forelse ($courses as $course)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4 pl-6 font-bold text-gray-900">{{ $course->title }}</td>
                        <td class="p-4 text-center">
                            <span
                                class="inline-block px-3 py-1 bg-gray-100 text-gray-600 rounded-full font-semibold text-xs">
                                {{ $course->users_count }} Student
                            </span>
                        </td>
                        <td class="p-4 text-right pr-6 space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.quiz.show', $course->id) }}"
                                class="inline-block bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-4 py-2 rounded-xl text-xs font-bold transition">
                                <i class="fas fa-chart-line mr-1"></i> Detail Progres
                            </a>
                            <a href="{{ route('admin.quiz.manage', $course->id) }}"
                                class="inline-block bg-orange-50 hover:bg-orange-100 text-orange-600 px-4 py-2 rounded-xl text-xs font-bold transition">
                                <i class="fas fa-tasks mr-1"></i> Kelola Soal
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-10 text-center text-gray-400 italic">Data kursus tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="block md:hidden space-y-3">
        @forelse ($courses as $course)
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex justify-between items-start gap-2">
                    <div class="space-y-1">
                        <h4 class="font-bold text-gray-900 text-base leading-tight">{{ $course->title }}</h4>
                        <span class="text-xs text-gray-400 block">Kursus Eduvan</span>
                    </div>
                    <span
                        class="inline-block px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg font-bold text-xs whitespace-nowrap">
                        {{ $course->users_count }} Student
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-50">
                    <a href="{{ route('admin.quiz.show', $course->id) }}"
                        class="bg-indigo-600 text-white py-2.5 rounded-xl text-xs font-bold text-center active:bg-indigo-700 transition flex items-center justify-center gap-1">
                        <i class="fas fa-chart-line text-[10px]"></i> Progres
                    </a>
                    <a href="{{ route('admin.quiz.manage', $course->id) }}"
                        class="bg-orange-50 text-orange-600 py-2.5 rounded-xl text-xs font-bold text-center active:bg-orange-100 transition flex items-center justify-center gap-1">
                        <i class="fas fa-tasks text-[10px]"></i> Kelola Soal
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-2xl text-center text-gray-400 text-sm italic border border-gray-100">
                Data kursus tidak ditemukan.
            </div>
        @endforelse
    </div>
@endsection
