<?php

namespace App\Events;

use App\Models\Attendance;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminAttendanceChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * This event is a refresh signal for administrator attendance views.
     * Its small scalar payload remains valid even after a record is deleted.
     */
    public function __construct(
        private readonly int $attendanceId,
        private readonly string $action,
    ) {}

    public static function fromAttendance(Attendance $attendance, string $action): self
    {
        return new self($attendance->id, $action);
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin.attendance')];
    }

    public function broadcastAs(): string
    {
        return 'AdminAttendanceChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'attendance_id' => $this->attendanceId,
            'action' => $this->action,
        ];
    }
}
