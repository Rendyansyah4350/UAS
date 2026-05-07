@extends('layouts.admin') {{-- Pastikan ini nama file layout admin kamu --}}

@section('content')
    <div class="container-fluid p-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Laporan Pembelian</h1>
        </div>

        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Ringkasan Pendapatan Materi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>Materi</th>
                                <th class="text-center">Total Terjual</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courseReports as $report)
                                <tr>
                                    <td class="font-weight-bold">{{ $report->title }}</td>
                                    <td class="text-center"><span class="badge badge-info">{{ $report->total_sold }}</span>
                                    </td>
                                    <td class="text-right">Rp
                                        {{ number_format($report->total_sold * $report->price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr class="font-weight-bold text-dark">
                                <td colspan="2" class="text-right">GRAND TOTAL</td>
                                <td class="text-right text-success" style="font-size: 1.2rem;">Rp
                                    {{ number_format($grandTotal, 0, ',', '.') }}</td>
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
                                <th>Materi yang Dibeli</th>
                                <th>Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactionDetails as $trans)
                                <tr>
                                    <td>{{ $trans->created_at->format('d M Y, H:i') }}</td>
                                    <td class="font-weight-bold text-secondary">{{ $trans->user->name }}</td>
                                    <td>{{ $trans->course->title }}</td>
                                    <td>Rp {{ number_format($trans->course->price, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-success px-3">
                                            <i class="fas fa-check-circle mr-1"></i> Success
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
