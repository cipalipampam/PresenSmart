<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'code' => 'MAT-W',
                'name' => 'Matematika Wajib',
                'cluster' => 'mipa',
                'color_code' => '#2563eb', // Blue
                'is_active' => true,
            ],
            [
                'code' => 'B-IND',
                'name' => 'Bahasa Indonesia',
                'cluster' => 'bahasa',
                'color_code' => '#059669', // Emerald
                'is_active' => true,
            ],
            [
                'code' => 'B-ING',
                'name' => 'Bahasa Inggris',
                'cluster' => 'bahasa',
                'color_code' => '#7c3aed', // Purple
                'is_active' => true,
            ],
            [
                'code' => 'FIS',
                'name' => 'Fisika',
                'cluster' => 'mipa',
                'color_code' => '#ea580c', // Orange
                'is_active' => true,
            ],
            [
                'code' => 'KIM',
                'name' => 'Kimia',
                'cluster' => 'mipa',
                'color_code' => '#0891b2', // Cyan
                'is_active' => true,
            ],
            [
                'code' => 'BIO',
                'name' => 'Biologi',
                'cluster' => 'mipa',
                'color_code' => '#16a34a', // Green
                'is_active' => true,
            ],
            [
                'code' => 'INF',
                'name' => 'Informatika',
                'cluster' => 'mipa',
                'color_code' => '#4f46e5', // Indigo
                'is_active' => true,
            ],
            [
                'code' => 'SEJ',
                'name' => 'Sejarah Indonesia',
                'cluster' => 'ips',
                'color_code' => '#b45309', // Amber
                'is_active' => true,
            ],
            [
                'code' => 'PAI',
                'name' => 'Pendidikan Agama Islam',
                'cluster' => 'umum',
                'color_code' => '#0d9488', // Teal
                'is_active' => true,
            ],
            [
                'code' => 'PJK',
                'name' => 'Pendidikan Jasmani & Olahraga',
                'cluster' => 'umum',
                'color_code' => '#e11d48', // Rose
                'is_active' => true,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['code' => $subject['code']],
                $subject
            );
        }

        $this->command->info('✅ 10 mata pelajaran berhasil di-seed.');
    }
}

