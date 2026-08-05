<!-- Employee CRUD Management Table -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Akun Karyawan</h3>
            <p class="text-xs text-gray-400 mt-0.5">Kelola data profil, email, dan kata sandi akses karyawan</p>
        </div>
        <button type="button" onclick="openAddEmployeeModal()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition duration-150 active:scale-95 shadow-md flex items-center gap-1.5">
            <i class="bi bi-person-plus-fill"></i> Tambah Karyawan Baru
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">
                    <th class="p-4">Nama</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Cabang</th>
                    <th class="p-4">Shift</th>
                    <th class="p-4">Tanggal Daftar</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-350">
                @forelse($employees as $emp)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition duration-150">
                        <td class="p-4 font-semibold text-gray-900 dark:text-white">{{ $emp->name }}</td>
                        <td class="p-4 font-mono text-xs text-gray-500">{{ $emp->email }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 font-bold rounded-lg text-[10px]">
                                {{ $emp->branch->name ?? 'Kantor Pusat' }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 font-bold rounded-lg text-[10px]">
                                {{ $emp->shift->name ?? 'Default (08:00)' }}
                            </span>
                        </td>
                        <td class="p-4 text-xs text-gray-500">{{ $emp->created_at->translatedFormat('d F Y') }}</td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                 <a href="{{ route('admin.employees.print-slip', $emp->id) }}" 
                                    target="_blank"
                                    class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 rounded-lg text-xs font-bold transition duration-150 border border-emerald-100 dark:border-emerald-900/60 active:scale-95 flex items-center justify-center gap-1">
                                     <i class="bi bi-printer"></i> Slip
                                 </a>
                                 <button type="button" 
                                         data-id="{{ $emp->id }}"
                                         data-name="{{ $emp->name }}"
                                         data-email="{{ $emp->email }}"
                                         data-branch-id="{{ $emp->branch_id }}"
                                         data-shift-id="{{ $emp->shift_id }}"
                                         data-birthdate="{{ $emp->birthdate ? $emp->birthdate->toDateString() : '' }}"
                                         onclick="openEditEmployeeModal(this)" 
                                         class="px-3 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 hover:bg-blue-100 rounded-lg text-xs font-bold transition duration-150 border border-blue-100 dark:border-blue-900/60 active:scale-95">
                                     <i class="bi bi-pencil mr-1"></i> Edit
                                 </button>
                                 <button type="button" 
                                         data-form-id="delete-employee-form-{{ $emp->id }}"
                                         data-title="Hapus Akun Karyawan"
                                         data-message="Apakah Anda yakin ingin menghapus akun karyawan &quot;{{ $emp->name }}&quot; beserta seluruh riwayat absensinya secara permanen?"
                                         onclick="event.preventDefault(); event.stopPropagation(); showConfirmModal(this)" 
                                         class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:rose-400 hover:bg-rose-100 rounded-lg text-xs font-bold transition duration-150 border border-rose-100 dark:border-rose-900/60 active:scale-95">
                                     <i class="bi bi-trash mr-1"></i> Hapus
                                 </button>
                                 <form id="delete-employee-form-{{ $emp->id }}" action="{{ route('admin.employees.destroy', $emp->id) }}" method="POST" class="hidden">
                                     @csrf
                                     @method('DELETE')
                                 </form>
                             </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400 dark:text-gray-500">
                            Belum ada akun karyawan terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
