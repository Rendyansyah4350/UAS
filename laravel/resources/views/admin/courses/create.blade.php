@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tambah Kursus Baru</h2>
        <p class="text-gray-600">Lengkapi formulir di bawah untuk membuat kursus baru di marketplace.</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Judul Kursus</label>
                    <input type="text" name="title" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="category" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Microsoft Office">Microsoft Office</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="4" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 bg-gray-50"
                        placeholder="Jelaskan apa yang akan dipelajari student..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                        <input type="number" name="price" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 bg-gray-50"
                            placeholder="Contoh: 150000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cover Kursus (Opsional)</label>
                        <input type="file" name="image"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-4">
                    <a href="{{ route('admin.courses.index') }}"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Batal</a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i> Simpan Kursus
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
