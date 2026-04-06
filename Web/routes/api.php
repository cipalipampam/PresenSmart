<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Presensi\PresensiController;
use App\Http\Controllers\Api\User\ProfileController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/presensi', [PresensiController::class, 'store']);
    Route::get('/presensi/riwayat', [PresensiController::class, 'riwayatPresensi']);
    Route::get('/presensi/{id}', [PresensiController::class, 'show']);
    Route::get('/presensi', [PresensiController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'profile']);
});
