<!-- Ringkasan Statistik & Performa Mandiri Karyawan -->
<div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
    
    <!-- Rasio Kehadiran Card (Visual Radial Progress) -->
    <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 flex items-center justify-between col-span-2 sm:col-span-1 hover:shadow-lg transition duration-150">
        <div class="space-y-1">
            <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Performa</span>
            <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300">Rasio Kehadiran</h4>
        </div>
        <div class="relative flex items-center justify-center w-14 h-14 flex-shrink-0">
            <svg class="w-full h-full transform -rotate-90">
                <circle cx="28" cy="28" r="22" stroke-width="4.5" stroke="currentColor" class="text-gray-100 dark:text-gray-750" fill="transparent" />
                <circle cx="28" cy="28" r="22" stroke-width="4.5" stroke="currentColor" class="text-emerald-500" fill="transparent"
                        stroke-dasharray="138.2" stroke-dashoffset="{{ 138.2 - (138.2 * min(100, max(0, $monthlyStats['attendance_rate']))) / 100 }}" />
            </svg>
            <span class="absolute text-[11px] font-black text-gray-800 dark:text-gray-200">{{ $monthlyStats['attendance_rate'] }}%</span>
        </div>
    </div>

    <!-- Total Hadir Card -->
    <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between hover:shadow-lg transition duration-150">
        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Hadir Bulan Ini</span>
        <div class="mt-2 flex items-baseline gap-1">
            <span class="text-2xl font-extrabold text-green-600 dark:text-green-400">{{ $monthlyStats['present'] }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">hari</span>
        </div>
    </div>

    <!-- Total Sakit Card -->
    <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between hover:shadow-lg transition duration-150">
        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Sakit Bulan Ini</span>
        <div class="mt-2 flex items-baseline gap-1">
            <span class="text-2xl font-extrabold text-amber-500 dark:text-amber-400">{{ $monthlyStats['sick'] }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">hari</span>
        </div>
    </div>

    <!-- Cuti Tahunan Card (Quota 15 Hari) -->
    <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between hover:shadow-lg transition duration-150">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Cuti Tahunan</span>
            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">Kuota 15</span>
        </div>
        <div class="mt-2 flex flex-col gap-1.5">
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-extrabold text-blue-600 dark:text-blue-450">{{ 15 - $monthlyStats['annual_leaves_left'] }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">terpakai</span>
            </div>
            <div class="text-[9px] font-bold text-gray-500 dark:text-gray-400">
                Sisa: <span class="text-blue-600 dark:text-blue-400">{{ $monthlyStats['annual_leaves_left'] }} hari</span>
            </div>
        </div>
    </div>

    <!-- Cuti Ulang Tahun Card (Quota 1 Hari) -->
    <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between hover:shadow-lg transition duration-150">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Cuti Ultah</span>
            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400">Kuota 1</span>
        </div>
        <div class="mt-2 flex flex-col gap-1.5">
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-extrabold text-pink-500 dark:text-pink-455">{{ 1 - $monthlyStats['birthday_leaves_left'] }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">terpakai</span>
            </div>
            <div class="text-[9px] font-bold text-gray-500 dark:text-gray-400">
                Sisa: <span class="text-pink-500 dark:text-pink-400">{{ $monthlyStats['birthday_leaves_left'] }} hari</span>
            </div>
        </div>
    </div>

    <!-- Keterlambatan Card -->
    <div class="p-5 bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between hover:shadow-lg transition duration-150">
        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Keterlambatan</span>
        <div class="mt-2 flex items-baseline gap-1">
            <span class="text-2xl font-extrabold text-rose-600 dark:text-rose-455">{{ $monthlyStats['late_minutes'] }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">menit</span>
        </div>
    </div>

</div>
