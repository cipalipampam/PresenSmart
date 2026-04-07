<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Setting\SettingController;

/*
|--------------------------------------------------------------------------
| Mobile API Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Settings
    Route::get('/settings/location', [SettingController::class, 'location']);

    // Attendance
    Route::controller(\App\Http\Controllers\Api\Attendance\AttendanceController::class)->prefix('attendances')->group(function () {
        Route::get('/', 'history');
        Route::post('/check-in', 'checkIn');
        Route::post('/permission', 'permission');
    });
});
