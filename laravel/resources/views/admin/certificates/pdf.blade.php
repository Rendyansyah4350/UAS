<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            size: a4 landscape;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', Arial, sans-serif;
            width: 29.7cm;
            height: 21cm;
            overflow: hidden;
            background-image: url('{{ public_path('assets/images/certificate/certificate-eduvan.png') }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Container utama pengunci halaman */
        .container {
            position: relative;
            width: 29.7cm;
            height: 21cm;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* Base style untuk block text absolute peniru preview */
        .absolute-center {
            position: absolute;
            left: 0;
            right: 0;
            width: 100%;
            text-align: center;
        }

        /* ==================== KONTEN TEXT (MENGGUNAKAN % TINGGI HALAMAN) ==================== */

        /* 1. Teks "Dengan ini menyatakan bahwa" */
        .pembuka {
            top: 40.5%;
            /* Menaruh teks pas di bawah pita emas */
            font-size: 15px;
            color: #2c3e50;
            margin: 0;
            padding: 0;
            font-weight: 500;
        }

        /* 2. Nama Peserta */
        .student-name {
            top: 44%;
            /* Pas nangkring di atas garis tipis tengah bawaan template */
            font-size: 46px;
            font-weight: bold;
            color: #1a252f;
            margin: 0;
            margin-top: 40px;
            padding: 0;
            line-height: 1;
        }

        /* 3. Teks Keterangan Kursus */
        .keterangan {
            top: 59%;
            /* Tepat di bawah garis tipis tengah */
            color: #7f8c8d;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        /* 4. Judul Kursus */
        .course-title {
            top: 65.5%;
            /* Pas di atas garis panjang abu-abu horizontal */
            font-size: 28px;
            font-weight: bold;
            color: #1d4ed8;
            text-transform: uppercase;
            margin: 0;
            padding: 0;
            letter-spacing: 0.5px;
        }

        /* ==================== SECTION VALIDASI BAWAH ==================== */

        /* 5. Logo EduVan & Nomor Sertifikat */
        .cert-info-box {
            position: absolute;
            top: 84%;
            left: 0;
            right: 0;
            text-align: center;
        }

        .cert-info-group {
            display: inline-block;
            text-align: left;
            height: 40px;
            padding-right: 40px;
        }

        .footer-logo {
            float: left;
            width: 36px;
            height: auto;
            margin-right: 10px;
        }

        .meta-text-center {
            float: left;
            margin-top: 1px;
        }

        .footer-label {
            font-size: 9px;
            color: #7f8c8d;
            text-transform: uppercase;
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
            line-height: 1;
        }

        .value-no {
            font-size: 13px;
            font-weight: bold;
            color: #1c3d5a;
            font-family: monospace;
            display: block;
            line-height: 1;
        }

        /* 6. Tanggal Terbit */
        .date-container {
            top: 88.5%;
            /* Pas nangkring di atas garis pendek paling bawah */
        }

        .date-text {
            font-size: 14px;
            color: #1d4ed8;
            font-weight: bold;
            display: block;
            margin-top: 2px;
            line-height: 1;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="absolute-center pembuka">
            Dengan ini menyatakan bahwa
        </div>

        <div class="absolute-center student-name">
            {{ $certificate->user->name }}
        </div>

        <div class="absolute-center keterangan">
            telah berhasil menyelesaikan persyaratan kursus untuk
        </div>

        <div class="absolute-center course-title">
            {{ $certificate->course->title }}
        </div>

        <div class="cert-info-box">
            <div class="cert-info-group">
                <img class="footer-logo" src="{{ public_path('assets/images/eduvan.png') }}" alt="Logo EduVan">
                <div class="meta-text-center">
                    <span class="footer-label">Nomor Sertifikat</span>
                    <span class="value-no">{{ $certificate->certificate_number }}</span>
                </div>
            </div>
        </div>

        <div class="absolute-center date-container">
            <span class="footer-label">Tanggal Terbit</span>
            <span class="date-text">{{ \Carbon\Carbon::parse($certificate->issued_at)->format('d F Y') }}</span>
        </div>

    </div>
</body>

</html>
