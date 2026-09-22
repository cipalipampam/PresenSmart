<?php

namespace App\Services\Web\Academic;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ScheduleService
{
    public const JP_MINUTES = 45;
    public const MIN_JP_PER_WEEK = 24;
    public const MAX_JP_PER_WEEK = 40;
    public const SCHOOL_START_TIME = '07:00';
    public const SCHOOL_END_TIME = '16:00';

    /**
     * Menghitung rincian beban mengajar guru secara komprehensif.
     */
    public function calculateTeacherWeeklyWorkload(User $teacher): array
    {
        $schedules = Schedule::with(['subject', 'classroom'])
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $totalMinutes = 0;
        $totalJp = 0;
        $subjectWorkloads = [];
        $dayBreakdown = [
            1 => ['name' => 'Senin', 'jp' => 0, 'minutes' => 0, 'slots' => 0],
            2 => ['name' => 'Selasa', 'jp' => 0, 'minutes' => 0, 'slots' => 0],
            3 => ['name' => 'Rabu', 'jp' => 0, 'minutes' => 0, 'slots' => 0],
            4 => ['name' => 'Kamis', 'jp' => 0, 'minutes' => 0, 'slots' => 0],
            5 => ['name' => 'Jumat', 'jp' => 0, 'minutes' => 0, 'slots' => 0],
        ];

        // Eager load teacher subjects (utama vs serumpun)
        $teacherSubjects = $teacher->subjects()->get()->keyBy('id');

        foreach ($schedules as $sched) {
            $duration = $sched->duration_in_minutes;
            $jp = $sched->jp_count;

            $totalMinutes += $duration;
            $totalJp += $jp;

            $subId = $sched->subject_id;
            if (! isset($subjectWorkloads[$subId])) {
                $isPrimary = false;
                $cluster = $sched->subject->cluster ?? 'umum';
                if ($teacherSubjects->has($subId)) {
                    $isPrimary = (bool) ($teacherSubjects->get($subId)->pivot->is_primary ?? false);
                }

                $subjectWorkloads[$subId] = [
                    'subject' => $sched->subject,
                    'is_primary' => $isPrimary,
                    'cluster' => $cluster,
                    'total_jp' => 0,
                    'total_minutes' => 0,
                    'slots_count' => 0,
                    'classrooms' => [],
                ];
            }

            $subjectWorkloads[$subId]['total_jp'] += $jp;
            $subjectWorkloads[$subId]['total_minutes'] += $duration;
            $subjectWorkloads[$subId]['slots_count']++;
            if ($sched->classroom && ! in_array($sched->classroom->name, $subjectWorkloads[$subId]['classrooms'], true)) {
                $subjectWorkloads[$subId]['classrooms'][] = $sched->classroom->name;
            }

            if (isset($dayBreakdown[$sched->day_of_week])) {
                $dayBreakdown[$sched->day_of_week]['jp'] += $jp;
                $dayBreakdown[$sched->day_of_week]['minutes'] += $duration;
                $dayBreakdown[$sched->day_of_week]['slots']++;
            }
        }

        // Status beban tatap muka regulasi
        $workloadStatus = 'optimal';
        $statusLabel = 'Memenuhi Syarat (24 - 40 JP)';
        $statusColor = 'success';

        if ($totalJp < self::MIN_JP_PER_WEEK) {
            $workloadStatus = 'underload';
            $deficit = self::MIN_JP_PER_WEEK - $totalJp;
            $statusLabel = "Kurang {$deficit} JP dari Batas Minimal (24 JP)";
            $statusColor = 'warning';
        } elseif ($totalJp > self::MAX_JP_PER_WEEK) {
            $workloadStatus = 'overload';
            $excess = $totalJp - self::MAX_JP_PER_WEEK;
            $statusLabel = "Kelebihan {$excess} JP dari Batas Maksimal (40 JP)";
            $statusColor = 'danger';
        }

        $percentage = min(100, round(($totalJp / self::MIN_JP_PER_WEEK) * 100));

        return [
            'total_jp' => $totalJp,
            'total_minutes' => $totalMinutes,
            'total_hours_real' => round($totalMinutes / 60, 1),
            'slots_count' => $schedules->count(),
            'status' => $workloadStatus,
            'status_label' => $statusLabel,
            'status_color' => $statusColor,
            'target_percentage' => $percentage,
            'min_target_jp' => self::MIN_JP_PER_WEEK,
            'max_target_jp' => self::MAX_JP_PER_WEEK,
            'subjects' => array_values($subjectWorkloads),
            'days' => $dayBreakdown,
            'schedules' => $schedules,
        ];
    }

