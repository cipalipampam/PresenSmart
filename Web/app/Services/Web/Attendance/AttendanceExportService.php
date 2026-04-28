<?php

namespace App\Services\Web\Attendance;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;
use Shuchkin\SimpleXLSXGen;
use Carbon\Carbon;

class AttendanceExportService
{
    /**
     * Export attendance data to the requested format.
     * Returns an HTTP response ready to stream/download.
     */
    public function export(string $type, Builder $query)
    {
        $data = $query->orderBy('recorded_at', 'desc')->get();

        [$dataArray, $csvArray] = $this->buildRows($data);

        return match ($type) {
            'excel' => $this->toExcel($dataArray),
            'csv'   => $this->toCsv($csvArray),
            'pdf'   => $this->toPdf($data),
            'zip'   => $this->toZip($dataArray, $csvArray, $data),
            default => abort(400, 'Unsupported export type'),
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function buildRows($records): array
    {
        $header = [
            '<center><b>No</b></center>',
            '<center><b>Name</b></center>',
            '<center><b>Role</b></center>',
            '<center><b>Status</b></center>',
            '<center><b>Time</b></center>',
            '<center><b>Notes</b></center>',
            '<center><b>Approved</b></center>',
        ];

        $dataArray = [$header];
        $csvArray  = [['No', 'Name', 'Role', 'Status', 'Time', 'Notes', 'Approved']];

        foreach ($records as $index => $row) {
            $roleStr = '';
            if ($row->user->hasRole('siswa'))  $roleStr = 'Student';
            elseif ($row->user->hasRole('guru'))  $roleStr = 'Teacher';
            elseif ($row->user->hasRole('staff')) $roleStr = 'Staff';

            $item = [
                $index + 1,
                $row->user->name,
                $roleStr,
                ucfirst($row->status),
                Carbon::parse($row->recorded_at)->format('d M Y, H:i'),
                $row->notes ?? '-',
                $row->is_approved === null ? 'N/A' : ($row->is_approved ? 'Yes' : 'No'),
            ];

            $dataArray[] = $item;
            $csvArray[]  = $item;
        }

        return [$dataArray, $csvArray];
    }

    private function toExcel(array $dataArray)
    {
        $content = (string) SimpleXLSXGen::fromArray($dataArray);
        return response()->streamDownload(
            fn() => print($content),
            'Attendance_Records.xlsx'
        );
    }

    private function toCsv(array $csvArray)
    {
        return response()->streamDownload(function () use ($csvArray) {
            $file = fopen('php://output', 'w');
            foreach ($csvArray as $line) fputcsv($file, $line);
            fclose($file);
        }, 'Attendance_Records.csv');
    }

    private function toPdf($data)
    {
        $pdf = Pdf::loadView('admin.attendances.export_pdf', ['attendances' => $data]);
        return $pdf->download('Attendance_Records.pdf');
    }

    private function toZip(array $dataArray, array $csvArray, $data)
    {
        $zip      = new \ZipArchive();
        $zipPath  = storage_path('app/public/Attendance_Records_' . time() . '.zip');

        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            abort(500, 'Gagal membuat file ZIP.');
        }

        // Excel
        $xlsxTemp = tempnam(sys_get_temp_dir(), 'xlsx');
        SimpleXLSXGen::fromArray($dataArray)->saveAs($xlsxTemp);
        $zip->addFile($xlsxTemp, 'Attendance_Records.xlsx');

        // CSV
        $csvTemp = tempnam(sys_get_temp_dir(), 'csv');
        $f = fopen($csvTemp, 'w');
        foreach ($csvArray as $line) fputcsv($f, $line);
        fclose($f);
        $zip->addFile($csvTemp, 'Attendance_Records.csv');

        // PDF
        $pdfTemp = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($pdfTemp, Pdf::loadView('admin.attendances.export_pdf', ['attendances' => $data])->output());
        $zip->addFile($pdfTemp, 'Attendance_Records.pdf');

        $zip->close();
        unlink($xlsxTemp);
        unlink($csvTemp);
        unlink($pdfTemp);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
