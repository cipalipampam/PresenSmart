<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\PresensiController;

Route::get('/', function () {
    return view('welcome');
});

// Route Login Admin
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login_form');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login');

// Route Logout
Route::post('/logout', [AuthController::class, 'adminLogout'])->name('login');

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/lokasi', [AdminController::class, 'lokasi'])->name('lokasi');
    Route::post('/lokasi', [AdminController::class, 'updateLokasi'])->name('update_lokasi');

    // Route logout admin
    Route::post('/logout', [AuthController::class, 'adminLogout'])->name('logout');

    // Manajemen User
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Manajemen Absen
    Route::get('/absen', [AbsenController::class, 'index'])->name('absen');
    Route::get('/absen/create', [AdminController::class, 'createAbsen'])->name('absen_create');
    Route::post('/absen/create', [AdminController::class, 'storeAbsen'])->name('absen_store');
    Route::get('/absen/{id}/edit', [AdminController::class, 'editAbsen'])->name('absen_edit');
    Route::post('/absen/{id}/update', [AdminController::class, 'updateAbsen'])->name('absen_update');
    Route::post('/absen/{id}/delete', [AdminController::class, 'deleteAbsen'])->name('absen_delete');

    // Route tambahan untuk presensi
    Route::get('/absen/{id}/show', [AbsenController::class, 'show'])->name('absen.show');
    Route::get('/absen/{id}/edit-presensi', [AbsenController::class, 'edit'])->name('absen.edit');
    Route::put('/absen/{id}/update-presensi', [AbsenController::class, 'update'])->name('absen.update');
    Route::delete('/absen/{id}/hapus-presensi', [AbsenController::class, 'destroy'])->name('absen.destroy');

    // Route untuk manajemen presensi admin
    Route::get('/presensi', [PresensiController::class, 'adminIndex'])->name('presensi.index');
    Route::get('/presensi/{id}', [PresensiController::class, 'adminShow'])->name('presensi.show');
    Route::get('/presensi/{id}/edit', [PresensiController::class, 'adminEdit'])->name('presensi.edit');
    Route::put('/presensi/{id}', [PresensiController::class, 'adminUpdate'])->name('presensi.update');
    Route::delete('/presensi/{id}', [PresensiController::class, 'adminDestroy'])->name('presensi.destroy');
});

Route::get('/test-alpha-presensi', [App\Http\Controllers\PresensiController::class, 'checkAndRecordAlpha'])
    ->name('test.alpha.presensi');

Route::get('/debug-presensi-status', [App\Http\Controllers\PresensiController::class, 'debugPresensiStatus'])
    ->name('debug.presensi.status');

Route::get('/presensi/stats', [App\Http\Controllers\PresensiController::class, 'getPresensiStats'])
    ->name('presensi.stats');

// Hapus route export absen
// Route::get('/admin/absen/export', [App\Http\Controllers\PresensiController::class, 'export'])
//     ->name('admin.absen.export');

Route::get('/admin/absen/print', [App\Http\Controllers\PresensiController::class, 'print'])
    ->name('admin.absen.print');

Route::get('/admin/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->name('admin.dashboard');
