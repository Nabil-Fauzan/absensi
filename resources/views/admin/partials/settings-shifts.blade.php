<!-- Shifts Settings CRUD Section -->
<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Shift Kerja</h3>
            <p class="text-xs text-gray-400 mt-0.5">Kelola jam operasional / jam masuk kerja karyawan secara fleksibel</p>
        </div>
        <button type="button" onclick="openAddShiftModal()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition duration-150 active:scale-95 shadow-md flex items-center gap-1.5">
            <i class="bi bi-clock-fill"></i> Tambah Shift Baru
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">
                    <th class="p-4">Nama Shift</th>
                    <th class="p-4">Jam Masuk</th>
                    <th class="p-4">Jam Pulang</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-350">
                @forelse($shifts as $shift)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition duration-150">
                        <td class="p-4 font-semibold text-gray-900 dark:text-white">{{ $shift->name }}</td>
                        <td class="p-4 font-mono text-xs text-emerald-600 font-bold">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</td>
                        <td class="p-4 font-mono text-xs text-gray-500">{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button type="button" 
                                        data-id="{{ $shift->id }}"
                                        data-name="{{ $shift->name }}"
                                        data-start-time="{{ $shift->start_time }}"
                                        data-end-time="{{ $shift->end_time }}"
                                        onclick="openEditShiftModal(this)" 
                                        class="px-3 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 hover:bg-blue-100 rounded-lg text-xs font-bold transition duration-150 border border-blue-100 dark:border-blue-900/60 active:scale-95">
                                    <i class="bi bi-pencil mr-1"></i> Edit
                                </button>
                                <button type="button" 
                                        data-form-id="delete-shift-form-{{ $shift->id }}"
                                        data-title="Hapus Shift Kerja"
                                        data-message="Apakah Anda yakin ingin menghapus shift &quot;{{ $shift->name }}&quot;? Karyawan yang terhubung akan dikembalikan ke setelan jam kerja default."
                                        onclick="event.preventDefault(); event.stopPropagation(); showConfirmModal(this)" 
                                        class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 hover:bg-rose-100 rounded-lg text-xs font-bold transition duration-150 border border-rose-100 dark:border-rose-900/60 active:scale-95">
                                    <i class="bi bi-trash mr-1"></i> Hapus
                                </button>
                                <form id="delete-shift-form-{{ $shift->id }}" action="{{ route('admin.shifts.destroy', $shift->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400 dark:text-gray-500">
                            Belum ada shift kerja yang ditambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL ADD SHIFT -->
<div id="addShiftModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white"><i class="bi bi-plus-circle-fill text-emerald-600 mr-1.5"></i> Tambah Shift Kerja Baru</h3>
            <button type="button" onclick="closeAddShiftModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 text-xl p-2">✕</button>
        </div>
        <form action="{{ route('admin.shifts.store') }}" method="POST" class="p-6 space-y-4 text-sm">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nama Shift</label>
                <input type="text" name="name" required placeholder="Contoh: Shift Pagi (Komersial)" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Jam Masuk</label>
                    <input type="time" name="start_time" value="08:00" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Jam Pulang</label>
                    <input type="time" name="end_time" value="17:00" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeAddShiftModal()" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-xs active:scale-95">Batal</button>
                <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs active:scale-95 shadow-md">Simpan Shift</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT SHIFT -->
<div id="editShiftModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white"><i class="bi bi-pencil-fill text-blue-600 mr-1.5"></i> Edit Shift Kerja</h3>
            <button type="button" onclick="closeEditShiftModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 text-xl p-2">✕</button>
        </div>
        <form id="editShiftForm" method="POST" class="p-6 space-y-4 text-sm">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nama Shift</label>
                <input type="text" name="name" id="edit_shift_name" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Jam Masuk</label>
                    <input type="time" name="start_time" id="edit_shift_start" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Jam Pulang</label>
                    <input type="time" name="end_time" id="edit_shift_end" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditShiftModal()" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-xs active:scale-95">Batal</button>
                <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs active:scale-95 shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
