<!-- Custom Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" onclick="closeConfirmModal()"></div>
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-100 dark:border-gray-700">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-rose-500 text-2xl"></i>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="confirmTitle">Konfirmasi Tindakan</h3>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed" id="confirmMessage">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeConfirmModal()" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl text-sm transition duration-150 active:scale-95">
                        Batal
                    </button>
                    <button type="button" id="confirmSubmitBtn" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-sm transition duration-150 active:scale-95">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map Modal Overlay -->
<div id="mapModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" onclick="closeMapModal()"></div>
    
    <!-- Modal wrapper -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-100 dark:border-gray-700">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modalTitle">Lokasi Absensi</h3>
                    <p class="text-xs text-gray-400 mt-0.5" id="modalSubtitle">Detail koordinat absen karyawan</p>
                </div>
                <button type="button" onclick="closeMapModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none text-xl p-2">
                    ✕
                </button>
            </div>
            
            <!-- Modal Body (Map Container) -->
            <div class="p-6">
                <div id="mapContainer" class="h-96 w-full rounded-2xl border border-gray-150 dark:border-gray-700 z-10"></div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex justify-end border-t border-gray-100 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 rounded-b-3xl">
                <button type="button" id="copyCoordsBtn" onclick="copyMapCoordinates()" class="px-5 py-2.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-400 font-bold rounded-xl text-sm transition duration-150 active:scale-95 mr-2">
                    <i class="bi bi-clipboard mr-1"></i> Salin Koordinat
                </button>
                <button type="button" onclick="closeMapModal()" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-sm transition duration-150 active:scale-95">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@if(isset($belumAbsenUsers))
<!-- Belum Absen Modal -->
<div id="belumAbsenModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" onclick="closeBelumAbsenModal()"></div>
    
    <!-- Modal wrapper -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-100 dark:border-gray-700">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                        <span><i class="bi bi-clipboard-x-fill text-rose-500 mr-1"></i> Karyawan Belum Absen</span>
                        <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 rounded-full text-xs border border-rose-200 dark:border-rose-900/60">
                            Hari Ini
                        </span>
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Daftar staf terdaftar yang belum mencatatkan absensi atau izin hari ini</p>
                </div>
                <button type="button" onclick="closeBelumAbsenModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none text-xl p-2">
                    ✕
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 max-h-96 overflow-y-auto">
                @forelse($belumAbsenUsers as $u)
                    <div class="flex items-center justify-between py-2.5 border-b border-gray-50 dark:border-gray-700 last:border-b-0">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $u->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $u->email }}</div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 bg-gray-50 dark:bg-gray-900 text-gray-400 rounded-md text-[10px] font-bold">
                            Belum Absen
                        </span>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-500 dark:text-gray-500">
                        🎉 Luar biasa! Semua karyawan sudah melakukan absensi hari ini.
                    </div>
                @endforelse
            </div>
            
            <!-- Modal Footer -->
            <div class="flex justify-between border-t border-gray-100 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 rounded-b-3xl">
                @if($belumAbsenUsers->count() > 0)
                    <button type="button" id="copyBelumAbsenBtn" onclick="copyBelumAbsenList()" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-400 font-bold rounded-xl text-xs transition duration-150 active:scale-95">
                        <i class="bi bi-clipboard mr-1"></i> Salin Daftar Nama
                    </button>
                @else
                    <div></div>
                @endif
                <button type="button" onclick="closeBelumAbsenModal()" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-sm transition duration-150 active:scale-95">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Add Employee Modal -->
<div id="addEmployeeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" onclick="closeAddEmployeeModal()"></div>
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-1.5"><i class="bi bi-person-plus-fill text-emerald-600"></i> Tambah Karyawan Baru</h3>
                <button type="button" onclick="closeAddEmployeeModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 text-xl p-2">✕</button>
            </div>
            <form action="{{ route('admin.employees.store') }}" method="POST" class="p-6 space-y-4 text-sm">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nama Karyawan</label>
                    <input type="text" name="name" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Alamat Email</label>
                    <input type="email" name="email" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Kata Sandi</label>
                    <input type="password" name="password" required minlength="8" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeAddEmployeeModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-xs active:scale-95">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs active:scale-95 shadow-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Employee Modal -->
