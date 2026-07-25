<x-app-layout>
    <!-- Leaflet CSS & JS for Map Modal -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js for Weekly Attendance Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard Absensi') }}
            </h2>
            
            @if(Auth::user()->isAdmin())
            <!-- Admin Navigation Tab Pills inside Header -->
            <div class="flex bg-gray-100 dark:bg-gray-900 p-1 rounded-2xl gap-1 border border-gray-200 dark:border-gray-800 shadow-inner">
                <button onclick="switchAdminTab('presence-tab')" id="tab-presence-tab" class="admin-tab-btn px-4 py-1.5 rounded-xl text-xs font-bold transition-all duration-150 bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-sm">
                    📋 Kehadiran
                </button>
                <button onclick="switchAdminTab('employee-tab')" id="tab-employee-tab" class="admin-tab-btn px-4 py-1.5 rounded-xl text-xs font-bold transition-all duration-150 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    👥 Karyawan
                </button>
                <button onclick="switchAdminTab('settings-tab')" id="tab-settings-tab" class="admin-tab-btn px-4 py-1.5 rounded-xl text-xs font-bold transition-all duration-150 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    ⚙️ Geofencing
                </button>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Notification -->
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-850 dark:text-green-400 border border-green-200 dark:border-green-800 shadow-sm transition duration-150 ease-in-out" role="alert">
                    <span class="font-medium">Sukses!</span> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-850 dark:text-red-400 border border-red-200 dark:border-red-800 shadow-sm transition duration-150 ease-in-out" role="alert">
                    <span class="font-medium">Gagal!</span> {{ session('error') }}
                </div>
            @endif

            @if (Auth::user()->isAdmin())
                <!-- ================= ADMIN DASHBOARD ================= -->
                
                <!-- Tab 1: Presence Log -->
                <div id="presence-tab" class="admin-tab-content space-y-6">
                    @include('admin.partials.stat-cards')
                    @include('admin.partials.geofence-status')
                    @include('admin.partials.weekly-chart')
                    @include('admin.partials.attendance-table')
                </div>

                <!-- Tab 2: Employee CRUD Management -->
                <div id="employee-tab" class="admin-tab-content hidden space-y-6">
                    @include('admin.partials.employees-crud')
                </div>

                <!-- Tab 3: Office Settings -->
                <div id="settings-tab" class="admin-tab-content hidden space-y-6">
                    @include('admin.partials.settings-form')
                </div>

            @else
                <!-- ================= EMPLOYEE DASHBOARD ================= -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    @include('employee.partials.clock-widget')
                    @include('employee.partials.leave-form')
                </div>
                @include('employee.partials.history-table')
            @endif

        </div>
    </div>

    @include('admin.partials.modals')

    <script>
        // Leaflet Map modal instance variables
        let myMap = null;
        let myMarker = null;
        let currentModalCoords = null;

        function openMapModal(latitude, longitude, employeeName, timeLabel, modeText) {
            currentModalCoords = `${latitude},${longitude}`;
            
            // Show modal
            const modal = document.getElementById('mapModal');
            modal.classList.remove('hidden');
            
            // Set header labels
            document.getElementById('modalTitle').innerText = `Lokasi Absen: ${employeeName}`;
            document.getElementById('modalSubtitle').innerText = `${timeLabel} (${modeText})`;
            
            // Set map position after a small timeout to let modal display first
            setTimeout(() => {
                const centerCoords = [latitude, longitude];
                
                if (myMap === null) {
                    myMap = L.map('mapContainer').setView(centerCoords, 16);
                    
                    // Use openstreetmap tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
                    }).addTo(myMap);
                } else {
                    myMap.setView(centerCoords, 16);
                }
                
                // Clear existing marker if any
                if (myMarker !== null) {
                    myMap.removeLayer(myMarker);
                }
                
                // Create new marker
                myMarker = L.marker(centerCoords).addTo(myMap);
                
                // Add popup
                myMarker.bindPopup(`
                    <div class="text-sm">
                        <strong class="text-emerald-700">${employeeName}</strong><br>
                        <span class="text-xs text-gray-500">${timeLabel}</span><br>
                        <span class="inline-block mt-1 px-1.5 py-0.5 text-[10px] font-bold bg-emerald-55 text-emerald-850 rounded border border-emerald-250">${modeText}</span>
                    </div>
                `).openPopup();
                
                // Trigger map resize to redraw correctly inside dynamic container
                myMap.invalidateSize();
            }, 200);
        }

        function closeMapModal() {
            const modal = document.getElementById('mapModal');
            modal.classList.add('hidden');
        }

        function copyMapCoordinates() {
            if (currentModalCoords) {
                navigator.clipboard.writeText(currentModalCoords).then(() => {
                    const btn = document.getElementById('copyCoordsBtn');
                    const origText = btn.innerHTML;
                    btn.innerHTML = '✅ Tersalin!';
                    setTimeout(() => {
                        btn.innerHTML = origText;
                    }, 2000);
                });
            }
        }

        @if(Auth::user()->isAdmin())
        function openBelumAbsenModal() {
            document.getElementById('belumAbsenModal').classList.remove('hidden');
        }

        function closeBelumAbsenModal() {
            document.getElementById('belumAbsenModal').classList.add('hidden');
        }

        function copyBelumAbsenList() {
            const names = [
                @foreach($belumAbsenUsers as $u)
                    "{{ $u->name }} ({{ $u->email }})",
                @endforeach
            ];
            
            const textToCopy = "Daftar Karyawan Belum Absen Hari Ini:\n" + names.map((n, i) => `${i+1}. ${n}`).join("\n");
            
            navigator.clipboard.writeText(textToCopy).then(() => {
                const btn = document.getElementById('copyBelumAbsenBtn');
                const origText = btn.innerHTML;
                btn.innerHTML = '✅ Berhasil Disalin!';
                setTimeout(() => {
                    btn.innerHTML = origText;
                }, 2000);
            });
        }
        @endif

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

        // --- Filter Helpers ---
        function filterByStatus(status) {
            document.getElementById('filterStatus').value = status;
            document.getElementById('filterForm').submit();
        }

        function clearStatusFilter() {
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterForm').submit();
        }

        function setDateRange(range) {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            const now = new Date();
            
            // Helper to format Date to YYYY-MM-DD local string
            const formatDate = (d) => {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            if (range === 'today') {
                startInput.value = formatDate(now);
                endInput.value = formatDate(now);
            } else if (range === 'week') {
                const firstDay = new Date(now.setDate(now.getDate() - now.getDay() + (now.getDay() === 0 ? -6 : 1))); // Monday
                const lastDay = new Date(firstDay);
                lastDay.setDate(lastDay.getDate() + 6); // Sunday
                
                startInput.value = formatDate(firstDay);
                endInput.value = formatDate(lastDay);
            } else if (range === 'month') {
                const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                
                startInput.value = formatDate(firstDay);
                endInput.value = formatDate(lastDay);
            }
            
            document.getElementById('filterForm').submit();
        }

        // --- Chart.js Weekly rendering ---
        @if(Auth::user()->isAdmin())
        function initWeeklyChart() {
            const ctx = document.getElementById('weeklyAttendanceChart');
            if (!ctx) return;
            
            const rawData = @json($chartData);
            const labels = rawData.map(d => d.label);
            const hadirData = rawData.map(d => d.hadir);
            const izinData = rawData.map(d => d.izin);
            const belumAbsenData = rawData.map(d => d.belum_absen);
            
            // Check dark mode
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? '#374151' : '#f3f4f6';
            const labelColor = isDark ? '#9ca3af' : '#4b5563';
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '🟢 Hadir',
                            data: hadirData,
                            backgroundColor: '#10b981', // Emerald 500
                            borderRadius: 6,
                        },
                        {
                            label: '🩺/📄 Sakit/Izin',
                            data: izinData,
                            backgroundColor: '#3b82f6', // Blue 500
                            borderRadius: 6,
                        },
                        {
                            label: '❌ Belum Absen',
                            data: belumAbsenData,
                            backgroundColor: '#f43f5e', // Rose 500
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                            ticks: { color: labelColor }
                        },
                        y: {
                            stacked: true,
                            grid: { color: gridColor },
                            ticks: { 
                                stepSize: 1,
                                color: labelColor 
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: labelColor,
                                boxWidth: 12,
                                padding: 15,
                                font: { weight: 'bold', size: 11 }
                            }
                        }
                    }
                }
            });
        }
        @endif

        // Initialize clock & charts on load
        document.addEventListener('DOMContentLoaded', () => {
            startLiveClock();
            @if(Auth::user()->isAdmin())
                initWeeklyChart();
            @endif
        });

        // --- ADMIN TABS SWITCHER ---
        function switchAdminTab(tabId) {
            document.querySelectorAll('.admin-tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.admin-tab-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'dark:bg-gray-800', 'text-emerald-600', 'dark:text-emerald-400', 'shadow-sm');
                btn.classList.add('text-gray-500', 'dark:text-gray-400');
            });
            
            document.getElementById(tabId).classList.remove('hidden');
            const activeBtn = document.getElementById('tab-' + tabId);
            activeBtn.classList.remove('text-gray-500', 'dark:text-gray-400');
            activeBtn.classList.add('bg-white', 'dark:bg-gray-800', 'text-emerald-600', 'dark:text-emerald-400', 'shadow-sm');
        }

        // --- EMPLOYEE MODAL CRUD ---
        function openAddEmployeeModal() {
            document.getElementById('addEmployeeModal').classList.remove('hidden');
        }
        function closeAddEmployeeModal() {
            document.getElementById('addEmployeeModal').classList.add('hidden');
        }
        
        function openEditEmployeeModal(id, name, email) {
            document.getElementById('editEmpName').value = name;
            document.getElementById('editEmpEmail').value = email;
            document.getElementById('editEmployeeForm').action = `/admin/employees/${id}`;
            document.getElementById('editEmployeeModal').classList.remove('hidden');
        }
        function closeEditEmployeeModal() {
            document.getElementById('editEmployeeModal').classList.add('hidden');
        }
        
        // --- ATTENDANCE CORRECTION MODAL ---
        function openEditAttendanceModal(id, status, workMode, minutesLate, notes) {
            document.getElementById('editAttStatus').value = status;
            document.getElementById('editAttWorkMode').value = workMode || 'wfo';
            document.getElementById('editAttMinutesLate').value = minutesLate || 0;
            document.getElementById('editAttNotes').value = notes || '';
            document.getElementById('editAttendanceForm').action = `/admin/attendance/${id}`;
            toggleEditAttFields();
            document.getElementById('editAttendanceModal').classList.remove('hidden');
        }
        function closeEditAttendanceModal() {
            document.getElementById('editAttendanceModal').classList.add('hidden');
        }
        
        function toggleEditAttFields() {
            const status = document.getElementById('editAttStatus').value;
            const fields = document.getElementById('editAttPresentFields');
            if (status === 'present') {
                fields.classList.remove('hidden');
                document.getElementById('editAttMinutesLate').required = true;
            } else {
                fields.classList.add('hidden');
                document.getElementById('editAttMinutesLate').required = false;
            }
        }
    </script>
</x-app-layout>
