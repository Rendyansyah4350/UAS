@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Pembelian</h2>
            <p class="text-gray-600 text-sm">Pantau ringkasan pendapatan materi dan detail transaksi masuk.</p>
        </div>
        <a href="{{ route('admin.pembelian.pdf') }}"
            class="w-full sm:w-auto inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all shadow-lg shadow-red-100">
            <i class="fas fa-file-pdf mr-2"></i> Cetak PDF
        </a>
    </div>

    {{-- KONTEN RINGKASAN PENDAPATAN DENGAN FITUR SEARCH BAR RESPONSIF (LAPTOP & HP) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div
            class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h4 class="font-bold text-gray-800">Ringkasan Pendapatan Per Materi</h4>

            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </span>
                <input type="text" id="courseSearchInput" placeholder="Cari materi / kursus..."
                    class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>
        </div>

        {{-- TAMPILAN MATERI: LAPTOP (TABLE) --}}
        <div class="hidden md:block">
            <table class="w-full text-left border-collapse" id="courseDataTable">
                <thead class="bg-gray-50 text-gray-700 text-sm font-semibold">
                    <tr>
                        <th class="p-4 border-b">Materi / Kursus</th>
                        <th class="p-4 border-b text-center">Total Terjual</th>
                        <th class="p-4 border-b text-right">Total Pendapatan</th>
                        <th class="p-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                    @forelse ($courseReports as $report)
                        <tr class="hover:bg-gray-50/80 transition-colors course-row">
                            <td class="p-4 font-semibold text-gray-900 course-title-text">{{ $report->title }}</td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full font-bold text-xs">
                                    {{ $report->total_sold ?? 0 }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold text-gray-900">
                                Rp {{ number_format($report->total_revenue ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center"> <a href="{{ route('admin.pembelian.course_pdf', $report->id) }}"
                                    class="inline-flex items-center px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors duration-200 gap-1">
                                    <i class="fas fa-file-pdf text-[10px]"></i> Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-400 italic">Belum ada data materi.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-100 font-bold text-gray-800" id="courseTableFoot">
                    <tr>
                        <td colspan="2" class="p-4 text-right uppercase tracking-wider text-xs text-gray-500">Grand Total
                            Pendapatan</td>
                        <td class="p-4 text-right text-lg text-emerald-600 font-extrabold">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                        <td class="bg-gray-50"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- TAMPILAN MATERI: HP (CARDS) --}}
        <div class="block md:hidden p-4 space-y-3 bg-gray-50/30" id="mobileCourseCardsContainer">
            @forelse ($courseReports as $report)
                <div
                    class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex justify-between items-center mobile-course-card">
                    <div class="space-y-1 max-w-[60%]">
                        <h5 class="font-bold text-gray-900 text-sm leading-tight break-words mobile-course-title">
                            {{ $report->title }}</h5>
                        <p class="text-xs text-gray-500">Terjual: <span
                                class="font-bold text-indigo-600">{{ $report->total_sold ?? 0 }}</span></p>
                    </div>
                    <div class="text-right flex flex-col items-end gap-2">
                        <div>
                            <span class="text-[11px] text-gray-400 block">Pendapatan</span>
                            <span class="font-extrabold text-gray-900 text-sm">Rp
                                {{ number_format($report->total_revenue ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('admin.pembelian.course_pdf', $report->id) }}"
                            class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-[10px] font-bold rounded-md shadow-sm transition-colors duration-200 gap-1">
                            <i class="fas fa-file-pdf text-[9px]"></i> Cetak
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-400 text-sm italic">Belum ada data materi.</div>
            @endforelse

            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 flex justify-between items-center mt-4"
                id="mobileCourseGrandTotal">
                <span class="text-xs font-bold text-emerald-800 tracking-wide uppercase">Grand Total</span>
                <span class="text-base font-black text-emerald-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>


    {{-- KONTEN DETAIL TRANSAKSI DENGAN FITUR SEARCH BAR RESPONSIF (LAPTOP & HP) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div
            class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h4 class="font-bold text-gray-800">Detail Transaksi Student</h4>

            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </span>
                <input type="text" id="studentSearchInput" placeholder="Cari nama student atau materi..."
                    class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>
        </div>

        {{-- TAMPILAN TRANSAKSI: LAPTOP (TABLE) --}}
        <div class="hidden md:block">
            <table class="w-full text-left border-collapse" id="dataTable">
                <thead class="bg-gray-50 text-gray-700 text-sm font-semibold">
                    <tr>
                        <th class="p-4 border-b">Tanggal</th>
                        <th class="p-4 border-b">Nama Student</th>
                        <th class="p-4 border-b">Materi</th>
                        <th class="p-4 border-b text-right">Harga Beli</th>
                        <th class="p-4 border-b text-center">Status</th>
                        <th class="p-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                    @forelse ($transactionDetails as $trans)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="p-4 text-gray-500 whitespace-nowrap">{{ $trans->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="p-4 font-semibold text-gray-900">{{ $trans->user->name }}</td>
                            <td class="p-4 text-gray-700 font-medium">{{ $trans->course->title }}</td>
                            <td class="p-4 text-right font-semibold text-gray-900">
                                Rp {{ number_format($trans->price_bought, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                <span
                                    class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full font-bold text-xs">
                                    <i class="fas fa-check-circle mr-1 text-[10px]"></i> Success
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('admin.pembelian.download', $trans->id) }}"
                                    class="inline-flex items-center px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors duration-200 gap-1">
                                    <i class="fas fa-file-download text-[10px]"></i> Laporan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400 italic">Belum ada transaksi mahasiswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TAMPILAN TRANSAKSI: HP (CARDS) --}}
        <div class="block md:hidden p-4 space-y-3 bg-gray-50/30" id="mobileCardsContainer">
            @forelse ($transactionDetails as $trans)
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-3 relative mobile-card">
                    <div class="flex justify-between items-start">
                        <div class="max-w-[70%]">
                            <h5 class="font-bold text-gray-900 text-sm leading-tight student-name">
                                {{ $trans->user->name }}
                            </h5>
                            <span
                                class="text-[10px] text-gray-400 block mt-0.5">{{ $trans->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <span
                            class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full font-bold text-[10px]">
                            <i class="fas fa-check-circle mr-1 text-[9px]"></i> Success
                        </span>
                    </div>

                    <div class="pt-2 border-t border-gray-50 flex justify-between items-end">
                        <div class="max-w-[50%]">
                            <span class="text-[10px] text-gray-400 block leading-none mb-1">Materi</span>
                            <p class="text-xs text-gray-700 font-semibold truncate course-title">
                                {{ $trans->course->title }}</p>
                        </div>
                        <div class="text-right flex flex-col items-end gap-2">
                            <div>
                                <span class="text-[10px] text-gray-400 block leading-none mb-1">Harga Beli</span>
                                <p class="text-sm font-extrabold text-gray-900">Rp
                                    {{ number_format($trans->price_bought, 0, ',', '.') }}</p>
                            </div>
                            <a href="{{ route('admin.pembelian.download', $trans->id) }}"
                                class="inline-flex items-center px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-md shadow-sm transition-colors duration-200 gap-1">
                                <i class="fas fa-file-download text-[9px]"></i> Laporan
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-400 text-sm italic">Belum ada transaksi mahasiswa.</div>
            @endforelse
        </div>
    </div>

    {{-- SCRIPT LIVE SEARCH UNTUK RINGKASAN MATERI & DETAIL STUDENT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ----------------------------------------------------
            // LOGIKA 1: LIVE SEARCH UNTUK RINGKASAN MATERI
            // ----------------------------------------------------
            const courseSearchInput = document.getElementById('courseSearchInput');
            const desktopCourseRows = document.querySelectorAll('#courseDataTable tbody .course-row');
            const mobileCourseCards = document.querySelectorAll('#mobileCourseCardsContainer .mobile-course-card');
            const courseTableFoot = document.getElementById('courseTableFoot');
            const mobileCourseGrandTotal = document.getElementById('mobileCourseGrandTotal');

            courseSearchInput.addEventListener('input', function() {
                const filterValue = courseSearchInput.value.toLowerCase().trim();

                // Sembunyikan grand total kalau user sedang menyaring/mencari spesifik
                if (filterValue !== '') {
                    if (courseTableFoot) courseTableFoot.style.display = 'none';
                    if (mobileCourseGrandTotal) mobileCourseGrandTotal.style.display = 'none';
                } else {
                    if (courseTableFoot) courseTableFoot.style.display = '';
                    if (mobileCourseGrandTotal) mobileCourseGrandTotal.style.display = '';
                }

                // Filter tabel desktop
                desktopCourseRows.forEach(row => {
                    const courseTitle = row.querySelector('.course-title-text').textContent
                        .toLowerCase();
                    if (courseTitle.includes(filterValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Filter kartu mobile
                mobileCourseCards.forEach(card => {
                    const courseTitle = card.querySelector('.mobile-course-title').textContent
                        .toLowerCase();
                    if (courseTitle.includes(filterValue)) {
                        card.style.setProperty('display', 'flex', 'important');
                    } else {
                        card.style.setProperty('display', 'none', 'important');
                    }
                });
            });


            // ----------------------------------------------------
            // LOGIKA 2: LIVE SEARCH UNTUK DETAIL TRANSAKSI STUDENT
            // ----------------------------------------------------
            const studentSearchInput = document.getElementById('studentSearchInput');
            const desktopStudentRows = document.querySelectorAll('#dataTable tbody tr');
            const mobileStudentCards = document.querySelectorAll('#mobileCardsContainer .mobile-card');

            studentSearchInput.addEventListener('input', function() {
                const filterValue = studentSearchInput.value.toLowerCase().trim();

                // Filter tabel desktop
                desktopStudentRows.forEach(row => {
                    if (row.cells.length < 6) return; // Lewati baris empty state

                    const studentName = row.cells[1].textContent.toLowerCase();
                    const courseTitle = row.cells[2].textContent.toLowerCase();

                    if (studentName.includes(filterValue) || courseTitle.includes(filterValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Filter kartu mobile
                mobileStudentCards.forEach(card => {
                    const studentName = card.querySelector('.student-name').textContent
                        .toLowerCase();
                    const courseTitle = card.querySelector('.course-title').textContent
                        .toLowerCase();

                    if (studentName.includes(filterValue) || courseTitle.includes(filterValue)) {
                        card.style.setProperty('display', 'block', 'important');
                    } else {
                        card.style.setProperty('display', 'none', 'important');
                    }
                });
            });
        });
    </script>
@endsection
