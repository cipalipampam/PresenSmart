<?php

namespace App\Services\Console\Attendance;

use App\Models\Attendance;
use App\Models\User;
use Carbon\CarbonInterface;

class AbsentAttendanceService
{
    /**
     * Record one alfa entry for each required user without issuing one
     * attendance lookup per user.
     */
    public function recordForDate(CarbonInterface $date): int
    {
        $startOfDay = $date->copy()->startOfDay();
        $startOfNextDay = $startOfDay->copy()->addDay();
        $created = 0;

        User::role(['guru', 'staff', 'siswa'])
            ->select('id')
            ->orderBy('id')
            ->chunkById(500, function ($users) use ($startOfDay, $startOfNextDay, &$created) {
                $userIds = $users->pluck('id');
                $recordedUserIds = Attendance::query()
                    ->whereIn('user_id', $userIds)
                    ->where('recorded_at', '>=', $startOfDay)
                    ->where('recorded_at', '<', $startOfNextDay)
                    ->pluck('user_id')
                    ->all();

                $alreadyRecorded = array_flip($recordedUserIds);
                $timestamp = now();
                $records = $userIds
                    ->reject(fn (int $userId) => isset($alreadyRecorded[$userId]))
                    ->map(fn (int $userId) => [
                        'user_id' => $userId,
                        'status' => 'alfa',
                        'is_late' => false,
                        'recorded_at' => $timestamp,
                        'attendance_date' => $startOfDay->toDateString(),
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ])
                    ->all();

                if ($records !== []) {
                    Attendance::insert($records);
                    $created += count($records);
                }
            });

        return $created;
    }
}
