<!-- Geofencing Status Box -->
<div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4 text-sm text-gray-600 dark:text-gray-300">
    <div class="flex items-center gap-2">
        <span class="p-2 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
            <i class="bi bi-building text-base"></i>
        </span>
        <div>
            <div class="font-bold text-gray-800 dark:text-white">Status Konfigurasi Geofencing Kantor</div>
            <div class="text-xs text-gray-400">Parameter deteksi kehadiran fisik di kantor</div>
        </div>
    </div>
    <div class="flex flex-wrap gap-4 text-xs">
        <div class="px-3 py-1.5 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl font-medium flex items-center gap-1.5">
            <span class="text-gray-400">Koordinat:</span>
            <span class="font-mono font-semibold text-gray-800 dark:text-gray-300">{{ $officeConfig['latitude'] }}, {{ $officeConfig['longitude'] }}</span>
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
