<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DirectoryChanged implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        private readonly int $userId,
        private readonly string $audience,
        private readonly string $action,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin.directory')];
    }

    public function broadcastAs(): string
    {
        return 'DirectoryChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'audience' => $this->audience,
            'action' => $this->action,
        ];
    }
}
