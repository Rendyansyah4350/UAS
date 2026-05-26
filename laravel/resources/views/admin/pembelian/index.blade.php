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

    {{-- NOTIFIKASI ALERT BERHASIL / GAGAL --}}
    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-excurtion-circle text-red-500 text-base"></i>
            {{ session('error') }}
        </div>
    @endif

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
                <tbody class="bg-gray-50 border-t border-gray-100 font-bold text-gray-800" id="courseTableFoot">
                    <tr>
                        <td colspan="2" class="p-4 text-right uppercase tracking-wider text-xs text-gray-500">Grand Total
                            Pendapatan</td>
                        <td class="p-4 text-right text-lg text-emerald-600 font-extrabold">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                        <td class="bg-gray-50"></td>
                    </tr>
                </tbody>
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


    {{-- ANTREAN VERIFIKASI PEMBAYARAN MANUAL (CHECKING ADMIN) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-100 bg-amber-50/40">
            <h4 class="font-bold text-amber-800 flex items-center gap-2">
                <i class="fas fa-clock text-amber-500"></i> Antrean Verifikasi Pembayaran Manual
            </h4>
            <p class="text-gray-500 text-xs mt-1">Daftar pendaftaran kelas mahasiswa yang menunggu konfirmasi bukti transfer.</p>
        </div>

        {{-- VERIFIKASI: LAPTOP (TABLE) --}}
        <div class="hidden md:block">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 text-sm font-semibold">
                    <tr>
                        <th class="p-4 border-b">Tanggal Pengajuan</th>
                        <th class="p-4 border-b">Nama Student</th>
                        <th class="p-4 border-b">Materi Kursus</th>
                        <th class="p-4 border-b text-right">Harga Beli</th>
                        <th class="p-4 border-b text-center">Bukti Transfer</th>
                        <th class="p-4 border-b text-center">Aksi Konfirmasi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                    @forelse ($pendingVerifications as $verify)
                        <tr class="hover:bg-amber-50/20 transition-colors">
                            <td class="p-4 text-gray-500 whitespace-nowrap">{{ $verify->created_at->format('d M Y, H:i') }}</td>
                            <td class="p-4 font-semibold text-gray-900">{{ $verify->user->name }}</td>
                            <td class="p-4 text-gray-700 font-medium">{{ $verify->course->title }}</td>
                            <td class="p-4 text-right font-semibold text-gray-900">
                                Rp {{ number_format($verify->price_bought, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                @if($verify->proof_of_payment)
                                        <button type="button"
                                        onclick="openProofModal('{{ asset('uploads/proofs/' . $verify->proof_of_payment) }}')"
                                                class="inline-flex items-center px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow-sm transition-colors gap-1">
                                            <i class="fas fa-eye text-[10px]"></i> Lihat Bukti
                                        </button>
                                @else
                                    <span class="text-gray-400 italic text-xs">Tidak ada file</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <form action="{{ route('admin.pembelian.updateStatus', $verify->id) }}" method="POST" onsubmit="return confirm('Setujui transaksi ini?')">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="success">
                                        <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                            <i class="fas fa-check mr-1"></i> Terima
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.pembelian.updateStatus', $verify->id) }}" method="POST" onsubmit="return confirm('Tolak transaksi ini?')">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Fail">
                                        <button type="submit" class="px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                            <i class="fas fa-times mr-1"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Tidak ada antrean verifikasi pembayaran saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- VERIFIKASI: HP (CARDS) --}}
        <div class="block md:hidden p-4 space-y-3 bg-gray-50/30">
            @forelse ($pendingVerifications as $verify)
                <div class="bg-white p-4 rounded-xl border border-amber-100 shadow-sm space-y-3 relative">
                    <div class="flex justify-between items-start">
                        <div class="max-w-[70%]">
                            <h5 class="font-bold text-gray-900 text-sm leading-tight">{{ $verify->user->name }}</h5>
                            <span class="text-[10px] text-gray-400 block mt-0.5">{{ $verify->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded-full font-bold text-[10px]">
                            {{ ucfirst($verify->status) }}
                        </span>
                    </div>

                    <div class="pt-2 border-t border-gray-50 space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400">Materi:</span>
                            <span class="text-gray-700 font-semibold truncate max-w-[180px]">{{ $verify->course->title }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400">Total Tagihan:</span>
                            <span class="font-extrabold text-gray-900">Rp {{ number_format($verify->price_bought, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-1">
                            <span class="text-gray-400 text-xs">File:</span>
                            @if($verify->proof_of_payment)
                                    <button type="button"
                                            onclick="openProofModal('{{ asset($verify->proof_of_payment) }}')"
                                            class="w-full text-center px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow-sm transition-colors block">
                                        Lihat Bukti
                                    </button>
                            @else
                                <span class="text-gray-400 italic text-[11px]">Tidak ada</span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex gap-2">
                        <form action="{{ route('admin.pembelian.updateStatus', $verify->id) }}" method="POST" class="w-1/2" onsubmit="return confirm('Setujui transaksi ini?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="success">
                            <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg text-center shadow-sm">
                                <i class="fas fa-check mr-1"></i> Terima
                            </button>
                        </form>
                        <form action="{{ route('admin.pembelian.updateStatus', $verify->id) }}" method="POST" class="w-1/2" onsubmit="return confirm('Tolak transaksi ini?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="Fail">
                            <button type="submit" class="w-full py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg text-center shadow-sm">
                                <i class="fas fa-times mr-1"></i> Tolak
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-400 text-sm italic bg-white rounded-xl border border-dashed">Tidak ada antrean verifikasi.</div>
            @endforelse
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


    {{-- MODAL COMPONENT UNTUK PREVIEW BUKTI TRANSFER --}}
    <div id="proofModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div onclick="closeProofModal()" class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-800" id="modal-title">Bukti Transfer Pembayaran</h3>
                    <button type="button" onclick="closeProofModal()" class="text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                        <i class="fas fa-times text-base"></i>
                    </button>
                </div>
                <div class="p-4 bg-white flex justify-center items-center max-h-[70vh] overflow-y-auto">
                    <img id="modalProofImage" src="" alt="Bukti Transfer Mahasiswa" class="max-w-full h-auto rounded-xl border shadow-inner">
                </div>
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-100 flex justify-end">
                    <button type="button" onclick="closeProofModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- SCRIPT LIVE SEARCH & MODAL CONTROLLER --}}
    <script>
        // MODAL INTERACTION
        function openProofModal(imageUrl) {
            document.getElementById('modalProofImage').src = imageUrl;
            document.getElementById('proofModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Lock scrolling background
        }

        function closeProofModal() {
            document.getElementById('proofModal').classList.add('hidden');
            document.getElementById('modalProofImage').src = '';
            document.body.style.overflow = ''; // Unlock scrolling background
        }

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
