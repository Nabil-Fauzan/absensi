<!-- Ringkasan Statistik Bulanan Karyawan -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-6 mb-6">
    <!-- Total Hadir Card -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between hover:shadow-lg transition duration-150">
        <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Hadir Bulan Ini</span>
        <div class="flex items-baseline gap-1 mt-2">
            <span class="text-3xl font-extrabold text-green-600 dark:text-green-400">{{ $monthlyStats['present'] }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">hari</span>
        </div>
    </div>
    <!-- Total Sakit Card -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between hover:shadow-lg transition duration-150">
        <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Sakit Bulan Ini</span>
        <div class="flex items-baseline gap-1 mt-2">
            <span class="text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ $monthlyStats['sick'] }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">hari</span>
        </div>
    </div>
    <!-- Total Izin Card -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between hover:shadow-lg transition duration-150">
        <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Izin Bulan Ini</span>
        <div class="flex items-baseline gap-1 mt-2">
            <span class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">{{ $monthlyStats['leave'] }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">hari</span>
        </div>
    </div>
    <!-- Keterlambatan Card -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between hover:shadow-lg transition duration-150">
        <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Keterlambatan</span>
        <div class="flex items-baseline gap-1 mt-2">
            <span class="text-3xl font-extrabold text-rose-600 dark:text-rose-400">{{ $monthlyStats['late_minutes'] }}</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">menit</span>
        </div>
    </div>
    <!-- Persentase Kehadiran Card -->
    <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-100 dark:border-gray-700 flex flex-col justify-between col-span-2 md:col-span-1 hover:shadow-lg transition duration-150">
        <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Rasio Kehadiran</span>
        <div class="flex items-baseline gap-1 mt-2">
            <span class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $monthlyStats['attendance_rate'] }}%</span>
        </div>
    </div>
</div>
