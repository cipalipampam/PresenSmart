<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Presensi\PresensiController;
use App\Http\Controllers\Api\User\ProfileController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/profile', [ProfileController::class, 'profile']);

    Route::controller(PresensiController::class)->prefix('presensi')->group(function () {
        Route::post('/', 'store');
        Route::get('/riwayat', 'riwayatPresensi');
        Route::get('/{id}', 'show');
        Route::get('/', 'index');
    });
});
