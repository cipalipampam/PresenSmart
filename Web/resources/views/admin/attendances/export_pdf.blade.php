<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Attendance Records</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Role</th>
                <th>Status</th>
                <th>Time</th>
                <th>Approved</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $attendance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $attendance->user->name }}</td>
                    <td>
                        @if($attendance->user->hasRole('siswa')) Student
                        @elseif($attendance->user->hasRole('guru')) Teacher
                        @elseif($attendance->user->hasRole('staff')) Staff
                        @endif
                    </td>
                    <td>{{ ucfirst($attendance->status) }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->recorded_at)->format('d M Y, H:i') }}</td>
                    <td>
                        @if($attendance->is_approved === null) N/A
                        @else {{ $attendance->is_approved ? 'Yes' : 'No' }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
