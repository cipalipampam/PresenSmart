<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Presensi - {{ $tanggal }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .filter-info {
            margin-bottom: 15px;
            font-style: italic;
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</head>
<body>
    <h1>Laporan Presensi Siswa</h1>
    
    <div class="filter-info">
        <p>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</p>
        @if($kelas)
            <p>Kelas: {{ $kelas }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>NISN</th>
                <th>Kelas</th>
                <th>Status</th>
                <th>Waktu</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($presensis as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $p->user->name }}</td>
                    <td>{{ $p->user->nisn }}</td>
                    <td>{{ $p->user->kelas ?? '-' }}</td>
                    <td>{{ ucfirst($p->status) }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->waktu)->format('H:i:s') }}</td>
                    <td>{{ $p->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 