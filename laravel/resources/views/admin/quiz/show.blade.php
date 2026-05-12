@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-6">
    <div class="mb-6">
        <a href="{{ route('admin.quiz.index') }}" class="text-indigo-600 font-bold text-sm hover:underline">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kursus
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-4">Progres Student: {{ $course->title }}</h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-400 text-[10px] uppercase font-black">
                <tr>
                    <th class="px-6 py-4">Nama Student</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Nilai Quiz Terakhir</th>
                    <th class="px-6 py-4 text-center">Progres Belajar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($course->users as $student)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-700">{{ $student->name }}</div>
                        <div class="text-xs text-gray-400">{{ $student->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-600 uppercase">
                            Aktif
                        </span>
                    </td>
                    <td class="px-6 py-4 font-mono font-bold text-indigo-600">
                        {{ rand(70, 100) }} </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center space-x-3">
                            <div class="w-full bg-gray-100 rounded-full h-2 max-w-[120px]">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 45%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-600">45%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
