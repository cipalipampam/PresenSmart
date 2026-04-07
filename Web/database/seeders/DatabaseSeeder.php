<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Setup Roles
        $this->call([RoleSeeder::class]);

        // 2. Buat user admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@sekolah.com'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('admin123'),
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // 3. Buat Pegawai / Guru
        $guru = User::firstOrCreate(
            ['email' => 'guru@sekolah.com'],
            [
                'name' => 'Guru Teladan',
                'password' => Hash::make('password123'),
            ]
        );
        if (!$guru->hasRole('guru')) {
            $guru->assignRole('guru');
        }
        Employee::firstOrCreate(
            ['user_id' => $guru->id],
            [
                'nip' => '198001012005011001',
                'position' => 'Guru Matematika',
                'gender' => 'male',
                'religion' => 'islam',
                'phone_number' => '08123456789',
            ]
        );

        // 4. Buat contoh Siswa
        $siswa = User::firstOrCreate(
            ['email' => 'siswa@sekolah.com'],
            [
                'name' => 'Siswa Contoh',
                'password' => Hash::make('password123'),
            ]
        );
        if (!$siswa->hasRole('siswa')) {
            $siswa->assignRole('siswa');
        }
        Student::firstOrCreate(
            ['user_id' => $siswa->id],
            [
                'nis' => '654320',
                'nisn' => '1234567890',
                'grade' => 'X-IPA-1',
                'gender' => 'male',
                'place_of_birth' => 'Bandung',
                'date_of_birth' => '2005-05-15',
                'religion' => 'islam',
                'address' => 'Jl. Merdeka No. 45',
            ]
        );

        // 5. Settings Standard
        Setting::updateOrCreate(['key' => 'school_lat'], ['value' => '-6.200000']);
        Setting::updateOrCreate(['key' => 'school_long'], ['value' => '106.816666']);
        Setting::updateOrCreate(['key' => 'school_radius'], ['value' => '100']);
    }
}
