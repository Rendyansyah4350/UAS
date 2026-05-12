@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Quiz</h1>
            <p class="text-gray-600">Course: {{ $course->title }}</p>
        </div>
        <a href="{{ route('admin.quiz.index') }}" class="text-gray-500 hover:text-gray-700"> kembali ke daftar</a>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <h2 class="text-lg font-bold mb-4 text-indigo-600">Tambah Soal Baru</h2>
        <form action="{{ route('admin.quiz.store', $course->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan</label>
                <textarea name="question" rows="3" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Masukkan soal di sini..." required></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Opsi A</label>
                    <input type="text" name="option_a" class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Opsi B</label>
                    <input type="text" name="option_b" class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Opsi C</label>
                    <input type="text" name="option_c" class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Opsi D</label>
                    <input type="text" name="option_d" class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Jawaban yang Benar</label>
                <select name="answer" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg" required>
                    <option value="a">Opsi A</option>
                    <option value="b">Opsi B</option>
                    <option value="c">Opsi C</option>
                    <option value="d">Opsi D</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-xl hover:bg-indigo-700 transition duration-200">
                Simpan Soal
            </button>
        </form>
    </div>

    <h2 class="text-xl font-bold mb-4 text-gray-800">Daftar Soal Saat Ini ({{ $quizzes->count() }})</h2>

    @if($quizzes->isEmpty())
        <div class="bg-gray-50 border-2 border-dashed border-gray-200 p-8 text-center rounded-2xl">
            <p class="text-gray-500">Belum ada soal untuk course ini. Silakan tambah soal di atas.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($quizzes as $index => $quiz)
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold uppercase text-indigo-500 tracking-wider">Soal #{{ $index + 1 }}</span>
                    <p class="text-gray-800 font-semibold mt-1 mb-3">{{ $quiz->question }}</p>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-1 text-sm text-gray-600">
                        <p @if($quiz->answer == 'a') class="text-green-600 font-bold" @endif>A: {{ $quiz->option_a }}</p>
                        <p @if($quiz->answer == 'b') class="text-green-600 font-bold" @endif>B: {{ $quiz->option_b }}</p>
                        <p @if($quiz->answer == 'c') class="text-green-600 font-bold" @endif>C: {{ $quiz->option_c }}</p>
                        <p @if($quiz->answer == 'd') class="text-green-600 font-bold" @endif>D: {{ $quiz->option_d }}</p>
                    </div>
                </div>
                <form action="{{ route('admin.quiz.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus soal ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
