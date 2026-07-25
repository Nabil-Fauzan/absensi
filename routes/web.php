<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Models\Attendance;
use App\Models\User;
use App\Http\Controllers\AttendanceController;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::post('/attendance/leave', [AttendanceController::class, 'leave'])->name('attendance.leave');
    Route::get('/admin/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');

    // Admin QoL feature routes
    Route::post('/admin/settings', [AttendanceController::class, 'updateSettings'])->name('admin.settings.update');
    Route::post('/admin/employees', [AttendanceController::class, 'storeEmployee'])->name('admin.employees.store');
    Route::patch('/admin/employees/{user}', [AttendanceController::class, 'updateEmployee'])->name('admin.employees.update');
    Route::delete('/admin/employees/{user}', [AttendanceController::class, 'destroyEmployee'])->name('admin.employees.destroy');
    Route::patch('/admin/attendance/{attendance}', [AttendanceController::class, 'updateAttendance'])->name('admin.attendance.update');
    Route::delete('/admin/attendance/{attendance}', [AttendanceController::class, 'destroyAttendance'])->name('admin.attendance.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
