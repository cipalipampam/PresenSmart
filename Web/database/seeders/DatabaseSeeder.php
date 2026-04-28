<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles (harus pertama)
        $this->call([RoleSeeder::class]);

        // 2. Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@sekolah.com'],
            [
                'name'     => 'Admin Utama',
                'password' => Hash::make('admin123'),
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
        $this->command->info('✅ Admin berhasil di-seed. (admin@sekolah.com / admin123)');

        // 3. Siswa (10 orang)
        $this->call([StudentSeeder::class]);

        // 4. Guru & Staff (3 guru + 2 staff)
        $this->call([EmployeeSeeder::class]);

        // 5. Pengumuman (5 pengumuman aktif)
        $this->call([AnnouncementSeeder::class]);

        // 6. Riwayat Presensi (14 hari terakhir, semua user)
        $this->call([AttendanceSeeder::class]);

        // 7. Settings lengkap
        $settings = [
            // Lokasi sekolah (default: SMKN 1 Jakarta)
            'school_lat'             => '-6.200000',
            'school_long'            => '106.816666',
            'school_radius'          => '100',          // meter

            // Jam operasional
            'check_in_start'         => '06:00',
            'check_in_end'           => '07:00',        // jam masuk tepat waktu
            'late_tolerance_minutes' => '15',           // toleransi terlambat
            'check_out_start'        => '15:00',        // jam pulang minimal
            'check_out_end'          => '17:00',

            // Alias yang mungkin dipakai di tempat lain
            'presensi_start_time'    => '07:00',
            'presensi_end_time'      => '09:00',

            // Info sekolah
            'school_name'            => 'SMA Negeri 1 Contoh',
            'school_address'         => 'Jl. Pendidikan No.1, Jakarta Pusat',
            'school_phone'           => '021-12345678',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        $this->command->info('✅ Settings lengkap berhasil di-seed.');

        $this->command->info('');
        $this->command->info('══════════════════════════════════════════════');
        $this->command->info('  ✅ Seeding selesai! Akun yang tersedia:');
        $this->command->info('  Admin  : admin@sekolah.com / admin123');
        $this->command->info('  Siswa  : ahmad.rizki@siswa.sch.id / password123');
        $this->command->info('  Guru   : hendra.kusuma@sekolah.sch.id / password123');
        $this->command->info('  Staff  : agus.triyono@sekolah.sch.id / password123');
        $this->command->info('══════════════════════════════════════════════');
    }
}
