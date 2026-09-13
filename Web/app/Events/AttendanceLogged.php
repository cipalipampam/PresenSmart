<?php

namespace App\Events;

use App\Models\Attendance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceLogged implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Attendance $attendance;

    public string $action;

    /**
     * Create a new event instance.
     */
    public function __construct(Attendance $attendance, string $action = 'created')
    {
        $this->attendance = $attendance->load('user');
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.attendance'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AttendanceLogged';
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
            'action' => $this->action,
            'audience' => $this->audience(),
        ];
    }

    private function audience(): string
    {
        return $this->attendance->user?->hasRole('siswa') ? 'siswa' : 'employee';
    }
}
