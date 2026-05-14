@extends('layouts.admin')

@section('content')
    <div class="container mx-auto py-8 px-6">

        <!-- Navigation & Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 pb-6 border-b border-gray-100">
            <div class="flex items-center space-x-5">
                {{-- Tombol Kembali yang Lebih Modern --}}
                <a href="{{ route('admin.certificates.index') }}"
                    class="group flex items-center justify-center w-12 h-12 bg-white border border-gray-200 rounded-2xl text-gray-400 hover:text-indigo-600 hover:border-indigo-600 hover:shadow-xl transition-all duration-300">
                    <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform"></i>
                </a>

                <div>
                    <nav class="flex mb-1" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-xs text-gray-400 uppercase tracking-wider font-bold">
                            <li>Monitoring</li>
                            <li><i class="fas fa-chevron-right text-[8px]"></i></li>
                            <li class="text-indigo-500">Preview Certificate</li>
                        </ol>
                    </nav>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Detail Kelulusan</h1>
                </div>
            </div>
        </div>

        <!-- Sisi Sertifikat (Simulasi Kertas A4 Landscape) -->
        <div class="bg-white mx-auto shadow-2xl border-[15px] border-indigo-600 p-12 max-w-4xl relative overflow-hidden transition-all duration-500 hover:shadow-indigo-100"
            style="min-height: 500px; background-image: url('https://www.transparenttextures.com/patterns/cubes.png');">

            <!-- Watermark Background -->
            <div class="absolute inset-0 opacity-5 flex items-center justify-center pointer-events-none">
                <h1 class="text-[120px] font-black rotate-12">EDUVAN</h1>
            </div>

            <div class="relative z-10 text-center">
                <h1 class="text-5xl font-black text-gray-800 tracking-tighter mb-2">CERTIFICATE</h1>
                <p class="text-indigo-600 font-bold tracking-[0.2em] uppercase text-sm mb-12">Of Achievement</p>

                <p class="text-gray-500 italic text-lg">Sertifikat ini diberikan kepada:</p>
                <h2
                    class="text-4xl font-serif font-bold text-gray-900 my-6 underline decoration-indigo-500 decoration-2 underline-offset-8">
                    {{ $certificate->user->name }}
                </h2>

                <p class="text-gray-600 max-w-md mx-auto leading-relaxed">
                    Telah berhasil menyelesaikan kursus secara menyeluruh pada platform <span
                        class="font-bold text-indigo-600">EduVan</span> dalam program:
                </p>
                <h3 class="text-2xl font-black text-gray-800 mt-4 uppercase">{{ $certificate->course->title }}</h3>

                <!-- Footer Sertifikat -->
                <div class="mt-16 flex justify-between items-end px-10">
                    <div class="text-left">
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Nomor Sertifikat</p>
                        <p class="font-mono text-sm text-indigo-700">{{ $certificate->certificate_number }}</p>
                    </div>

                    <div class="text-center">
                        <div class="mb-2 text-indigo-600">
                            <i class="fas fa-certificate fa-3x"></i>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase leading-none">Verified By</p>
                        <p class="font-black text-lg text-gray-800">EduVan Team</p>
                    </div>

                    <div class="text-right">
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Tanggal Terbit</p>
                        <p class="font-bold text-sm text-gray-800">{{ $certificate->issued_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
