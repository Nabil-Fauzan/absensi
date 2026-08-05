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

    // Admin Pages Routes
    Route::get('/admin/attendance', [AttendanceController::class, 'adminAttendance'])->name('admin.attendance');
    Route::get('/admin/employees', [AttendanceController::class, 'adminEmployees'])->name('admin.employees');
    Route::get('/admin/settings', [AttendanceController::class, 'adminSettings'])->name('admin.settings');

    // Admin QoL feature routes
    Route::get('/admin/attendance/print', [AttendanceController::class, 'printReport'])->name('admin.attendance.print');
    Route::post('/admin/settings', [AttendanceController::class, 'updateSettings'])->name('admin.settings.update');
    Route::post('/admin/employees', [AttendanceController::class, 'storeEmployee'])->name('admin.employees.store');
    Route::patch('/admin/employees/{user}', [AttendanceController::class, 'updateEmployee'])->name('admin.employees.update');
    Route::delete('/admin/employees/{user}', [AttendanceController::class, 'destroyEmployee'])->name('admin.employees.destroy');
    Route::patch('/admin/attendance/{attendance}', [AttendanceController::class, 'updateAttendance'])->name('admin.attendance.update');
    Route::delete('/admin/attendance/{attendance}', [AttendanceController::class, 'destroyAttendance'])->name('admin.attendance.destroy');
    Route::patch('/admin/attendance/{attendance}/approve', [AttendanceController::class, 'approveLeave'])->name('admin.attendance.approve');
    Route::patch('/admin/attendance/{attendance}/reject', [AttendanceController::class, 'rejectLeave'])->name('admin.attendance.reject');

    // Branch CRUD Routes
    Route::post('/admin/branches', [AttendanceController::class, 'storeBranch'])->name('admin.branches.store');
    Route::patch('/admin/branches/{branch}', [AttendanceController::class, 'updateBranch'])->name('admin.branches.update');
    Route::delete('/admin/branches/{branch}', [AttendanceController::class, 'destroyBranch'])->name('admin.branches.destroy');

    // Shift CRUD Routes
    Route::post('/admin/shifts', [AttendanceController::class, 'storeShift'])->name('admin.shifts.store');
    Route::patch('/admin/shifts/{shift}', [AttendanceController::class, 'updateShift'])->name('admin.shifts.update');
    Route::delete('/admin/shifts/{shift}', [AttendanceController::class, 'destroyShift'])->name('admin.shifts.destroy');

    // Holiday CRUD Routes
    Route::post('/admin/holidays', [AttendanceController::class, 'storeHoliday'])->name('admin.holidays.store');
    Route::delete('/admin/holidays/{holiday}', [AttendanceController::class, 'destroyHoliday'])->name('admin.holidays.destroy');

    // Employee Monthly Presensi Slip
    Route::get('/admin/employees/{user}/print-slip', [AttendanceController::class, 'printSlip'])->name('admin.employees.print-slip');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
