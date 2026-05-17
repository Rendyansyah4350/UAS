@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Kursus: {{ $course->title }}</h2>
    </div>

    <div class="hidden md:block bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Judul Kursus</label>
                    <input type="text" name="title" value="{{ $course->title }}" required
                        class="mt-1 block w-full border rounded-md p-2 bg-gray-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="category" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 bg-gray-50">
                        <option value="Computer Science" {{ $course->category == 'Computer Science' ? 'selected' : '' }}>
                            Computer Science</option>
                        <option value="Microsoft Office" {{ $course->category == 'Microsoft Office' ? 'selected' : '' }}>
                            Microsoft Office</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="4" required class="mt-1 block w-full border rounded-md p-2 bg-gray-50">{{ $course->description }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                        <input type="number" name="price" value="{{ $course->price }}" required
                            class="mt-1 block w-full border rounded-md p-2 bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ganti Cover (Opsional)</label>
                        <input type="file" name="image" class="mt-1 block w-full text-sm text-gray-500">
                        @if ($course->image)
                            <p class="mt-2 text-xs text-gray-400">Gambar saat ini sudah tersimpan.</p>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-4">
                    <a href="{{ route('admin.courses.index') }}"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Batal</a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Update Kursus
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="block md:hidden bg-white rounded-lg shadow-md p-4">
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Judul Kursus</label>
                    <input type="text" name="title" value="{{ $course->title }}" required
                        class="mt-1 block w-full border rounded-md p-2 bg-gray-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="category" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 bg-gray-50">
                        <option value="Computer Science" {{ $course->category == 'Computer Science' ? 'selected' : '' }}>
                            Computer Science</option>
                        <option value="Microsoft Office" {{ $course->category == 'Microsoft Office' ? 'selected' : '' }}>
                            Microsoft Office</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="4" required class="mt-1 block w-full border rounded-md p-2 bg-gray-50">{{ $course->description }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ $course->price }}" required
                        class="mt-1 block w-full border rounded-md p-2 bg-gray-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Cover (Opsional)</label>
                    <input type="file" name="image"
                        class="block w-full text-sm text-gray-500 file:w-full file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700">
                    @if ($course->image)
                        <p class="mt-1 text-xs text-gray-400">Gambar saat ini sudah tersimpan.</p>
                    @endif
                </div>

                <div class="flex flex-col gap-2 pt-4 border-t border-gray-100">
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2.5 rounded-lg hover:bg-blue-700 text-center font-medium">
                        Update Kursus
                    </button>
                    <a href="{{ route('admin.courses.index') }}"
                        class="w-full bg-gray-200 text-gray-700 py-2.5 rounded-lg hover:bg-gray-300 text-center font-medium">Batal</a>
                </div>
            </div>
        </form>
    </div>
@endsection
