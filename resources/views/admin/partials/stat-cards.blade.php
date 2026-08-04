<!-- Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <!-- Hadir Card -->
    <div onclick="filterByStatus('present')" class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 flex flex-col justify-between cursor-pointer hover:shadow-lg hover:scale-[1.02] transform transition-all duration-150 active:scale-95 group">
        <div>
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider group-hover:text-emerald-500 transition duration-150">Hadir Hari Ini</div>
                <span class="text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 transition duration-150"><i class="bi bi-funnel"></i> Klik filter</span>
            </div>
            <div class="text-4xl font-extrabold mt-2 text-emerald-600 dark:text-emerald-400">
                {{ $stats['hadir'] }}
            </div>
        </div>
        <div class="text-xs mt-3 text-gray-500 dark:text-gray-400">Karyawan masuk kantor (WFO/WFH)</div>
    </div>
    <!-- Sakit/Izin Card -->
    <div onclick="filterByStatus('izin_sakit')" class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 flex flex-col justify-between cursor-pointer hover:shadow-lg hover:scale-[1.02] transform transition-all duration-150 active:scale-95 group">
        <div>
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider group-hover:text-blue-500 transition duration-150">Sakit / Izin</div>
                <span class="text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 transition duration-150"><i class="bi bi-funnel"></i> Klik filter</span>
            </div>
            <div class="text-4xl font-extrabold mt-2 text-blue-600 dark:text-blue-400">
                {{ $stats['izin_sakit'] }}
            </div>
        </div>
        <div class="text-xs mt-3 text-gray-500 dark:text-gray-400">Karyawan absen dengan keterangan</div>
    </div>
    <!-- Terlambat Card -->
    <div onclick="filterByStatus('terlambat')" class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 flex flex-col justify-between cursor-pointer hover:shadow-lg hover:scale-[1.02] transform transition-all duration-150 active:scale-95 group">
        <div>
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider group-hover:text-amber-500 transition duration-150">Terlambat</div>
                <span class="text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 transition duration-150"><i class="bi bi-funnel"></i> Klik filter</span>
            </div>
            <div class="text-4xl font-extrabold mt-2 text-amber-600 dark:text-amber-400">
                {{ $stats['terlambat'] }}
            </div>
        </div>
        <div class="text-xs mt-3 text-gray-500 dark:text-gray-400">Absen masuk setelah {{ \App\Models\Setting::get('office_check_in_time', '08:00:00') }} WIB</div>
    </div>
    <!-- Belum Absen Card -->
    <div onclick="openBelumAbsenModal()" class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 flex flex-col justify-between cursor-pointer hover:shadow-lg hover:scale-[1.02] transform transition-all duration-150 active:scale-95 group">
        <div>
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider group-hover:text-rose-500 transition duration-150">Belum Absen</div>
                <span class="text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 transition duration-150"><i class="bi bi-list-ul"></i> Lihat daftar</span>
            </div>
            <div class="text-4xl font-extrabold mt-2 text-rose-600 dark:text-rose-400">
                {{ $stats['belum_absen'] }}
            </div>
        </div>
        <div class="text-xs mt-3 text-gray-500 dark:text-gray-400">Karyawan belum absen hari ini</div>
    </div>
</div>
