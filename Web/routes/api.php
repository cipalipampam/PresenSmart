<?php

use App\Http\Controllers\Api\Academic\ClassAttendanceApiController;
use App\Http\Controllers\Api\Academic\ScheduleApiController;
use App\Http\Controllers\Api\Attendance\AttendanceController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\Notification\NotificationApiController;
use App\Http\Controllers\Api\Setting\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile API Routes — v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Authentication (public)
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/attendances/{attendance}/proof', [AttendanceController::class, 'proof'])
        ->middleware(['signed', 'throttle:60,1'])
        ->name('api.attendances.proof');

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Dashboard Aggregate
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Settings
        Route::get('/settings/location', [SettingController::class, 'location']);

        // Attendance
        Route::controller(AttendanceController::class)->prefix('attendances')->group(function () {
            Route::get('/', 'history');
            Route::post('/check-in', 'checkIn');
            Route::post('/check-out', 'checkOut');
            Route::post('/permission', 'permission');
        });

        // Academic schedule and subject attendance
        Route::get('/schedules', [ScheduleApiController::class, 'index']);
        Route::get('/schedules/{schedule}/class-attendance', [ClassAttendanceApiController::class, 'index']);
        Route::post('/schedules/{schedule}/class-attendance', [ClassAttendanceApiController::class, 'store']);

        // In-app notification centre
        Route::get('/notifications', [NotificationApiController::class, 'index']);
        Route::patch('/notifications/{notification}/read', [NotificationApiController::class, 'markAsRead'])->whereNumber('notification');
        Route::post('/notifications/mark-all-read', [NotificationApiController::class, 'markAllAsRead']);
    });

});
