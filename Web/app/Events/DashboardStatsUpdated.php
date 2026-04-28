<?php

namespace App\Events;

use App\Models\Attendance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class DashboardStatsUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $stats;

    /**
     * Stats are computed lazily in broadcastWith() — NOT in the constructor.
     * Constructors are serialized to the queue; running DB queries here
     * means 4 extra queries per serialization round-trip, not just per broadcast.
     */
    public function __construct() {}

    public function broadcastOn(): array
    {
        return [
            new Channel('dashboard-stats'),
        ];
    }

    /**
     * Build the stats payload right before broadcasting (not in constructor).
     * Using ShouldBroadcastNow so this runs inline without a queue.
     */
    public function broadcastWith(): array
    {
        return [
            'total_students'   => User::role('siswa')->count(),
            'total_present'    => Attendance::whereDate('recorded_at', today())->where('status', 'present')->count(),
            'total_late'       => Attendance::whereDate('recorded_at', today())->where('is_late', true)->count(),
            'total_permission' => Attendance::whereDate('recorded_at', today())->whereIn('status', ['permission', 'sick'])->count(),
        ];
    }
}
