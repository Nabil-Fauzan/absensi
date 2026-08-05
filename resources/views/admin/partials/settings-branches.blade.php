<!-- Branches Settings CRUD Section -->
<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Cabang Kantor</h3>
            <p class="text-xs text-gray-400 mt-0.5">Kelola lokasi cabang kantor dengan koordinat dan radius geofencing terpisah</p>
        </div>
        <button type="button" onclick="openAddBranchModal()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition duration-150 active:scale-95 shadow-md flex items-center gap-1.5">
            <i class="bi bi-plus-circle-fill"></i> Tambah Cabang Baru
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">
                    <th class="p-4">Nama Cabang</th>
                    <th class="p-4">Latitude</th>
                    <th class="p-4">Longitude</th>
                    <th class="p-4">Radius (Meter)</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-350">
                @forelse($branches as $branch)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition duration-150">
                        <td class="p-4 font-semibold text-gray-900 dark:text-white">{{ $branch->name }}</td>
                        <td class="p-4 font-mono text-xs">{{ $branch->latitude }}</td>
                        <td class="p-4 font-mono text-xs">{{ $branch->longitude }}</td>
                        <td class="p-4">{{ $branch->radius_meters }} m</td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button type="button" 
                                        data-id="{{ $branch->id }}"
                                        data-name="{{ $branch->name }}"
                                        data-latitude="{{ $branch->latitude }}"
                                        data-longitude="{{ $branch->longitude }}"
                                        data-radius="{{ $branch->radius_meters }}"
                                        onclick="openEditBranchModal(this)" 
                                        class="px-3 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 hover:bg-blue-100 rounded-lg text-xs font-bold transition duration-150 border border-blue-100 dark:border-blue-900/60 active:scale-95">
                                    <i class="bi bi-pencil mr-1"></i> Edit
                                </button>
                                <button type="button" 
                                        data-form-id="delete-branch-form-{{ $branch->id }}"
                                        data-title="Hapus Cabang Kantor"
                                        data-message="Apakah Anda yakin ingin menghapus cabang &quot;{{ $branch->name }}&quot;? Karyawan yang terhubung akan dikembalikan ke setelan Kantor Pusat."
                                        onclick="event.preventDefault(); event.stopPropagation(); showConfirmModal(this)" 
                                        class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 hover:bg-rose-100 rounded-lg text-xs font-bold transition duration-150 border border-rose-100 dark:border-rose-900/60 active:scale-95">
                                    <i class="bi bi-trash mr-1"></i> Hapus
                                </button>
                                <form id="delete-branch-form-{{ $branch->id }}" action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400 dark:text-gray-500">
                            Belum ada cabang kantor yang ditambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL ADD BRANCH -->
<div id="addBranchModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-xl w-full shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white"><i class="bi bi-plus-circle-fill text-emerald-600 mr-1.5"></i> Tambah Cabang Kantor Baru</h3>
            <button type="button" onclick="closeAddBranchModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 text-xl p-2">✕</button>
        </div>
        <form action="{{ route('admin.branches.store') }}" method="POST" class="p-6 space-y-4 text-sm">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nama Cabang</label>
                <input type="text" name="name" required placeholder="Contoh: Kantor Cabang Bandung" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Latitude</label>
                    <input type="text" name="latitude" id="add_branch_lat" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Longitude</label>
                    <input type="text" name="longitude" id="add_branch_lng" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Radius Toleransi Geofencing (Meter)</label>
                <input type="number" name="radius_meters" id="add_branch_radius" value="100" required min="1" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1.5">Pilih Lokasi di Peta</label>
                <div id="addBranchMap" class="h-48 w-full rounded-xl border border-gray-200 dark:border-gray-700 z-10 shadow-inner"></div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeAddBranchModal()" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-xs active:scale-95">Batal</button>
                <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs active:scale-95 shadow-md">Simpan Cabang</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT BRANCH -->
<div id="editBranchModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-xl w-full shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white"><i class="bi bi-pencil-fill text-blue-600 mr-1.5"></i> Edit Cabang Kantor</h3>
            <button type="button" onclick="closeEditBranchModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 text-xl p-2">✕</button>
        </div>
        <form id="editBranchForm" method="POST" class="p-6 space-y-4 text-sm">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nama Cabang</label>
                <input type="text" name="name" id="edit_branch_name" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Latitude</label>
                    <input type="text" name="latitude" id="edit_branch_lat" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Longitude</label>
                    <input type="text" name="longitude" id="edit_branch_lng" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Radius Toleransi Geofencing (Meter)</label>
                <input type="number" name="radius_meters" id="edit_branch_radius" required min="1" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1.5">Pilih Lokasi di Peta</label>
                <div id="editBranchMap" class="h-48 w-full rounded-xl border border-gray-200 dark:border-gray-700 z-10 shadow-inner"></div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditBranchModal()" class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-xs active:scale-95">Batal</button>
                <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs active:scale-95 shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
