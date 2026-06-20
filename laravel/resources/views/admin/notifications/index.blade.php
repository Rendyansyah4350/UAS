@extends('layouts.admin') {{-- Pastikan nama layout induk ini sudah sama dengan file utama adminmu --}}

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Pemberitahuan Baru (Localhost Mode)</h1>
                <p class="text-sm text-gray-500">Kirim pesan informasi, pengumuman, atau promo massal langsung ke aplikasi
                    Ionic mahasiswa.</p>
            </div>
        </div>

        {{-- Alert Status --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"
                style="border-radius: 12px;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" style="border-radius: 12px;">
                {{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl"
            style="border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
            <form action="{{ url('/admin/notifications/send') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Judul Notifikasi</label>
                    <input type="text" name="title" required
                        placeholder="Contoh: Info Maintenance / Pengumuman Libur Kuliah"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                        style="border-radius: 10px;">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori / Tipe Pesan</label>
                    <select name="type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                        style="border-radius: 10px;">
                        <option value="info">Info Umum Aplikasi (Warna Biru)</option>
                        <option value="pengumuman">Pengumuman Akademik (Warna Biru Tema)</option>
                        <option value="promo">Promo Unggulan EduVan (Warna Jingga Megaphone)</option>
                        <option value="alert">Peringatan Sistem/Alert (Warna Merah)</option>
                        <option value="success">Sukses Transaksi (Warna Hijau)</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Isi Pesan Pemberitahuan</label>
                    <textarea name="message" rows="4" required
                        placeholder="Tulis rincian pesan pengumuman yang ingin disampaikan..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                        style="border-radius: 10px;"></textarea>
                </div>

                <button type="submit" class="text-white font-bold py-2 px-6 rounded-lg transition shadow-md"
                    style="border-radius: 12px; background-color: #093C5D; border: none; cursor: pointer;">
                    Blast Notifikasi Sekarang
                </button>
            </form>
        </div>
    </div>
@endsection
