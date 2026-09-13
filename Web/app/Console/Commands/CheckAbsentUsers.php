<?php

namespace App\Console\Commands;

use App\Services\Console\Attendance\AbsentAttendanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckAbsentUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check-absent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perekam otomatis status Alfa bagi pengguna yang tidak mempunyai data presensi hari ini.';

    /**
     * Execute the console command.
     */
    public function handle(AbsentAttendanceService $absentAttendanceService): int
    {
        $absentCount = $absentAttendanceService->recordForDate(Carbon::today());

        $this->info("Operasi Razia Alfa Selesai: Menambahkan {$absentCount} data Alfa ke database untuk hari ini.");

        return self::SUCCESS;
    }
}
