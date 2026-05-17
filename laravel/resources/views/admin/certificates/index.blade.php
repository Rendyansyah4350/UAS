@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Monitoring Sertifikat</h2>
        <p class="text-sm text-gray-600">Daftar student yang telah menyelesaikan kursus dan siap menerima sertifikat resmi
            Eduvan.</p>
    </div>

    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-400 text-xs uppercase font-bold tracking-wider">
                <tr>
                    <th class="p-4 pl-6 border-b w-1/4">Student</th>
                    <th class="p-4 border-b w-1/3">Kursus</th>
                    <th class="p-4 border-b text-right pr-6">Status & Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                @forelse($pendingCertificates as $progress)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4 pl-6">
                            <div class="font-bold text-gray-900 text-base leading-snug">{{ $progress->user->name }}</div>
                            <div class="text-xs text-gray-500 font-medium mt-0.5">{{ $progress->user->email }}</div>
                        </td>

                        <td class="p-4 font-semibold text-gray-700">
                            {{ $progress->course->title }}
                        </td>

                        <td class="p-4 text-right pr-6 whitespace-nowrap">
                            @if ($progress->user->already_has_certificate)
                                <div class="inline-flex items-center gap-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 font-black text-[10px] uppercase tracking-wider rounded-md">
                                        <i class="fas fa-check-circle mr-1"></i> Sudah Terbit
                                    </span>

                                    @php
                                        $certId = \App\Models\Certificate::where('user_id', $progress->user->id)
                                            ->where('course_id', $progress->course->id)
                                            ->value('id');
                                    @endphp

                                    <a href="{{ route('admin.certificates.download', $certId) }}"
                                        class="inline-flex items-center bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs px-3 py-2 rounded-xl transition-colors">
                                        <i class="fas fa-download mr-1.5"></i> Download PDF
                                    </a>

                                    <a href="{{ route('admin.certificates.preview', $certId) }}"
                                        class="inline-flex items-center bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold text-xs px-3 py-2 rounded-xl transition-colors">
                                        <i class="fas fa-eye mr-1.5"></i> Lihat
                                    </a>
                                </div>
                            @else
                                <form
                                    action="{{ route('admin.certificates.issue', [$progress->user->id, $progress->course->id]) }}"
                                    method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-indigo-700 transition active:scale-95 shadow-sm shadow-indigo-100">
                                        <i class="fas fa-certificate mr-1.5"></i> Validasi Sertifikat
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-10 text-center text-gray-400 italic">Belum ada student yang
                            menyelesaikan kursus.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="block md:hidden space-y-3">
        @forelse($pendingCertificates as $progress)
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm space-y-3">
                <div class="flex justify-between items-start gap-2">
                    <div class="max-w-[65%]">
                        <h4 class="font-bold text-gray-900 text-base leading-tight">{{ $progress->user->name }}</h4>
                        <span class="text-xs text-gray-400 block mt-0.5 break-all">{{ $progress->user->email }}</span>
                    </div>

                    @if ($progress->user->already_has_certificate)
                        <span
                            class="inline-block px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider bg-emerald-50 text-emerald-700 uppercase whitespace-nowrap">
                            Terbit
                        </span>
                    @else
                        <span
                            class="inline-block px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider bg-amber-50 text-amber-700 uppercase whitespace-nowrap">
                            Pending
                        </span>
                    @endif
                </div>

                <div class="p-2.5 bg-gray-50 rounded-xl">
                    <span class="text-[9px] text-gray-400 font-bold block uppercase tracking-wider">Menyelesaikan
                        Kursus:</span>
                    <span class="text-xs font-bold text-gray-800 block mt-0.5">{{ $progress->course->title }}</span>
                </div>

                <div class="pt-2 border-t border-gray-50">
                    @if ($progress->user->already_has_certificate)
                        @php
                            $certId = \App\Models\Certificate::where('user_id', $progress->user->id)
                                ->where('course_id', $progress->course->id)
                                ->value('id');
                        @endphp
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('admin.certificates.download', $certId) }}"
                                class="bg-rose-600 text-white py-2.5 rounded-xl text-xs font-bold text-center active:bg-rose-700 transition flex items-center justify-center gap-1.5">
                                <i class="fas fa-download text-[10px]"></i> Download PDF
                            </a>
                            <a href="{{ route('admin.certificates.preview', $certId) }}"
                                class="bg-indigo-50 text-indigo-600 py-2.5 rounded-xl text-xs font-bold text-center active:bg-indigo-100 transition flex items-center justify-center gap-1.5">
                                <i class="fas fa-eye text-[10px]"></i> Lihat
                            </a>
                        </div>
                    @else
                        <form
                            action="{{ route('admin.certificates.issue', [$progress->user->id, $progress->course->id]) }}"
                            method="POST" class="w-full">
                            @csrf
                            <button type="submit"
                                class="w-full bg-indigo-600 text-white py-2.5 rounded-xl text-xs font-bold text-center active:bg-indigo-700 transition flex items-center justify-center gap-1.5">
                                <i class="fas fa-certificate text-[10px]"></i> Validasi Sertifikat Sekarang
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-2xl text-center text-gray-400 text-sm italic border border-gray-100">
                Belum ada student yang menyelesaikan kursus.
            </div>
        @endforelse
    </div>
@endsection
