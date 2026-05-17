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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-100 bg-gray-50/50">
            <h4 class="font-bold text-gray-800">Ringkasan Pendapatan Per Materi</h4>
        </div>

        <div class="hidden md:block">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-700 text-sm font-semibold">
                    <tr>
                        <th class="p-4 border-b">Materi / Kursus</th>
                        <th class="p-4 border-b text-center">Total Terjual</th>
                        <th class="p-4 border-b text-right">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-100">
                    @forelse ($courseReports as $report)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="p-4 font-semibold text-gray-900">{{ $report->title }}</td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full font-bold text-xs">
                                    {{ $report->total_sold ?? 0 }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold text-gray-900">
                                Rp {{ number_format($report->total_revenue ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-400 italic">Belum ada data materi.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-100 font-bold text-gray-800">
                    <tr>
                        <td colspan="2" class="p-4 text-right uppercase tracking-wider text-xs text-gray-500">Grand Total
                            Pendapatan</td>
                        <td class="p-4 text-right text-lg text-emerald-600 font-extrabold">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    </tbody>
            </table>
        </div>

        <div class="block md:hidden p-4 space-y-3 bg-gray-50/30">
            @forelse ($courseReports as $report)
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex justify-between items-center">
                    <div class="space-y-1 max-w-[65%]">
                        <h5 class="font-bold text-gray-900 text-sm leading-tight break-words">{{ $report->title }}</h5>
                        <p class="text-xs text-gray-500">Terjual: <span
                                class="font-bold text-indigo-600">{{ $report->total_sold ?? 0 }}</span></p>
                    </div>
                    <div class="text-right">
                        <span class="text-[11px] text-gray-400 block">Pendapatan</span>
                        <span class="font-extrabold text-gray-900 text-sm">Rp
                            {{ number_format($report->total_revenue ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-400 text-sm italic">Belum ada data materi.</div>
            @endforelse

            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 flex justify-between items-center mt-4">
                <span class="text-xs font-bold text-emerald-800 tracking-wide uppercase">Grand Total</span>
                <span class="text-base font-black text-emerald-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>


    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50/50">
            <h4 class="font-bold text-gray-800">Detail Transaksi Mahasiswa</h4>
        </div>

        <div class="hidden md:block">
            <table class="w-full text-left border-collapse" id="dataTable">
                <thead class="bg-gray-50 text-gray-700 text-sm font-semibold">
                    <tr>
                        <th class="p-4 border-b">Tanggal</th>
                        <th class="p-4 border-b">Nama Mahasiswa</th>
                        <th class="p-4 border-b">Materi</th>
                        <th class="p-4 border-b text-right">Harga Beli</th>
                        <th class="p-4 border-b text-center">Status</th>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400 italic">Belum ada transaksi mahasiswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="block md:hidden p-4 space-y-3 bg-gray-50/30">
            @forelse ($transactionDetails as $trans)
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm space-y-3 relative">
                    <div class="flex justify-between items-start">
                        <div class="max-w-[70%]">
                            <h5 class="font-bold text-gray-900 text-sm leading-tight">{{ $trans->user->name }}</h5>
                            <span
                                class="text-[10px] text-gray-400 block mt-0.5">{{ $trans->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <span
                            class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full font-bold text-[10px]">
                            <i class="fas fa-check-circle mr-1 text-[9px]"></i> Success
                        </span>
                    </div>

                    <div class="pt-2 border-t border-gray-50 flex justify-between items-end">
                        <div class="max-w-[60%]">
                            <span class="text-[10px] text-gray-400 block leading-none mb-1">Materi</span>
                            <p class="text-xs text-gray-700 font-semibold truncate">{{ $trans->course->title }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-gray-400 block leading-none mb-1">Harga Beli</span>
                            <p class="text-sm font-extrabold text-gray-900">Rp
                                {{ number_format($trans->price_bought, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-400 text-sm italic">Belum ada transaksi mahasiswa.</div>
            @endforelse
        </div>
    </div>
@endsection
