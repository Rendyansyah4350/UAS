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
            font-family: 'Helvetica', sans-serif;
            width: 29.7cm;
            height: 21cm;
            overflow: hidden;
        }

        /* Border Utama */
        .border-purple {
            position: absolute;
            top: 0.5cm;
            left: 0.5cm;
            right: 0.5cm;
            bottom: 0.5cm;
            border: 15pt solid #4f46e5;
            box-sizing: border-box;
        }

        .border-thin {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 2pt solid #4f46e5;
            text-align: center;
        }

        /* Konten diatur manual jaraknya */
        .title {
            margin-top: 50px;
            font-size: 50pt;
            font-weight: bold;
            color: #1e1b4b;
        }

        .subtitle {
            font-size: 14pt;
            letter-spacing: 10px;
            color: #6366f1;
            text-transform: uppercase;
        }

        .name-box {
            margin-top: 40px;
        }

        .student-name {
            font-size: 35pt;
            font-weight: bold;
            color: #000;
            border-bottom: 2pt solid #eee;
            display: inline-block;
            padding: 0 40px;
            margin-bottom: 10px;
        }

        .course-title {
            font-size: 22pt;
            font-weight: bold;
            color: #1e1b4b;
            text-transform: uppercase;
        }

        /* Footer dikunci posisinya */
        .footer {
            position: absolute;
            bottom: 40px;
            width: 100%;
            padding: 0 60px;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .label {
            font-size: 9pt;
            color: #9ca3af;
            text-transform: uppercase;
        }

        .value {
            font-size: 11pt;
            font-weight: bold;
            color: #111827;
            display: block;
        }
    </style>
</head>

<body>
    <div class="border-purple">
        <div class="border-thin">
            <h1 class="title">CERTIFICATE</h1>
            <p class="subtitle">OF ACHIEVEMENT</p>

            <div class="name-box">
                <p style="color: #6b7280; font-style: italic;">Sertifikat ini diberikan kepada:</p>
                <div class="student-name">{{ $certificate->user->name }}</div>
            </div>

            <p style="color: #4b5563; margin-top: 20px;">Telah berhasil menyelesaikan kursus pada platform
                <strong>EduVan</strong>:</p>
            <h2 class="course-title">{{ $certificate->course->title }}</h2>

            <div class="footer">
                <table>
                    <tr>
                        <td align="left">
                            <span class="label">Nomor Sertifikat</span>
                            <span class="value">{{ $certificate->certificate_number }}</span>
                        </td>
                        <td align="center">
                            <span class="label">Verified By</span>
                            <span class="value" style="color: #4f46e5;">EduVan Official</span>
                        </td>
                        <td align="right">
                            <span class="label">Tanggal Terbit</span>
                            <span
                                class="value">{{ \Carbon\Carbon::parse($certificate->issued_at)->format('d F Y') }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
