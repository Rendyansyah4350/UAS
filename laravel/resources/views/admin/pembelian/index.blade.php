@extends('layouts.admin')

@section('content')
    <div class="container-fluid p-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Laporan Pembelian</h1>
        </div>
        <a href="{{ route('admin.pembelian.pdf') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
            <i class="fas fa-file-pdf mr-2"></i> Cetak PDF
        </a>
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Pendapatan Per Materi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>Materi</th>
                                <th class="text-center">Total Terjual</th>
                                <th class="text-right">Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($courseReports as $report)
                                <tr>
                                    <td class="font-weight-bold text-dark">{{ $report->title }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-primary px-3">{{ $report->total_sold ?? 0 }}</span>
                                    </td>
                                    <td class="text-right font-weight-bold text-dark">
                                        Rp {{ number_format($report->total_revenue ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada data materi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light text-dark">
                            <tr class="font-weight-bold">
                                <td colspan="2" class="text-right">GRAND TOTAL PENDAPATAN</td>
                                <td class="text-right text-success" style="font-size: 1.2rem;">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4 border-left-dark">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-dark">Detail Transaksi Mahasiswa</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Mahasiswa</th>
                                <th>Materi</th>
                                <th class="text-right">Harga Beli</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactionDetails as $trans)
                                <tr>
                                    <td>{{ $trans->created_at->format('d M Y, H:i') }}</td>
                                    <td class="font-weight-bold text-secondary">{{ $trans->user->name }}</td>
                                    <td>{{ $trans->course->title }}</td>
                                    <td class="text-right text-dark">
                                        Rp {{ number_format($trans->price_bought, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success px-3">
                                            <i class="fas fa-check-circle mr-1"></i> Success
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada transaksi mahasiswa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
