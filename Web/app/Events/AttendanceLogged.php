<?php

namespace App\Events;

use App\Models\Attendance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceLogged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $attendance;

    /**
     * Create a new event instance.
     */
    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance->load('user');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('attendance-channel'),
        ];
    }

    /**
     * Data yang akan dikirim ke klien.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->attendance->id,
            'user_name' => $this->attendance->user->name ?? 'Unknown',
            'status' => $this->attendance->status,
            'time' => $this->attendance->recorded_at ? $this->attendance->recorded_at->format('H:i:s') : now()->format('H:i:s'),
        ];
    }
}
