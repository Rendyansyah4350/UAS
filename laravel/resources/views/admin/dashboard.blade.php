@extends('layouts.admin')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
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
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="p-4 border-b font-semibold">Student</th>
                    <th class="p-4 border-b text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentStudents as $student)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 border-b">
                            <p class="font-bold text-gray-800">{{ $student->name }}</p>
                            <p class="text-xs text-gray-500">{{ $student->email }}</p>
                        </td>
                        <td class="p-4 border-b text-center">
                            <button onclick="openStudentModal('{{ $student->id }}')"
                                class="bg-indigo-100 text-indigo-700 px-4 py-1.5 rounded-full text-xs font-bold hover:bg-indigo-600 hover:text-white transition shadow-sm cursor-pointer">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="studentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" onclick="closeModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modalStudentName" class="text-xl font-bold text-gray-800">Detail Progress</h3>
                    <button onclick="closeModal()"
                        class="text-gray-400 hover:text-gray-600 text-2xl cursor-pointer">&times;</button>
                </div>
                <div id="modalLoading" class="py-10 text-center">
                    <div
                        class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-indigo-500 border-t-transparent">
                    </div>
                </div>
                <div id="modalBody" class="hidden">
                    <div id="modalChart"></div>
                    <div id="modalCourseList" class="mt-4 space-y-2"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        let studentChart;

        function openStudentModal(id) {
            document.getElementById('studentModal').classList.remove('hidden');
            document.getElementById('modalLoading').classList.remove('hidden');
            document.getElementById('modalBody').classList.add('hidden');

            // Perbaikan minor endpoint API URL agar sesuai standar pemanggilan
            fetch(`/admin/api/students/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalLoading').classList.add('hidden');
                    document.getElementById('modalBody').classList.remove('hidden');
                    document.getElementById('modalStudentName').innerText = "Detail Progres: " + data.name;

                    // Update Chart
                    renderChart(data.courses, data.progress);

                    // Tampilkan Daftar Kursus di bawah Chart
                    let listHtml = '<h4 class="font-bold text-gray-700 mt-4 mb-2">Kursus yang Diikuti:</h4>';
                    data.courses.forEach((course, index) => {
                        listHtml += `
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg mb-2 border border-gray-100">
                            <span class="text-sm font-medium text-gray-700">${course}</span>
                            <span class="text-sm font-bold text-blue-600">${data.progress[index]}%</span>
                        </div>`;
                    });
                    document.getElementById('modalCourseList').innerHTML = listHtml;
                })
                .catch(err => {
                    alert("Gagal mengambil data. Pastikan Route API sudah benar.");
                    closeModal();
                });
        }

        function renderChart(titles, progress) {
            if (studentChart) studentChart.destroy();

            const dynamicHeight = titles.length > 3 ? (titles.length * 70) : 250;

            const options = {
                series: [{
                    name: 'Progres',
                    data: progress
                }],
                chart: {
                    type: 'bar',
                    height: dynamicHeight,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 5,
                        distributed: true,
                        barHeight: '70%'
                    }
                },
                xaxis: {
                    categories: titles,
                    max: 100
                },
            };
            studentChart = new ApexCharts(document.querySelector("#modalChart"), options);
            studentChart.render();
        }

        function closeModal() {
            document.getElementById('studentModal').classList.add('hidden');
        }
    </script>
@endsection
