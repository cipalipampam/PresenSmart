<?php

namespace App\Events;

use App\Services\Web\Dashboard\AdminDashboardStatsService;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DashboardStatsUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
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
            new PrivateChannel('admin.dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'DashboardStatsUpdated';
    }

    /**
     * Build stats in the broadcast worker after the transaction commits so
     * write requests do not wait for aggregate queries.
     */
    public function broadcastWith(): array
    {
        return app(AdminDashboardStatsService::class)->current();
    }
}
