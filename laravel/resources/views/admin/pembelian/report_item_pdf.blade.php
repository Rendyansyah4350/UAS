<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Kuitansi Laporan Transaksi EduVan</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #374151;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            background-color: #ffffff;
        }

        .invoice-card {
            max-width: 750px;
            margin: auto;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .header-container {
            border-bottom: 2px dashed #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .brand-section {
            vertical-align: middle;
        }

        .logo-img {
            vertical-align: middle;
            margin-right: 12px;
            width: 42px;
            height: auto;
        }

        .brand-text {
            display: inline-block;
            vertical-align: middle;
        }

        .brand-name {
            font-size: 26px;
            font-weight: 800;
            color: #4f46e5;
            line-height: 1.1;
        }

        .brand-tagline {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .invoice-title {
            text-align: right;
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .meta-section {
            margin-bottom: 30px;
        }

        .meta-block {
            width: 48%;
            vertical-align: top;
        }

        .meta-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #9ca3af;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .meta-value {
            font-size: 13.5px;
            color: #1f2937;
        }

        /* Layout Detail Kursus (Pengganti Tabel) */
        .item-details-box {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .course-title-label {
            font-size: 12px;
            color: #4f46e5;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .course-main-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .course-description {
            font-size: 13px;
            color: #6b7280;
        }

        /* Box Rincian Pembayaran */
        .payment-summary {
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
            margin-top: 20px;
        }

        .summary-row {
            margin-bottom: 8px;
        }

        .summary-label {
            font-size: 14px;
            color: #4b5563;
        }

        .summary-value {
            font-size: 14px;
            text-align: right;
            font-weight: 600;
            color: #1f2937;
        }

        .grand-total-row {
            background-color: #ecfdf5;
            border-radius: 4px;
            padding: 10px 15px;
            margin-top: 12px;
        }

        .grand-total-label {
            font-size: 15px;
            font-weight: 700;
            color: #065f46;
        }

        .grand-total-value {
            font-size: 18px;
            font-weight: 800;
            color: #059669;
            text-align: right;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 15px;
        }
    </style>
</head>

<body>

    <div class="invoice-card">

        <table style="width: 100%;" class="header-container">
            <tr>
                <td class="brand-section">
                    <img class="logo-img" src="{{ public_path('assets/images/Eduvan.png') }}" alt="EduVan Logo">
                    <div class="brand-text">
                        <div class="brand-name">EduVan</div>
                        <div class="brand-tagline">Learning Course Marketplace</div>
                    </div>
                </td>
                <td class="invoice-title" style="vertical-align: middle;">
                    Kuitansi Pembayaran
                </td>
            </tr>
        </table>

        <table style="width: 100%;" class="meta-section">
            <tr>
                <td class="meta-block">
                    <div class="meta-label">Diterbitkan Kepada:</div>
                    <div class="meta-value">
                        <strong>{{ $trans->user->name }}</strong><br>
                        Email: {{ $trans->user->email }}<br>
                        <span style="color: #6b7280; font-size: 12px;">Status: Universitas Student</span>
                    </div>
                </td>
                <td class="meta-block" style="text-align: right;">
                    <div class="meta-label">Detail Dokumen:</div>
                    <div class="meta-value">
                        Nomor: <span
                            style="font-family: monospace; font-weight: bold; color: #4f46e5;">#TRX-{{ $trans->id }}</span><br>
                        Tanggal: {{ $trans->created_at->format('d F Y, H:i') }} WIB<br>
                        Status: <span
                            style="color: #10b981; font-weight: bold; font-size: 12px; background-color: #d1fae5; padding: 2px 8px; border-radius: 4px;">SUCCESS</span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="item-details-box">
            <div class="course-title-label">Materi Pembelajaran</div>
            <div class="course-main-title">{{ $trans->course->title }}</div>
            <div class="course-description">
                Selamat, Anda telah mendapatkan hak akses penuh secara permanen untuk modul pembelajaran digital ini di
                dalam sistem platform EduVan.
            </div>
        </div>

        <div class="payment-summary">
            <table style="width: 100%; border-collapse: collapse;">
                <tr class="summary-row">
                    <td class="summary-label">Subtotal Materi</td>
                    <td class="summary-value">Rp {{ number_format($trans->price_bought, 0, ',', '.') }}</td>
                </tr>
                <tr class="summary-row">
                    <td class="summary-label">Potongan / Diskon</td>
                    <td class="summary-value" style="color: #9ca3af;">Rp 0</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table style="width: 100%;" class="grand-total-row">
                            <tr>
                                <td class="grand-total-label">Total Pembayaran</td>
                                <td class="grand-total-value">Rp {{ number_format($trans->price_bought, 0, ',', '.') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Kuitansi ini dikeluarkan secara sah melalui dasbor monitoring pusat EduVan.<br>
            <span style="color: #cbd5e1;">Dicetak otomatis pada: {{ $downloaded_at }}</span>
        </div>

    </div>

</body>

</html>
