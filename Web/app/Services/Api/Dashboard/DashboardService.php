<?php

namespace App\Services\Api\Dashboard;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\User;
use App\Services\Shared\Settings\SettingCache;

class DashboardService
{
    public function forUser(User $user): array
    {
        $settings = SettingCache::all();
        $startOfDay = now()->startOfDay();
        $startOfNextDay = $startOfDay->copy()->addDay();
        $startOfMonth = now()->startOfMonth();
        $startOfNextMonth = $startOfMonth->copy()->addMonth();

        $todayAttendance = Attendance::query()
            ->where('user_id', $user->id)
            ->where('recorded_at', '>=', $startOfDay)
            ->where('recorded_at', '<', $startOfNextDay)
            ->first();

        $stats = Attendance::query()
            ->where('user_id', $user->id)
            ->where('recorded_at', '>=', $startOfMonth)
            ->where('recorded_at', '<', $startOfNextMonth)
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) as hadir,
                 COALESCE(SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END), 0) as izin,
                 COALESCE(SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END), 0) as alfa',
                ['present', 'permission', 'sick', 'alpha', 'absent'],
            )
            ->first();

        return [
            'schedule' => [
                'masuk' => substr($settings->get('check_in_end', '07:15'), 0, 5),
                'pulang' => substr($settings->get('check_out_start', '15:00'), 0, 5),
                'status' => $this->todayStatus($todayAttendance),
            ],
            'stats' => [
                'hadir' => (int) $stats->hadir,
                'izin' => (int) $stats->izin,
                'alfa' => (int) $stats->alfa,
            ],
            'announcements' => Announcement::query()
                ->where('is_active', true)
                ->latest()
                ->take(5)
                ->get(['id', 'title', 'content', 'created_at'])
                ->map(fn (Announcement $item) => [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'created_at' => $item->created_at?->toDateTimeString(),
                ]),
        ];
    }

    private function todayStatus(?Attendance $attendance): string
    {
        return match (true) {
            $attendance === null => 'Belum Absen',
            $attendance->status === 'sick' => 'Sakit',
            $attendance->status === 'permission' => 'Izin',
            $attendance->status === 'present' && $attendance->check_out_time !== null => 'Selesai',
            $attendance->status === 'present' => 'Hadir',
            default => 'Belum Absen',
        };
    }
}
