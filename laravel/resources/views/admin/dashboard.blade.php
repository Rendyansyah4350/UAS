@extends('layouts.admin')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Stat Cards -->
    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full text-blue-600 mr-4">
                <i class="fas fa-book fa-2x"></i>
            </div>
            <div>
                <p class="text-gray-500">Total Course</p>
                <h3 class="text-2xl font-bold">{{ $stats['total_courses'] }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full text-green-600 mr-4">
                <i class="fas fa-users fa-2x"></i>
            </div>
            <div>
                <p class="text-gray-500">Total Student</p>
                <h3 class="text-2xl font-bold">{{ $stats['total_students'] }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-full text-yellow-600 mr-4">
                <i class="fas fa-shopping-bag fa-2x"></i>
            </div>
            <div>
                <p class="text-gray-500">Total Pembelian</p>
                <h3 class="text-2xl font-bold">{{ $stats['total_purchases'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Aktivitas Terbaru Table -->
<div class="bg-white rounded-lg shadow-md">
    <div class="p-4 border-b">
        <h3 class="font-bold text-gray-700">Aktivitas Pembelajaran Terbaru</h3>
    </div>
    <div class="p-4">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-400 text-sm">
                    <th class="p-2 border-b">Student</th>
                    <th class="p-2 border-b">Course</th>
                    <th class="p-2 border-b">Progress</th>
                    <th class="p-2 border-b">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentEnrollments as $enroll)
                <tr>
                    <td class="p-2 border-b">{{ $enroll->user->name }}</td>
                    <td class="p-2 border-b">{{ $enroll->course->title }}</td>
                    <td class="p-2 border-b">0%</td> <!-- Progress akan kita kerjakan nanti -->
                    <td class="p-2 border-b">
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">
                            {{ ucfirst($enroll->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center text-gray-500">Belum ada aktivitas pembelian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection