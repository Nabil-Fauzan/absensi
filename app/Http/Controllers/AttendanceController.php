<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\Attendance;
use App\Models\User;
use App\Mail\LeaveRequestedMail;
use App\Mail\LeaveStatusUpdatedMail;
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

        // Check if there is an active check-in (present and no check_out)
        $active = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->where('status', 'present')
            ->whereNull('check_out')
            ->first();

        if ($active) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absen masuk dan belum absen keluar.');
        }

        // Check if already submitted sick/leave today
        $hasLeave = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->whereIn('status', ['sick', 'leave'])
            ->first();

        if ($hasLeave) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan izin/sakit hari ini.');
        }

        // Calculate Geofencing
        $officeLat = \App\Models\Setting::get('office_latitude', env('OFFICE_LATITUDE', -6.873218738309585));
        $officeLon = \App\Models\Setting::get('office_longitude', env('OFFICE_LONGITUDE', 107.5609385222725));
        $officeRadius = \App\Models\Setting::get('office_radius_meters', env('OFFICE_RADIUS_METERS', 100));
        
        $workMode = 'wfo';
        if ($request->latitude && $request->longitude) {
            $distance = $this->calculateDistance($request->latitude, $request->longitude, $officeLat, $officeLon);
            if ($distance > $officeRadius) {
                $workMode = 'wfh';
            }
        } else {
            $workMode = 'wfh';
        }

        // Calculate Keterlambatan
        $limitStr = \App\Models\Setting::get('office_check_in_time', env('OFFICE_CHECK_IN_TIME', '08:00:00'));
        $now = Carbon::now();
        
        try {
            $limitTime = Carbon::parse($limitStr);
        } catch (\Exception $e) {
            $limitTime = Carbon::parse('08:00:00');
        }
        
        $limitTime->setDate($now->year, $now->month, $now->day);
        
        $minutesLate = 0;
        if ($now->greaterThan($limitTime)) {
            $minutesLate = $now->diffInMinutes($limitTime);
        }

        // If a present record already exists for today, we update it instead of creating a new one (due to DB unique constraint)
        $existingPresent = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->where('status', 'present')
            ->first();

        if ($existingPresent) {
            $existingPresent->update([
                'check_in' => $now->toTimeString(),
                'check_out' => null,
                'latitude_in' => $request->latitude,
                'longitude_in' => $request->longitude,
                'latitude_out' => null,
                'longitude_out' => null,
                'work_mode' => $workMode,
                'minutes_late' => $minutesLate,
            ]);
        } else {
            Attendance::create([
                'user_id' => $userId,
                'date' => $today,
                'check_in' => $now->toTimeString(),
                'status' => 'present',
                'latitude_in' => $request->latitude,
                'longitude_in' => $request->longitude,
                'work_mode' => $workMode,
                'minutes_late' => $minutesLate,
            ]);
        }

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
            ->whereNull('check_out')
            ->latest()
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
            'approval_status' => 'pending',
        ]);

        // Send email notification to Admin
        try {
            $adminEmail = User::where('role', 'admin')->value('email') ?? 'admin@absenkita.com';
            Mail::to($adminEmail)->send(new LeaveRequestedMail($attendance));
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

        if ($user instanceof \App\Models\User && $user->isAdmin()) {
            return redirect()->route('admin.attendance');
        }

        // Find if there is an open check-in today (present and no check_out)
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->where('status', 'present')
            ->whereNull('check_out')
            ->first();

        if (!$todayAttendance) {
            // Fallback to latest attendance of today (checked out or sick/leave)
            $todayAttendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->latest()
                ->first();
        }

        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        // Calculate Monthly Statistics for Employee
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        
        $monthlyAttendance = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();
            
        $monthlyStats = [
            'present' => $monthlyAttendance->where('status', 'present')->count(),
            'sick' => $monthlyAttendance->where('status', 'sick')->count(),
            'leave' => $monthlyAttendance->where('status', 'leave')->count(),
            'late_minutes' => $monthlyAttendance->where('status', 'present')->sum('minutes_late'),
        ];
        
        $totalDays = $monthlyStats['present'] + $monthlyStats['sick'] + $monthlyStats['leave'];
        $monthlyStats['attendance_rate'] = $totalDays > 0 ? round(($monthlyStats['present'] / $totalDays) * 100) : 0;

        return view('dashboard', compact('todayAttendance', 'attendances', 'monthlyStats'));
    }

    /**
     * Show admin attendance logs page.
     */
    public function adminAttendance(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $today = Carbon::today()->toDateString();
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

        if ($request->status) {
            if ($request->status === 'present') {
                $query->where('status', 'present');
            } elseif ($request->status === 'izin_sakit') {
                $query->whereIn('status', ['sick', 'leave']);
            } elseif ($request->status === 'terlambat') {
                $query->where('status', 'present')->where('minutes_late', '>', 0);
            }
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate daily stats for today
        $totalHadir = Attendance::where('date', $today)
            ->where('status', 'present')
            ->count();
            
        $totalIzinSakit = Attendance::where('date', $today)
            ->whereIn('status', ['leave', 'sick'])
            ->count();
            
        $totalTerlambat = Attendance::where('date', $today)
            ->where('status', 'present')
            ->where('minutes_late', '>', 0)
            ->count();
            
        $employeeUserIds = User::where('role', 'employee')->pluck('id');
        $absentTodayUserIds = Attendance::where('date', $today)->pluck('user_id');
        $totalBelumAbsen = $employeeUserIds->diff($absentTodayUserIds)->count();

        // Compile names of employees who haven't checked in
        $belumAbsenUsers = User::whereIn('id', $employeeUserIds->diff($absentTodayUserIds))
            ->orderBy('name', 'asc')
            ->get(['name', 'email']);

        $stats = [
            'hadir' => $totalHadir,
            'izin_sakit' => $totalIzinSakit,
            'terlambat' => $totalTerlambat,
            'belum_absen' => $totalBelumAbsen,
        ];

        // Calculate weekly attendance data (last 7 days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $dateString = $d->toDateString();
            $dayName = $d->translatedFormat('D'); // Sen, Sel, Rab, etc.
            
            $hadirCount = Attendance::where('date', $dateString)
                ->where('status', 'present')
                ->count();
                
            $izinCount = Attendance::where('date', $dateString)
                ->whereIn('status', ['sick', 'leave'])
                ->count();
                
            $employeeCount = User::where('role', 'employee')->count();
            $belumAbsenCount = max(0, $employeeCount - ($hadirCount + $izinCount));
            
            $chartData[] = [
                'label' => $dayName,
                'hadir' => $hadirCount,
                'izin' => $izinCount,
                'belum_absen' => $belumAbsenCount
            ];
        }

        // Office config parameter to display status details
        $officeLat = \App\Models\Setting::get('office_latitude', env('OFFICE_LATITUDE', -6.873218738309585));
        $officeLon = \App\Models\Setting::get('office_longitude', env('OFFICE_LONGITUDE', 107.5609385222725));
        $officeRadius = \App\Models\Setting::get('office_radius_meters', env('OFFICE_RADIUS_METERS', 100));
        $checkInTimeLimit = \App\Models\Setting::get('office_check_in_time', env('OFFICE_CHECK_IN_TIME', '08:00:00'));

        $officeConfig = [
            'latitude' => $officeLat,
            'longitude' => $officeLon,
            'radius' => $officeRadius,
            'check_in_limit' => $checkInTimeLimit,
        ];

        return view('admin.attendance', compact('attendances', 'stats', 'belumAbsenUsers', 'chartData', 'officeConfig'));
    }

    /**
     * Show admin employees list page.
     */
    public function adminEmployees(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $employees = User::where('role', 'employee')->orderBy('name', 'asc')->get();

        return view('admin.employees', compact('employees'));
    }

    /**
     * Show admin geofencing settings page.
     */
    public function adminSettings(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $officeLat = \App\Models\Setting::get('office_latitude', env('OFFICE_LATITUDE', -6.873218738309585));
        $officeLon = \App\Models\Setting::get('office_longitude', env('OFFICE_LONGITUDE', 107.5609385222725));
        $officeRadius = \App\Models\Setting::get('office_radius_meters', env('OFFICE_RADIUS_METERS', 100));
        $checkInTimeLimit = \App\Models\Setting::get('office_check_in_time', env('OFFICE_CHECK_IN_TIME', '08:00:00'));

        $officeConfig = [
            'latitude' => $officeLat,
            'longitude' => $officeLon,
            'radius' => $officeRadius,
            'check_in_limit' => $checkInTimeLimit,
        ];

        return view('admin.settings', compact('officeConfig'));
    }

    /**
     * Export the attendance report to a CSV file.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
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

        if ($request->status) {
            if ($request->status === 'present') {
                $query->where('status', 'present');
            } elseif ($request->status === 'izin_sakit') {
                $query->whereIn('status', ['sick', 'leave']);
            } elseif ($request->status === 'terlambat') {
                $query->where('status', 'present')->where('minutes_late', '>', 0);
            }
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

        $columns = ['Nama Karyawan', 'Tanggal', 'Status', 'Jam Masuk', 'Jam Pulang', 'Mode Kerja', 'Keterlambatan', 'Keterangan', 'Lokasi Masuk', 'Lokasi Pulang'];

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

                $workModeLabel = '-';
                if ($att->status === 'present') {
                    $workModeLabel = $att->work_mode === 'wfh' ? 'WFH (Luar Kantor)' : 'WFO (Di Kantor)';
                }

                $lateLabel = '-';
                if ($att->status === 'present') {
                    $lateLabel = $att->minutes_late > 0 ? "Terlambat {$att->minutes_late} Menit" : 'Tepat Waktu';
                }

                $locationIn = $att->latitude_in ? "https://www.google.com/maps/search/?api=1&query={$att->latitude_in},{$att->longitude_in}" : 'Tidak Ada GPS';
                $locationOut = $att->latitude_out ? "https://www.google.com/maps/search/?api=1&query={$att->latitude_out},{$att->longitude_out}" : 'Tidak Ada GPS';

                fputcsv($file, [
                    $att->user->name,
                    $att->date,
                    $statusLabel,
                    $att->check_in ?? '-',
                    $att->check_out ?? '-',
                    $workModeLabel,
                    $lateLabel,
                    $att->notes ?? '-',
                    $locationIn,
                    $locationOut
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calculate distance between two coordinates using the Haversine formula.
     * Returns distance in meters.
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // in meters
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    /**
     * Update geofencing limits and standard entrance office hours.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'office_latitude' => 'required|numeric',
            'office_longitude' => 'required|numeric',
            'office_radius_meters' => 'required|integer|min:1',
            'office_check_in_time' => 'required',
        ]);

        \App\Models\Setting::set('office_latitude', $request->office_latitude);
        \App\Models\Setting::set('office_longitude', $request->office_longitude);
        \App\Models\Setting::set('office_radius_meters', $request->office_radius_meters);
        \App\Models\Setting::set('office_check_in_time', $request->office_check_in_time);

        return back()->with('success', 'Pengaturan geofencing dan jam kantor berhasil diperbarui!')->with('active_tab', 'settings-tab');
    }

    /**
     * Store a newly created employee.
     */
    public function storeEmployee(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
        ]);

        return back()->with('success', 'Akun karyawan baru berhasil dibuat!')->with('active_tab', 'employee-tab');
    }

    /**
     * Update employee credentials.
     */
    public function updateEmployee(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $employee = User::where('role', 'employee')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'password' => 'nullable|string|min:8',
        ]);

        $employee->name = $request->name;
        $employee->email = $request->email;
        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }
        $employee->save();

        return back()->with('success', 'Data karyawan berhasil diperbarui!')->with('active_tab', 'employee-tab');
    }

    /**
     * Safely delete employee and their logs.
     */
    public function destroyEmployee(int $id)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $employee = User::where('role', 'employee')->findOrFail($id);
        
        // Remove linked attendances
        Attendance::where('user_id', $employee->id)->delete();
        $employee->delete();

        return back()->with('success', 'Akun karyawan beserta seluruh riwayat absensinya telah dihapus permanen.')->with('active_tab', 'employee-tab');
    }

    /**
     * Correct an attendance log.
     */
    public function updateAttendance(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'status' => 'required|in:present,sick,leave',
            'work_mode' => 'nullable|in:wfo,wfh',
            'minutes_late' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $attendance->status = $request->status;
        $attendance->work_mode = $request->status === 'present' ? $request->work_mode : null;
        $attendance->minutes_late = $request->status === 'present' ? $request->minutes_late : 0;
        $attendance->notes = $request->notes;
        $attendance->save();

        return back()->with('success', 'Catatan absensi karyawan berhasil diperbarui!');
    }

    /**
     * Remove an attendance record.
     */
    public function destroyAttendance(int $id)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Catatan absensi berhasil dihapus!');
    }

    /**
     * Approve a pending leave or sick request.
     */
    public function approveLeave(int $id)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $attendance = Attendance::findOrFail($id);
        
        if (!in_array($attendance->status, ['sick', 'leave'])) {
            return back()->with('error', 'Hanya pengajuan izin/sakit yang membutuhkan persetujuan.');
        }

        $attendance->update([
            'approval_status' => 'approved',
            'rejection_reason' => null
        ]);

        // Send email notification to employee
        try {
            Mail::to($attendance->user->email)->send(new LeaveStatusUpdatedMail($attendance));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send leave status email: " . $e->getMessage());
        }

        $typeLabel = $attendance->status === 'sick' ? 'Sakit' : 'Izin';
        return back()->with('success', "Pengajuan {$typeLabel} dari {$attendance->user->name} berhasil disetujui!");
    }

    /**
     * Reject a pending leave or sick request.
     */
    public function rejectLeave(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $attendance = Attendance::findOrFail($id);

        if (!in_array($attendance->status, ['sick', 'leave'])) {
            return back()->with('error', 'Hanya pengajuan izin/sakit yang membutuhkan persetujuan.');
        }

        $attendance->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        // Send email notification to employee
        try {
            Mail::to($attendance->user->email)->send(new LeaveStatusUpdatedMail($attendance));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send leave status email: " . $e->getMessage());
        }

        $typeLabel = $attendance->status === 'sick' ? 'Sakit' : 'Izin';
        return back()->with('success', "Pengajuan {$typeLabel} dari {$attendance->user->name} berhasil ditolak.");
    }
}
