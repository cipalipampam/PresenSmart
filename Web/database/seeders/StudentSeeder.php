<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    private array $students = [
        ['name' => 'Ahmad Rizki Pratama',    'email' => 'ahmad.rizki@siswa.sch.id',    'nis' => '2024001', 'nisn' => '0041234501', 'grade' => 'XII-IPA-1', 'gender' => 'male',   'place_of_birth' => 'Jakarta',    'date_of_birth' => '2006-03-12', 'religion' => 'Islam',   'phone' => '081211110001', 'address' => 'Jl. Kebon Jeruk No.12, Jakarta Barat'],
        ['name' => 'Siti Nurhaliza',          'email' => 'siti.nur@siswa.sch.id',       'nis' => '2024002', 'nisn' => '0041234502', 'grade' => 'XII-IPA-1', 'gender' => 'female', 'place_of_birth' => 'Bandung',    'date_of_birth' => '2006-07-20', 'religion' => 'Islam',   'phone' => '081211110002', 'address' => 'Jl. Dago No.55, Bandung'],
        ['name' => 'Budi Santoso',            'email' => 'budi.santoso@siswa.sch.id',   'nis' => '2024003', 'nisn' => '0041234503', 'grade' => 'XII-IPA-2', 'gender' => 'male',   'place_of_birth' => 'Surabaya',   'date_of_birth' => '2006-01-05', 'religion' => 'Islam',   'phone' => '081211110003', 'address' => 'Jl. Rungkut No.8, Surabaya'],
        ['name' => 'Dewi Anggraeni',          'email' => 'dewi.ang@siswa.sch.id',       'nis' => '2024004', 'nisn' => '0041234504', 'grade' => 'XII-IPS-1', 'gender' => 'female', 'place_of_birth' => 'Semarang',   'date_of_birth' => '2006-09-17', 'religion' => 'Islam',   'phone' => '081211110004', 'address' => 'Jl. Pemuda No.22, Semarang'],
        ['name' => 'Rizal Maulana',           'email' => 'rizal.maulana@siswa.sch.id',  'nis' => '2024005', 'nisn' => '0041234505', 'grade' => 'XII-IPS-1', 'gender' => 'male',   'place_of_birth' => 'Yogyakarta', 'date_of_birth' => '2006-05-30', 'religion' => 'Islam',   'phone' => '081211110005', 'address' => 'Jl. Malioboro No.1, Yogyakarta'],
        ['name' => 'Farah Azzahra',           'email' => 'farah.azzahra@siswa.sch.id',  'nis' => '2024006', 'nisn' => '0041234506', 'grade' => 'XI-IPA-1',  'gender' => 'female', 'place_of_birth' => 'Medan',      'date_of_birth' => '2007-02-14', 'religion' => 'Islam',   'phone' => '081211110006', 'address' => 'Jl. Sisingamangaraja No.34, Medan'],
        ['name' => 'Eko Prasetyo',            'email' => 'eko.prasetyo@siswa.sch.id',   'nis' => '2024007', 'nisn' => '0041234507', 'grade' => 'XI-IPA-2',  'gender' => 'male',   'place_of_birth' => 'Solo',       'date_of_birth' => '2007-08-25', 'religion' => 'Kristen', 'phone' => '081211110007', 'address' => 'Jl. Slamet Riyadi No.7, Solo'],
        ['name' => 'Maya Indah Sari',         'email' => 'maya.indah@siswa.sch.id',     'nis' => '2024008', 'nisn' => '0041234508', 'grade' => 'XI-IPS-1',  'gender' => 'female', 'place_of_birth' => 'Palembang',  'date_of_birth' => '2007-11-03', 'religion' => 'Islam',   'phone' => '081211110008', 'address' => 'Jl. Sudirman No.15, Palembang'],
        ['name' => 'Doni Firmansyah',         'email' => 'doni.firm@siswa.sch.id',      'nis' => '2024009', 'nisn' => '0041234509', 'grade' => 'X-IPA-1',   'gender' => 'male',   'place_of_birth' => 'Makassar',   'date_of_birth' => '2008-04-18', 'religion' => 'Islam',   'phone' => '081211110009', 'address' => 'Jl. Penghibur No.6, Makassar'],
        ['name' => 'Anisa Rahma Putri',       'email' => 'anisa.rahma@siswa.sch.id',    'nis' => '2024010', 'nisn' => '0041234510', 'grade' => 'X-IPA-1',   'gender' => 'female', 'place_of_birth' => 'Bogor',      'date_of_birth' => '2008-06-22', 'religion' => 'Islam',   'phone' => '081211110010', 'address' => 'Jl. Pajajaran No.99, Bogor'],
    ];

    public function run(): void
    {
        foreach ($this->students as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password123'),
                ]
            );

            if (!$user->hasRole('siswa')) {
                $user->assignRole('siswa');
            }

            Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nis'            => $data['nis'],
                    'nisn'           => $data['nisn'],
                    'grade'          => $data['grade'],
                    'gender'         => $data['gender'],
                    'place_of_birth' => $data['place_of_birth'],
                    'date_of_birth'  => $data['date_of_birth'],
                    'religion'       => $data['religion'],
                    'phone_number'   => $data['phone'],
                    'address'        => $data['address'],
                ]
            );
        }

        $this->command->info('✅ 10 siswa berhasil di-seed.');
    }
}
