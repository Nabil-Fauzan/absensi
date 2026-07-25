<!-- Quick Check-In / Check-Out Widget -->
<!-- Today's Status -->
<div class="lg:col-span-2 p-8 bg-emerald-600 rounded-3xl shadow-xl text-white flex flex-col justify-between min-h-[250px]">
    <div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="text-sm font-semibold opacity-75 uppercase tracking-wider">Status Absensi Hari Ini</div>
                <div class="text-lg font-bold mt-1 opacity-90 text-emerald-50">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</div>
            </div>
            <!-- Real-time Live Clock Card (Glassmorphism) -->
            <div class="px-5 py-2 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 flex flex-col items-center">
                <span class="text-[9px] uppercase tracking-widest font-bold text-emerald-200">Waktu Sekarang</span>
                <span class="text-xl font-black font-mono tracking-wider mt-0.5" id="liveClock">00:00:00</span>
            </div>
        </div>
        
        <div class="mt-6">
            @if(!$todayAttendance)
                <div class="text-xl font-medium text-emerald-100">Belum melakukan absensi hari ini. Silakan klik absen masuk atau ajukan izin.</div>
            @elseif($todayAttendance->status === 'present')
                <div class="space-y-2">
                    <div class="text-3xl font-extrabold flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded-full bg-green-400 animate-ping"></span>
                        Sudah Absen Masuk
                    </div>
                    <div class="text-sm text-emerald-100">
                        Jam Masuk: <span class="font-mono font-bold bg-white/20 px-2 py-0.5 rounded text-white">{{ $todayAttendance->check_in }}</span>
                        @if($todayAttendance->check_out)
                            | Jam Pulang: <span class="font-mono font-bold bg-white/20 px-2 py-0.5 rounded text-white">{{ $todayAttendance->check_out }}</span>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-2xl font-bold flex items-center gap-2">
                    Status: {{ $todayAttendance->status === 'sick' ? '🩺 Sakit' : '📄 Izin' }}
                </div>
                <p class="text-sm text-emerald-100 mt-2 italic">Keterangan: "{{ $todayAttendance->notes }}"</p>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex flex-wrap gap-4">
        @if(!$todayAttendance || ($todayAttendance->status === 'present' && $todayAttendance->check_out))
            <form action="{{ route('attendance.check-in') }}" method="POST" id="check-in-form">
                @csrf
                <input type="hidden" name="latitude" value="">
                <input type="hidden" name="longitude" value="">
                <button type="button" onclick="requestLocationAndSubmit('check-in-form')" class="px-6 py-3 bg-white text-emerald-800 font-bold rounded-xl shadow-lg hover:bg-emerald-50 hover:scale-[1.02] active:scale-95 transition duration-150">
                    👉 Absen Masuk (Check-In)
                </button>
            </form>
        @elseif($todayAttendance->status === 'present' && !$todayAttendance->check_out)
            <form action="{{ route('attendance.check-out') }}" method="POST" id="check-out-form">
                @csrf
                <input type="hidden" name="latitude" value="">
                <input type="hidden" name="longitude" value="">
                <button type="button" onclick="requestLocationAndSubmit('check-out-form')" class="px-6 py-3 bg-rose-500 text-white font-bold rounded-xl shadow-lg hover:bg-rose-600 hover:scale-[1.02] active:scale-95 transition duration-150">
                    👈 Absen Keluar (Check-Out)
                </button>
            </form>
        @endif
    </div>
</div>
