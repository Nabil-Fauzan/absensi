<x-app-layout>
    <!-- Leaflet CSS & JS for Map Modal -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Notification -->
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-850 dark:text-green-400 border border-green-200 dark:border-green-800 shadow-sm transition duration-150 ease-in-out" role="alert">
                    <span class="font-medium">Sukses!</span> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-850 dark:text-red-400 border border-red-200 dark:border-red-800 shadow-sm transition duration-150 ease-in-out" role="alert">
                    <span class="font-medium">Gagal!</span> {{ session('error') }}
                </div>
            @endif

            @if (Auth::user()->isAdmin())
                <!-- ================= ADMIN DASHBOARD ================= -->
                
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-150 dark:border-gray-700 flex flex-col justify-between">
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hadir Hari Ini</div>
                            <div class="text-4xl font-extrabold mt-2 text-emerald-600 dark:text-emerald-400">
                                {{ $stats['hadir'] }}
                            </div>
                        </div>
                        <div class="text-xs mt-3 text-gray-500 dark:text-gray-400">Karyawan masuk kantor (WFO/WFH)</div>
                    </div>
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-150 dark:border-gray-700 flex flex-col justify-between">
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sakit / Izin</div>
                            <div class="text-4xl font-extrabold mt-2 text-blue-600 dark:text-blue-400">
                                {{ $stats['izin_sakit'] }}
                            </div>
                        </div>
                        <div class="text-xs mt-3 text-gray-500 dark:text-gray-400">Karyawan absen dengan keterangan</div>
                    </div>
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-150 dark:border-gray-700 flex flex-col justify-between">
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Terlambat</div>
                            <div class="text-4xl font-extrabold mt-2 text-amber-600 dark:text-amber-400">
                                {{ $stats['terlambat'] }}
                            </div>
                        </div>
                        <div class="text-xs mt-3 text-gray-500 dark:text-gray-400">Absen masuk setelah 08:00 WIB</div>
                    </div>
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-150 dark:border-gray-700 flex flex-col justify-between">
                        <div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Belum Absen</div>
                            <div class="text-4xl font-extrabold mt-2 text-rose-600 dark:text-rose-400">
                                {{ $stats['belum_absen'] }}
                            </div>
                        </div>
                        <div class="text-xs mt-3 text-gray-500 dark:text-gray-400">Karyawan belum absen hari ini</div>
                    </div>
                </div>

                <!-- Geofencing Status Box -->
                <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-150 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4 text-sm text-gray-600 dark:text-gray-300">
                    <div class="flex items-center gap-2">
                        <span class="p-2 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl">
                            🏢
                        </span>
                        <div>
                            <div class="font-bold text-gray-850 dark:text-white">Status Konfigurasi Geofencing Kantor</div>
                            <div class="text-xs text-gray-400">Parameter deteksi kehadiran fisik di kantor</div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 text-xs">
                        <div class="px-3 py-1.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl font-medium flex items-center gap-1.5">
                            <span class="text-gray-400">Koordinat:</span>
                            <span class="font-mono font-semibold text-gray-800 dark:text-gray-250">{{ $officeConfig['latitude'] }}, {{ $officeConfig['longitude'] }}</span>
                        </div>
                        <div class="px-3 py-1.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl font-medium flex items-center gap-1.5">
                            <span class="text-gray-400">Radius Batas:</span>
                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $officeConfig['radius'] }} meter</span>
                        </div>
                        <div class="px-3 py-1.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl font-medium flex items-center gap-1.5">
                            <span class="text-gray-400">Jam Masuk Standar:</span>
                            <span class="font-semibold text-rose-600 dark:text-rose-400">{{ \Carbon\Carbon::parse($officeConfig['check_in_limit'])->format('H:i') }} WIB</span>
                        </div>
                    </div>
                </div>

                <!-- Filter Form & Export -->
                <div class="mb-6 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                    <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label for="search" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Cari Karyawan</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik nama..." class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label for="start_date" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition duration-150 active:scale-95 shadow-md">
                                🔍 Filter
                            </button>
                            <a href="{{ route('dashboard') }}" class="py-2.5 px-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-750 dark:text-gray-200 font-bold rounded-xl text-sm transition duration-150 active:scale-95 text-center flex items-center justify-center">
                                Reset
                            </a>
                            <a href="{{ route('attendance.export', request()->all()) }}" class="py-2.5 px-4 bg-emerald-100 dark:bg-emerald-950/40 hover:bg-emerald-250 dark:hover:bg-emerald-900/60 text-emerald-800 dark:text-emerald-450 font-bold rounded-xl text-sm transition duration-150 active:scale-95 text-center flex items-center justify-center gap-1">
                                📥 Ekspor
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Admin Attendance Log Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-150">Rekap Absensi Karyawan</h3>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-xs font-semibold text-gray-600 dark:text-gray-300 rounded-full">
                            Semua Riwayat
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">
                                    <th class="p-4">Karyawan</th>
                                    <th class="p-4">Tanggal</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4">Jam Masuk</th>
                                    <th class="p-4">Jam Keluar</th>
                                    <th class="p-4">Lokasi</th>
                                    <th class="p-4">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-350">
                                @forelse($attendances as $att)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition duration-150">
                                        <td class="p-4 font-semibold text-gray-900 dark:text-white">
                                            {{ $att->user->name }}
                                            <div class="text-xs text-gray-400 font-normal">{{ $att->user->email }}</div>
                                            @if($att->status === 'present')
                                                <div class="mt-1">
                                                    @if($att->work_mode === 'wfo')
                                                        <span class="px-2 py-0.5 text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-450 rounded border border-emerald-200 dark:border-emerald-900/60">🏢 WFO (Di Kantor)</span>
                                                    @else
                                                        <span class="px-2 py-0.5 text-[9px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-450 rounded border border-amber-200 dark:border-amber-900/60">🏠 WFH (Luar Kantor)</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-4 text-gray-900 dark:text-gray-300 font-semibold">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }}</td>
                                        <td class="p-4">
                                            @if($att->status === 'present')
                                                <span class="px-2.5 py-1 text-xs font-bold bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-400 rounded-full">🟢 Hadir</span>
                                            @elseif($att->status === 'sick')
                                                <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-400 rounded-full">🩺 Sakit</span>
                                            @else
                                                <span class="px-2.5 py-1 text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-400 rounded-full">📄 Izin</span>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            <div class="font-mono font-bold text-gray-900 dark:text-white">{{ $att->check_in ?? '-' }}</div>
                                            @if($att->status === 'present' && $att->minutes_late > 0)
                                                <div class="text-[10px] font-semibold text-rose-600 dark:text-rose-400 mt-0.5">
                                                    ⏱ Terlambat {{ $att->minutes_late }} m
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-4 font-mono font-bold text-gray-900 dark:text-white">{{ $att->check_out ?? '-' }}</td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-1 text-xs">
                                                @if($att->latitude_in)
                                                    <button type="button" onclick="openMapModal({{ $att->latitude_in }}, {{ $att->longitude_in }}, '{{ addslashes($att->user->name) }}', 'Absen Masuk (Check-In) - {{ $att->check_in }}', '{{ $att->work_mode === 'wfh' ? '🏠 WFH (Luar Kantor)' : '🏢 WFO (Di Kantor)' }}')" class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-450 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 rounded-md font-semibold flex items-center gap-0.5 border border-emerald-150 dark:border-emerald-900/60 transition duration-150 active:scale-95">
                                                        📍 Masuk
                                                    </button>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                                
                                                <span class="text-gray-300 dark:text-gray-600">/</span>
                                                
                                                @if($att->latitude_out)
                                                    <button type="button" onclick="openMapModal({{ $att->latitude_out }}, {{ $att->longitude_out }}, '{{ addslashes($att->user->name) }}', 'Absen Keluar (Check-Out) - {{ $att->check_out }}', '{{ $att->work_mode === 'wfh' ? '🏠 WFH (Luar Kantor)' : '🏢 WFO (Di Kantor)' }}')" class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-450 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 rounded-md font-semibold flex items-center gap-0.5 border border-emerald-150 dark:border-emerald-900/60 transition duration-150 active:scale-95">
                                                        📍 Keluar
                                                    </button>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="p-4 text-xs text-gray-500 dark:text-gray-400 italic max-w-xs truncate">{{ $att->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-8 text-center text-gray-400 dark:text-gray-500">
                                            Belum ada data absensi tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                <!-- ================= EMPLOYEE DASHBOARD ================= -->

                <!-- Quick Check-In / Check-Out Widget -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Today's Status -->
                    <div class="lg:col-span-2 p-8 bg-emerald-600 rounded-3xl shadow-xl text-white flex flex-col justify-between min-h-[250px]">
                        <div>
                            <div class="text-sm font-semibold opacity-75 uppercase tracking-wider">Status Absensi Hari Ini</div>
                            <div class="text-lg font-bold mt-1 opacity-90">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</div>
                            
                            <div class="mt-6">
                                @if(!$todayAttendance)
                                    <div class="text-xl font-medium text-emerald-100">Belum melakukan absensi hari ini. Silakan klik absen masuk atau ajukan izin.</div>
                                @elseif($todayAttendance->status === 'present')
                                    <div class="space-y-2">
                                        <div class="text-3xl font-extrabold flex items-center gap-2">
                                            <span class="inline-block w-3 h-3 rounded-full bg-green-400 animate-ping"></span>
                                            Sudah Absen Masuk
                                        </div>
                                        <div class="text-sm text-emerald-100">
                                            Jam Masuk: <span class="font-mono font-bold bg-white/20 px-2 py-0.5 rounded text-white">{{ $todayAttendance->check_in }}</span>
                                            @if($todayAttendance->check_out)
                                                | Jam Pulang: <span class="font-mono font-bold bg-white/20 px-2 py-0.5 rounded text-white">{{ $todayAttendance->check_out }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="text-2xl font-bold flex items-center gap-2">
                                        Status: {{ $todayAttendance->status === 'sick' ? '🩺 Sakit' : '📄 Izin' }}
                                    </div>
                                    <p class="text-sm text-emerald-100 mt-2 italic">Keterangan: "{{ $todayAttendance->notes }}"</p>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex flex-wrap gap-4">
                            @if(!$todayAttendance || ($todayAttendance->status === 'present' && $todayAttendance->check_out))
                                <form action="{{ route('attendance.check-in') }}" method="POST" id="check-in-form">
                                    @csrf
                                    <input type="hidden" name="latitude" value="">
                                    <input type="hidden" name="longitude" value="">
                                    <button type="button" onclick="requestLocationAndSubmit('check-in-form')" class="px-6 py-3 bg-white text-emerald-800 font-bold rounded-xl shadow-lg hover:bg-emerald-50 hover:scale-[1.02] active:scale-95 transition duration-150">
                                        👉 Absen Masuk (Check-In)
                                    </button>
                                </form>
                            @elseif($todayAttendance->status === 'present' && !$todayAttendance->check_out)
                                <form action="{{ route('attendance.check-out') }}" method="POST" id="check-out-form">
                                    @csrf
                                    <input type="hidden" name="latitude" value="">
                                    <input type="hidden" name="longitude" value="">
                                    <button type="button" onclick="requestLocationAndSubmit('check-out-form')" class="px-6 py-3 bg-rose-500 text-white font-bold rounded-xl shadow-lg hover:bg-rose-600 hover:scale-[1.02] active:scale-95 transition duration-150">
                                        👈 Absen Keluar (Check-Out)
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Sick / Leave Application Form -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                            <span>📅 Pengajuan Sakit / Izin</span>
                        </h3>
                        
                        @if(!$todayAttendance)
                            <form action="{{ route('attendance.leave') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Pilih Status</label>
                                    <select name="status" id="status" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="sick">🩺 Sakit</option>
                                        <option value="leave">📄 Izin</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="notes" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Alasan / Keterangan</label>
                                    <textarea name="notes" id="notes" rows="3" required placeholder="Tulis alasan tidak masuk kantor..." class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm"></textarea>
                                </div>
                                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition duration-150 active:scale-95 shadow-md">
                                    Ajukan Keterangan
                                </button>
                            </form>
                        @else
                            <div class="flex flex-col items-center justify-center h-48 text-center text-gray-400 dark:text-gray-500">
                                <span>🔒 Pengajuan ditutup karena Anda sudah absensi/izin hari ini.</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- History Table for Employee -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 dark:border-gray-700 mt-6">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-150">Riwayat Kehadiran Anda</h3>
                        <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-xs font-semibold text-emerald-600 dark:text-emerald-400 rounded-full">
                            Bulan Ini
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">
                                    <th class="p-4">Tanggal</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4">Jam Masuk</th>
                                    <th class="p-4">Jam Keluar</th>
                                    <th class="p-4">Keterangan / Alasan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-350">
                                @forelse($attendances as $att)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition duration-150">
                                        <td class="p-4 font-semibold text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }}
                                            @if($att->status === 'present')
                                                <div class="mt-1">
                                                    @if($att->work_mode === 'wfo')
                                                        <span class="px-2 py-0.5 text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-450 rounded border border-emerald-200 dark:border-emerald-900/60">🏢 WFO</span>
                                                    @else
                                                        <span class="px-2 py-0.5 text-[9px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-450 rounded border border-amber-200 dark:border-amber-900/60">🏠 WFH</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            @if($att->status === 'present')
                                                <span class="px-2.5 py-1 text-xs font-bold bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-400 rounded-full">Hadir</span>
                                            @elseif($att->status === 'sick')
                                                <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-400 rounded-full">Sakit</span>
                                            @else
                                                <span class="px-2.5 py-1 text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-400 rounded-full">Izin</span>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            <div class="font-mono font-bold text-gray-900 dark:text-white">{{ $att->check_in ?? '-' }}</div>
                                            @if($att->status === 'present' && $att->minutes_late > 0)
                                                <div class="text-[10px] font-semibold text-rose-600 dark:text-rose-400 mt-0.5">
                                                    ⏱ Terlambat {{ $att->minutes_late }} m
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-4 font-mono font-bold">{{ $att->check_out ?? '-' }}</td>
                                        <td class="p-4 text-xs text-gray-500 dark:text-gray-400 italic max-w-sm truncate">{{ $att->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-gray-400 dark:text-gray-500">
                                            Belum ada riwayat absensi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @endif

        </div>
    </div>

    <!-- Map Modal Overlay -->
    <div id="mapModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" onclick="closeMapModal()"></div>
        
        <!-- Modal wrapper -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-2xl transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-100 dark:border-gray-700">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modalTitle">Lokasi Absensi</h3>
                        <p class="text-xs text-gray-400 mt-0.5" id="modalSubtitle">Detail koordinat absen karyawan</p>
                    </div>
                    <button type="button" onclick="closeMapModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none text-xl p-2">
                        ✕
                    </button>
                </div>
                
                <!-- Modal Body (Map Container) -->
                <div class="p-6">
                    <div id="mapContainer" class="h-96 w-full rounded-2xl border border-gray-150 dark:border-gray-700 z-10"></div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end border-t border-gray-100 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 rounded-b-3xl">
                    <button type="button" onclick="closeMapModal()" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-sm transition duration-150 active:scale-95">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Leaflet Map modal instance variables
        let myMap = null;
        let myMarker = null;

        function openMapModal(latitude, longitude, employeeName, timeLabel, modeText) {
            // Show modal
            const modal = document.getElementById('mapModal');
            modal.classList.remove('hidden');
            
            // Set header labels
            document.getElementById('modalTitle').innerText = `Lokasi Absen: ${employeeName}`;
            document.getElementById('modalSubtitle').innerText = `${timeLabel} (${modeText})`;
            
            // Set map position after a small timeout to let modal display first
            setTimeout(() => {
                const centerCoords = [latitude, longitude];
                
                if (myMap === null) {
                    myMap = L.map('mapContainer').setView(centerCoords, 16);
                    
                    // Use openstreetmap tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
                    }).addTo(myMap);
                } else {
                    myMap.setView(centerCoords, 16);
                }
                
                // Clear existing marker if any
                if (myMarker !== null) {
                    myMap.removeLayer(myMarker);
                }
                
                // Create new marker
                myMarker = L.marker(centerCoords).addTo(myMap);
                
                // Add popup
                myMarker.bindPopup(`
                    <div class="text-sm">
                        <strong class="text-emerald-700">${employeeName}</strong><br>
                        <span class="text-xs text-gray-500">${timeLabel}</span><br>
                        <span class="inline-block mt-1 px-1.5 py-0.5 text-[10px] font-bold bg-emerald-50 text-emerald-850 rounded border border-emerald-200">${modeText}</span>
                    </div>
                `).openPopup();
                
                // Trigger map resize to redraw correctly inside dynamic container
                myMap.invalidateSize();
            }, 200);
        }

        function closeMapModal() {
            const modal = document.getElementById('mapModal');
            modal.classList.add('hidden');
        }

        function requestLocationAndSubmit(formId) {
            const form = document.getElementById(formId);
            const button = form.querySelector('button');
            
            // Set loading state
            button.disabled = true;
            button.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-current inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menghubungkan GPS...
            `;
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        form.querySelector('input[name="latitude"]').value = position.coords.latitude;
                        form.querySelector('input[name="longitude"]').value = position.coords.longitude;
                        form.submit();
                    },
                    (error) => {
                        console.warn("Geolocation warning: " + error.message);
                        alert("Gagal mendapatkan lokasi GPS: " + error.message + ". Absensi tetap dicatat tanpa lokasi.");
                        form.submit();
                    },
                    { enableHighAccuracy: true, timeout: 5000 }
                );
            } else {
                alert("Browser Anda tidak mendukung deteksi lokasi. Absensi tetap dicatat tanpa lokasi.");
                form.submit();
            }
        }
    </script>
</x-app-layout>
