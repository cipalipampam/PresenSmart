<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\ScheduleAttendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ScheduleAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $xMipa1 = Classroom::where('name', 'X-MIPA 1')->first();
        if (! $xMipa1) {
            return;
        }

        $students = Student::where('classroom_id', $xMipa1->id)
            ->where('academic_status', 'active')
            ->get();

        $schedules = Schedule::where('classroom_id', $xMipa1->id)
            ->where('is_active', true)
            ->get();

        if ($students->isEmpty() || $schedules->isEmpty()) {
            return;
        }

        $count = 0;

        // Buat absensi mapel untuk 7 hari ke belakang yang harinya cocok dengan day_of_week jadwal
        for ($i = 7; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            // Skip Minggu (0)
            if ($date->dayOfWeek === Carbon::SUNDAY) {
                continue;
            }

            // Cari jadwal yang day_of_week-nya sama dengan hari $date
            // Di Laravel/Carbon: Minggu=0, Senin=1, Selasa=2, Rabu=3, Kamis=4, Jumat=5, Sabtu=6
            $matchingSchedules = $schedules->where('day_of_week', $date->dayOfWeek);

            foreach ($matchingSchedules as $schedule) {
                foreach ($students as $student) {
                    $exists = ScheduleAttendance::where('schedule_id', $schedule->id)
                        ->where('student_id', $student->id)
                        ->where('attendance_date', $date->toDateString())
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    // Distribusi status: 85% Hadir, 5% Terlambat, 5% Sakit, 5% Izin
                    $rand = rand(1, 100);
                    if ($rand <= 85) {
                        $status = 'present';
                        $notes = null;
                    } elseif ($rand <= 90) {
                        $status = 'late';
                        $notes = 'Masuk setelah 10 menit pelajaran dimulai.';
                    } elseif ($rand <= 95) {
                        $status = 'sick';
                        $notes = 'Sakit flu, beristirahat di UKS.';
                    } else {
                        $status = 'permission';
                        $notes = 'Izin mengikuti lomba sekolah.';
                    }

                    $recordedAt = Carbon::parse($date->toDateString().' '.$schedule->start_time)->addMinutes(rand(5, 20));

                    ScheduleAttendance::create([
                        'schedule_id' => $schedule->id,
                        'student_id' => $student->id,
                        'teacher_id' => $schedule->teacher_id,
                        'attendance_date' => $date->toDateString(),
                        'status' => $status,
                        'notes' => $notes,
                        'recorded_at' => $recordedAt,
                    ]);

                    $count++;
                }
            }
        }

        $this->command->info("✅ {$count} rekaman absensi mata pelajaran di kelas berhasil di-seed.");
    }
}

