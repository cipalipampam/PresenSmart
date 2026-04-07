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

Route::controller(AuthController::class)->group(function () {
    Route::get('/admin/login', 'showAdminLogin')->name('admin.login_form');
    Route::post('/admin/login', 'adminLogin')->name('admin.login');
    Route::post('/logout', 'adminLogout')->name('logout'); 
});

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::controller(SettingController::class)->prefix('lokasi')->group(function () {
        Route::get('/', 'lokasi')->name('lokasi');
        Route::post('/', 'updateLokasi')->name('update_lokasi');
    });

    Route::controller(AdminUserController::class)->prefix('users')->group(function () {
        Route::get('/', 'index')->name('users');
        Route::get('/create', 'create')->name('users.create');
        Route::post('/', 'store')->name('users.store');
        Route::get('/{id}', 'show')->name('users.show');
        Route::get('/{id}/edit', 'edit')->name('users.edit');
        Route::put('/{id}', 'update')->name('users.update');
        Route::delete('/{id}', 'destroy')->name('users.destroy');
    });

    Route::controller(AbsenController::class)->prefix('absen')->group(function () {
        Route::get('/', 'index')->name('absen');
        Route::get('/create', 'createAbsen')->name('absen_create');
        Route::post('/create', 'storeAbsen')->name('absen_store');
        Route::get('/{id}/edit', 'editAbsen')->name('absen_edit');
        Route::post('/{id}/update', 'updateAbsen')->name('absen_update');
        Route::post('/{id}/delete', 'deleteAbsen')->name('absen_delete');
        
        Route::get('/{id}/show', 'show')->name('absen.show');
        Route::get('/{id}/edit-presensi', 'edit')->name('absen.edit');
        Route::put('/{id}/update-presensi', 'update')->name('absen.update');
        Route::delete('/{id}/hapus-presensi', 'destroy')->name('absen.destroy');
    });
    
    Route::controller(AdminPresensiController::class)->prefix('presensi')->name('presensi.')->group(function () {
        Route::get('/', 'adminIndex')->name('index');
        Route::get('/{id}', 'adminShow')->name('show');
        Route::get('/{id}/edit', 'adminEdit')->name('edit');
        Route::put('/{id}', 'adminUpdate')->name('update');
        Route::delete('/{id}', 'adminDestroy')->name('destroy');
    });
    
    // Utilities dari Print Absen dari AdminPresensiController
    Route::get('/absen/print', [AdminPresensiController::class, 'print'])->name('absen.print');
});

// Endpoint Utilities & CronJobs
Route::controller(AdminPresensiController::class)->group(function () {
    Route::get('/test-alpha-presensi', 'checkAndRecordAlpha')->name('test.alpha.presensi');
    Route::get('/debug-presensi-status', 'debugPresensiStatus')->name('debug.presensi.status');
    Route::get('/presensi/stats', 'getPresensiStats')->name('presensi.stats');
});
