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

        /* Container utama pengunci halaman anti-jebol */
        .container {
            position: relative;
            width: 29.7cm;
            height: 21cm;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* ==================== KONTEN TENGAH ==================== */

        .content-wrapper {
            width: 100%;
            padding-top: 312px;
            text-align: center;
        }

        .pembuka {
            font-size: 14pt;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1;
        }

        .student-name {
            font-size: 44pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 15px;
            margin-bottom: 0;
            line-height: 1;
        }

        .name-line-container {
            margin-top: 12px;
            text-align: center;
        }

        .name-line {
            display: inline-block;
            width: 500px;
            border-bottom: 1.5pt solid #cbd5e1;
            position: relative;
        }

        .name-line::after {
            content: "◆";
            position: absolute;
            color: #d97706;
            font-size: 10pt;
            top: -7px;
            left: 50%;
            margin-left: -6px;
            background-color: #ffffff;
            padding: 0 6px;
        }

        .keterangan {
            color: #475569;
            font-size: 13pt;
            margin-top: 25px;
            margin-bottom: 0;
            padding: 0;
            line-height: 1;
        }

        .course-box {
            margin-top: 45px;
            text-align: center;
        }

        .course-title-wrapper {
            display: inline-block;
            position: relative;
            padding: 0 70px;
        }

        .course-line-left {
            position: absolute;
            left: 0;
            top: 50%;
            width: 45px;
            border-bottom: 2pt solid #d97706;
            margin-top: -1px;
        }

        .course-line-left::after {
            content: "◆";
            position: absolute;
            color: #d97706;
            font-size: 8pt;
            right: -4px;
            top: -6px;
        }

        .course-line-right {
            position: absolute;
            right: 0;
            top: 50%;
            width: 45px;
            border-bottom: 2pt solid #d97706;
            margin-top: -1px;
        }

        .course-line-right::before {
            content: "◆";
            position: absolute;
            color: #d97706;
            font-size: 8pt;
            left: -4px;
            top: -6px;
        }

        .course-title {
            font-size: 26pt;
            font-weight: bold;
            color: #1d4ed8;
            text-transform: uppercase;
            margin: 0;
            padding: 0;
            line-height: 1;
            letter-spacing: 0.5px;
        }


        /* ==================== SECTION VALIDASI (TENGAH BAWAH) ==================== */

        /* Blok Utama Komponen Tengah Bawah */
        .validation-center-box {
            position: absolute;
            bottom: 100px;
            /* Diatur agar pas dengan posisi tinggi area dekorasi bawah */
            left: 0;
            right: 0;
            text-align: center;
        }

        /* 1. Atas Garis Bawaan Template: Group Logo & Nomor Sertifikat */
        .cert-info-group {
            display: inline-block;
            height: 45px;
            margin-bottom: 35px;
            /* Memberi ruang space tepat di atas garis tengah bawaan */
            text-align: left;
        }

        .footer-logo {
            float: left;
            width: 42px;
            height: 42px;
            margin-right: 12px;
        }

        .footer-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .meta-text-center {
            float: left;
            margin-top: 2px;
        }

        .footer-label {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }

        .value-no {
            font-size: 11pt;
            font-weight: bold;
            color: #1e3a8a;
            display: block;
            line-height: 1;
        }

        /* 2. Bawah Garis Bawaan Template: Tanggal Terbit */
        .date-container {
            display: block;
            margin-top: 5px;
            /* Menyesuaikan agar teks langsung berada di bawah garis pendek */
        }

        .date-text {
            font-size: 12.5pt;
            color: #1d4ed8;
            font-weight: bold;
            display: block;
            margin-top: 3px;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="content-wrapper">
            <p class="pembuka">Dengan ini menyatakan bahwa</p>

            <h1 class="student-name">{{ $certificate->user->name }}</h1>

            <div class="name-line-container">
                <div class="name-line"></div>
            </div>

            <p class="keterangan">telah berhasil menyelesaikan persyaratan kursus untuk</p>

            <div class="course-box">
                <div class="course-title-wrapper">
                    <div class="course-line-left"></div>
                    <h2 class="course-title">{{ $certificate->course->title }}</h2>
                    <div class="course-line-right"></div>
                </div>
            </div>
        </div>

        <div class="validation-center-box">

            <div class="cert-info-group">
                <div class="footer-logo">
                    <img src="{{ public_path('assets/images/eduvan.png') }}" alt="Logo EduVan">
                </div>
                <div class="meta-text-center">
                    <span class="footer-label">Nomor Sertifikat</span>
                    <span class="value-no">{{ $certificate->certificate_number }}</span>
                </div>
            </div>

            <div class="date-container">
                <span class="footer-label">Tanggal Terbit</span>
                <span class="date-text">{{ \Carbon\Carbon::parse($certificate->issued_at)->format('d F Y') }}</span>
            </div>

        </div>

    </div>
</body>

</html>
