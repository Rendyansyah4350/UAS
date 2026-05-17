@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.students.index') }}"
            class="text-blue-600 hover:underline mb-2 inline-block text-sm sm:text-base">
            &larr; Kembali ke Daftar
        </a>
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Detail Student: {{ $student->name }}</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md h-fit border border-gray-100">
            <div class="text-center mb-4">
                <div
                    class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full mx-auto flex items-center justify-center text-3xl font-bold mb-3">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </div>
                <h3 class="text-xl font-bold text-gray-800">{{ $student->name }}</h3>
                <p class="text-gray-500 text-sm break-all">{{ $student->email }}</p>
            </div>
            <div class="border-t pt-4 text-sm space-y-2 text-gray-600">
                <p><span class="font-semibold text-gray-700">Terdaftar:</span> {{ $student->created_at->format('d M Y') }}
                </p>
                <p><span class="font-semibold text-gray-700">Total Course:</span> {{ $student->enrollments->count() }}</p>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            @if ($student->enrollments->count() > 0)
                <div class="hidden md:block bg-white p-6 rounded-xl shadow-md border border-gray-100">
                    <h4 class="text-lg font-bold text-gray-700 mb-4">Grafik Progres Belajar</h4>
                    <div id="chart-progres-desktop"></div>
                </div>

                <div class="block md:hidden bg-white p-4 rounded-xl shadow-md border border-gray-100">
                    <h4 class="text-base font-bold text-gray-700 mb-3">Grafik Progres Belajar</h4>
                    <div class="w-full overflow-hidden">
                        <div id="chart-progres-mobile"></div>
                    </div>
                </div>

                <div class="bg-white p-5 sm:p-6 rounded-xl shadow-md border border-gray-100">
                    <h4 class="text-lg font-bold text-gray-700 mb-4">Daftar Kursus</h4>
                    <div class="space-y-3">
                        @foreach ($student->enrollments as $enroll)
                            @php $prog = $enroll->calculateProgress(); @endphp
                            <div
                                class="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-gray-50 rounded-lg gap-2">
                                <span
                                    class="font-medium text-gray-800 text-sm sm:text-base">{{ $enroll->course->title }}</span>
                                <span class="w-fit px-3 py-1 bg-blue-600 text-white text-xs rounded-full font-semibold">
                                    {{ $prog }}% Selesai
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white p-12 rounded-xl shadow-md text-center border border-gray-100">
                    <p class="text-gray-500 italic">Student ini belum memiliki kursus yang diikuti.</p>
                </div>
            @endif
        </div>
    </div>

    <div id="data-container" data-titles='@json($student->enrollments->map(fn($e) => $e->course->title))' data-progress='@json($student->enrollments->map(fn($e) => $e->calculateProgress()))'
        style="display: none;">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('data-container');
            if (!container) return;

            const titles = JSON.parse(container.getAttribute('data-titles'));
            const dataProgress = JSON.parse(container.getAttribute('data-progress'));

            // Cek jika data kosong agar tidak memicu error charting
            if (titles.length === 0) return;

            // ----------------------------------------------------
            // CONFIG GRAFIK LAPTOP (Horizontal, Luas)
            // ----------------------------------------------------
            const optionsDesktop = {
                series: [{
                    name: 'Progres Belajar',
                    data: dataProgress
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        horizontal: true,
                        barHeight: '50%',
                        distributed: true
                    }
                },
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val + "%"
                    }
                },
                xaxis: {
                    categories: titles,
                    max: 100,
                    labels: {
                        style: {
                            colors: '#64748b'
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9'
                },
                legend: {
                    show: false
                }
            };

            // ----------------------------------------------------
            // CONFIG GRAFIK MOBILE (Vertikal, Ringkas, Anti Potong)
            // ----------------------------------------------------
            const optionsMobile = {
                series: [{
                    name: 'Progres Belajar',
                    data: dataProgress
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        horizontal: false,
                        columnWidth: '45%',
                        distributed: true
                    }
                },
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val + "%"
                    },
                    style: {
                        fontSize: '10px'
                    }
                },
                xaxis: {
                    categories: titles,
                    labels: {
                        rotate: -45, // Memutar teks judul agar muat banyak di layar kecil
                        style: {
                            colors: '#64748b',
                            fontSize: '10px'
                        }
                    }
                },
                yaxis: {
                    max: 100,
                    labels: {
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9'
                },
                legend: {
                    show: false
                }
            };

            // Render Grafik Desktop
            const chartDesktop = new ApexCharts(document.querySelector("#chart-progres-desktop"), optionsDesktop);
            chartDesktop.render();

            // Render Grafik Mobile
            const chartMobile = new ApexCharts(document.querySelector("#chart-progres-mobile"), optionsMobile);
            chartMobile.render();
        });
    </script>
@endsection
