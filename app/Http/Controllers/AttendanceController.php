<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Handle the check-in process.
     */
    public function checkIn(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        // Check if already checked in or submitted permission
        $existing = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi atau mengajukan izin hari ini.');
        }

        Attendance::create([
            'user_id' => $userId,
            'date' => $today,
            'check_in' => Carbon::now()->toTimeString(),
            'status' => 'present',
        ]);

        return redirect()->back()->with('success', 'Absen masuk berhasil dicatat!');
    }

    /**
     * Handle the check-out process.
     */
    public function checkOut(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->where('status', 'present')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'Data absensi masuk hari ini tidak ditemukan.');
        }

        if ($attendance->check_out) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absen keluar hari ini.');
        }

        $attendance->update([
            'check_out' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Absen keluar berhasil dicatat!');
    }

    /**
     * Handle the sick or leave request.
     */
    public function leave(Request $request)
    {
        $request->validate([
            'status' => 'required|in:sick,leave',
            'notes' => 'required|string|max:500',
        ]);

        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        $existing = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi atau mengajukan izin hari ini.');
        }

        Attendance::create([
            'user_id' => $userId,
            'date' => $today,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        $statusLabel = $request->status === 'sick' ? 'Sakit' : 'Izin';
        return redirect()->back()->with('success', "Pengajuan {$statusLabel} berhasil diajukan!");
    }
}
