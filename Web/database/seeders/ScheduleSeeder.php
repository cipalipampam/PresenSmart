<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Kelas
        $xMipa1 = Classroom::where('name', 'X-MIPA 1')->first();
        $xiMipa1 = Classroom::where('name', 'XI-MIPA 1')->first();
        $xiiMipa1 = Classroom::where('name', 'XII-MIPA 1')->first();

        // Guru
        $hendra = User::where('email', 'hendra.kusuma@sekolah.sch.id')->first();
        $sari = User::where('email', 'sari.dewantari@sekolah.sch.id')->first();
        $antonius = User::where('email', 'antonius.wibowo@sekolah.sch.id')->first();
        $ratna = User::where('email', 'ratna.permata@sekolah.sch.id')->first();
        $bambang = User::where('email', 'bambang.sutrisno@sekolah.sch.id')->first();
        $siti = User::where('email', 'siti.khadijah@sekolah.sch.id')->first();

        // Mapel
        $mat = Subject::where('code', 'MAT-W')->first();
        $bind = Subject::where('code', 'B-IND')->first();
        $bing = Subject::where('code', 'B-ING')->first();
        $fis = Subject::where('code', 'FIS')->first();
        $inf = Subject::where('code', 'INF')->first();
        $pai = Subject::where('code', 'PAI')->first();

        if (! $xMipa1 || ! $xiMipa1 || ! $hendra || ! $mat) {
            $this->command->warn('Data kelas, guru, atau mapel belum lengkap untuk schedule seeder.');

            return;
        }

        $schedules = [
            // ══════════════════════════════════════════════════════════════════════
            // SENIN (day_of_week = 1)
            // ══════════════════════════════════════════════════════════════════════
            // X-MIPA 1
            ['classroom_id' => $xMipa1->id, 'subject_id' => $mat->id, 'teacher_id' => $hendra->id, 'day_of_week' => 1, 'start_time' => '07:15:00', 'end_time' => '08:45:00', 'room' => 'R.101'],
            ['classroom_id' => $xMipa1->id, 'subject_id' => $bind->id, 'teacher_id' => $sari->id, 'day_of_week' => 1, 'start_time' => '09:00:00', 'end_time' => '10:30:00', 'room' => 'R.101'],
            ['classroom_id' => $xMipa1->id, 'subject_id' => $inf->id, 'teacher_id' => $antonius->id, 'day_of_week' => 1, 'start_time' => '10:45:00', 'end_time' => '12:15:00', 'room' => 'Lab Komputer 1'],
            // XI-MIPA 1
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $bind->id, 'teacher_id' => $sari->id, 'day_of_week' => 1, 'start_time' => '07:15:00', 'end_time' => '08:45:00', 'room' => 'R.201'],
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $fis->id, 'teacher_id' => $bambang->id, 'day_of_week' => 1, 'start_time' => '09:00:00', 'end_time' => '10:30:00', 'room' => 'Lab Fisika'],
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $mat->id, 'teacher_id' => $hendra->id, 'day_of_week' => 1, 'start_time' => '10:45:00', 'end_time' => '12:15:00', 'room' => 'R.201'],
            // XII-MIPA 1
            ['classroom_id' => $xiiMipa1->id, 'subject_id' => $fis->id, 'teacher_id' => $bambang->id, 'day_of_week' => 1, 'start_time' => '07:15:00', 'end_time' => '08:45:00', 'room' => 'Lab Fisika'],
            ['classroom_id' => $xiiMipa1->id, 'subject_id' => $mat->id, 'teacher_id' => $hendra->id, 'day_of_week' => 1, 'start_time' => '09:00:00', 'end_time' => '10:30:00', 'room' => 'R.301'],
            ['classroom_id' => $xiiMipa1->id, 'subject_id' => $bing->id, 'teacher_id' => $ratna->id, 'day_of_week' => 1, 'start_time' => '10:45:00', 'end_time' => '12:15:00', 'room' => 'R.301'],

            // ══════════════════════════════════════════════════════════════════════
            // SELASA (day_of_week = 2)
            // ══════════════════════════════════════════════════════════════════════
            // X-MIPA 1
            ['classroom_id' => $xMipa1->id, 'subject_id' => $fis->id, 'teacher_id' => $bambang->id, 'day_of_week' => 2, 'start_time' => '07:15:00', 'end_time' => '08:45:00', 'room' => 'Lab Fisika'],
            ['classroom_id' => $xMipa1->id, 'subject_id' => $bing->id, 'teacher_id' => $ratna->id, 'day_of_week' => 2, 'start_time' => '09:00:00', 'end_time' => '10:30:00', 'room' => 'R.101'],
            ['classroom_id' => $xMipa1->id, 'subject_id' => $pai->id, 'teacher_id' => $siti->id, 'day_of_week' => 2, 'start_time' => '10:45:00', 'end_time' => '12:15:00', 'room' => 'R.101'],
            // XI-MIPA 1
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $inf->id, 'teacher_id' => $antonius->id, 'day_of_week' => 2, 'start_time' => '07:15:00', 'end_time' => '08:45:00', 'room' => 'Lab Komputer 1'],
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $pai->id, 'teacher_id' => $siti->id, 'day_of_week' => 2, 'start_time' => '09:00:00', 'end_time' => '10:30:00', 'room' => 'R.201'],
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $bing->id, 'teacher_id' => $ratna->id, 'day_of_week' => 2, 'start_time' => '10:45:00', 'end_time' => '12:15:00', 'room' => 'R.201'],

            // ══════════════════════════════════════════════════════════════════════
            // RABU (day_of_week = 3)
            // ══════════════════════════════════════════════════════════════════════
            // X-MIPA 1
            ['classroom_id' => $xMipa1->id, 'subject_id' => $bing->id, 'teacher_id' => $ratna->id, 'day_of_week' => 3, 'start_time' => '07:15:00', 'end_time' => '08:45:00', 'room' => 'R.101'],
            ['classroom_id' => $xMipa1->id, 'subject_id' => $mat->id, 'teacher_id' => $hendra->id, 'day_of_week' => 3, 'start_time' => '09:00:00', 'end_time' => '10:30:00', 'room' => 'R.101'],
            ['classroom_id' => $xMipa1->id, 'subject_id' => $bind->id, 'teacher_id' => $sari->id, 'day_of_week' => 3, 'start_time' => '10:45:00', 'end_time' => '12:15:00', 'room' => 'R.101'],
            // XI-MIPA 1
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $mat->id, 'teacher_id' => $hendra->id, 'day_of_week' => 3, 'start_time' => '07:15:00', 'end_time' => '08:45:00', 'room' => 'R.201'],
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $bind->id, 'teacher_id' => $sari->id, 'day_of_week' => 3, 'start_time' => '09:00:00', 'end_time' => '10:30:00', 'room' => 'R.201'],
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $fis->id, 'teacher_id' => $bambang->id, 'day_of_week' => 3, 'start_time' => '10:45:00', 'end_time' => '12:15:00', 'room' => 'Lab Fisika'],

            // ══════════════════════════════════════════════════════════════════════
            // KAMIS (day_of_week = 4)
            // ══════════════════════════════════════════════════════════════════════
            // X-MIPA 1
            ['classroom_id' => $xMipa1->id, 'subject_id' => $inf->id, 'teacher_id' => $antonius->id, 'day_of_week' => 4, 'start_time' => '07:15:00', 'end_time' => '08:45:00', 'room' => 'Lab Komputer 1'],
            ['classroom_id' => $xMipa1->id, 'subject_id' => $pai->id, 'teacher_id' => $siti->id, 'day_of_week' => 4, 'start_time' => '09:00:00', 'end_time' => '10:30:00', 'room' => 'R.101'],
            ['classroom_id' => $xMipa1->id, 'subject_id' => $fis->id, 'teacher_id' => $bambang->id, 'day_of_week' => 4, 'start_time' => '10:45:00', 'end_time' => '12:15:00', 'room' => 'Lab Fisika'],
            // XI-MIPA 1
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $pai->id, 'teacher_id' => $siti->id, 'day_of_week' => 4, 'start_time' => '07:15:00', 'end_time' => '08:45:00', 'room' => 'R.201'],
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $inf->id, 'teacher_id' => $antonius->id, 'day_of_week' => 4, 'start_time' => '09:00:00', 'end_time' => '10:30:00', 'room' => 'Lab Komputer 1'],
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $mat->id, 'teacher_id' => $hendra->id, 'day_of_week' => 4, 'start_time' => '10:45:00', 'end_time' => '12:15:00', 'room' => 'R.201'],

            // ══════════════════════════════════════════════════════════════════════
            // JUMAT (day_of_week = 5)
            // ══════════════════════════════════════════════════════════════════════
            // X-MIPA 1
            ['classroom_id' => $xMipa1->id, 'subject_id' => $pai->id, 'teacher_id' => $siti->id, 'day_of_week' => 5, 'start_time' => '07:15:00', 'end_time' => '08:45:00', 'room' => 'R.101'],
            ['classroom_id' => $xMipa1->id, 'subject_id' => $bind->id, 'teacher_id' => $sari->id, 'day_of_week' => 5, 'start_time' => '09:00:00', 'end_time' => '10:30:00', 'room' => 'R.101'],
            // XI-MIPA 1
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $bing->id, 'teacher_id' => $ratna->id, 'day_of_week' => 5, 'start_time' => '07:15:00', 'end_time' => '08:45:00', 'room' => 'R.201'],
            ['classroom_id' => $xiMipa1->id, 'subject_id' => $inf->id, 'teacher_id' => $antonius->id, 'day_of_week' => 5, 'start_time' => '09:00:00', 'end_time' => '10:30:00', 'room' => 'Lab Komputer 1'],
        ];

        $count = 0;
        foreach ($schedules as $sched) {
            Schedule::updateOrCreate(
                [
                    'classroom_id' => $sched['classroom_id'],
                    'day_of_week' => $sched['day_of_week'],
                    'start_time' => $sched['start_time'],
                ],
                array_merge($sched, ['is_active' => true])
            );
            $count++;
        }

        $this->command->info("✅ {$count} slot jadwal pelajaran mingguan anti-bentrok berhasil di-seed.");
    }
}

