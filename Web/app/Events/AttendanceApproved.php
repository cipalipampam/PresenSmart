<?php

namespace App\Events;

use App\Models\Attendance;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceApproved implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Attendance $attendance;
    public string $message;

    public function __construct(Attendance $attendance, string $message = '')
    {
        $this->attendance = $attendance;
        $this->message    = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->attendance->user_id),
        ];
    }

    /**
     * Previously had no broadcastWith() — the full Attendance model would be
     * serialized as the payload which exposes unnecessary data.
     * Now sends a compact, intentional payload.
     */
    public function broadcastWith(): array
    {
        return [
            'attendance_id' => $this->attendance->id,
            'status'        => $this->attendance->status,
            'is_approved'   => $this->attendance->is_approved,
            'message'       => $this->message,
        ];
    }
}
