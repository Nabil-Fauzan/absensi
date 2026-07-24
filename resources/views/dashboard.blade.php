<x-app-layout>
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 bg-emerald-600 rounded-2xl shadow-xl text-white">
                        <div class="text-sm font-semibold opacity-75 uppercase tracking-wider">Total Kehadiran Hari Ini</div>
                        <div class="text-4xl font-bold mt-2">
                            {{ $attendances->where('date', \Carbon\Carbon::today()->toDateString())->where('status', 'present')->count() }}
                        </div>
                        <div class="text-xs mt-2 opacity-75">Karyawan masuk kantor</div>
                    </div>
                    <div class="p-6 bg-emerald-600 rounded-2xl shadow-xl text-white">
                        <div class="text-sm font-semibold opacity-75 uppercase tracking-wider">Sakit / Izin Hari Ini</div>
                        <div class="text-4xl font-bold mt-2">
                            {{ $attendances->where('date', \Carbon\Carbon::today()->toDateString())->whereIn('status', ['sick', 'leave'])->count() }}
                        </div>
                        <div class="text-xs mt-2 opacity-75">Karyawan absen dengan keterangan</div>
                    </div>
                    <div class="p-6 bg-emerald-600 rounded-2xl shadow-xl text-white">
                        <div class="text-sm font-semibold opacity-75 uppercase tracking-wider">Total Karyawan Terdaftar</div>
                        <div class="text-4xl font-bold mt-2">
                            {{ \App\Models\User::where('role', 'employee')->count() }}
                        </div>
                        <div class="text-xs mt-2 opacity-75">Akun karyawan aktif</div>
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
                                        </td>
                                        <td class="p-4">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }}</td>
                                        <td class="p-4">
                                            @if($att->status === 'present')
                                                <span class="px-2.5 py-1 text-xs font-bold bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-400 rounded-full">Hadir</span>
                                            @elseif($att->status === 'sick')
                                                <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-400 rounded-full">Sakit</span>
                                            @else
                                                <span class="px-2.5 py-1 text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-400 rounded-full">Izin</span>
                                            @endif
                                        </td>
                                        <td class="p-4 font-mono font-bold">{{ $att->check_in ?? '-' }}</td>
                                        <td class="p-4 font-mono font-bold">{{ $att->check_out ?? '-' }}</td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2 text-xs">
                                                @if($att->latitude_in)
                                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $att->latitude_in }},{{ $att->longitude_in }}" target="_blank" class="px-2 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-450 rounded-md hover:underline font-semibold flex items-center gap-0.5">
                                                        📍 Masuk
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                                
                                                <span class="text-gray-300 dark:text-gray-600">/</span>
                                                
                                                @if($att->latitude_out)
                                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $att->latitude_out }},{{ $att->longitude_out }}" target="_blank" class="px-2 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-450 rounded-md hover:underline font-semibold flex items-center gap-0.5">
                                                        📍 Keluar
                                                    </a>
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
                                        <td class="p-4 font-mono font-bold">{{ $att->check_in ?? '-' }}</td>
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

    <script>
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
