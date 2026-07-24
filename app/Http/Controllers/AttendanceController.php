<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Attendance;
use App\Mail\LeaveRequestedMail;
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
            'latitude_in' => $request->latitude,
            'longitude_in' => $request->longitude,
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
            'latitude_out' => $request->latitude,
            'longitude_out' => $request->longitude,
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

        $attendance = Attendance::create([
            'user_id' => $userId,
            'date' => $today,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // Send email notification to Admin
        try {
            Mail::to('admin@absenkita.com')->send(new LeaveRequestedMail($attendance));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send email notification: " . $e->getMessage());
        }

        $statusLabel = $request->status === 'sick' ? 'Sakit' : 'Izin';
        return redirect()->back()->with('success', "Pengajuan {$statusLabel} berhasil diajukan!");
    }

    /**
     * Show the application dashboard.
     */
    public function dashboard(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        if ($user->isAdmin()) {
            // Admin sees all records with search & date filters
            $query = Attendance::with('user');

            if ($request->search) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->start_date) {
                $query->where('date', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->where('date', '<=', $request->end_date);
            }

            $attendances = $query->orderBy('date', 'desc')
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
    }

    /**
     * Export the attendance report to a CSV file.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $query = Attendance::with('user');

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "rekap_absensi_" . Carbon::now()->format('Y-m-d_H-i-s') . ".csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Nama Karyawan', 'Tanggal', 'Status', 'Jam Masuk', 'Jam Pulang', 'Keterangan', 'Lokasi Masuk', 'Lokasi Pulang'];

        $callback = function() use($attendances, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($file, $columns, ';');

            foreach ($attendances as $att) {
                $statusLabel = match($att->status) {
                    'present' => 'Hadir',
                    'sick' => 'Sakit',
                    'leave' => 'Izin',
                    default => $att->status
                };

                $locationIn = $att->latitude_in ? "https://www.google.com/maps/search/?api=1&query={$att->latitude_in},{$att->longitude_in}" : 'Tidak Ada GPS';
                $locationOut = $att->latitude_out ? "https://www.google.com/maps/search/?api=1&query={$att->latitude_out},{$att->longitude_out}" : 'Tidak Ada GPS';

                fputcsv($file, [
                    $att->user->name,
                    $att->date,
                    $statusLabel,
                    $att->check_in ?? '-',
                    $att->check_out ?? '-',
                    $att->notes ?? '-',
                    $locationIn,
                    $locationOut
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
