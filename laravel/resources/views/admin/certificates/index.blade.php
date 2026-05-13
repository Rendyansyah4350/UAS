@extends('layouts.admin')

@section('content')
    <div class="container mx-auto py-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Monitoring Sertifikat</h2>
            <p class="text-sm text-gray-500">Daftar student yang telah menyelesaikan kursus dan siap menerima sertifikat.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-400 text-[10px] uppercase font-black">
                    <tr>
                        <th class="px-6 py-4">Student</th>
                        <th class="px-6 py-4">Kursus</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pendingCertificates as $progress)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-700">{{ $progress->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $progress->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $progress->course->title }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{-- Menggunakan properti hasil pengecekan di Controller --}}
                                @if ($progress->user->already_has_certificate)
                                    <div class="flex flex-col items-center space-y-2">
                                        {{-- Badge Status --}}
                                        <span
                                            class="bg-green-100 text-green-700 font-bold text-[10px] uppercase px-3 py-1 rounded-full flex items-center">
                                            <i class="fas fa-check-circle mr-1"></i> Sudah Terbit
                                        </span>

                                        {{-- Tombol Lihat/Preview Sertifikat --}}
                                        {{-- Kita ambil ID sertifikat dari data yang sudah divalidasi --}}
                                        @php
                                            $certId = \App\Models\Certificate::where('user_id', $progress->user->id)
                                                ->where('course_id', $progress->course->id)
                                                ->value('id');
                                        @endphp

                                        <a href="{{ route('admin.certificates.preview', $certId) }}"
                                            class="text-indigo-600 hover:text-white hover:bg-indigo-600 border border-indigo-600 font-bold text-[10px] uppercase flex items-center justify-center px-4 py-1.5 rounded-lg transition-all duration-200">
                                            <i class="fas fa-eye mr-1"></i> Lihat Sertifikat
                                        </a>
                                    </div>
                                @else
                                    {{-- Tombol Validasi untuk yang belum punya sertifikat --}}
                                    <form
                                        action="{{ route('admin.certificates.issue', [$progress->user->id, $progress->course->id]) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="bg-blue-600 text-white px-5 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-blue-700 transition shadow-md active:transform active:scale-95">
                                            Validasi Sertifikat
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">
                                Belum ada student yang menyelesaikan kursus.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
