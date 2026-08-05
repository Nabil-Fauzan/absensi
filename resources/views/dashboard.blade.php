<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Notification -->
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-900 dark:text-green-400 border border-green-200 dark:border-green-850 shadow-sm transition duration-150 ease-in-out" role="alert">
                    <span class="font-medium">Sukses!</span> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-900 dark:text-red-400 border border-red-200 dark:border-red-850 shadow-sm transition duration-150 ease-in-out" role="alert">
                    <span class="font-medium">Gagal!</span> {{ session('error') }}
                </div>
            @endif

            <!-- Birthday Banner -->
            @if (isset($isBirthday) && $isBirthday)
                <div class="relative overflow-hidden p-6 rounded-3xl bg-gradient-to-r from-pink-500 to-rose-500 text-white shadow-xl border border-pink-400/30 flex items-center gap-4 transition duration-150 transform hover:scale-[1.01]">
                    <div class="absolute -right-6 -bottom-6 text-9xl opacity-20 transform rotate-12">🎉</div>
                    <div class="text-4xl">🎂</div>
                    <div>
                        <h4 class="font-extrabold text-lg">Selamat Ulang Tahun, {{ Auth::user()->name }}!</h4>
                        <p class="text-xs text-pink-100 font-medium mt-0.5">Semoga panjang umur dan sukses selalu! Nikmati jatah khusus 1 Hari Cuti Ulang Tahun Anda.</p>
                    </div>
                </div>
            @endif

            <!-- Holiday Banner -->
            @if (isset($todayHoliday) && $todayHoliday)
                <div class="p-6 rounded-3xl bg-emerald-50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 shadow-md border border-emerald-100 dark:border-emerald-900/40 flex items-center gap-4 transition duration-150">
                    <div class="text-3xl text-emerald-600 dark:text-emerald-400"><i class="bi bi-calendar-check-fill"></i></div>
                    <div>
                        <h4 class="font-extrabold text-sm md:text-base">Hari Libur Nasional: {{ $todayHoliday->name }}</h4>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400/80 font-medium mt-0.5">Hari ini adalah hari libur resmi. Aktivitas absensi dibebaskan untuk hari ini.</p>
                    </div>
                </div>
            @endif

            <!-- ================= EMPLOYEE DASHBOARD ================= -->
            @include('employee.partials.monthly-stats')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @include('employee.partials.clock-widget')
                @include('employee.partials.leave-form')
            </div>
            @include('employee.partials.history-table')

        </div>
    </div>

    <script>
        // --- Live Clock Script ---
        function startLiveClock() {
            const clockEl = document.getElementById('liveClock');
            if (!clockEl) return;
            
            setInterval(() => {
                const now = new Date();
                const hrs = String(now.getHours()).padStart(2, '0');
                const mins = String(now.getMinutes()).padStart(2, '0');
                const secs = String(now.getSeconds()).padStart(2, '0');
                clockEl.textContent = `${hrs}:${mins}:${secs}`;
            }, 1000);
        }

        // --- Geolocation Request for Check-In / Check-Out with IP Fallback and Spoof Detection ---
        async function requestLocationAndSubmit(formId) {
            const form = document.getElementById(formId);
            if (!form) return;
            
            // Disable button and show spinner loading indicator
            const btn = form.querySelector('button');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<span class="inline-block w-3.5 h-3.5 rounded-full border-2 border-white/30 border-t-white animate-spin mr-1"></span> Memproses...`;
            }

            // Fetch IP-based Location fallback (with 3-second timeout limit)
            let ipData = null;
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 3000);
                const res = await fetch('https://ipapi.co/json/', { signal: controller.signal });
                clearTimeout(timeoutId);
                if (res.ok) {
                    ipData = await res.json();
                }
            } catch (err) {
                console.warn("Gagal meload IP Geolocation:", err);
            }

            if (ipData) {
                form.querySelector('input[name="ip_latitude"]').value = ipData.latitude || '';
                form.querySelector('input[name="ip_longitude"]').value = ipData.longitude || '';
                form.querySelector('input[name="ip_city"]').value = ipData.city || '';
                form.querySelector('input[name="ip_accuracy"]').value = ipData.accuracy || '';
            }

            // Retrieve HTML5 GPS Geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        form.querySelector('input[name="latitude"]').value = position.coords.latitude;
                        form.querySelector('input[name="longitude"]').value = position.coords.longitude;
                        form.querySelector('input[name="accuracy"]').value = position.coords.accuracy || '';
                        form.submit();
                    },
                    (error) => {
                        console.warn("Akses GPS diblokir, menggunakan koordinat IP fallback.");
                        form.submit();
                    },
                    { enableHighAccuracy: true, timeout: 5000 }
                );
            } else {
                form.submit();
            }
        }

        // Initialize clock on load
        document.addEventListener('DOMContentLoaded', () => {
            startLiveClock();
        });
    </script>
</x-app-layout>
