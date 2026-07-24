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

Route::get('/dashboard', function () {
    $user = Auth::user();
    $today = Carbon::today()->toDateString();

    if ($user->isAdmin()) {
        // Admin sees all records
        $attendances = Attendance::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('dashboard', compact('attendances'));
    }

    // Employee sees their own records
    $todayAttendance = Attendance::where('user_id', $user->id)
        ->where('date', $today)
        ->first();

    $attendances = Attendance::where('user_id', $user->id)
        ->orderBy('date', 'desc')
        ->get();

    return view('dashboard', compact('todayAttendance', 'attendances'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::post('/attendance/leave', [AttendanceController::class, 'leave'])->name('attendance.leave');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
