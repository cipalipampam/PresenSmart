<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil guru untuk dijadikan wali kelas
        $hendra = User::where('email', 'hendra.kusuma@sekolah.sch.id')->first();
        $sari = User::where('email', 'sari.dewantari@sekolah.sch.id')->first();
        $antonius = User::where('email', 'antonius.wibowo@sekolah.sch.id')->first();
        $ratna = User::where('email', 'ratna.permata@sekolah.sch.id')->first();
        $bambang = User::where('email', 'bambang.sutrisno@sekolah.sch.id')->first();
        $siti = User::where('email', 'siti.khadijah@sekolah.sch.id')->first();

        $classrooms = [
            // Kelas 10
            [
                'name' => 'X-MIPA 1',
                'level' => '10',
                'major' => 'MIPA',
                'section' => '1',
                'academic_year' => '2026/2027',
                'homeroom_teacher_id' => $hendra?->id,
                'is_active' => true,
            ],
            [
                'name' => 'X-MIPA 2',
                'level' => '10',
                'major' => 'MIPA',
                'section' => '2',
                'academic_year' => '2026/2027',
                'homeroom_teacher_id' => $sari?->id,
                'is_active' => true,
            ],
            [
                'name' => 'X-IPS 1',
                'level' => '10',
                'major' => 'IPS',
                'section' => '1',
                'academic_year' => '2026/2027',
                'homeroom_teacher_id' => $antonius?->id,
                'is_active' => true,
            ],

            // Kelas 11
            [
                'name' => 'XI-MIPA 1',
                'level' => '11',
                'major' => 'MIPA',
                'section' => '1',
                'academic_year' => '2026/2027',
                'homeroom_teacher_id' => $bambang?->id,
                'is_active' => true,
            ],
            [
                'name' => 'XI-MIPA 2',
                'level' => '11',
                'major' => 'MIPA',
                'section' => '2',
                'academic_year' => '2026/2027',
                'homeroom_teacher_id' => $ratna?->id,
                'is_active' => true,
            ],
            [
                'name' => 'XI-IPS 1',
                'level' => '11',
                'major' => 'IPS',
                'section' => '1',
                'academic_year' => '2026/2027',
                'homeroom_teacher_id' => $siti?->id,
                'is_active' => true,
            ],

            // Kelas 12
            [
                'name' => 'XII-MIPA 1',
                'level' => '12',
                'major' => 'MIPA',
                'section' => '1',
                'academic_year' => '2025/2026',
                'homeroom_teacher_id' => $hendra?->id,
                'is_active' => true,
            ],
            [
                'name' => 'XII-MIPA 2',
                'level' => '12',
                'major' => 'MIPA',
                'section' => '2',
                'academic_year' => '2025/2026',
                'homeroom_teacher_id' => $sari?->id,
                'is_active' => true,
            ],
            [
                'name' => 'XII-IPS 1',
                'level' => '12',
                'major' => 'IPS',
                'section' => '1',
                'academic_year' => '2025/2026',
                'homeroom_teacher_id' => $antonius?->id,
                'is_active' => true,
            ],
        ];

        foreach ($classrooms as $item) {
            Classroom::updateOrCreate(
                ['name' => $item['name'], 'academic_year' => $item['academic_year']],
                $item
            );
        }

        $this->command->info('✅ 9 rombel kelas (Tingkat 10, 11, 12) berhasil di-seed.');
    }
}

