<!-- Office Settings Configuration Form -->
<div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 w-full">
    <div class="mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-1.5"><i class="bi bi-gear-fill text-emerald-600"></i> Pengaturan Geofencing Kantor</h3>
        <p class="text-xs text-gray-400 dark:text-gray-400 mt-1">Konfigurasi letak geografis wilayah kehadiran karyawan dan waktu batas keterlambatan masuk kantor pusat.</p>
    </div>
    
    <form action="{{ route('admin.settings.update') }}" method="POST" class="text-sm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left inputs panel: col-span-5 -->
            <div class="lg:col-span-5 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Office Latitude</label>
                        <input type="text" name="office_latitude" id="office_lat" value="{{ \App\Models\Setting::get('office_latitude', '-6.873218738309585') }}" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Office Longitude</label>
                        <input type="text" name="office_longitude" id="office_lng" value="{{ \App\Models\Setting::get('office_longitude', '107.5609385222725') }}" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Radius Toleransi Geofencing (Meter)</label>
                    <input type="number" name="office_radius_meters" id="office_radius" value="{{ \App\Models\Setting::get('office_radius_meters', '100') }}" required min="1" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <p class="text-[10px] text-gray-400 dark:text-gray-450 mt-1">Jarak radius GPS (dalam meter) untuk melabeli status WFO karyawan.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Batas Jam Masuk Kantor (Keterlambatan)</label>
                    <input type="text" name="office_check_in_time" value="{{ \App\Models\Setting::get('office_check_in_time', '08:00:00') }}" required placeholder="Contoh: 08:00:00" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <p class="text-[10px] text-gray-400 dark:text-gray-450 mt-1">Gunakan format 24 jam (HH:MM:SS), misal 08:00:00.</p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition duration-150 active:scale-95 shadow-md text-sm flex items-center justify-center gap-1.5">
                        <i class="bi bi-floppy"></i> Simpan Setelan Kantor
                    </button>
                </div>
            </div>

            <!-- Right map panel: col-span-7 -->
            <div class="lg:col-span-7 space-y-3">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-0.5">Peta Lokasi Kantor & Radius Geofence</label>
                <div id="configMap" class="h-[320px] w-full rounded-2xl border border-gray-200 dark:border-gray-700 z-10 shadow-inner"></div>
                <div class="p-3 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40 rounded-xl text-xs text-emerald-800 dark:text-emerald-350 flex gap-2">
                    <i class="bi bi-lightbulb-fill text-emerald-600 flex-shrink-0 mt-0.5 animate-pulse"></i>
                    <span>Anda dapat menggeser penanda biru di peta atau klik di mana saja pada peta untuk mengatur koordinat kantor secara instan.</span>
                </div>
            </div>
            
        </div>
    </form>
</div>
