<!-- History Table for Employee -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 dark:border-gray-700 mt-6">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Riwayat Kehadiran Anda</h3>
        <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-xs font-semibold text-emerald-600 dark:text-emerald-400 rounded-full">
            Bulan Ini
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">
                    <th class="p-4">Tanggal</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Jam Masuk</th>
                    <th class="p-4">Jam Keluar</th>
                    <th class="p-4">Keterangan / Alasan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-350">
                @forelse($attendances as $att)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition duration-150">
                        <td class="p-4 text-gray-900 dark:text-gray-300 font-semibold">
                            {{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }}
                            @if($att->status === 'present')
                                <div class="mt-1">
                                    @if($att->work_mode === 'wfo')
                                        <span class="px-2 py-0.5 text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 rounded border border-emerald-200 dark:border-emerald-900/60 flex items-center gap-1 w-max"><i class="bi bi-building"></i> WFO (Di Kantor)</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[9px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 rounded border border-amber-200 dark:border-amber-900/60 flex items-center gap-1 w-max"><i class="bi bi-house-door"></i> WFH (Luar Kantor)</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($att->status === 'present')
                                <span class="px-2.5 py-1 text-xs font-bold bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-400 rounded-full inline-flex items-center gap-1"><i class="bi bi-check-circle-fill"></i> Hadir</span>
                            @elseif($att->status === 'sick' || $att->status === 'leave')
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="px-2.5 py-1 text-xs font-bold bg-{{ $att->status === 'sick' ? 'amber' : 'blue' }}-100 dark:bg-{{ $att->status === 'sick' ? 'amber' : 'blue' }}-900/40 text-{{ $att->status === 'sick' ? 'amber' : 'blue' }}-800 dark:text-{{ $att->status === 'sick' ? 'amber' : 'blue' }}-400 rounded-full inline-flex items-center gap-1">
                                        @if($att->status === 'sick')
                                            <i class="bi bi-heart-pulse-fill"></i> Sakit
                                        @else
                                            <i class="bi bi-file-earmark-text-fill"></i> Izin
                                        @endif
                                    </span>
                                    @if($att->approval_status === 'pending')
                                        <span class="px-1.5 py-0.5 text-[9px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded border border-gray-200 dark:border-gray-600 inline-flex items-center gap-1"><i class="bi bi-hourglass-split"></i> Pending</span>
                                    @elseif($att->approval_status === 'approved')
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 rounded border border-green-200 dark:border-green-900/60 inline-flex items-center gap-1"><i class="bi bi-check-circle-fill"></i> Disetujui</span>
                                    @elseif($att->approval_status === 'rejected')
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 rounded border border-rose-200 dark:border-rose-900/60 inline-flex items-center gap-1"><i class="bi bi-x-circle-fill"></i> Ditolak</span>
                                        @if($att->rejection_reason)
                                            <span class="text-[9px] text-rose-500 max-w-[120px] leading-tight mt-0.5">Alasan: {{ $att->rejection_reason }}</span>
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
                        <td class="p-4 font-mono font-bold">{{ $att->check_out ?? '-' }}</td>
                        <td class="p-4 text-xs text-gray-500 dark:text-gray-400 italic max-w-sm truncate">{{ $att->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400 dark:text-gray-500">
                            Belum ada riwayat absensi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
