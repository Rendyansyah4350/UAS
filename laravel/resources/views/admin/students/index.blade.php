@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Student</h2>
            <p class="text-gray-600">Pantau aktivitas dan progres belajar setiap student.</p>
        </div>
        <button onclick="openCreateModal()"
            class="w-full sm:w-auto inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-indigo-200">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Student Baru
        </button>
    </div>

    <div id="createStudentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeCreateModal()"></div>

            <div id="addStudentModal"
                class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-gray-900/50 backdrop-blur-sm transition-all duration-300">

                <div
                    class="relative bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300 translate-y-0 opacity-100 border border-gray-100">

                    <div class="block sm:hidden w-12 h-1 bg-gray-200 rounded-full mx-auto my-3"></div>

                    <div class="bg-indigo-600 p-5 sm:p-6 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold tracking-tight">Tambah Student</h3>
                                <p class="text-indigo-200 text-xs mt-0.5 hidden sm:block">Daftarkan akun student baru
                                    Eduvan.</p>
                            </div>
                            <button onclick="closeCreateModal()"
                                class="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-xl transition-colors text-xl leading-none">&times;</button>
                        </div>
                    </div>

                    <form action="{{ route('admin.students.store') }}" method="POST"
                        class="p-6 space-y-4 max-h-[75vh] sm:max-h-none overflow-y-auto">
                        @csrf

                        <div>
                            <label class="text-xs font-black text-gray-700 uppercase tracking-wider block mb-1.5">Nama
                                Lengkap</label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 pointer-events-none">
                                    <i class="fas fa-user text-xs"></i>
                                </span>
                                <input type="text" name="name"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all text-sm font-medium"
                                    placeholder="Nama student..." required>
                            </div>
                        </div>

                        <div>
                            <label
                                class="text-xs font-black text-gray-700 uppercase tracking-wider block mb-1.5">Email</label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 pointer-events-none">
                                    <i class="fas fa-envelope text-xs"></i>
                                </span>
                                <input type="email" name="email"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all text-sm font-medium"
                                    placeholder="email@example.com" required>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-black text-gray-700 uppercase tracking-wider block">Password</label>
                            <div class="relative">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 pointer-events-none">
                                    <i class="fas fa-lock text-xs"></i>
                                </span>
                                <input type="password" name="password" id="passwordField"
                                    class="w-full pl-10 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all text-sm font-medium"
                                    placeholder="Min. 8 karakter" required>

                                <button type="button" onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-indigo-600 transition-colors">
                                    <i id="eyeIcon" class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                            <p class="text-[11px] text-gray-400 font-medium"><i class="fas fa-info-circle mr-0.5"></i>
                                Gunakan password yang aman.</p>
                        </div>

                        <div class="pt-4 flex flex-col sm:flex-row gap-2">
                            <button type="button" onclick="closeCreateModal()"
                                class="order-2 sm:order-1 w-full sm:w-auto px-5 py-3.5 rounded-xl border border-gray-200 bg-white text-gray-600 text-xs font-bold hover:bg-gray-50 transition active:scale-98">
                                Batal
                            </button>
                            <button type="submit"
                                class="order-1 sm:order-2 w-full flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-indigo-100 transition-all active:scale-98 flex items-center justify-center gap-1.5 text-sm">
                                <i class="fas fa-save text-xs"></i> Simpan Student
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="hidden md:block">
        <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <form action="{{ route('admin.students.index') }}" method="GET" class="flex items-center gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama student..."
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <select name="filter"
                        class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Semua Status</option>
                        <option value="bought" {{ request('filter') == 'bought' ? 'selected' : '' }}>Sudah Beli Course
                        </option>
                        <option value="not_bought" {{ request('filter') == 'not_bought' ? 'selected' : '' }}>Belum Beli
                            Course</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    Filter
                </button>
                <a href="{{ route('admin.students.index') }}" class="text-gray-500 py-2 hover:underline">Reset</a>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="p-4 border-b">Nama Student</th>
                        <th class="p-4 border-b text-center">Status</th>
                        <th class="p-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 border-b font-medium text-gray-900">
                                {{ $student->name }}
                                <br>
                                <span class="text-xs text-gray-400 font-normal">{{ $student->email }}</span>
                            </td>
                            <td class="p-4 border-b text-center">
                                @if ($student->enrollments->count() > 0)
                                    <span
                                        class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full font-semibold">Aktif</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-500 text-xs rounded-full">Belum Ada
                                        Course</span>
                                @endif
                            </td>
                            <td class="p-4 border-b text-center space-x-2">
                                <button onclick="openStudentModal('{{ $student->id }}')"
                                    class="inline-block bg-indigo-100 text-indigo-700 px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-indigo-200 transition">
                                    Detail Cepat
                                </button>

                                <a href="{{ route('admin.students.show', $student->id) }}"
                                    class="text-indigo-600 hover:underline text-sm font-medium">Halaman Detail</a>

                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 p-2"
                                        onclick="return confirm('Hapus student ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-10 text-center text-gray-500">Data student tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <div class="block md:hidden space-y-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <form action="{{ route('admin.students.index') }}" method="GET" class="flex flex-col gap-3">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama student..."
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>
                <div>
                    <select name="filter"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        <option value="">Semua Status</option>
                        <option value="bought" {{ request('filter') == 'bought' ? 'selected' : '' }}>Sudah Beli Course
                        </option>
                        <option value="not_bought" {{ request('filter') == 'not_bought' ? 'selected' : '' }}>Belum Beli
                            Course</option>
                    </select>
                </div>
                <div class="flex gap-2 items-center pt-1">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg text-sm font-medium transition text-center">
                        Filter
                    </button>
                    <a href="{{ route('admin.students.index') }}"
                        class="flex-1 bg-gray-100 text-gray-600 py-2.5 rounded-lg text-sm font-medium text-center hover:bg-gray-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="space-y-3">
            @forelse($students as $student)
                <div
                    class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 relative flex flex-col justify-between gap-4">
                    <div class="flex justify-between items-start pr-8">
                        <div>
                            <h4 class="font-bold text-gray-900 text-base leading-tight">{{ $student->name }}</h4>
                            <span class="text-xs text-gray-500 mt-0.5 block">{{ $student->email }}</span>

                            <div class="mt-2">
                                @if ($student->enrollments->count() > 0)
                                    <span
                                        class="inline-block px-2.5 py-0.5 bg-green-100 text-green-700 text-[11px] rounded-full font-semibold">Aktif</span>
                                @else
                                    <span
                                        class="inline-block px-2.5 py-0.5 bg-gray-100 text-gray-500 text-[11px] rounded-full">Belum
                                        Ada Course</span>
                                @endif
                            </div>
                        </div>

                        <div class="absolute top-4 right-4">
                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition active:scale-95"
                                    onclick="return confirm('Hapus student ini?')">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-50">
                        <button onclick="openStudentModal('{{ $student->id }}')"
                            class="bg-indigo-50 text-indigo-700 py-2 rounded-lg text-xs font-bold text-center active:bg-indigo-100 transition">
                            Detail Cepat
                        </button>
                        <a href="{{ route('admin.students.show', $student->id) }}"
                            class="bg-indigo-600 text-white py-2 rounded-lg text-xs font-bold text-center active:bg-indigo-700 transition flex items-center justify-center">
                            Halaman Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl text-center text-gray-500 border border-gray-100">
                    Data student tidak ditemukan.
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-4 p-2">
        {{ $students->links() }}
    </div>

    <div id="studentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full p-6 w-full">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Progres Belajar</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>

                <div id="loadingState" class="py-10 text-center">
                    <div
                        class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-indigo-500 border-t-transparent">
                    </div>
                    <p class="mt-2 text-gray-500">Mengambil data...</p>
                </div>

                <div id="modalContent" class="hidden">
                    <div id="chart-container" class="mb-6 w-full">
                        <div id="modalChart"></div>
                    </div>
                    <div id="modalCourseList" class="space-y-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        let chart;

        function openStudentModal(id) {
            const modal = document.getElementById('studentModal');
            const loading = document.getElementById('loadingState');
            const content = document.getElementById('modalContent');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            content.classList.add('hidden');

            fetch(`/admin/api/students/${id}`)
                .then(response => response.json())
                .then(data => {
                    loading.classList.add('hidden');
                    content.classList.remove('hidden');

                    // Render chart
                    updateChart(data.courses, data.progress);

                    // Render list
                    let listHtml = '<h4 class="font-bold text-gray-700 mb-2">Detail Kursus:</h4>';
                    if (data.courses.length === 0) {
                        listHtml += '<p class="text-sm italic text-gray-500">Student belum membeli kursus.</p>';
                    } else {
                        data.courses.forEach((course, index) => {
                            listHtml += `
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <span class="text-sm font-medium text-gray-700">${course}</span>
                                <span class="text-sm font-bold text-indigo-600">${data.progress[index]}%</span>
                            </div>
                        `;
                        });
                    }
                    document.getElementById('modalCourseList').innerHTML = listHtml;
                });
        }

        function updateChart(categories, data) {
            if (chart) {
                chart.destroy();
            }

            const options = {
                series: [{
                    name: 'Progres',
                    data: data
                }],
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 8,
                        distributed: true,
                        barHeight: '60%'
                    }
                },
                colors: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
                xaxis: {
                    categories: categories,
                    max: 100
                },
                legend: {
                    show: false
                },
                dataLabels: {
                    formatter: (val) => val + '%'
                }
            };

            chart = new ApexCharts(document.querySelector("#modalChart"), options);
            chart.render();
        }

        function closeModal() {
            document.getElementById('studentModal').classList.add('hidden');
        }

        function openCreateModal() {
            document.getElementById('createStudentModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCreateModal() {
            document.getElementById('createStudentModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function togglePasswordVisibility() {
            const passwordField = document.getElementById('passwordField');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
@endsection
