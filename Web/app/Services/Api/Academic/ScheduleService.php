<?php

namespace App\Services\Api\Academic;

use App\Models\Schedule;
use App\Models\User;

class ScheduleService
{
    /** Return null when the account has no active academic schedule context. */
    public function forUser(User $user, ?int $dayOfWeek): ?array
    {
        $dayOfWeek ??= now()->dayOfWeekIso;

        if ($user->hasRole('guru') && ($user->employee?->is_teacher ?? true)) {
            $role = 'teacher';
            $query = Schedule::query()->where('teacher_id', $user->id);
        } elseif ($user->student?->classroom_id && $user->student->academic_status === 'active') {
            $role = 'student';
            $query = Schedule::query()->where('classroom_id', $user->student->classroom_id);
        } else {
            return null;
        }

        $schedules = $query
            ->where('is_active', true)
            ->where('day_of_week', $dayOfWeek)
            ->with(['classroom:id,name,level,major,section,academic_year', 'subject:id,code,name,color_code', 'teacher:id,name'])
            ->orderBy('start_time')
            ->get()
            ->map(fn (Schedule $schedule) => $this->scheduleData($schedule));

        return [
            'role' => $role,
            'day_of_week' => $dayOfWeek,
            'schedules' => $schedules,
        ];
    }

    private function scheduleData(Schedule $schedule): array
    {
        return [
            'id' => $schedule->id,
            'day_of_week' => $schedule->day_of_week,
            'day_name' => $schedule->day_name,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'room' => $schedule->room,
            'classroom' => $schedule->classroom,
            'subject' => $schedule->subject,
            'teacher' => $schedule->teacher,
        ];
    }
}
