<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

/**
 * Private channel for per-user notifications (e.g. AttendanceApproved).
 * Only the authenticated user whose ID matches can subscribe.
 */
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Public channels — no auth needed (admin dashboard, announcements).
 * Public Channel objects do not require registration here,
 * but we define them explicitly for clarity and future restrictions.
 *
 * Channels: 'attendance-channel', 'dashboard-stats', 'announcements', 'system-settings'
 */
