<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\User\AdminUserController;
use App\Http\Controllers\Web\Setting\SettingController;
use App\Http\Controllers\Web\Dashboard\DashboardController;
use App\Http\Controllers\Web\Presensi\AbsenController;
use App\Http\Controllers\Web\Presensi\AdminPresensiController;

Route::get('/', function () {
    return view('welcome');
});

// Route Login Admin
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login_form');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login');

// Route Logout
Route::post('/logout', [AuthController::class, 'adminLogout'])->name('login');

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/lokasi', [SettingController::class, 'lokasi'])->name('lokasi');
    Route::post('/lokasi', [SettingController::class, 'updateLokasi'])->name('update_lokasi');

    // Route logout admin
    Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');

    // Manajemen User
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Manajemen Absen
    Route::get('/absen', [AbsenController::class, 'index'])->name('absen');
    Route::get('/absen/create', [AbsenController::class, 'createAbsen'])->name('absen_create');
    Route::post('/absen/create', [AbsenController::class, 'storeAbsen'])->name('absen_store');
    Route::get('/absen/{id}/edit', [AbsenController::class, 'editAbsen'])->name('absen_edit');
    Route::post('/absen/{id}/update', [AbsenController::class, 'updateAbsen'])->name('absen_update');
    Route::post('/absen/{id}/delete', [AbsenController::class, 'deleteAbsen'])->name('absen_delete');

    // Route tambahan untuk presensi
    Route::get('/absen/{id}/show', [AbsenController::class, 'show'])->name('absen.show');
    Route::get('/absen/{id}/edit-presensi', [AbsenController::class, 'edit'])->name('absen.edit');
    Route::put('/absen/{id}/update-presensi', [AbsenController::class, 'update'])->name('absen.update');
    Route::delete('/absen/{id}/hapus-presensi', [AbsenController::class, 'destroy'])->name('absen.destroy');

    // Route untuk manajemen presensi admin
    Route::get('/presensi', [AdminPresensiController::class, 'adminIndex'])->name('presensi.index');
    Route::get('/presensi/{id}', [AdminPresensiController::class, 'adminShow'])->name('presensi.show');
    Route::get('/presensi/{id}/edit', [AdminPresensiController::class, 'adminEdit'])->name('presensi.edit');
    Route::put('/presensi/{id}', [AdminPresensiController::class, 'adminUpdate'])->name('presensi.update');
    Route::delete('/presensi/{id}', [AdminPresensiController::class, 'adminDestroy'])->name('presensi.destroy');
});

Route::get('/test-alpha-presensi', [AdminPresensiController::class, 'checkAndRecordAlpha'])
    ->name('test.alpha.presensi');

Route::get('/debug-presensi-status', [AdminPresensiController::class, 'debugPresensiStatus'])
    ->name('debug.presensi.status');

Route::get('/presensi/stats', [AdminPresensiController::class, 'getPresensiStats'])
    ->name('presensi.stats');

// Hapus route export absen
// Route::get('/admin/absen/export', [AdminPresensiController::class, 'export'])
//     ->name('admin.absen.export');

Route::get('/admin/absen/print', [AdminPresensiController::class, 'print'])
    ->name('admin.absen.print');

Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->name('admin.dashboard');
