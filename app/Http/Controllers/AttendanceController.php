<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Branch;
use App\Models\Shift;
use App\Models\Holiday;
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

        // Prevent check-in if there is already an active or completed present attendance today
        $existingPresent = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->where('status', 'present')
            ->first();

        if ($existingPresent) {
            if ($existingPresent->check_out) {
                return redirect()->back()->with('error', 'Anda sudah melakukan absen masuk dan keluar hari ini.');
            }
            return redirect()->back()->with('error', 'Anda sudah melakukan absen masuk hari ini.');
        }

        // Check if already submitted sick/leave today
        $hasLeave = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->whereIn('status', ['sick', 'leave'])
            ->first();

        if ($hasLeave) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan izin/sakit hari ini.');
        }

        // Load user with branch and shift relations
        $user = User::with(['branch', 'shift'])->find($userId);

        // 1. Resolve Office/Branch Coordinates
        if ($user && $user->branch) {
            $officeLat = $user->branch->latitude;
            $officeLon = $user->branch->longitude;
            $officeRadius = $user->branch->radius_meters;
        } else {
            $officeLat = \App\Models\Setting::get('office_latitude', env('OFFICE_LATITUDE', -6.873218738309585));
            $officeLon = \App\Models\Setting::get('office_longitude', env('OFFICE_LONGITUDE', 107.5609385222725));
            $officeRadius = \App\Models\Setting::get('office_radius_meters', env('OFFICE_RADIUS_METERS', 100));
        }

        // 2. Resolve Client Location & IP Fallback
        $gpsLat = $request->latitude;
        $gpsLon = $request->longitude;
        $ipLat = $request->ip_latitude;
        $ipLon = $request->ip_longitude;
        
        $lat = null;
        $lon = null;
        $isIpFallback = false;

        if ($gpsLat !== null && $gpsLon !== null && $gpsLat !== '' && $gpsLon !== '') {
            $lat = floatval($gpsLat);
            $lon = floatval($gpsLon);
        } elseif ($ipLat !== null && $ipLon !== null && $ipLat !== '' && $ipLon !== '') {
            $lat = floatval($ipLat);
            $lon = floatval($ipLon);
            $isIpFallback = true;
        }

        // 3. Mock Geolocation Detection
        $isSuspicious = false;
        $spoofReason = null;

        // A. Accuracy Check
        if ($gpsLat !== null && $gpsLon !== null) {
            // Check if accuracy is 0 (a standard signature of fake GPS software on web browser)
            if ($request->has('accuracy') && floatval($request->accuracy) === 0.0) {
                $isSuspicious = true;
                $spoofReason = 'Akurasi GPS bernilai 0 (Potensi GPS Palsu)';
            }
            if ($request->mocked == true || $request->mocked === 'true') {
                $isSuspicious = true;
                $spoofReason = 'Deteksi emulator GPS aktif';
            }
            
            // B. IP Distance Deviation Check
            if ($ipLat !== null && $ipLon !== null && $ipLat !== '' && $ipLon !== '') {
                $ipDistance = $this->calculateDistance($lat, $lon, floatval($ipLat), floatval($ipLon));
                // If GPS is > 50km away from IP location, flag as suspicious
                if ($ipDistance > 50000) {
                    $isSuspicious = true;
                    $spoofReason = 'Penyimpangan GPS dan IP > 50km (' . round($ipDistance / 1000) . ' km)';
                }
            }
        }

        // 4. Calculate Geofencing
        $workMode = 'wfh';
        if ($lat !== null && $lon !== null) {
            $distance = $this->calculateDistance($lat, $lon, $officeLat, $officeLon);
            if ($distance <= $officeRadius) {
                $workMode = 'wfo';
            }
        }

        // 5. Calculate Keterlambatan relative to Shift
        if ($user && $user->shift) {
            $limitStr = $user->shift->start_time;
        } else {
            $limitStr = \App\Models\Setting::get('office_check_in_time', env('OFFICE_CHECK_IN_TIME', '08:00:00'));
        }

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

        // 6. Create new attendance
        Attendance::create([
            'user_id' => $userId,
            'date' => $today,
            'check_in' => $now->toTimeString(),
            'status' => 'present',
            'latitude_in' => $lat,
            'longitude_in' => $lon,
            'work_mode' => $workMode,
            'minutes_late' => $minutesLate,
            'is_suspicious' => $isSuspicious,
            'spoof_reason' => $spoofReason,
            'is_ip_fallback' => $isIpFallback,
            'notes' => $isIpFallback ? 'Absen via IP Fallback (' . ($request->ip_city ?? 'Lokasi IP') . ')' : null,
        ]);

        if ($isSuspicious) {
            return redirect()->back()->with('success', 'Absen masuk berhasil dicatat, namun ditandai mencurigakan: ' . $spoofReason);
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
            return redirect()->back()->with('error', 'Data absensi masuk hari ini tidak ditemukan atau Anda sudah melakukan absen keluar.');
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

        // Detect National Holiday for Today
        $todayHoliday = Holiday::where('date', $today)->first();

        // Detect Birthday
        $isBirthday = false;
        if ($user->birthdate) {
            $isBirthday = Carbon::parse($user->birthdate)->format('m-d') === Carbon::today()->format('m-d');
        }

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

        // Calculate Yearly Leaves Usage for Quotas
        $startOfYear = Carbon::now()->startOfYear()->toDateString();
        $endOfYear = Carbon::now()->endOfYear()->toDateString();
        
        $yearlyAttendance = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfYear, $endOfYear])
            ->get();

        $birthdayLeaveUsed = 0;
        if ($user->birthdate) {
            $birthdayThisYear = Carbon::parse($user->birthdate)->year(Carbon::now()->year)->toDateString();
            $birthdayLeaveUsed = $yearlyAttendance->where('status', 'leave')
                ->where('approval_status', 'approved')
                ->where('date', $birthdayThisYear)
                ->count();
        }

        $yearlyLeaves = $yearlyAttendance->where('status', 'leave')->where('approval_status', 'approved');
        if ($user->birthdate) {
            $birthdayThisYear = Carbon::parse($user->birthdate)->year(Carbon::now()->year)->toDateString();
            $yearlyLeaves = $yearlyLeaves->where('date', '!=', $birthdayThisYear);
        }
        $annualLeavesUsed = $yearlyLeaves->count();

        $monthlyStats['annual_leaves_left'] = max(0, 15 - $annualLeavesUsed);
        $monthlyStats['birthday_leaves_left'] = $user->birthdate ? max(0, 1 - $birthdayLeaveUsed) : 0;

        return view('dashboard', compact('todayAttendance', 'attendances', 'monthlyStats', 'todayHoliday', 'isBirthday'));
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
        $query = $this->applyAttendanceFilters($query, $request);

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Detect National Holiday for Today
        $todayHoliday = Holiday::where('date', $today)->first();

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
        $belumAbsenUserIds = $employeeUserIds->diff($absentTodayUserIds);
        
        // If today is a national holiday, set belum_absen to 0
        if ($todayHoliday) {
            $totalBelumAbsen = 0;
            $belumAbsenUsers = collect();
        } else {
            $totalBelumAbsen = $belumAbsenUserIds->count();
            $belumAbsenUsers = User::whereIn('id', $belumAbsenUserIds)
                ->orderBy('name', 'asc')
                ->get(['name', 'email']);
        }

        $stats = [
            'hadir' => $totalHadir,
            'izin_sakit' => $totalIzinSakit,
            'terlambat' => $totalTerlambat,
            'belum_absen' => $totalBelumAbsen,
        ];

        // Calculate weekly attendance data (last 7 days)
        $chartData = [];
        $employeeCount = User::where('role', 'employee')->count();
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
                
            $isDateHoliday = Holiday::where('date', $dateString)->exists();
            if ($isDateHoliday) {
                $belumAbsenCount = 0;
            } else {
                $belumAbsenCount = max(0, $employeeCount - ($hadirCount + $izinCount));
            }
            
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

        return view('admin.attendance', compact('attendances', 'stats', 'belumAbsenUsers', 'chartData', 'officeConfig', 'todayHoliday'));
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

        $employees = User::with(['branch', 'shift'])->where('role', 'employee')->orderBy('name', 'asc')->get();
        $branches = Branch::orderBy('name', 'asc')->get();
        $shifts = Shift::orderBy('name', 'asc')->get();

        return view('admin.employees', compact('employees', 'branches', 'shifts'));
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

        $branches = Branch::orderBy('name', 'asc')->get();
        $shifts = Shift::orderBy('name', 'asc')->get();
        $holidays = Holiday::orderBy('date', 'desc')->get();

        return view('admin.settings', compact('officeConfig', 'branches', 'shifts', 'holidays'));
    }

    /**
     * Export the attendance report to a styled Excel (XLS) file.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $query = Attendance::with(['user.branch', 'user.shift']);
        $query = $this->applyAttendanceFilters($query, $request);

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "rekap_absensi_" . Carbon::now()->format('Y-m-d_H-i-s') . ".xls";

        $headers = [
            "Content-Type"        => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($attendances) {
            $output = fopen('php://output', 'w');
            
            // Output UTF-8 BOM
            fputs($output, "\xEF\xBB\xBF");
            
            // Styled Excel HTML markup
            $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
<style>
    th {
        background-color: #059669;
        color: #ffffff;
        font-weight: bold;
        border: 0.5px solid #d1d5db;
        padding: 10px 14px;
        font-size: 11px;
        text-align: center;
        font-family: "Segoe UI", Arial, sans-serif;
    }
    td {
        border: 0.5px solid #e5e7eb;
        padding: 8px 10px;
        font-size: 10px;
        vertical-align: middle;
        font-family: "Segoe UI", Arial, sans-serif;
    }
    .font-mono {
        font-family: "Courier New", monospace;
    }
    .text-center {
        text-align: center;
    }
    .text-right {
        text-align: right;
    }
    .badge-present {
        background-color: #d1fae5;
        color: #065f46;
        font-weight: bold;
        text-align: center;
    }
    .badge-sick {
        background-color: #fef3c7;
        color: #92400e;
        font-weight: bold;
        text-align: center;
    }
    .badge-leave {
        background-color: #dbeafe;
        color: #1e40af;
        font-weight: bold;
        text-align: center;
    }
    .badge-wfo {
        background-color: #f3f4f6;
        color: #374151;
        text-align: center;
    }
    .badge-wfh {
        background-color: #fef3c7;
        color: #92400e;
        text-align: center;
    }
    .text-suspicious {
        background-color: #fee2e2;
        color: #b91c1c;
        font-weight: bold;
    }
    .text-late {
        color: #dc2626;
        font-weight: bold;
    }
    .text-on-time {
        color: #059669;
        font-weight: bold;
    }
</style>
<!--[if gte mso 9]>
<xml>
<x:ExcelWorkbook>
  <x:ExcelWorksheets>
    <x:ExcelWorksheet>
      <x:Name>Rekap Absensi</x:Name>
      <x:WorksheetOptions>
        <x:DisplayGridlines/>
      </x:WorksheetOptions>
    </x:ExcelWorksheet>
  </x:ExcelWorksheets>
</x:ExcelWorkbook>
</xml>
<![endif]-->
</head>
<body>
<table>
    <thead>
        <tr>
            <th>Nama Karyawan</th>
            <th>Email</th>
            <th>Cabang Kantor</th>
            <th>Shift Kerja</th>
            <th>Tanggal</th>
            <th>Status Kehadiran</th>
            <th>Status Approval</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
            <th>Mode Kerja</th>
            <th>Keterlambatan</th>
            <th>Lokasi Masuk (Koordinat)</th>
            <th>Lokasi Keluar (Koordinat)</th>
            <th>Status Keamanan GPS</th>
            <th>Catatan / Alasan Penolakan</th>
        </tr>
    </thead>
    <tbody>';
            
            fputs($output, $html);

            foreach ($attendances as $att) {
                $statusLabel = match($att->status) {
                    'present' => 'Hadir',
                    'sick' => 'Sakit',
                    'leave' => 'Izin',
                    default => $att->status
                };

                $statusClass = match($att->status) {
                    'present' => 'badge-present',
                    'sick' => 'badge-sick',
                    'leave' => 'badge-leave',
                    default => ''
                };

                $approvalLabel = '-';
                $approvalClass = '';
                if (in_array($att->status, ['sick', 'leave'])) {
                    $approvalLabel = match($att->approval_status) {
                        'pending' => 'Menunggu Persetujuan',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $att->approval_status
                    };
                    $approvalClass = match($att->approval_status) {
                        'approved' => 'badge-present',
                        'rejected' => 'text-late',
                        default => ''
                    };
                }

                $workModeLabel = '-';
                $workModeClass = '';
                if ($att->status === 'present') {
                    $workModeLabel = $att->work_mode === 'wfh' ? 'WFH' : 'WFO';
                    $workModeClass = $att->work_mode === 'wfh' ? 'badge-wfh' : 'badge-wfo';
                }

                $lateLabel = '-';
                $lateClass = '';
                if ($att->status === 'present') {
                    $lateLabel = $att->minutes_late > 0 ? "Terlambat {$att->minutes_late} Menit" : 'Tepat Waktu';
                    $lateClass = $att->minutes_late > 0 ? 'text-late' : 'text-on-time';
                }

                // Clean coordinates display
                $locationIn = 'Tidak Ada GPS';
                if ($att->latitude_in) {
                    $locationIn = $att->is_ip_fallback 
                        ? "IP Fallback: {$att->latitude_in}, {$att->longitude_in}"
                        : "{$att->latitude_in}, {$att->longitude_in}";
                }

                $locationOut = 'Tidak Ada GPS';
                if ($att->latitude_out) {
                    $locationOut = "{$att->latitude_out}, {$att->longitude_out}";
                }

                // Security Status
                $securityLabel = 'Aman';
                $securityClass = '';
                if ($att->status === 'present' && $att->is_suspicious) {
                    $securityLabel = "Mencurigakan: {$att->spoof_reason}";
                    $securityClass = "text-suspicious";
                }

                // Combine notes and rejection reason
                $notes = $att->notes ?? '';
                if ($att->status !== 'present' && $att->approval_status === 'rejected' && $att->rejection_reason) {
                    $notes = ($notes ? $notes . ' | ' : '') . 'Penolakan: ' . $att->rejection_reason;
                }
                if (empty($notes)) {
                    $notes = '-';
                }

                $row = '<tr>';
                $row .= '<td>' . htmlspecialchars($att->user->name) . '</td>';
                $row .= '<td>' . htmlspecialchars($att->user->email) . '</td>';
                $row .= '<td>' . htmlspecialchars($att->user->branch->name ?? 'Kantor Pusat') . '</td>';
                $row .= '<td>' . htmlspecialchars($att->user->shift->name ?? 'Default (08:00)') . '</td>';
                $row .= '<td class="text-center font-mono">' . Carbon::parse($att->date)->translatedFormat('d-m-Y') . '</td>';
                $row .= '<td class="' . $statusClass . '">' . $statusLabel . '</td>';
                $row .= '<td class="text-center ' . $approvalClass . '">' . $approvalLabel . '</td>';
                $row .= '<td class="text-center font-mono">' . ($att->check_in ?? '-') . '</td>';
                $row .= '<td class="text-center font-mono">' . ($att->check_out ?? '-') . '</td>';
                $row .= '<td class="' . $workModeClass . '">' . $workModeLabel . '</td>';
                $row .= '<td class="text-center ' . $lateClass . '">' . $lateLabel . '</td>';
                $row .= '<td class="font-mono">' . htmlspecialchars($locationIn) . '</td>';
                $row .= '<td class="font-mono">' . htmlspecialchars($locationOut) . '</td>';
                $row .= '<td class="' . $securityClass . '">' . htmlspecialchars($securityLabel) . '</td>';
                $row .= '<td>' . htmlspecialchars($notes) . '</td>';
                $row .= '</tr>';

                fputs($output, $row);
            }

            fputs($output, '</tbody></table></body></html>');
            fclose($output);
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
            'branch_id' => 'nullable|exists:branches,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'birthdate' => 'nullable|date',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
            'branch_id' => $request->branch_id,
            'shift_id' => $request->shift_id,
            'birthdate' => $request->birthdate,
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
            'branch_id' => 'nullable|exists:branches,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'birthdate' => 'nullable|date',
        ]);

        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->branch_id = $request->branch_id;
        $employee->shift_id = $request->shift_id;
        $employee->birthdate = $request->birthdate;
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
        
        // Attendances are automatically deleted via database foreign key cascade constraint
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

    /**
     * Show a print-ready report of the attendance log.
     */
    public function printReport(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $query = Attendance::with('user');
        $query = $this->applyAttendanceFilters($query, $request);

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.print-report', compact('attendances'));
    }

    // --- BRANCH CRUD ---
    public function storeBranch(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:1',
        ]);

        Branch::create($request->all());

        return back()->with('success', 'Cabang kantor baru berhasil ditambahkan!')->with('active_tab', 'branches-tab');
    }

    public function updateBranch(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $branch = Branch::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:1',
        ]);

        $branch->update($request->all());

        return back()->with('success', 'Data cabang kantor berhasil diperbarui!')->with('active_tab', 'branches-tab');
    }

    public function destroyBranch(int $id)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $branch = Branch::findOrFail($id);
        $branch->delete();

        return back()->with('success', 'Cabang kantor berhasil dihapus!')->with('active_tab', 'branches-tab');
    }

    // --- SHIFT CRUD ---
    public function storeShift(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        Shift::create($request->all());

        return back()->with('success', 'Shift kerja baru berhasil ditambahkan!')->with('active_tab', 'shifts-tab');
    }

    public function updateShift(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $shift = Shift::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $shift->update($request->all());

        return back()->with('success', 'Data shift kerja berhasil diperbarui!')->with('active_tab', 'shifts-tab');
    }

    public function destroyShift(int $id)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $shift = Shift::findOrFail($id);
        $shift->delete();

        return back()->with('success', 'Shift kerja berhasil dihapus!')->with('active_tab', 'shifts-tab');
    }

    // --- HOLIDAY CRUD ---
    public function storeHoliday(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|unique:holidays,date',
        ]);

        Holiday::create($request->all());

        return back()->with('success', 'Hari libur nasional baru berhasil ditambahkan!')->with('active_tab', 'holidays-tab');
    }

    public function destroyHoliday(int $id)
    {
        $user = Auth::user();
        if (!$user instanceof \App\Models\User || !$user->isAdmin()) {
            abort(403);
        }

        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return back()->with('success', 'Hari libur berhasil dihapus!')->with('active_tab', 'holidays-tab');
    }

    /**
     * Show a print-ready monthly attendance slip for a specific employee.
     */
    public function printSlip(User $user, Request $request)
    {
        $admin = Auth::user();
        if (!$admin instanceof \App\Models\User || !$admin->isAdmin()) {
            abort(403);
        }

        // Target month/year default to current month
        $monthParam = $request->input('month', Carbon::now()->format('m'));
        $yearParam = $request->input('year', Carbon::now()->format('Y'));
        
        $startDate = Carbon::createFromDate($yearParam, $monthParam, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($yearParam, $monthParam, 1)->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy('date');

        $holidays = Holiday::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->keyBy(fn($h) => Carbon::parse($h->date)->toDateString());

        $daysGrid = [];
        $tempDate = $startDate->copy();

        $totalHadir = 0;
        $totalSakit = 0;
        $totalIzinTahunan = 0;
        $totalIzinUltah = 0;
        $totalAlfa = 0;
        $totalTerlambatMenit = 0;

        while ($tempDate->lte($endDate)) {
            $dateStr = $tempDate->toDateString();
            $isWeekend = $tempDate->isWeekend();
            $holidayObj = $holidays->get($dateStr);
            $att = $attendances->get($dateStr);

            $status = 'Alfa'; // Default if work day and no check-in
            $checkIn = '-';
            $checkOut = '-';
            $workMode = '-';
            $lateness = 0;
            $notes = '';

            if ($att) {
                $notes = $att->notes ?? '';
                if ($att->status === 'present') {
                    $status = 'Hadir';
                    $checkIn = $att->check_in ?? '-';
                    $checkOut = $att->check_out ?? '-';
                    $workMode = strtoupper($att->work_mode ?? '-');
                    $lateness = $att->minutes_late;
                    $totalHadir++;
                    $totalTerlambatMenit += $lateness;
                } elseif ($att->status === 'sick') {
                    $status = 'Sakit';
                    $totalSakit++;
                } elseif ($att->status === 'leave') {
                    // Check if birthday leave
                    $isBirthdayLeave = $user->birthdate && Carbon::parse($user->birthdate)->format('m-d') === $tempDate->format('m-d');
                    if ($isBirthdayLeave) {
                        $status = 'Cuti Ulang Tahun';
                        $totalIzinUltah++;
                    } else {
                        $status = 'Cuti Tahunan';
                        $totalIzinTahunan++;
                    }
                }
            } else {
                if ($isWeekend) {
                    $status = 'Akhir Pekan';
                } elseif ($holidayObj) {
                    $status = 'Hari Libur (' . $holidayObj->name . ')';
                } elseif ($tempDate->gt(Carbon::now())) {
                    $status = '-'; // Future date
                } else {
                    $totalAlfa++;
                }
            }

            $daysGrid[] = [
                'date' => $tempDate->copy(),
                'day_name' => $tempDate->translatedFormat('l'),
                'status' => $status,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'work_mode' => $workMode,
                'lateness' => $lateness,
                'notes' => $notes,
            ];

            $tempDate->addDay();
        }

        $stats = [
            'hadir' => $totalHadir,
            'sakit' => $totalSakit,
            'izin_tahunan' => $totalIzinTahunan,
            'izin_ultah' => $totalIzinUltah,
            'alfa' => $totalAlfa,
            'terlambat' => $totalTerlambatMenit,
        ];

        $monthName = $startDate->translatedFormat('F Y');

        return view('admin.print-slip', compact('user', 'daysGrid', 'stats', 'monthName'));
    }

    /**
     * Helper to apply common search and date filters to the attendance query.
     */
    private function applyAttendanceFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request): \Illuminate\Database\Eloquent\Builder
    {
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

        return $query;
    }
}
