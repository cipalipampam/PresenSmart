<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    private array $employees = [
        // 3 Guru
        [
            'name' => 'Dr. Hendra Kusuma, M.Pd.',
            'email' => 'hendra.kusuma@sekolah.sch.id',
            'role' => 'guru',
            'nip' => '197805102006041001',
            'position' => 'Guru Matematika',
            'gender' => 'male',
            'place_of_birth' => 'Bandung',
            'date_of_birth' => '1978-05-10',
            'religion' => 'Islam',
            'phone' => '082111220001',
            'address' => 'Jl. Ganesha No.10, Bandung',
        ],
        [
            'name' => 'Ibu Sari Dewantari, S.Pd.',
            'email' => 'sari.dewantari@sekolah.sch.id',
            'role' => 'guru',
            'nip' => '198203152007042002',
            'position' => 'Guru Bahasa Indonesia',
            'gender' => 'female',
            'place_of_birth' => 'Yogyakarta',
            'date_of_birth' => '1982-03-15',
            'religion' => 'Islam',
            'phone' => '082111220002',
            'address' => 'Jl. Cendana No.3, Yogyakarta',
        ],
        [
            'name' => 'Bpk. Antonius Wibowo, S.Kom.',
            'email' => 'antonius.wibowo@sekolah.sch.id',
            'role' => 'guru',
            'nip' => '198509202010011003',
            'position' => 'Guru Informatika',
            'gender' => 'male',
            'place_of_birth' => 'Semarang',
            'date_of_birth' => '1985-09-20',
            'religion' => 'Kristen',
            'phone' => '082111220003',
            'address' => 'Jl. Pandanaran No.7, Semarang',
        ],
        // 2 Staff
        [
            'name' => 'Agus Triyono',
            'email' => 'agus.triyono@sekolah.sch.id',
            'role' => 'staff',
            'nip' => '199001052015031004',
            'position' => 'Staff Tata Usaha',
            'gender' => 'male',
            'place_of_birth' => 'Surakarta',
            'date_of_birth' => '1990-01-05',
            'religion' => 'Islam',
            'phone' => '082111220004',
            'address' => 'Jl. Adi Sucipto No.45, Surakarta',
        ],
        [
            'name' => 'Rina Lestari Wulandari',
            'email' => 'rina.lestari@sekolah.sch.id',
            'role' => 'staff',
            'nip' => '199204182016042005',
            'position' => 'Staff Perpustakaan',
            'gender' => 'female',
            'place_of_birth' => 'Madiun',
            'date_of_birth' => '1992-04-18',
            'religion' => 'Islam',
            'phone' => '082111220005',
            'address' => 'Jl. Pahlawan No.12, Madiun',
        ],
    ];

    public function run(): void
    {
        foreach ($this->employees as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password123'),
                ]
            );

            if (!$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }

            Employee::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nip'            => $data['nip'],
                    'position'       => $data['position'],
                    'gender'         => $data['gender'],
                    'place_of_birth' => $data['place_of_birth'],
                    'date_of_birth'  => $data['date_of_birth'],
                    'religion'       => $data['religion'],
                    'phone_number'   => $data['phone'],
                    'address'        => $data['address'],
                ]
            );
        }

        $this->command->info('✅ 3 guru + 2 staff berhasil di-seed.');
    }
}
