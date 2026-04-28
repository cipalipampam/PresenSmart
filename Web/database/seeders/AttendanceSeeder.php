<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Seed riwayat presensi 14 hari ke belakang untuk semua user siswa & employee.
     * Senin–Sabtu saja (skip Minggu). Distribusi acak: 80% hadir, 10% izin, 10% sakit.
     */
    public function run(): void
    {
        $users = User::with('roles')->get()->filter(function ($u) {
            return $u->hasRole(['siswa', 'guru', 'staff']);
        });

        $count = 0;

        foreach ($users as $user) {
            for ($dayOffset = 14; $dayOffset >= 1; $dayOffset--) {
                $date = Carbon::today()->subDays($dayOffset);

                // Skip Minggu
                if ($date->dayOfWeek === Carbon::SUNDAY) {
                    continue;
                }

                // Skip jika sudah ada record di hari ini untuk user ini
                $exists = Attendance::where('user_id', $user->id)
                    ->whereDate('recorded_at', $date)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $roll = rand(1, 10); // 1–8 hadir, 9 izin, 10 sakit

                if ($roll <= 8) {
                    // HADIR — jam masuk acak antara 06:30–07:20
                    $checkInMinutes = rand(0, 50); // 0 = jam 06:30
                    $checkIn = $date->copy()->setHour(6)->setMinute(30)->addMinutes($checkInMinutes);
                    $isLate  = $checkIn->greaterThan($date->copy()->setHour(7)->setMinute(0));

                    // Jam pulang acak antara 15:00–16:00
                    $checkOut = $date->copy()->setHour(15)->addMinutes(rand(0, 60));

                    Attendance::create([
                        'user_id'        => $user->id,
                        'status'         => 'present',
                        'is_late'        => $isLate,
                        'is_approved'    => true,
                        'latitude'       => -6.200000 + (rand(-10, 10) / 10000),
                        'longitude'      => 106.816666 + (rand(-10, 10) / 10000),
                        'recorded_at'    => $checkIn,
                        'check_out_time' => $checkOut,
                    ]);
                } elseif ($roll === 9) {
                    // IZIN
                    Attendance::create([
                        'user_id'     => $user->id,
                        'status'      => 'permission',
                        'is_late'     => false,
                        'is_approved' => (rand(0, 1) === 1) ? true : null, // setengah sudah di-approve
                        'notes'       => 'Keperluan keluarga.',
                        'recorded_at' => $date->copy()->setHour(7)->setMinute(0),
                    ]);
                } else {
                    // SAKIT
                    Attendance::create([
                        'user_id'     => $user->id,
                        'status'      => 'sick',
                        'is_late'     => false,
                        'is_approved' => (rand(0, 1) === 1) ? true : null,
                        'notes'       => 'Demam dan flu.',
                        'recorded_at' => $date->copy()->setHour(7)->setMinute(0),
                    ]);
                }

                $count++;
            }
        }

        $this->command->info("✅ {$count} record presensi (14 hari terakhir) berhasil di-seed.");
    }
}
