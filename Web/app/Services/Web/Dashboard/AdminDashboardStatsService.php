<?php

namespace App\Services\Web\Dashboard;

use App\Models\Attendance;
use App\Models\User;

class AdminDashboardStatsService
{
    public function current(): array
    {
        $startOfDay = now()->startOfDay();
        $startOfNextDay = $startOfDay->copy()->addDay();

        $attendance = Attendance::query()
            ->where('recorded_at', '>=', $startOfDay)
            ->where('recorded_at', '<', $startOfNextDay)
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) as total_present,
                 COALESCE(SUM(CASE WHEN is_late = ? THEN 1 ELSE 0 END), 0) as total_late,
                 COALESCE(SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END), 0) as total_permission',
                ['present', true, 'permission', 'sick'],
            )
            ->first();

        $pendingApprovals = Attendance::query()
            ->whereIn('status', ['permission', 'sick'])
            ->whereNull('is_approved')
            ->count();

        return [
            'total_students' => User::role('siswa')->count(),
            'total_present' => (int) $attendance->total_present,
            'total_late' => (int) $attendance->total_late,
            'total_permission' => (int) $attendance->total_permission,
            'pending_approvals' => $pendingApprovals,
        ];
    }
}
