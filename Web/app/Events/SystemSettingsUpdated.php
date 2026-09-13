<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SystemSettingsUpdated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $settings;

    /**
     * Create a new event instance.
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('settings'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'SystemSettingsUpdated';
    }

    /** Clients refetch settings instead of receiving their values over Reverb. */
    public function broadcastWith(): array
    {
        return ['changed_keys' => array_keys($this->settings)];
    }
}
