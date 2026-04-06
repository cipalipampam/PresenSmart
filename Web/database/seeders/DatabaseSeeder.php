<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat user admin
        // Password default: admin123
        // Email: admin@sekolah.com
        User::updateOrCreate(
            ['nisn' => '0000000000'],
            [
                'name' => 'Admin Utama',
                'email' => 'admin@sekolah.com',
                'password' => bcrypt('admin123'),
                'nisn' => '0000000000', // NISN admin
                'role' => UserRole::ADMIN,
                
                // Field baru
                'nis' => 123456,
                'jenis_kelamin' => 'laki_laki',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1990-01-01',
                'agama' => 'islam',
                'alamat' => 'Jl. Contoh No. 123, Jakarta',
                'no_telp' => 6281234567890,
                'pas_foto' => null,
            ]
        );

        // Contoh user siswa
        User::updateOrCreate(
            ['nisn' => '1234567890'],
            [
                'name' => 'Siswa Contoh',
                'email' => 'siswa@sekolah.com',
                'password' => bcrypt('admin123'),
                'nisn' => '1234567890', // NISN siswa
                'role' => UserRole::SISWA,
                
                // Field baru
                'nis' => 654320,
                'jenis_kelamin' => 'laki_laki',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2005-05-15',
                'agama' => 'kristen',
                'alamat' => 'Jl. Merdeka No. 45, Bandung',
                'no_telp' => 6287654321098,
                'pas_foto' => null,
            ]
        );

        // Buat user admin tambahan
        User::updateOrCreate(
            ['nisn' => '0000000001'],
            [
                'name' => 'Admin Sekolah',
                'email' => 'admin.sekolah@sekolah.com',
                'password' => bcrypt('admin123'),
                'nisn' => '0000000001',
                'role' => UserRole::ADMIN,
                
                'nis' => 123457,
                'jenis_kelamin' => 'perempuan',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1985-07-15',
                'agama' => 'kristen',
                'alamat' => 'Jl. Merdeka No. 456, Surabaya',
                'no_telp' => 6287654321000,
                'pas_foto' => null,
            ]
        );

        User::updateOrCreate(
            ['nisn' => '0000000002'],
            [
                'name' => 'Admin Keuangan',
                'email' => 'admin.keuangan@sekolah.com',
            'password' => bcrypt('admin123'),
                'nisn' => '0000000002',
                'role' => UserRole::ADMIN,
                
                'nis' => 123458,
                'jenis_kelamin' => 'laki_laki',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1988-03-20',
                'agama' => 'katholik',
                'alamat' => 'Jl. Raya Bandung No. 789',
                'no_telp' => 6282345678901,
                'pas_foto' => null,
            ]
        );

        \App\Models\Setting::updateOrCreate([
            'key' => 'school_lat',
        ], [
            'value' => '-6.200000',
        ]);
        \App\Models\Setting::updateOrCreate([
            'key' => 'school_long',
        ], [
            'value' => '106.816666',
        ]);
        \App\Models\Setting::updateOrCreate([
            'key' => 'school_radius',
        ], [
            'value' => '100',
        ]);

        // Panggil seeder datasiswa
        $this->call([datasiswa::class]);
    }
}
