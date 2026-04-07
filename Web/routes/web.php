<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Setting\SettingController;
use App\Http\Controllers\Web\Dashboard\DashboardController;
use App\Http\Controllers\Web\Attendance\AdminAttendanceController;
use App\Http\Controllers\Web\Student\StudentController;
use App\Http\Controllers\Web\Employee\EmployeeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect()->route('admin.login_form');
})->name('login');

Route::controller(AuthController::class)->group(function () {
    Route::get('/admin/login', 'showAdminLogin')->name('admin.login_form');
    Route::post('/admin/login', 'adminLogin')->name('admin.login');
    Route::post('/logout', 'adminLogout')->name('logout'); 
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::controller(SettingController::class)->prefix('locations')->group(function () {
        Route::get('/', 'location')->name('location');
        Route::post('/', 'updateLocation')->name('update_location');
    });

    // Delegated to decoupled StudentController
    Route::controller(StudentController::class)->prefix('students')->name('students.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{student}', 'show')->name('show');
        Route::get('/{student}/edit', 'edit')->name('edit');
        Route::put('/{student}', 'update')->name('update');
        Route::delete('/{student}', 'destroy')->name('destroy');
    });

    // Delegated to decoupled EmployeeController
    Route::controller(EmployeeController::class)->prefix('employees')->name('employees.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{employee}', 'show')->name('show');
        Route::get('/{employee}/edit', 'edit')->name('edit');
        Route::put('/{employee}', 'update')->name('update');
        Route::delete('/{employee}', 'destroy')->name('destroy');
    });

    Route::controller(AdminAttendanceController::class)->prefix('attendances')->name('attendances.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/report/print', 'print')->name('print');
    });
});
