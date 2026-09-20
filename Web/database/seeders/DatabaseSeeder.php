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

        // 2. Admin Utama
        $admin = User::firstOrCreate(
            ['email' => 'admin@sekolah.com'],
            [
                'name' => 'Admin Utama PresenSmart',
                'password' => Hash::make('admin123'),
            ]
        );
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
        $this->command->info('✅ Admin berhasil di-seed (admin@sekolah.com / admin123).');

        // 3. Master Mata Pelajaran (10 mapel lengkap kode, rumpun, dan warna aksen)
        $this->call([SubjectSeeder::class]);

        // 4. Guru & Staff Pegawai (6 guru + 2 staff, lengkap dengan mapel yang diampu)
        $this->call([EmployeeSeeder::class]);

        // 5. Master Rombel & Kelas (Tingkat 10, 11, 12 lengkap wali kelas)
        $this->call([ClassroomSeeder::class]);

        // 6. Siswa (24 siswa: 20 aktif tersebar di rombel + 4 alumni)
        $this->call([StudentSeeder::class]);

        // 7. Jadwal Pelajaran Mingguan Anti-Bentrok (Senin-Jumat)
        $this->call([ScheduleSeeder::class]);

        // 8. Riwayat Absensi Presensi Mata Pelajaran di Kelas
        $this->call([ScheduleAttendanceSeeder::class]);

        // 9. Riwayat Presensi Gerbang Harian (14 hari terakhir)
        $this->call([AttendanceSeeder::class]);

        // 10. Pengumuman Sekolah Aktif
        $this->call([AnnouncementSeeder::class]);

        // 11. Notifikasi Pengguna (Siswa & Guru)
        $this->call([AppNotificationSeeder::class]);

        // 12. Settings Lengkap Sekolah
        $settings = [
            // Lokasi sekolah (default koordinat PresenSmart)
            'school_lat' => '-6.200000',
            'school_long' => '106.816666',
            'school_radius' => '100',          // meter

            // Jam operasional
            'check_in_start' => '06:00',
            'check_in_end' => '07:00',        // batas tepat waktu
            'late_tolerance_minutes' => '15',           // toleransi keterlambatan
            'check_out_start' => '15:00',
            'check_out_end' => '17:00',

            // Jam presensi fallback
            'presensi_start_time' => '07:00',
            'presensi_end_time' => '09:00',

            // Profil sekolah
            'school_name' => 'SMA PresenSmart Unggulan',
            'school_address' => 'Jl. Pendidikan Karakter No. 1, Jakarta Pusat',
            'school_phone' => '021-88997766',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        $this->command->info('✅ Settings lengkap sekolah berhasil di-seed.');

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  🎉 SEEDING LENGKAP SELESAI! AKUN SIAP DIGUNAKAN:');
        $this->command->info('  • Admin  : admin@sekolah.com / admin123');
        $this->command->info('  • Guru 1 : hendra.kusuma@sekolah.sch.id / password123 (Matematika)');
        $this->command->info('  • Guru 2 : sari.dewantari@sekolah.sch.id / password123 (B. Indonesia)');
        $this->command->info('  • Guru 3 : antonius.wibowo@sekolah.sch.id / password123 (Informatika)');
        $this->command->info('  • Guru 4 : ratna.permata@sekolah.sch.id / password123 (B. Inggris)');
        $this->command->info('  • Guru 5 : bambang.sutrisno@sekolah.sch.id / password123 (Fisika)');
        $this->command->info('  • Guru 6 : siti.khadijah@sekolah.sch.id / password123 (PAI)');
        $this->command->info('  • Staff  : agus.triyono@sekolah.sch.id / password123 (Tata Usaha)');
        $this->command->info('  • Siswa  : ahmad.rizki@siswa.sch.id / password123 (X-MIPA 1)');
        $this->command->info('  • Siswa  : dimas.arya@siswa.sch.id / password123 (XI-MIPA 1)');
        $this->command->info('  • Siswa  : fajar.alfian@siswa.sch.id / password123 (XII-MIPA 1)');
        $this->command->info('═══════════════════════════════════════════════════════════');
    }
}
