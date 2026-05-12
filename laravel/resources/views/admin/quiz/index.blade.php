@extends('layouts.admin')

@section('content')
    <div class="container mx-auto py-6">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Quiz & Student Progress</h2>
            <p class="text-gray-500 text-sm">Pantau efektivitas materi dan pencapaian skor student.</p>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <form action="{{ route('admin.quiz.index') }}" method="GET" class="relative w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kursus..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-orange-500 outline-none transition text-sm shadow-sm">
                <div class="absolute left-3 top-3 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50">
                <h4 class="font-bold text-gray-800">Progres Per Kursus</h4>
            </div>
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-400 text-[10px] uppercase font-black">
                    <tr>
                        <th class="px-6 py-4">Nama Kursus</th>
                        <th class="px-6 py-4">Total Student</th>
                        <th class="px-6 py-4">Rata-rata Progres</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($courses as $course)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-700">{{ $course->title }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $course->users_count }} Student</td>
                            <td class="px-6 py-4">
                                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ rand(10, 90) }}%"></div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.quiz.show', $course->id) }}"
                                    class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-4 py-2 rounded-lg text-xs font-bold transition inline-block">
                                    Lihat Detail Progres
                                </a>
                                <a href="{{ route('admin.quiz.manage', $course->id) }}"
                                    class="bg-orange-50 hover:bg-orange-100 text-orange-600 px-4 py-2 rounded-lg text-xs font-bold transition">
                                    Kelola Soal
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
