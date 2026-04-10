<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

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
    public function handle()
    {
        $today = Carbon::today();
        
        // Ambil SEMUA user yang wajib absen (Siswa, Guru, Staff)
        $users = User::role(['guru', 'staff', 'siswa'])->get();
        $absentCount = 0;

        foreach ($users as $user) {
            $hasAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('recorded_at', $today)
                ->exists();

            if (!$hasAttendance) {
                Attendance::create([
                    'user_id' => $user->id,
                    'status' => 'alfa',
                    'is_late' => false,
                    'recorded_at' => Carbon::now(),
                ]);
                $absentCount++;
            }
        }

        $this->info("Operasi Razia Alfa Selesai: Menambahkan {$absentCount} data Alfa ke database untuk hari ini.");
    }
}
