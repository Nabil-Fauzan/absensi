<!-- Office Settings Configuration Form -->
<div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 max-w-2xl">
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pengaturan Geofencing Kantor</h3>
        <p class="text-xs text-gray-400 mt-0.5">Konfigurasi letak geografis wilayah kehadiran karyawan dan waktu batas keterlambatan masuk</p>
    </div>
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-5 text-sm">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Office Latitude</label>
                <input type="text" name="office_latitude" value="{{ \App\Models\Setting::get('office_latitude', '-6.873218738309585') }}" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Office Longitude</label>
                <input type="text" name="office_longitude" value="{{ \App\Models\Setting::get('office_longitude', '107.5609385222725') }}" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Radius Toleransi Geofencing (Meter)</label>
            <input type="number" name="office_radius_meters" value="{{ \App\Models\Setting::get('office_radius_meters', '100') }}" required min="1" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            <p class="text-[10px] text-gray-400 mt-1">Jarak radius GPS (dalam satuan meter) untuk melabeli status WFO karyawan.</p>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Batas Jam Masuk Kantor (Keterlambatan)</label>
            <input type="text" name="office_check_in_time" value="{{ \App\Models\Setting::get('office_check_in_time', '08:00:00') }}" required placeholder="Contoh: 08:00:00" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            <p class="text-[10px] text-gray-400 mt-1">Gunakan format 24 jam (HH:MM:SS), misal 08:00:00 atau 08:30:00.</p>
        </div>

        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition duration-150 active:scale-95 shadow-md text-sm">
            💾 Simpan Setelan Kantor
        </button>
    </form>
</div>
