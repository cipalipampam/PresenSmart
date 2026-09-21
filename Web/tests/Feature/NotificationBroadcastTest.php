<?php

namespace Tests\Feature;

use App\Events\NotificationCreated;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NotificationBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_notification_dispatches_notification_created_event(): void
    {
        Event::fake([NotificationCreated::class]);

        $user = User::factory()->create();

        $notification = AppNotification::create([
            'user_id' => $user->id,
            'title' => 'Surat Sakit Disetujui',
            'body' => 'Pengajuan surat sakit Anda telah disetujui.',
            'type' => 'attendance',
            'data' => ['screen' => 'attendance'],
            'is_read' => false,
        ]);

        Event::assertDispatched(NotificationCreated::class, function ($event) use ($notification, $user) {
            $channels = $event->broadcastOn();
            $payload = $event->broadcastWith();

            return $event->notification->id === $notification->id
                && count($channels) === 1
                && $channels[0]->name === 'private-App.Models.User.'.$user->id
                && $event->broadcastAs() === 'NotificationCreated'
                && $payload['title'] === 'Surat Sakit Disetujui'
                && $payload['type'] === 'attendance'
                && $payload['unread_count'] === 1;
        });
    }

    public function test_broadcast_with_reflects_accurate_unread_count(): void
    {
        $user = User::factory()->create();

        // 1 notifikasi belum dibaca sebelumnya
        AppNotification::create([
            'user_id' => $user->id,
            'title' => 'Pengumuman 1',
            'body' => 'Info 1',
            'type' => 'announcement',
            'is_read' => false,
        ]);

        // Buat notifikasi kedua
        $notification2 = AppNotification::create([
            'user_id' => $user->id,
            'title' => 'Pengumuman 2',
            'body' => 'Info 2',
            'type' => 'announcement',
            'is_read' => false,
        ]);

        $event = new NotificationCreated($notification2);
        $payload = $event->broadcastWith();

        $this->assertSame(2, $payload['unread_count']);
    }
}

