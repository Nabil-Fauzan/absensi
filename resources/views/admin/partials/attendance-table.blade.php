<!-- Filter Form & Export -->
<div class="mb-6 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
    <div class="flex flex-wrap items-center justify-between mb-4 gap-2">
        <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <span>Pencarian & Filter Data</span>
            @if(request('status'))
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/60">
                    Filter Status: 
                    @if(request('status') === 'present') Hadir @endif
                    @if(request('status') === 'izin_sakit') Sakit/Izin @endif
                    @if(request('status') === 'terlambat') Terlambat @endif
                    <button type="button" onclick="clearStatusFilter()" class="hover:text-emerald-900 dark:hover:text-white font-bold ml-1">✕</button>
                </span>
            @endif
        </h4>
    </div>
    <form method="GET" action="{{ route('admin.attendance') }}" id="filterForm" class="space-y-4">
        <input type="hidden" name="status" id="filterStatus" value="{{ request('status') }}">
        
        <!-- Search Inputs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label for="search" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Cari Karyawan</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik nama..." class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm h-11">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Rentang Tanggal</label>
                <button type="button" onclick="openDateFilterModal()" class="w-full h-11 px-4 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl text-sm text-gray-750 dark:text-gray-200 flex items-center justify-between hover:border-emerald-500 transition duration-150 active:scale-[0.98] text-left">
                    <span class="flex items-center gap-2 overflow-hidden">
                        <i class="bi bi-calendar-event text-emerald-600 flex-shrink-0"></i>
                        <span id="dateRangeIndicator" class="truncate font-semibold">
                            @if(request('start_date') || request('end_date'))
                                {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d M Y') : '' }} - {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d M Y') : '' }}
                            @else
                                Semua Tanggal
                            @endif
                        </span>
                    </span>
                    <i class="bi bi-chevron-down text-gray-400 text-xs flex-shrink-0 ml-1"></i>
                </button>
            </div>
        </div>

        <!-- Date Filter Modal -->
        <div id="dateFilterModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-65 backdrop-blur-sm transition-opacity" onclick="closeDateFilterModal()"></div>
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-2xl transition-all border border-gray-100 dark:border-gray-700 p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                            <i class="bi bi-calendar-range text-emerald-600"></i> Filter Rentang Tanggal
                        </h3>
                        <button type="button" onclick="closeDateFilterModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 text-lg p-1">✕</button>
                    </div>

                    <!-- Date Shortcuts inside modal -->
                    <div>
                        <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-2">Pintasan Cepat:</span>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="setDateRangeAndSubmit('today')" class="py-2 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-350 rounded-xl border border-gray-200 dark:border-gray-700 text-[11px] font-bold transition active:scale-95">Hari Ini</button>
                            <button type="button" onclick="setDateRangeAndSubmit('week')" class="py-2 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-350 rounded-xl border border-gray-200 dark:border-gray-700 text-[11px] font-bold transition active:scale-95">Minggu Ini</button>
                            <button type="button" onclick="setDateRangeAndSubmit('month')" class="py-2 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-350 rounded-xl border border-gray-200 dark:border-gray-700 text-[11px] font-bold transition active:scale-95">Bulan Ini</button>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                        <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-2.5">Pilih Rentang Manual:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            </div>
                            <div>
                                <label for="end_date" class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Tanggal Selesai</label>
                                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center gap-2 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" onclick="clearDateFilterAndSubmit()" class="px-4 py-2 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-450 hover:bg-rose-100 rounded-xl text-xs font-bold active:scale-95 transition border border-rose-100 dark:border-rose-900/60">Hapus Filter</button>
                        <div class="flex gap-2">
                            <button type="button" onclick="closeDateFilterModal()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl text-xs active:scale-95 transition">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs active:scale-95 transition shadow-md">Terapkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons Row -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-3 border-t border-gray-100 dark:border-gray-700/60">
            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1"><i class="bi bi-info-circle-fill text-emerald-600"></i> Menampilkan rekap kehadiran karyawan terdaftar.</span>
            
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-initial px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition duration-150 active:scale-95 shadow-md flex items-center justify-center gap-1.5 h-10">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('admin.attendance') }}" class="flex-1 sm:flex-initial px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-750 dark:text-gray-200 font-bold rounded-xl text-xs transition duration-150 active:scale-95 text-center flex items-center justify-center h-10">
                    Reset
                </a>
                <a href="{{ route('attendance.export', request()->all()) }}" class="flex-1 sm:flex-initial px-5 py-2.5 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-800 dark:text-emerald-400 font-bold rounded-xl text-xs transition duration-150 active:scale-95 text-center flex items-center justify-center gap-1.5 h-10" title="Ekspor data rekap absensi ke Excel">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
                <a href="{{ route('admin.attendance.print', request()->all()) }}" target="_blank" class="flex-1 sm:flex-initial px-5 py-2.5 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/60 text-blue-800 dark:text-blue-400 font-bold rounded-xl text-xs transition duration-150 active:scale-95 text-center flex items-center justify-center gap-1.5 h-10" title="Buka pratinjau cetak PDF laporan premium">
                    <i class="bi bi-printer"></i> Cetak (PDF)
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Admin Attendance Log Table -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100 dark:border-gray-700">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Rekap Absensi Karyawan</h3>
        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-xs font-semibold text-gray-600 dark:text-gray-300 rounded-full">
            Semua Riwayat
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">
                    <th class="p-4">Karyawan</th>
                    <th class="p-4">Tanggal</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Jam Masuk</th>
                    <th class="p-4">Jam Keluar</th>
                    <th class="p-4">Lokasi</th>
                    <th class="p-4">Keterangan</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-350">
                @forelse($attendances as $att)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition duration-150">
                        <td class="p-4 font-semibold text-gray-900 dark:text-white">
                            {{ $att->user->name }}
                            <div class="text-xs text-gray-400 font-normal">{{ $att->user->email }}</div>
                            @if($att->status === 'present')
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @if($att->work_mode === 'wfo')
                                        <span class="px-2 py-0.5 text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 rounded border border-emerald-200 dark:border-emerald-900/60 inline-flex items-center gap-1"><i class="bi bi-building"></i> WFO (Di Kantor)</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[9px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 rounded border border-amber-200 dark:border-amber-900/60 inline-flex items-center gap-1"><i class="bi bi-house-door"></i> WFH (Luar Kantor)</span>
                                    @endif
                                    
                                    @if($att->is_suspicious)
                                        <span class="px-2 py-0.5 text-[9px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-450 rounded border border-rose-200 dark:border-rose-900/40 inline-flex items-center gap-1 animate-pulse" title="{{ $att->spoof_reason }}">
                                            <i class="bi bi-exclamation-triangle-fill text-rose-600"></i> Mencurigakan
                                        </span>
                                    @endif

                                    @if($att->is_ip_fallback)
                                        <span class="px-2 py-0.5 text-[9px] font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 rounded border border-blue-200 dark:border-blue-900/60 inline-flex items-center gap-1" title="Absen menggunakan pencarian lokasi IP fallback">
                                            <i class="bi bi-globe"></i> IP Fallback
                                        </span>
                                    @endif
                                </div>
                                @if($att->is_suspicious && $att->spoof_reason)
                                    <div class="text-[8px] text-rose-500 font-semibold mt-1">Alasan: {{ $att->spoof_reason }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="p-4 text-gray-900 dark:text-gray-300 font-semibold">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }}</td>
                        <td class="p-4">
                            @if($att->status === 'present')
                                <span class="px-2.5 py-1 text-xs font-bold bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-400 rounded-full inline-flex items-center gap-1"><i class="bi bi-check-circle-fill"></i> Hadir</span>
                            @elseif($att->status === 'sick' || $att->status === 'leave')
                                <div class="flex flex-col gap-1.5 items-start">
                                    <span class="px-2.5 py-1 text-xs font-bold bg-{{ $att->status === 'sick' ? 'amber' : 'blue' }}-100 dark:bg-{{ $att->status === 'sick' ? 'amber' : 'blue' }}-900/40 text-{{ $att->status === 'sick' ? 'amber' : 'blue' }}-800 dark:text-{{ $att->status === 'sick' ? 'amber' : 'blue' }}-400 rounded-full inline-flex items-center gap-1">
                                        @if($att->status === 'sick')
                                            <i class="bi bi-heart-pulse-fill"></i> Sakit
                                        @else
                                            <i class="bi bi-file-earmark-text-fill"></i> Izin
                                        @endif
                                    </span>
                                    @if($att->approval_status === 'pending')
                                        <span class="px-2 py-0.5 text-[10px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded border border-gray-200 dark:border-gray-600 inline-flex items-center gap-1">
                                            <i class="bi bi-hourglass-split"></i> Menunggu
                                        </span>
                                        <div class="flex gap-1 mt-1">
                                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('approve-attendance-form-{{ $att->id }}').submit()" class="px-2 py-0.5 bg-green-600 hover:bg-green-700 text-white text-[10px] font-bold rounded shadow transition active:scale-95">Setujui</button>
                                            <form id="approve-attendance-form-{{ $att->id }}" action="{{ route('admin.attendance.approve', $att->id) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                            </form>
                                            <button type="button" onclick="openRejectModal({{ $att->id }})" class="px-2 py-0.5 bg-red-600 hover:bg-red-700 text-white text-[10px] font-bold rounded shadow transition active:scale-95">Tolak</button>
                                        </div>
                                    @elseif($att->approval_status === 'approved')
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 rounded border border-green-200 dark:border-green-900/60 inline-flex items-center gap-1">
                                            <i class="bi bi-check-circle-fill"></i> Disetujui
                                        </span>
                                    @elseif($att->approval_status === 'rejected')
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 rounded border border-rose-200 dark:border-rose-900/60 inline-flex items-center gap-1">
                                            <i class="bi bi-x-circle-fill"></i> Ditolak
                                        </span>
                                        @if($att->rejection_reason)
                                            <div class="text-[9px] text-rose-500 max-w-[150px] leading-tight mt-0.5">Alasan: {{ $att->rejection_reason }}</div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="font-mono font-bold text-gray-900 dark:text-white">{{ $att->check_in ?? '-' }}</div>
                            @if($att->status === 'present' && $att->minutes_late > 0)
                                <div class="text-[10px] font-semibold text-rose-600 dark:text-rose-400 mt-0.5 flex items-center gap-1">
                                     <i class="bi bi-alarm"></i> Terlambat {{ $att->minutes_late }} m
                                </div>
                            @endif
                        </td>
                        <td class="p-4 font-mono font-bold text-gray-900 dark:text-white">{{ $att->check_out ?? '-' }}</td>
                        <td class="p-4">
                              <div class="flex items-center gap-1.5 text-xs">
                                  @if($att->latitude_in)
                                      <button type="button" onclick="openMapModal({{ $att->latitude_in }}, {{ $att->longitude_in }}, '{{ addslashes($att->user->name) }}', 'Absen Masuk (Check-In) - {{ $att->check_in }}', '{{ $att->work_mode === 'wfh' ? 'WFH (Luar Kantor)' : 'WFO (Di Kantor)' }}')" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-400 font-bold rounded-xl flex items-center gap-1 border border-emerald-200 dark:border-emerald-900/60 transition duration-150 active:scale-95 shadow-sm text-[10px]">
                                          <i class="bi bi-geo-alt"></i> Masuk
                                      </button>
                                  @elseif($att->status === 'present')
                                      <span class="px-2 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/60 rounded-md font-bold text-[10px] inline-flex items-center gap-1"><i class="bi bi-exclamation-triangle-fill"></i> Tanpa GPS</span>
                                  @else
                                      <span class="text-gray-400">-</span>
                                  @endif
                                  
                                  <span class="text-gray-300 dark:text-gray-600">/</span>
                                  
                                  @if($att->latitude_out)
                                      <button type="button" onclick="openMapModal({{ $att->latitude_out }}, {{ $att->longitude_out }}, '{{ addslashes($att->user->name) }}', 'Absen Keluar (Check-Out) - {{ $att->check_out }}', '{{ $att->work_mode === 'wfh' ? 'WFH (Luar Kantor)' : 'WFO (Di Kantor)' }}')" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-400 font-bold rounded-xl flex items-center gap-1 border border-emerald-200 dark:border-emerald-900/60 transition duration-150 active:scale-95 shadow-sm text-[10px]">
                                          <i class="bi bi-geo-alt"></i> Keluar
                                      </button>
                                  @elseif($att->check_out)
                                      <span class="px-2 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/60 rounded-md font-bold text-[10px] inline-flex items-center gap-1"><i class="bi bi-exclamation-triangle-fill"></i> Tanpa GPS</span>
                                  @else
                                      <span class="text-gray-400">-</span>
                                  @endif
                              </div>
                        </td>
                        <td class="p-4 text-xs text-gray-500 dark:text-gray-400 italic max-w-xs truncate">{{ $att->notes ?? '-' }}</td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-1.5">
                                <button type="button" 
                                        data-id="{{ $att->id }}"
                                        data-status="{{ $att->status }}"
                                        data-work-mode="{{ $att->work_mode }}"
                                        data-minutes-late="{{ $att->minutes_late }}"
                                        data-notes="{{ $att->notes }}"
                                        onclick="openEditAttendanceModal(this)" 
                                        class="px-2.5 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 hover:bg-blue-100 rounded-lg text-xs font-bold border border-blue-100 dark:border-blue-900/60 active:scale-95 transition">
                                    <i class="bi bi-pencil mr-1"></i> Edit
                                </button>
                                <button type="button" 
                                        data-form-id="delete-attendance-form-{{ $att->id }}"
                                        data-title="Hapus Log Kehadiran"
                                        data-message="Apakah Anda yakin ingin menghapus log absensi ini?"
                                        onclick="event.preventDefault(); event.stopPropagation(); showConfirmModal(this)" 
                                        class="px-2.5 py-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-455 hover:bg-rose-100 rounded-lg text-xs font-bold border border-rose-100 dark:border-rose-900/60 active:scale-95 transition">
                                    <i class="bi bi-trash mr-1"></i> Hapus
                                </button>
                                <form id="delete-attendance-form-{{ $att->id }}" action="{{ route('admin.attendance.destroy', $att->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-400 dark:text-gray-500">
                            Belum ada data absensi tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
