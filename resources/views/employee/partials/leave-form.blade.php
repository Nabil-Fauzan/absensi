<!-- Sick / Leave Application Form -->
<div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700">
    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
        <span><i class="bi bi-calendar2-plus-fill text-emerald-600 mr-1"></i> Pengajuan Sakit / Izin</span>
    </h3>
    
    @if(!$todayAttendance)
        <form action="{{ route('attendance.leave') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Pilih Status</label>
                <select name="status" id="status" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="sick">Sakit</option>
                    <option value="leave">Izin</option>
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
            <span><i class="bi bi-lock-fill mr-1"></i> Pengajuan ditutup karena Anda sudah absensi/izin hari ini.</span>
        </div>
    @endif
</div>
