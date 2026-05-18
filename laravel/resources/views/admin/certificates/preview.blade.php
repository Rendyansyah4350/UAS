@extends('layouts.admin')

@section('content')
    <div class="container mx-auto py-6 px-2 sm:px-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 pb-4 border-b border-gray-100 gap-4">
            <div class="flex items-center space-x-4">
                {{-- Tombol Kembali Modern --}}
                <a href="{{ route('admin.certificates.index') }}"
                    class="group flex items-center justify-center w-10 h-10 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-indigo-600 hover:border-indigo-600 hover:shadow-md transition-all duration-300">
                    <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform text-sm"></i>
                </a>

                <div>
                    <nav class="flex mb-0.5" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                            <li>Monitoring</li>
                            <li><i class="fas fa-chevron-right text-[6px]"></i></li>
                            <li class="text-indigo-500">Preview</li>
                        </ol>
                    </nav>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Detail Kelulusan</h1>
                </div>
            </div>
        </div>

        {{-- KONTEN UTAMA PREVIEW SERTIFIKAT (SINKRONISASI 1:1 DENGAN PDF TERBAIK) --}}
        {{-- Penyesuaian h-[270px] di HP & h-[480px] di Tablet dilakukan untuk mengimbangi ruang kosong akibat efek penskalaan CSS --}}
        <div class="w-full max-w-5xl mx-auto overflow-x-visible md:overflow-x-auto pb-4 h-[270px] sm:h-[480px] md:h-auto">

            {{-- Wrapper Scale: Mengecilkan kontifikat di HP/Tablet, dan kembali normal (scale-100) di Laptop --}}
            <div
                class="transform origin-top-left md:origin-top scale-[0.33] sm:scale-[0.6] md:scale-100 transition-all duration-300 w-full flex justify-start md:justify-center">

                {{-- Mengunci dimensi rasio A4 landscape (1056px x 746px) --}}
                <div class="bg-white shadow-2xl relative overflow-hidden select-none rounded-2xl border border-gray-200 mx-auto flex-shrink-0"
                    style="width: 1056px; height: 746px; background-image: url('{{ asset('assets/images/certificate/certificate-eduvan.png') }}'); background-size: 100% 100%; background-repeat: no-repeat; background-position: center; font-family: 'Helvetica', Arial, sans-serif;">

                    {{-- 1. Teks "Dengan ini menyatakan bahwa" --}}
                    <div class="absolute left-0 right-0 text-center" style="top: 300px;">
                        <p class="text-[#2c3e50] text-[15px] m-0 p-0 font-medium">Dengan ini menyatakan bahwa</p>
                    </div>

                    {{-- 2. Nama Peserta (Dinaikkan agar pas berada di atas garis tipis tengah bawaan template) --}}
                    <div class="absolute left-0 right-0 text-center" style="top: 325px;">
                        <h1 class="text-[#1a252f] text-[46px] font-bold m-0 p-0 tracking-tight leading-tight">
                            {{ $certificate->user->name }}
                        </h1>
                    </div>

                    {{-- 3. Teks "telah berhasil menyelesaikan persyaratan kursus untuk" (Dinaikkan tepat di bawah garis tipis tengah) --}}
                    <div class="absolute left-0 right-0 text-center" style="top: 440px;">
                        <p class="text-[#7f8c8d] text-[14px] m-0 p-0">telah berhasil menyelesaikan persyaratan kursus untuk
                        </p>
                    </div>

                    {{-- 4. Judul Kursus (Dinaikkan tepat di atas garis panjang abu-abu horizontal) --}}
                    <div class="absolute left-0 right-0 text-center" style="top: 490px;">
                        <h2 class="text-[28px] font-bold text-[#1d4ed8] uppercase m-0 p-0 tracking-wide">
                            {{ $certificate->course->title }}
                        </h2>
                    </div>

                    {{-- 5. Logo EduVan & Nomor Sertifikat (Dinaikkan agar nangkring pas di bawah garis panjang abu-abu) --}}
                    <div class="absolute left-0 right-0 flex justify-center" style="top: 600px;">
                        <div class="flex items-center text-left h-[40px]">
                            <img src="{{ asset('assets/images/eduvan.png') }}" alt="Logo EduVan"
                                class="w-[36px] h-[36px] object-contain mr-2.5">
                            <div class="flex flex-col justify-center">
                                <span
                                    class="text-[9px] text-[#7f8c8d] uppercase tracking-wider font-bold leading-none mb-0.5">Nomor
                                    Sertifikat</span>
                                <span class="text-[13px] font-semibold text-[#1c3d5a] font-mono leading-none tracking-wide">
                                    {{ $certificate->certificate_number }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- 6. Tanggal Terbit (Dinaikkan agar pas berada di atas garis pendek paling bawah) --}}
                    <div class="absolute left-0 right-0 text-center" style="top: 660px;">
                        <div class="flex flex-col items-center justify-center">
                            <span
                                class="text-[9px] text-[#7f8c8d] uppercase tracking-wider font-bold leading-none mb-0.5">Tanggal
                                Terbit</span>
                            <span class="text-[14px] font-bold text-[#1d4ed8] leading-none">
                                {{ \Carbon\Carbon::parse($certificate->issued_at)->format('d F Y') }}
                            </span>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
@endsection
