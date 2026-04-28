<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Setting\SettingController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\Attendance\AttendanceController;

/*
|--------------------------------------------------------------------------
| Mobile API Routes — v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Authentication (public)
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
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
    });

});
