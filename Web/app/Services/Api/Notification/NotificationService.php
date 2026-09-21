<?php

namespace App\Services\Api\Notification;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function forUser(User $user, string $filter, int $perPage): LengthAwarePaginator
    {
        $query = $user->appNotifications()->latest();

        if ($filter === 'unread') {
            $query->where('is_read', false);
        }

        return $query->paginate($perPage);
    }

    public function markAsRead(User $user, int $notificationId): ?AppNotification
    {
        $notification = $user->appNotifications()->find($notificationId);

        if (! $notification) {
            return null;
        }

        if (! $notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return $notification->fresh();
    }

    public function markAllAsRead(User $user): int
    {
        return $user->appNotifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
