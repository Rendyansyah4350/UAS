<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan Materi EduVan</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #374151;
            line-height: 1.5;
            margin: 0;
            padding: 15px;
            font-size: 13px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 6px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 15px;
        }

        .logo-img {
            vertical-align: middle;
            margin-right: 12px;
            width: 42px;
            height: auto;
        }

        .brand-name {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            display: inline-block;
            vertical-align: middle;
        }

        .report-title {
            text-align: right;
            font-size: 14px;
            text-transform: uppercase;
            color: #4b5563;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .course-summary-box {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
        }

        .summary-title {
            font-size: 11px;
            text-transform: uppercase;
            color: #9ca3af;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .course-main-title {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 6px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .details-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #e5e7eb;
            font-size: 12px;
        }

        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #f3f4f6;
        }

        .total-section-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .total-section-table td {
            padding: 8px 10px;
        }

        .footer {
            margin-top: 45px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 12px;
        }
    </style>
</head>

<body>

    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td>
                    <img class="logo-img" src="{{ public_path('assets/images/Eduvan.png') }}" alt="EduVan Logo">
                    <span class="brand-name">EduVan</span>
                </td>
                <td class="report-title">
                    Laporan Penjualan Materi
                </td>
            </tr>
        </table>

        <div class="course-summary-box">
            <div class="summary-title">Materi / Kursus Terpilih:</div>
            <div class="course-main-title">{{ $course->title }}</div>
            <div style="font-size: 13px; color: #4b5563;">
                Total Kuantitas Terjual: <strong style="color: #4f46e5;">{{ $totalSold }} Kali</strong>
            </div>
        </div>

        <div class="summary-title" style="margin-bottom: 8px;">Daftar Riwayat Student Pembeli:</div>
        <table class="details-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 32%;">Tanggal Membeli</th>
                    <th style="width: 35%;">Nama Student</th>
                    <th style="width: 25%; text-align: right;">Harga Beli</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($course->enrollments as $index => $enroll)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="color: #6b7280;">{{ $enroll->created_at->format('d M Y, H:i') }} WIB</td>
                        <td style="font-weight: bold; color: #111827;">{{ $enroll->user->name }}</td>
                        <td style="text-align: right; font-weight: 600;">Rp
                            {{ number_format($enroll->price_bought, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4"
                            style="text-align: center; color: #9ca3af; font-style: italic; padding: 20px;">
                            Belum ada riwayat pembelian mahasiswa untuk materi ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="total-section-table">
            <tr>
                <td style="width: 50%;"></td>
                <td
                    style="width: 25%; text-align: right; font-weight: bold; color: #4b5563; font-size: 12px; text-transform: uppercase;">
                    Total Pendapatan:
                </td>
                <td
                    style="width: 25%; text-align: right; font-size: 16px; font-weight: 900; color: #059669; border-top: 2px solid #e5e7eb;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <div class="footer">
            Data laporan ini bersifat resmi dan ditarik langsung dari database EduVan.<br>
            Dicetak otomatis pada: {{ $downloaded_at }}
        </div>
    </div>

</body>

</html>
