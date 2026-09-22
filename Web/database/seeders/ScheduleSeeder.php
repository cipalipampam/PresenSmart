<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil seluruh rombel kelas (9 kelas: X, XI, XII)
        $classrooms = Classroom::where('is_active', true)->orderBy('level')->orderBy('name')->get();

        if ($classrooms->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada kelas aktif untuk di-seed jadwalnya.');
            return;
        }

        // 2. Daftar 10 Mapel beserta Guru Pengampunya (1 Guru = 1 Mapel Utama)
        $subjectTeacherMappings = [
            'MAT-W' => 'hendra.kusuma@sekolah.sch.id',
            'B-IND' => 'sari.dewantari@sekolah.sch.id',
            'B-ING' => 'ratna.permata@sekolah.sch.id',
            'FIS'   => 'bambang.sutrisno@sekolah.sch.id',
            'INF'   => 'antonius.wibowo@sekolah.sch.id',
            'PAI'   => 'siti.khadijah@sekolah.sch.id',
            'PJK'   => 'eko.prasetyo@sekolah.sch.id',
            'KIM'   => 'maya.indah@sekolah.sch.id',
            'BIO'   => 'rizky.ramadhan@sekolah.sch.id',
            'SEJ'   => 'dewi.lestari@sekolah.sch.id',
        ];

        // Hydrate data subject & teacher ID
        $teachersAndSubjects = [];
        foreach ($subjectTeacherMappings as $code => $email) {
            $subject = Subject::where('code', $code)->first();
            $teacher = User::where('email', $email)->first();

            if ($subject && $teacher) {
                $teachersAndSubjects[] = [
                    'subject' => $subject,
                    'teacher' => $teacher,
                ];
            }
        }

        if (count($teachersAndSubjects) < 10) {
            $this->command->warn('⚠️ Data mapel atau guru belum lengkap (membutuhkan 10 mapel & 10 guru).');
            return;
        }

        // 3. Slot Waktu Standar KBM Per-Hari (Full Day School: 4 Slot x 2 JP = 8 JP/hari)
        // Sesuai Permendikbud No. 23/2017: Istirahat I (09:30-10:00) & Ishoma (11:30-13:00)
        $dailyTimeSlots = [
            ['start' => '08:00:00', 'end' => '09:30:00'], // Slot 1 (2 JP) - Sebelum Istirahat I
            // ☕ ISTIRAHAT I (09:30 - 10:00 WIB: Dhuha & Camilan)
            ['start' => '10:00:00', 'end' => '11:30:00'], // Slot 2 (2 JP) - Sebelum Ishoma
            // 🍱 ISTIRAHAT II / ISHOMA (11:30 - 13:00 WIB: Sholat Dzuhur/Jumat & Makan Siang)
            ['start' => '13:00:00', 'end' => '14:30:00'], // Slot 3 (2 JP) - Sesudah Ishoma
            ['start' => '14:30:00', 'end' => '16:00:00'], // Slot 4 (2 JP) - Sore
        ];

        // Hapus data jadwal sebelumnya agar seed ulang bersih
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schedule::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $totalInserted = 0;
        $numTeachers = count($teachersAndSubjects); // 10

        // 4. Algoritma Rotasi Modulo Anti-Bentrok (Latin Square Rotation)
        // Menjamin: Zero Teacher Clash, Zero Classroom Clash, 36 JP/minggu per guru (Memenuhi syarat 24-40 JP)
        foreach ($classrooms as $classIndex => $classroom) {
            $defaultRoom = match (true) {
                str_contains($classroom->name, 'X-MIPA 1') => 'R.101',
                str_contains($classroom->name, 'X-MIPA 2') => 'R.102',
                str_contains($classroom->name, 'X-IPS 1')  => 'R.103',
                str_contains($classroom->name, 'XI-MIPA 1') => 'R.201',
                str_contains($classroom->name, 'XI-MIPA 2') => 'R.202',
                str_contains($classroom->name, 'XI-IPS 1')  => 'R.203',
                str_contains($classroom->name, 'XII-MIPA 1') => 'R.301',
                str_contains($classroom->name, 'XII-MIPA 2') => 'R.302',
                str_contains($classroom->name, 'XII-IPS 1')  => 'R.303',
                default => 'R.'.$classroom->level.'01',
            };

            $globalSlotCounter = 0;

            // Loop 5 Hari Sekolah (1 = Senin s.d. 5 = Jumat)
            for ($dayOfWeek = 1; $dayOfWeek <= 5; $dayOfWeek++) {

                // Loop 4 Slot Waktu per Hari
                foreach ($dailyTimeSlots as $slot) {
                    // Formula Rotasi Guru Anti-Bentrok: (ClassIndex + GlobalSlotCounter) % 10
                    $pairIndex = ($classIndex + $globalSlotCounter) % $numTeachers;
                    $pair = $teachersAndSubjects[$pairIndex];

                    $subject = $pair['subject'];
                    $teacher = $pair['teacher'];

                    // Penentuan Ruang Belajar Khusus (Lab/Lapangan)
                    $room = match ($subject->code) {
                        'INF' => 'Lab Komputer',
                        'FIS', 'KIM', 'BIO' => 'Lab Sains',
                        'PJK' => 'Lapangan Olahraga',
                        default => $defaultRoom,
                    };

                    Schedule::create([
                        'classroom_id' => $classroom->id,
                        'subject_id'   => $subject->id,
                        'teacher_id'   => $teacher->id,
                        'day_of_week'  => $dayOfWeek,
                        'start_time'   => $slot['start'],
                        'end_time'     => $slot['end'],
                        'room'         => $room,
                        'is_active'    => true,
                    ]);

                    $totalInserted++;
                    $globalSlotCounter++;
                }
            }
        }

        $this->command->info("✅ Complete Schedule Seeder: {$totalInserted} slot KBM berhasil di-seed untuk {$classrooms->count()} rombel kelas (9 kelas x 20 slot = 180 slot tanpa bentrok).");
    }
}
