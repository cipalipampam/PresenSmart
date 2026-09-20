<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    private array $employees = [
        // 6 Guru (is_teacher = true)
        [
            'name' => 'Dr. Hendra Kusuma, M.Pd.',
            'email' => 'hendra.kusuma@sekolah.sch.id',
            'role' => 'guru',
            'nip' => '197805102006041001',
            'position' => 'Guru Matematika',
            'is_teacher' => true,
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
            'is_teacher' => true,
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
            'is_teacher' => true,
            'gender' => 'male',
            'place_of_birth' => 'Semarang',
            'date_of_birth' => '1985-09-20',
            'religion' => 'Kristen',
            'phone' => '082111220003',
            'address' => 'Jl. Pandanaran No.7, Semarang',
        ],
        [
            'name' => 'Ibu Ratna Permata, S.Pd.',
            'email' => 'ratna.permata@sekolah.sch.id',
            'role' => 'guru',
            'nip' => '198811122012012004',
            'position' => 'Guru Bahasa Inggris',
            'is_teacher' => true,
            'gender' => 'female',
            'place_of_birth' => 'Surabaya',
            'date_of_birth' => '1988-11-12',
            'religion' => 'Islam',
            'phone' => '082111220006',
            'address' => 'Jl. Dharmawangsa No.18, Surabaya',
        ],
        [
            'name' => 'Bpk. Bambang Sutrisno, M.Si.',
            'email' => 'bambang.sutrisno@sekolah.sch.id',
            'role' => 'guru',
            'nip' => '198004142008031005',
            'position' => 'Guru Fisika & IPA',
            'is_teacher' => true,
            'gender' => 'male',
            'place_of_birth' => 'Malang',
            'date_of_birth' => '1980-04-14',
            'religion' => 'Islam',
            'phone' => '082111220007',
            'address' => 'Jl. Ijen No.40, Malang',
        ],
        [
            'name' => 'Ibu Siti Khadijah, S.Pd.I.',
            'email' => 'siti.khadijah@sekolah.sch.id',
            'role' => 'guru',
            'nip' => '198706242011022006',
            'position' => 'Guru Pendidikan Agama',
            'is_teacher' => true,
            'gender' => 'female',
            'place_of_birth' => 'Cirebon',
            'date_of_birth' => '1987-06-24',
            'religion' => 'Islam',
            'phone' => '082111220008',
            'address' => 'Jl. Siliwangi No.88, Cirebon',
        ],
        // 2 Staff (is_teacher = false)
        [
            'name' => 'Agus Triyono',
            'email' => 'agus.triyono@sekolah.sch.id',
            'role' => 'staff',
            'nip' => '199001052015031004',
            'position' => 'Staff Tata Usaha',
            'is_teacher' => false,
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
            'is_teacher' => false,
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
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                ]
            );

            if (! $user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }

            Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $data['nip'],
                    'position' => $data['position'],
                    'is_teacher' => $data['is_teacher'],
                    'gender' => $data['gender'],
                    'place_of_birth' => $data['place_of_birth'],
                    'date_of_birth' => $data['date_of_birth'],
                    'religion' => $data['religion'],
                    'phone_number' => $data['phone'],
                    'address' => $data['address'],
                ]
            );

            // Seed relasi linieritas mapel yang diampu guru (Kurikulum Merdeka)
            if ($data['is_teacher']) {
                $subjectMappings = [
                    // Dr. Hendra Kusuma: Matematika Wajib (Utama) & Informatika (Serumpun)
                    'hendra.kusuma@sekolah.sch.id' => [
                        'MAT-W' => true,
                        'INF' => false,
                    ],
                    // Ibu Sari Dewantari: Bahasa Indonesia (Utama) & Bahasa Inggris (Serumpun)
                    'sari.dewantari@sekolah.sch.id' => [
                        'B-IND' => true,
                        'B-ING' => false,
                    ],
                    // Bpk. Antonius Wibowo: Informatika (Utama) & Matematika (Serumpun)
                    'antonius.wibowo@sekolah.sch.id' => [
                        'INF' => true,
                        'MAT-W' => false,
                    ],
                    // Ibu Ratna Permata: Bahasa Inggris (Utama) & Bahasa Indonesia (Serumpun)
                    'ratna.permata@sekolah.sch.id' => [
                        'B-ING' => true,
                        'B-IND' => false,
                    ],
                    // Bpk. Bambang Sutrisno: Fisika (Utama), Kimia & Biologi (Serumpun MIPA)
                    'bambang.sutrisno@sekolah.sch.id' => [
                        'FIS' => true,
                        'KIM' => false,
                        'BIO' => false,
                    ],
                    // Ibu Siti Khadijah: PAI (Utama) & Sejarah Indonesia (Serumpun Umum/Humaniora)
                    'siti.khadijah@sekolah.sch.id' => [
                        'PAI' => true,
                        'SEJ' => false,
                    ],
                ];

                if (isset($subjectMappings[$data['email']])) {
                    $syncData = [];
                    foreach ($subjectMappings[$data['email']] as $code => $isPrimary) {
                        $subject = \App\Models\Subject::where('code', $code)->first();
                        if ($subject) {
                            $syncData[$subject->id] = ['is_primary' => $isPrimary];
                        }
                    }
                    $user->subjects()->sync($syncData);
                }
            }
        }

        $this->command->info('✅ 6 guru + 2 staff berhasil di-seed (lengkap dengan flag is_teacher & mapel yang diampu).');
    }
}
