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

        // --- Geolocation Request for Check-In / Check-Out ---
        function requestLocationAndSubmit(formId) {
            const form = document.getElementById(formId);
            if (!form) return;
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        // Set coords value
                        form.querySelector('input[name="latitude"]').value = position.coords.latitude;
                        form.querySelector('input[name="longitude"]').value = position.coords.longitude;
                        form.submit();
                    },
                    (error) => {
                        // Fallback - submit without coordinate data (will WFH)
                        console.warn("Akses GPS ditolak, mengisi WFH secara default.");
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
