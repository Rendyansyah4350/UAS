@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.students.index') }}" class="text-blue-600 hover:underline mb-2 inline-block">
        &larr; Kembali ke Daftar
    </a>
    <h2 class="text-3xl font-bold text-gray-800">Detail Student: {{ $student->name }}</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-md h-fit">
        <div class="text-center mb-4">
            <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full mx-auto flex items-center justify-center text-3xl font-bold mb-3">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
            <h3 class="text-xl font-bold text-gray-800">{{ $student->name }}</h3>
            <p class="text-gray-500">{{ $student->email }}</p>
        </div>
        <div class="border-t pt-4 text-sm space-y-2">
            <p><span class="font-semibold text-gray-700">Terdaftar:</span> {{ $student->created_at->format('d M Y') }}</p>
            <p><span class="font-semibold text-gray-700">Total Course:</span> {{ $student->enrollments->count() }}</p>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        @if($student->enrollments->count() > 0)
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h4 class="text-lg font-bold text-gray-700 mb-4">Grafik Progres Belajar</h4>
                <div id="chart-progres"></div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md">
                <h4 class="text-lg font-bold text-gray-700 mb-4">Daftar Kursus</h4>
                <div class="space-y-4">
                    @foreach($student->enrollments as $enroll)
                        @php $prog = $enroll->calculateProgress(); @endphp
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-800">{{ $enroll->course->title }}</span>
                            <span class="px-3 py-1 bg-blue-600 text-white text-xs rounded-full">{{ $prog }}% Selesai</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white p-12 rounded-xl shadow-md text-center">
                <p class="text-gray-500 italic">Student ini belum memiliki kursus yang diikuti.</p>
            </div>
        @endif
    </div>
</div>

  <div id="data-container" 
     data-titles='@json($student->enrollments->map(fn($e) => $e->course->title))'
     data-progress='@json($student->enrollments->map(fn($e) => $e->calculateProgress()))'
     style="display: none;">
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil data dari atribut HTML (Cara ini 100% aman dari error VS Code)
        const container = document.getElementById('data-container');
        const titles = JSON.parse(container.getAttribute('data-titles'));
        const dataProgress = JSON.parse(container.getAttribute('data-progress'));

        const options = {
            series: [{
                name: 'Progres Belajar',
                data: dataProgress
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    horizontal: true,
                    barHeight: '50%',
                    distributed: true,
                }
            },
            colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
            dataLabels: {
                enabled: true,
                formatter: function (val) { return val + "%" }
            },
            xaxis: {
                categories: titles,
                max: 100,
                labels: { style: { colors: '#64748b' } }
            },
            grid: { borderColor: '#f1f5f9' },
            legend: { show: false }
        };

        const chart = new ApexCharts(document.querySelector("#chart-progres"), options);
        chart.render();
    });
</script>
@endsection