<div id="editEmployeeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" onclick="closeEditEmployeeModal()"></div>
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-1.5"><i class="bi bi-pencil-square text-blue-600"></i> Edit Karyawan</h3>
                <button type="button" onclick="closeEditEmployeeModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 text-xl p-2">✕</button>
            </div>
            <form id="editEmployeeForm" method="POST" class="p-6 space-y-4 text-sm">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Nama Karyawan</label>
                    <input type="text" name="name" id="editEmpName" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Alamat Email</label>
                    <input type="email" name="email" id="editEmpEmail" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Kata Sandi Baru (Opsional)</label>
                    <input type="password" name="password" minlength="8" placeholder="Kosongkan jika tidak ingin diubah" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeEditEmployeeModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-xs active:scale-95">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs active:scale-95 shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Attendance Modal -->
<div id="editAttendanceModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" onclick="closeEditAttendanceModal()"></div>
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-1.5"><i class="bi bi-pencil-square text-blue-600"></i> Koreksi Absensi Karyawan</h3>
                <button type="button" onclick="closeEditAttendanceModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 text-xl p-2">✕</button>
            </div>
            <form id="editAttendanceForm" method="POST" class="p-6 space-y-4 text-sm">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Status Kehadiran</label>
                    <select name="status" id="editAttStatus" onchange="toggleEditAttFields()" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="present">Hadir</option>
                        <option value="sick">Sakit</option>
                        <option value="leave">Izin</option>
                    </select>
                </div>
                
                <div id="editAttPresentFields" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Mode Kerja</label>
                        <select name="work_mode" id="editAttWorkMode" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="wfo">WFO (Di Kantor)</option>
                            <option value="wfh">WFH (Luar Kantor)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Keterlambatan (Menit)</label>
                        <input type="number" name="minutes_late" id="editAttMinutesLate" min="0" required class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Alasan / Catatan</label>
                    <textarea name="notes" id="editAttNotes" rows="2" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm"></textarea>
                </div>
                
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeEditAttendanceModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-xs active:scale-95">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs active:scale-95 shadow-md">Simpan Koreksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" onclick="closeRejectModal()"></div>
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-1.5"><i class="bi bi-x-circle-fill text-rose-600"></i> Tolak Pengajuan Absensi</h3>
                <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 text-xl p-2">✕</button>
            </div>
            <form id="rejectForm" method="POST" class="p-6 space-y-4 text-sm">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">Alasan Penolakan</label>
                    <textarea name="rejection_reason" id="rejectionReason" required rows="3" placeholder="Masukkan alasan penolakan pengajuan..." class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-xs active:scale-95">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs active:scale-95 shadow-md">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- GLOBAL CUSTOM CONFIRMATION MODAL HELPER FUNCTIONS ---
    let activeConfirmFormId = null;

    function showConfirmModal(target, title, message) {
        if (typeof target === 'object' && target !== null && target.dataset) {
            activeConfirmFormId = target.dataset.formId;
            document.getElementById('confirmTitle').innerText = target.dataset.title || 'Konfirmasi';
            document.getElementById('confirmMessage').innerText = target.dataset.message || '';
        } else {
            activeConfirmFormId = target;
            document.getElementById('confirmTitle').innerText = title || 'Konfirmasi';
            document.getElementById('confirmMessage').innerText = message || '';
        }
        
        const submitBtn = document.getElementById('confirmSubmitBtn');
        if (submitBtn) {
            submitBtn.onclick = function(e) {
                e.preventDefault();
                const form = document.getElementById(activeConfirmFormId);
                if (form) form.submit();
            };
        }
        
        const modal = document.getElementById('confirmModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeConfirmModal() {
        const modal = document.getElementById('confirmModal');
        if (modal) modal.classList.add('hidden');
        activeConfirmFormId = null;
    }
</script>
