<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

/**
 * Per-user notifications, such as an attendance approval decision.
 */
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * School-wide attendance data and aggregate dashboard statistics are visible
 * only to authenticated administrators.
 */
Broadcast::channel('admin.attendance', function ($user) {
    return $user->hasRole('admin');
});

Broadcast::channel('admin.dashboard', function ($user) {
    return $user->hasRole('admin');
});

/**
 * Signed-in users may receive updated attendance settings and location data.
 */
Broadcast::channel('settings', function ($user) {
    return $user !== null;
});

/**
 * The sole public channel is 'announcements', which contains non-sensitive
 * information intended for everyone.
 */
