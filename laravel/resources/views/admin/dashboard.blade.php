@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Dashboard Monitoring</h2>
            <p class="text-sm text-gray-500 mt-0.5">Ringkasan aktivitas kursus dan progres belajar student.</p>
        </div>
        <a href="{{ route('admin.courses.index') }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-2 shadow-lg shadow-indigo-100 w-full sm:w-auto transition-all active:scale-95">
            <i class="fas fa-arrow-left text-[10px]"></i> Kelola Semua Kursus
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Total Course</p>
                    <h3 class="text-3xl font-black text-gray-900 tracking-tight">{{ $stats['total_courses'] }}</h3>
                </div>
                <div
                    class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 shadow-inner">
                    <i class="fas fa-book text-lg"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-50 flex items-center text-[11px] font-bold text-indigo-600">
                <span>Aktif di platform Eduvan</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Total Student</p>
                    <h3 class="text-3xl font-black text-gray-900 tracking-tight">{{ $stats['total_students'] }}</h3>
                </div>
                <div
                    class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 shadow-inner">
                    <i class="fas fa-users text-lg"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-50 flex items-center text-[11px] font-bold text-emerald-600">
                <span>Student terdaftar saat ini</span>
            </div>
        </div>
    </div>

    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-xs font-black text-gray-400 uppercase tracking-wider">Aktivitas Student Terbaru</h2>
    </div>

    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-400 text-xs uppercase font-bold tracking-wider">
                <tr>
                    <th class="p-4 pl-6 border-b">Student</th>
                    <th class="p-4 border-b text-right pr-6 w-40">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                @forelse($recentStudents as $student)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4 pl-6">
                            <div class="font-bold text-gray-900 text-base leading-snug">{{ $student->name }}</div>
                            <div class="text-xs text-gray-400 font-medium mt-0.5">{{ $student->email }}</div>
                        </td>
                        <td class="p-4 text-right pr-6 whitespace-nowrap">
                            <button onclick="openStudentModal('{{ $student->id }}')"
                                class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-indigo-600 hover:text-white transition shadow-sm cursor-pointer active:scale-95">
                                <i class="fas fa-chart-bar text-[10px]"></i> Lihat Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="p-8 text-center text-gray-400 italic">Belum ada student baru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="block md:hidden space-y-3 mb-6">
        @forelse($recentStudents as $student)
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <div class="font-bold text-gray-900 text-sm leading-tight truncate">{{ $student->name }}</div>
                    <div class="text-xs text-gray-400 font-medium truncate mt-0.5">{{ $student->email }}</div>
                </div>
                <button onclick="openStudentModal('{{ $student->id }}')"
                    class="flex-shrink-0 bg-indigo-50 text-indigo-700 px-3.5 py-2.5 rounded-xl text-xs font-bold active:bg-indigo-600 active:text-white transition flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-chart-bar text-[10px]"></i> Detail
                </button>
            </div>
        @empty
            <div class="bg-white p-8 rounded-2xl text-center text-gray-400 text-sm italic border border-gray-100">
                Belum ada student baru.
            </div>
        @endforelse
    </div>

    <div id="studentModal"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-gray-900/50 backdrop-blur-sm transition-all duration-300 hidden">

        <div class="fixed inset-0 bg-transparent" onclick="closeModal()"></div>

        <div
            class="relative bg-white w-full sm:max-w-2xl rounded-t-3xl sm:rounded-3xl shadow-2xl transform transition-all duration-300 translate-y-full sm:translate-y-0 opacity-0 sm:opacity-100 overflow-hidden border border-gray-100 z-10">

            <div class="block sm:hidden w-12 h-1 bg-gray-200 rounded-full mx-auto my-3"></div>

            <div class="px-6 pb-4 pt-2 sm:py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 id="modalStudentName" class="text-lg font-black text-gray-900 tracking-tight">Detail Progress</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Statistik performa dan capaian materi kursus student.</p>
                </div>
                <button onclick="closeModal()"
                    class="w-8 h-8 flex items-center justify-center bg-white hover:bg-gray-100 text-gray-400 hover:text-gray-600 rounded-xl border border-gray-200 shadow-sm text-lg transition-colors cursor-pointer active:scale-95">&times;</button>
            </div>

            <div class="p-6 max-h-[75vh] sm:max-h-[80vh] overflow-y-auto">
                <div id="modalLoading" class="py-12 flex flex-col items-center justify-center gap-3">
                    <div
                        class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-indigo-600 border-t-transparent">
                    </div>
                    <span class="text-xs font-bold text-gray-400 tracking-wider uppercase">Memuat Data...</span>
                </div>

                <div id="modalBody" class="hidden space-y-4">
                    <div class="bg-gray-50 p-3 sm:p-4 rounded-2xl border border-gray-100">
                        <div id="modalChart"></div>
                    </div>
                    <div id="modalCourseList"></div>
                </div>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-100 block sm:hidden">
                <button type="button" onclick="closeModal()"
                    class="w-full py-3.5 rounded-xl bg-indigo-600 text-white text-sm font-black shadow-lg shadow-indigo-100 transition active:scale-98">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        let studentChart;

        function openStudentModal(id) {
            const modal = document.getElementById('studentModal');
            const content = modal.querySelector('.transform');

            // Buka Modal & Overlay
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('translate-y-full', 'sm:opacity-0');
                content.classList.add('translate-y-0', 'opacity-100');
            }, 20);

            document.getElementById('modalLoading').classList.remove('hidden');
            document.getElementById('modalBody').classList.add('hidden');

            fetch(`/admin/api/students/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalLoading').classList.add('hidden');
                    document.getElementById('modalBody').classList.remove('hidden');
                    document.getElementById('modalStudentName').innerText = "Detail Progres: " + data.name;

                    renderChart(data.courses, data.progress);

                    let listHtml =
                        '<h4 class="text-xs font-black text-gray-400 uppercase tracking-wider mb-2.5">Kursus yang Diikuti</h4>';
                    listHtml += '<div class="space-y-2">';
                    data.courses.forEach((course, index) => {
                        listHtml += `
                        <div class="flex justify-between items-center p-3.5 bg-white border border-gray-100 rounded-xl shadow-sm">
                            <span class="text-sm font-bold text-gray-800 truncate pr-4">${course}</span>
                            <span class="text-sm font-black text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">${data.progress[index]}%</span>
                        </div>`;
                    });
                    listHtml += '</div>';
                    document.getElementById('modalCourseList').innerHTML = listHtml;
                })
                .catch(err => {
                    alert("Gagal mengambil data. Pastikan Route API sudah benar.");
                    closeModal();
                });
        }

        function renderChart(titles, progress) {
            if (studentChart) studentChart.destroy();

            const isMobile = window.innerWidth < 640; // sm breakpoint

            // Konfigurasi Chart Responsif
            const height = isMobile ? 320 : 250;
            const barHeight = isMobile ? '65%' : '60%';

            const options = {
                series: [{
                    name: 'Progres',
                    data: progress
                }],
                chart: {
                    type: 'bar',
                    height: height,
                    fontFamily: 'Plus Jakarta Sans, Inter, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#4f46e5'], // Indigo Eduvan
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: isMobile ? 4 : 6,
                        barHeight: barHeight,
                        dataLabels: {
                            position: 'center'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val + "%"
                    },
                    style: {
                        colors: ['#fff'],
                        fontSize: '11px',
                        fontWeight: 'bold'
                    }
                },
                xaxis: {
                    categories: titles,
                    max: 100,
                    labels: {
                        style: {
                            colors: '#9ca3af',
                            fontWeight: 'bold'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#4b5563',
                            fontWeight: 'bold'
                        },
                        maxWidth: isMobile ? 120 : 160 // Batasi lebar label y agar tidak memotong grafik
                    }
                },
                grid: {
                    borderColor: '#f3f4f6',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: 'dark'
                }
            };
            studentChart = new ApexCharts(document.querySelector("#modalChart"), options);
            studentChart.render();
        }

        function closeModal() {
            const modal = document.getElementById('studentModal');
            const content = modal.querySelector('.transform');

            content.classList.remove('translate-y-0', 'opacity-100');
            content.classList.add('translate-y-full', 'sm:opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 250);
        }

        // Tangani resize window agar chart tetap pas
        window.addEventListener('resize', () => {
            if (studentChart) {
                const modal = document.getElementById('studentModal');
                if (!modal.classList.contains('hidden')) {
                    // Hanya render ulang jika modal sedang terbuka
                    studentChart.updateOptions({
                        chart: {
                            height: window.innerWidth < 640 ? 320 : 250
                        },
                        plotOptions: {
                            bar: {
                                barHeight: window.innerWidth < 640 ? '65%' : '60%'
                            }
                        }
                    });
                }
            }
        });
    </script>
@endsection
