<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/presensi', [PresensiController::class, 'store']);
    Route::get('/presensi/riwayat', [PresensiController::class, 'riwayatPresensi']);
    Route::get('/presensi/{id}', [PresensiController::class, 'show']);
    Route::get('/presensi', [PresensiController::class, 'index']);
    Route::get('/profile', [UserController::class, 'profile']);
});
