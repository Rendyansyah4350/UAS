@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.quiz.index') }}"
            class="text-indigo-600 font-bold text-sm hover:underline inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kursus
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-3">Progres Student: {{ $course->title }}</h2>
        <p class="text-gray-600 text-sm mt-1">Nilai kuis terbaru beserta akumulasi persentase penyelesaian materi kuliah.</p>
    </div>

    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-400 text-xs uppercase font-bold tracking-wider">
                <tr>
                    <th class="p-4 pl-6 border-b">Nama Student</th>
                    <th class="p-4 border-b w-32 text-center">Status</th>
                    <th class="p-4 border-b w-44 text-center">Nilai Quiz Terakhir</th>
                    <th class="p-4 border-b w-64 pr-6">Progres Belajar</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                @forelse ($course->users as $student)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4 pl-6">
                            <div class="font-bold text-gray-900 text-base leading-snug">{{ $student->name }}</div>
                            <div class="text-xs text-gray-500 font-medium mt-0.5">{{ $student->email }}</div>
                        </td>
                        <td class="p-4 text-center">
                            <span
                                class="inline-block px-3 py-1 rounded-full text-[10px] font-black tracking-wider bg-emerald-50 text-emerald-700 uppercase">
                                Aktif
                            </span>
                        </td>
                        <td class="p-4 text-center font-mono font-bold text-lg text-indigo-600">
                            {{ $student->nilai_quiz_asli ?? '-' }}
                        </td>
                        <td class="p-4 pr-6">
                            <div class="flex items-center gap-3">
                                <div class="w-full bg-gray-100 rounded-full h-2.5 max-w-[140px] overflow-hidden">
                                    <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500"
                                        style="width: {{ $student->persentase_asli ?? 0 }}%"></div>
                                </div>
                                <span
                                    class="text-sm font-extrabold text-gray-700 w-12">{{ $student->persentase_asli ?? 0 }}%</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-10 text-center text-gray-400 italic">Belum ada student yang bergabung di
                            kursus ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="block md:hidden space-y-3">
        @forelse ($course->users as $student)
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex justify-between items-start gap-2">
                    <div class="max-w-[70%]">
                        <h4 class="font-bold text-gray-900 text-base leading-tight">{{ $student->name }}</h4>
                        <span class="text-xs text-gray-400 block mt-0.5 break-all">{{ $student->email }}</span>
                    </div>
                    <span
                        class="inline-block px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider bg-emerald-50 text-emerald-700 uppercase">
                        Aktif
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-50 items-center">
                    <div class="bg-indigo-50/50 p-2 rounded-xl text-center">
                        <span class="text-[10px] text-indigo-500 font-bold block uppercase tracking-wider mb-0.5">Skor
                            Kuis</span>
                        <span
                            class="font-mono font-black text-indigo-700 text-base">{{ $student->nilai_quiz_asli ?? '-' }}</span>
                    </div>

                    <div class="space-y-1.5 pl-1">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] text-gray-400 font-medium">Progres</span>
                            <span class="text-xs font-black text-gray-800">{{ $student->persentase_asli ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-500 h-2 rounded-full"
                                style="width: {{ $student->persentase_asli ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-2xl text-center text-gray-400 text-sm italic border border-gray-100">
                Belum ada student yang bergabung di kursus ini.
            </div>
        @endforelse
    </div>
@endsection
