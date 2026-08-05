<!-- Holidays Tab Form & List -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Add Holiday Form (col-span-4) -->
    <div class="lg:col-span-4 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
        <div class="mb-5">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-1.5"><i class="bi bi-calendar-plus text-emerald-600"></i> Tambah Hari Libur</h3>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Daftarkan hari libur nasional untuk menonaktifkan rekap kealpaan secara otomatis.</p>
        </div>

        <form action="{{ route('admin.holidays.store') }}" method="POST" class="space-y-4 text-sm">
            @csrf
            <div>
                <label for="holiday_name" class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nama Hari Libur</label>
                <input type="text" name="name" id="holiday_name" required placeholder="Misal: Tahun Baru Hijriah" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>

            <div>
                <label for="holiday_date" class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Tanggal</label>
                <input type="date" name="date" id="holiday_date" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>

            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition duration-150 active:scale-95 shadow-md text-sm flex items-center justify-center gap-1.5">
                <i class="bi bi-plus-circle"></i> Tambah Libur
            </button>
        </form>
    </div>

    <!-- Holidays List Table (col-span-8) -->
    <div class="lg:col-span-8 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
        <div class="mb-5 border-b border-gray-100 dark:border-gray-700 pb-3">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-1.5"><i class="bi bi-calendar-check text-emerald-600"></i> Daftar Hari Libur Nasional</h3>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Daftar hari libur nasional yang terdaftar dalam sistem perusahaan.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 dark:bg-gray-900/50">
                    <tr>
                        <th scope="col" class="px-4 py-3">No</th>
                        <th scope="col" class="px-4 py-3">Nama Libur</th>
                        <th scope="col" class="px-4 py-3">Tanggal</th>
                        <th scope="col" class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 dark:divide-gray-700/60">
                    @forelse($holidays as $index => $holiday)
                        <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/50 text-gray-900 dark:text-gray-200">
                            <td class="px-4 py-3 text-xs font-semibold text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-xs">{{ $holiday->name }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ \Carbon\Carbon::parse($holiday->date)->translatedFormat('d F Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('admin.holidays.destroy', $holiday->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hari libur ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg transition duration-150 active:scale-95" title="Hapus">
                                        <i class="bi bi-trash3 text-sm"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-xs text-gray-400">
                                <i class="bi bi-calendar-x text-3xl block mb-2 text-gray-300"></i>
                                Belum ada hari libur nasional yang didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
