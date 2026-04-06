<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Presensi;
use Carbon\Carbon;

class CheckDailyPresensi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:check-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa dan catat presensi alpha untuk siswa yang tidak melakukan presensi';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Ambil semua user (khusus siswa)
        $users = User::where('role', 'siswa')->get();
        
        // Tanggal hari ini
        $today = Carbon::now()->toDateString();

        // Hitung jumlah alpha
        $alphaCount = 0;

        foreach ($users as $user) {
            // Cek apakah user sudah presensi hari ini
            $presensiHariIni = Presensi::where('user_id', $user->id)
                ->whereDate('waktu', $today)
                ->exists();

            // Jika belum presensi, catat sebagai alpha
            if (!$presensiHariIni) {
                Presensi::create([
                    'user_id' => $user->id,
                    'waktu' => now(),
                    'status' => 'alpha',
                    'keterangan' => 'Tidak melakukan presensi'
                ]);

                $alphaCount++;
            }
        }

        $this->info("Berhasil mencatat $alphaCount siswa dengan status alpha.");

        return 0;
    }
} 