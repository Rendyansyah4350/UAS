<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pembelian</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN PEMBELIAN KURSUS</h1>
        <p>Admin Dashboard</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Student</th>
                <th>Kursus</th>
                <th>Harga Beli</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $index => $t)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $t->created_at->format('d-m-Y') }}</td>
                    <td>{{ $t->user->name }}</td>
                    <td>{{ $t->course->title }}</td>
                    <td>Rp {{ number_format($t->price_bought) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <h3>Total Pendapatan: Rp {{ number_format($totalRevenue) }}</h3>
    </div>
</body>

</html>
