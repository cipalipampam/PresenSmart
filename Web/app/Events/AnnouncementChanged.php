<?php

namespace App\Events;

use App\Models\Announcement;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnnouncementChanged implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        private readonly int $announcementId,
        private readonly string $action,
        private readonly ?string $changedAt = null,
    ) {}

    public static function fromAnnouncement(Announcement $announcement, string $action): self
    {
        return new self(
            $announcement->id,
            $action,
            $announcement->updated_at?->toIso8601String(),
        );
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('announcements')];
    }

    public function broadcastAs(): string
    {
        return 'AnnouncementChanged';
    }

    /** Clients refetch authorized data instead of receiving announcement content. */
    public function broadcastWith(): array
    {
        return [
            'announcement_id' => $this->announcementId,
            'action' => $this->action,
            'changed_at' => $this->changedAt,
        ];
    }
}
