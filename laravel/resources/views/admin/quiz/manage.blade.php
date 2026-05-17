@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <div>
            <a href="{{ route('admin.quiz.index') }}"
                class="text-indigo-600 font-bold text-sm hover:underline inline-flex items-center mb-2">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Quiz</h1>
            <p class="text-gray-600 text-sm">Course: <span class="font-semibold text-gray-900">{{ $course->title }}</span></p>
        </div>
    </div>

    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <div class="flex items-center gap-2 mb-4 border-b border-gray-50 pb-3">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <i class="fas fa-plus-circle text-sm"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-800">Tambah Soal Baru</h2>
        </div>

        <form action="{{ route('admin.quiz.store', $course->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Pertanyaan</label>
                <textarea name="question" rows="3"
                    class="w-full border border-gray-200 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-sm shadow-sm"
                    placeholder="Masukkan soal di sini..." required></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Opsi A</label>
                    <input type="text" name="option_a"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-sm shadow-sm"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Opsi B</label>
                    <input type="text" name="option_b"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-sm shadow-sm"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Opsi C</label>
                    <input type="text" name="option_c"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-sm shadow-sm"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Opsi D</label>
                    <input type="text" name="option_d"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-sm shadow-sm"
                        required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Jawaban yang Benar</label>
                <div class="relative">
                    <select name="answer"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition text-sm shadow-sm appearance-none cursor-pointer"
                        required>
                        <option value="a">Opsi A</option>
                        <option value="b">Opsi B</option>
                        <option value="c">Opsi C</option>
                        <option value="d">Opsi D</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition shadow-md shadow-indigo-100 flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Simpan Soal
            </button>
        </form>
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-800">Daftar Soal Saat Ini ({{ $quizzes->count() }})</h2>
    </div>

    @if ($quizzes->isEmpty())
        <div class="bg-white border border-gray-200 border-dashed p-8 text-center rounded-2xl shadow-sm">
            <div class="text-gray-300 mb-2">
                <i class="fas fa-clipboard-list text-4xl"></i>
            </div>
            <p class="text-gray-500 text-sm italic">Belum ada soal untuk course ini. Silakan tambah soal di atas.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($quizzes as $index => $quiz)
                <div
                    class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div class="w-full">
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-block px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded-md font-bold text-[10px] uppercase tracking-wider">
                                Soal #{{ $index + 1 }}
                            </span>
                            <span
                                class="inline-block px-2.5 py-0.5 bg-emerald-50 text-emerald-700 rounded-md font-bold text-[10px] uppercase tracking-wider">
                                Kunci: {{ strtoupper($quiz->answer) }}
                            </span>
                        </div>

                        <p class="text-gray-900 font-bold text-base mt-2 mb-3 leading-relaxed break-words">
                            {{ $quiz->question }}</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2 text-sm">
                            <div
                                class="flex items-center gap-2 p-2 rounded-lg transition-colors @if ($quiz->answer == 'a') bg-emerald-50/60 text-emerald-700 font-bold border border-emerald-100/50 @else text-gray-600 @endif">
                                <span
                                    class="w-5 h-5 rounded-md flex items-center justify-center text-xs @if ($quiz->answer == 'a') bg-emerald-500 text-white @else bg-gray-100 text-gray-400 @endif">A</span>
                                <span class="break-words">{{ $quiz->option_a }}</span>
                            </div>
                            <div
                                class="flex items-center gap-2 p-2 rounded-lg transition-colors @if ($quiz->answer == 'b') bg-emerald-50/60 text-emerald-700 font-bold border border-emerald-100/50 @else text-gray-600 @endif">
                                <span
                                    class="w-5 h-5 rounded-md flex items-center justify-center text-xs @if ($quiz->answer == 'b') bg-emerald-500 text-white @else bg-gray-100 text-gray-400 @endif">B</span>
                                <span class="break-words">{{ $quiz->option_b }}</span>
                            </div>
                            <div
                                class="flex items-center gap-2 p-2 rounded-lg transition-colors @if ($quiz->answer == 'c') bg-emerald-50/60 text-emerald-700 font-bold border border-emerald-100/50 @else text-gray-600 @endif">
                                <span
                                    class="w-5 h-5 rounded-md flex items-center justify-center text-xs @if ($quiz->answer == 'c') bg-emerald-500 text-white @else bg-gray-100 text-gray-400 @endif">C</span>
                                <span class="break-words">{{ $quiz->option_c }}</span>
                            </div>
                            <div
                                class="flex items-center gap-2 p-2 rounded-lg transition-colors @if ($quiz->answer == 'd') bg-emerald-50/60 text-emerald-700 font-bold border border-emerald-100/50 @else text-gray-600 @endif">
                                <span
                                    class="w-5 h-5 rounded-md flex items-center justify-center text-xs @if ($quiz->answer == 'd') bg-emerald-500 text-white @else bg-gray-100 text-gray-400 @endif">D</span>
                                <span class="break-words">{{ $quiz->option_d }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="w-full sm:w-auto flex sm:justify-end justify-start pt-2 sm:pt-0 border-t sm:border-t-0 border-gray-50">
                        <form action="{{ route('admin.quiz.destroy', $quiz->id) }}" method="POST"
                            onsubmit="return confirm('Yakin mau hapus soal ini?')" class="w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-3.5 py-2 sm:p-2.5 rounded-xl transition-all border border-red-100/30 text-sm font-semibold">
                                <i class="fas fa-trash-alt sm:text-sm text-xs"></i>
                                <span class="sm:hidden">Hapus Soal</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