    /**
     * Memvalidasi seluruh aturan operasional KBM, linieritas guru, dan anti-bentrok.
     *
     * @throws ValidationException
     */
    public function validateScheduleSlot(array $data, ?int $ignoreScheduleId = null): void
    {
        $dayOfWeek = (int) $data['day_of_week'];
        $startTime = $data['start_time'];
        $end_time = $data['end_time'];
        $teacherId = (int) $data['teacher_id'];
        $subjectId = (int) $data['subject_id'];
        $classroomId = (int) $data['classroom_id'];

        // 1. Hari Sekolah (Senin - Jumat)
        if ($dayOfWeek < 1 || $dayOfWeek > 5) {
            throw ValidationException::withMessages([
                'day_of_week' => 'Jadwal KBM hanya dapat diselenggarakan pada hari Senin hingga Jumat (Full Day School).',
            ]);
        }

        // 2. Format & Rentang Jam Belajar (07:00 - 16:00)
        $startCarbon = Carbon::parse($startTime);
        $endCarbon = Carbon::parse($end_time);

        if ($startCarbon->greaterThanOrEqualTo($endCarbon)) {
            throw ValidationException::withMessages([
                'end_time' => 'Jam selesai mengajar harus lebih besar dari jam mulai.',
            ]);
        }

        $earliest = Carbon::parse(self::SCHOOL_START_TIME);
        $latest = Carbon::parse(self::SCHOOL_END_TIME);

        if ($startCarbon->format('H:i') < $earliest->format('H:i') || $endCarbon->format('H:i') > $latest->format('H:i')) {
            throw ValidationException::withMessages([
                'start_time' => 'Waktu kegiatan belajar mengajar harus berada di dalam rentang pukul 07:00 hingga 16:00 WIB.',
            ]);
        }

        // 2b. Break Time Guard (Pasal 2 Permendikbud No. 23/2017 - Istirahat I & Ishoma)
        // Istirahat I (Dhuha & Camilan): 09:30 - 10:00 WIB (Senin - Jumat)
        // Istirahat II (Ishoma Utama): 11:45 - 13:00 WIB (Senin - Kamis), 11:30 - 13:00 WIB (Jumat)
        $startStr = $startCarbon->format('H:i');
        $endStr = $endCarbon->format('H:i');

        // Check Istirahat I (09:30 - 10:00)
        if ($startStr < '10:00' && $endStr > '09:30') {
            throw ValidationException::withMessages([
                'start_time' => 'Jadwal KBM bertabrakan dengan Istirahat I / Sholat Dhuha (09:30 - 10:00 WIB).',
            ]);
        }

        // Check Istirahat II / Ishoma Utama
        $ishomaStart = ($dayOfWeek == 5) ? '11:30' : '11:45';
        $ishomaEnd = '13:00';
        if ($startStr < $ishomaEnd && $endStr > $ishomaStart) {
            $label = ($dayOfWeek == 5) ? 'Sholat Jumat & Makan Siang (11:30 - 13:00 WIB)' : 'Ishoma Utama & Sholat Dzuhur (11:45 - 13:00 WIB)';
            throw ValidationException::withMessages([
                'start_time' => "Jadwal KBM bertabrakan dengan waktu {$label}.",
            ]);
        }

        // 3. Durasi Minimal (1 JP = 45 menit)
        $durationMinutes = $startCarbon->diffInMinutes($endCarbon);
        if ($durationMinutes < self::JP_MINUTES) {
            throw ValidationException::withMessages([
                'end_time' => 'Durasi mengajar minimal adalah 1 JP (45 menit).',
            ]);
        }

        // 4. Linieritas Guru-Mata Pelajaran (Guru hanya boleh mengajar mapel yang terdaftar di profile)
        $teacher = User::with('subjects')->find($teacherId);
        if (! $teacher) {
            throw ValidationException::withMessages([
                'teacher_id' => 'Data guru tidak ditemukan.',
            ]);
        }

        $canTeach = $teacher->subjects()->where('subjects.id', $subjectId)->exists();
        if (! $canTeach) {
            $subject = Subject::find($subjectId);
            $subjectName = $subject ? $subject->name : 'mata pelajaran tersebut';
            throw ValidationException::withMessages([
                'teacher_id' => "Guru {$teacher->name} tidak terdaftar linier untuk mengajar mapel {$subjectName}.",
            ]);
        }

        // 5. Anti-Bentrok Guru (Teacher Clash)
        $teacherClash = Schedule::with(['classroom', 'subject'])
            ->where('teacher_id', $teacherId)
            ->where('is_active', true)
            ->overlapping($dayOfWeek, $startTime, $end_time, $ignoreScheduleId)
            ->first();

        if ($teacherClash) {
            $clashClass = $teacherClash->classroom->name ?? 'Kelas lain';
            $clashRange = substr($teacherClash->start_time, 0, 5).' - '.substr($teacherClash->end_time, 0, 5);
            throw ValidationException::withMessages([
                'teacher_id' => "Bentrok jadwal! Guru {$teacher->name} sudah memiliki jadwal di {$clashClass} pada pukul {$clashRange}.",
            ]);
        }

        // 6. Anti-Bentrok Ruang Kelas / Rombel (Classroom Clash)
        $classroomClash = Schedule::with(['subject', 'teacher'])
            ->where('classroom_id', $classroomId)
            ->where('is_active', true)
            ->overlapping($dayOfWeek, $startTime, $end_time, $ignoreScheduleId)
            ->first();

        if ($classroomClash) {
            $clashSub = $classroomClash->subject->name ?? 'Mata Pelajaran lain';
            $clashRange = substr($classroomClash->start_time, 0, 5).' - '.substr($classroomClash->end_time, 0, 5);
            throw ValidationException::withMessages([
                'classroom_id' => "Bentrok jadwal! Rombel kelas ini sudah memiliki kegiatan pembelajaran {$clashSub} pada pukul {$clashRange}.",
            ]);
        }
    }

    /**
     * Membuat jadwal baru dengan validasi integritas.
     */
    public function createSchedule(array $data): Schedule
    {
        $this->validateScheduleSlot($data);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        return Schedule::create($data);
    }

    /**
     * Memperbarui jadwal dengan validasi integritas.
     */
    public function updateSchedule(Schedule $schedule, array $data): bool
    {
        $this->validateScheduleSlot($data, $schedule->id);

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }

        return $schedule->update($data);
    }

    /**
     * Menghapus jadwal.
     */
    public function deleteSchedule(Schedule $schedule): bool
    {
        return (bool) $schedule->delete();
    }
}

