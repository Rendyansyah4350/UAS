@extends('layouts.admin')

@section('content')
    <div class="container mx-auto py-10">
        <div class="mb-6">
            <a href="{{ route('admin.certificates.index') }}" class="text-indigo-600 font-bold text-sm hover:underline">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Monitoring
            </a>
        </div>

        <!-- Sisi Sertifikat (Simulasi Kertas A4 Landscape) -->
        <div class="bg-white mx-auto shadow-2xl border-[15px] border-indigo-600 p-12 max-w-4xl relative overflow-hidden"
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
                    Telah berhasil menyelesaikan kursus secara menyeluruh pada platform EduVan dalam program:
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

        <!-- Tombol Aksi Tambahan -->
        <div class="mt-8 text-center">
            <button onclick="window.print()"
                class="bg-gray-800 text-white px-8 py-3 rounded-2xl font-bold hover:bg-black transition">
                <i class="fas fa-print mr-2"></i> Print / Save as PDF
            </button>
        </div>
    </div>
@endsection
