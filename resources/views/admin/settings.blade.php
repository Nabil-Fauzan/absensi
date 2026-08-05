<x-app-layout>
    <!-- Leaflet CSS & JS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Geofencing Kantor') }}
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

            <!-- Navigation Tabs -->
            <div class="flex border-b border-gray-200 dark:border-gray-700 gap-1 mb-6 flex-wrap">
                <button type="button" id="tab-btn-settings" onclick="switchSettingsTab('settings-tab')" class="px-5 py-2.5 text-xs font-bold border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 transition">
                    <i class="bi bi-gear mr-1"></i> Pusat & Geofence
                </button>
                <button type="button" id="tab-btn-branches" onclick="switchSettingsTab('branches-tab')" class="px-5 py-2.5 text-xs font-bold border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 transition">
                    <i class="bi bi-buildings mr-1"></i> Cabang Kantor
                </button>
                <button type="button" id="tab-btn-shifts" onclick="switchSettingsTab('shifts-tab')" class="px-5 py-2.5 text-xs font-bold border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 transition">
                    <i class="bi bi-clock-history mr-1"></i> Shift Kerja
                </button>
                <button type="button" id="tab-btn-holidays" onclick="switchSettingsTab('holidays-tab')" class="px-5 py-2.5 text-xs font-bold border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 transition">
                    <i class="bi bi-calendar-event mr-1"></i> Hari Libur
                </button>
            </div>

            <!-- Tab Contents -->
            <div id="content-settings" class="settings-content hidden">
                @include('admin.partials.settings-form')
            </div>

            <div id="content-branches" class="settings-content hidden">
                @include('admin.partials.settings-branches')
            </div>

            <div id="content-shifts" class="settings-content hidden">
                @include('admin.partials.settings-shifts')
            </div>

            <div id="content-holidays" class="settings-content hidden">
                @include('admin.partials.settings-holidays')
            </div>

        </div>
    </div>

    @include('admin.partials.modals')

    <script>
        // --- TAB SWITCHER LOGIC ---
        function switchSettingsTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.settings-content').forEach(el => el.classList.add('hidden'));
            
            // Remove active classes from all buttons
            document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
                btn.classList.remove('border-emerald-600', 'text-emerald-600', 'dark:text-emerald-400', 'dark:border-emerald-500');
                btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            // Show active content and style button
            let activeBtnId = 'tab-btn-settings';
            let activeContentId = 'content-settings';

            if (tabId === 'branches-tab') {
                activeBtnId = 'tab-btn-branches';
                activeContentId = 'content-branches';
            } else if (tabId === 'shifts-tab') {
                activeBtnId = 'tab-btn-shifts';
                activeContentId = 'content-shifts';
            } else if (tabId === 'holidays-tab') {
                activeBtnId = 'tab-btn-holidays';
                activeContentId = 'content-holidays';
            }

            const activeBtn = document.getElementById(activeBtnId);
            const activeContent = document.getElementById(activeContentId);

            if (activeBtn && activeContent) {
                activeContent.classList.remove('hidden');
                activeBtn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                activeBtn.classList.add('border-emerald-600', 'text-emerald-600', 'dark:text-emerald-400', 'dark:border-emerald-500');
            }

            // Invalidate Leaflet maps sizes if visible
            if (tabId === 'settings-tab') {
                setTimeout(initConfigMap, 100);
            }
        }

        // --- GEOFENCE MAP CONFIGURATION ---
        let configMap = null;
        let configMarker = null;
        let configCircle = null;

        function initConfigMap() {
            if (typeof L === 'undefined') {
                console.warn('Gagal menginisialisasi peta: Leaflet library tidak terload.');
                return;
            }
            const latInput = document.getElementById('office_lat');
            const lngInput = document.getElementById('office_lng');
            const radiusInput = document.getElementById('office_radius');
            if (!latInput || !lngInput || !radiusInput) return;

            let lat = parseFloat(latInput.value) || {{ env('OFFICE_LATITUDE', -6.873218738309585) }};
            let lng = parseFloat(lngInput.value) || {{ env('OFFICE_LONGITUDE', 107.5609385222725) }};
            let radius = parseInt(radiusInput.value) || 100;

            setTimeout(() => {
                const centerCoords = [lat, lng];
                
                if (configMap === null) {
                    configMap = L.map('configMap').setView(centerCoords, 16);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
                    }).addTo(configMap);

                    // Add draggable marker
                    configMarker = L.marker(centerCoords, { draggable: true }).addTo(configMap);
                    
                    // Add radius circle
                    configCircle = L.circle(centerCoords, {
                        color: '#10b981',
                        fillColor: '#10b981',
                        fillOpacity: 0.15,
                        radius: radius
                    }).addTo(configMap);

                    // Event: Marker dragged
                    configMarker.on('dragend', function (e) {
                        const position = configMarker.getLatLng();
                        latInput.value = position.lat.toFixed(8);
                        lngInput.value = position.lng.toFixed(8);
                        configCircle.setLatLng(position);
                        configMap.panTo(position);
                    });

                    // Event: Click on map
                    configMap.on('click', function (e) {
                        const position = e.latlng;
                        configMarker.setLatLng(position);
                        latInput.value = position.lat.toFixed(8);
                        lngInput.value = position.lng.toFixed(8);
                        configCircle.setLatLng(position);
                        configMap.panTo(position);
                    });

                    // Sync changes from manual coordinate inputs
                    const updateFromInputs = () => {
                        const newLat = parseFloat(latInput.value);
                        const newLng = parseFloat(lngInput.value);
                        if (!isNaN(newLat) && !isNaN(newLng)) {
                            const newPos = [newLat, newLng];
                            configMarker.setLatLng(newPos);
                            configCircle.setLatLng(newPos);
                            configMap.setView(newPos, configMap.getZoom());
                        }
                    };

                    latInput.addEventListener('input', updateFromInputs);
                    lngInput.addEventListener('input', updateFromInputs);

                    radiusInput.addEventListener('input', function () {
                        const newRadius = parseInt(radiusInput.value);
                        if (!isNaN(newRadius) && newRadius > 0) {
                            configCircle.setRadius(newRadius);
                        }
                    });
                } else {
                    configMap.setView(centerCoords, 16);
                    configMarker.setLatLng(centerCoords);
                    configCircle.setLatLng(centerCoords);
                    configCircle.setRadius(radius);
                }

                configMap.invalidateSize();
            }, 200);
        }

        // --- BRANCH MAPS (ADD/EDIT) ---
        let addBranchMap = null, addBranchMarker = null, addBranchCircle = null;
        let editBranchMap = null, editBranchMarker = null, editBranchCircle = null;

        function openAddBranchModal() {
            document.getElementById('addBranchModal').classList.remove('hidden');
            
            const latInput = document.getElementById('add_branch_lat');
            const lngInput = document.getElementById('add_branch_lng');
            const radiusInput = document.getElementById('add_branch_radius');
            
            if (!latInput.value) latInput.value = "-6.87321873";
            if (!lngInput.value) lngInput.value = "107.56093852";
            
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            const radius = parseInt(radiusInput.value) || 100;
            
            setTimeout(() => {
                if (addBranchMap === null) {
                    addBranchMap = L.map('addBranchMap').setView([lat, lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(addBranchMap);
                    
                    addBranchMarker = L.marker([lat, lng], { draggable: true }).addTo(addBranchMap);
                    addBranchCircle = L.circle([lat, lng], {
                        color: '#10b981',
                        fillColor: '#10b981',
                        fillOpacity: 0.15,
                        radius: radius
                    }).addTo(addBranchMap);
                    
                    addBranchMarker.on('dragend', function() {
                        const pos = addBranchMarker.getLatLng();
                        latInput.value = pos.lat.toFixed(8);
                        lngInput.value = pos.lng.toFixed(8);
                        addBranchCircle.setLatLng(pos);
                    });
                    
                    addBranchMap.on('click', function(e) {
                        const pos = e.latlng;
                        addBranchMarker.setLatLng(pos);
                        latInput.value = pos.lat.toFixed(8);
                        lngInput.value = pos.lng.toFixed(8);
                        addBranchCircle.setLatLng(pos);
                    });
                    
                    radiusInput.addEventListener('input', function() {
                        const r = parseInt(radiusInput.value);
                        if (!isNaN(r) && r > 0) addBranchCircle.setRadius(r);
                    });
                } else {
                    addBranchMap.setView([lat, lng], 15);
                    addBranchMarker.setLatLng([lat, lng]);
                    addBranchCircle.setLatLng([lat, lng]);
                    addBranchCircle.setRadius(radius);
                }
                addBranchMap.invalidateSize();
            }, 200);
        }

        function closeAddBranchModal() {
            document.getElementById('addBranchModal').classList.add('hidden');
        }

        function openEditBranchModal(button) {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const lat = parseFloat(button.dataset.latitude);
            const lng = parseFloat(button.dataset.longitude);
            const radius = parseInt(button.dataset.radius);

            document.getElementById('edit_branch_name').value = name;
            document.getElementById('edit_branch_lat').value = lat;
            document.getElementById('edit_branch_lng').value = lng;
            document.getElementById('edit_branch_radius').value = radius;
            document.getElementById('editBranchForm').action = `/admin/branches/${id}`;
            
            document.getElementById('editBranchModal').classList.remove('hidden');
            
            const radiusInput = document.getElementById('edit_branch_radius');
            const latInput = document.getElementById('edit_branch_lat');
            const lngInput = document.getElementById('edit_branch_lng');

            setTimeout(() => {
                if (editBranchMap === null) {
                    editBranchMap = L.map('editBranchMap').setView([lat, lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(editBranchMap);
                    
                    editBranchMarker = L.marker([lat, lng], { draggable: true }).addTo(editBranchMap);
                    editBranchCircle = L.circle([lat, lng], {
                        color: '#3b82f6',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.15,
                        radius: radius
                    }).addTo(editBranchMap);
                    
                    editBranchMarker.on('dragend', function() {
                        const pos = editBranchMarker.getLatLng();
                        latInput.value = pos.lat.toFixed(8);
                        lngInput.value = pos.lng.toFixed(8);
                        editBranchCircle.setLatLng(pos);
                    });
                    
                    editBranchMap.on('click', function(e) {
                        const pos = e.latlng;
                        editBranchMarker.setLatLng(pos);
                        latInput.value = pos.lat.toFixed(8);
                        lngInput.value = pos.lng.toFixed(8);
                        editBranchCircle.setLatLng(pos);
                    });
                    
                    radiusInput.addEventListener('input', function() {
                        const r = parseInt(radiusInput.value);
                        if (!isNaN(r) && r > 0) editBranchCircle.setRadius(r);
                    });
                } else {
                    editBranchMap.setView([lat, lng], 15);
                    editBranchMarker.setLatLng([lat, lng]);
                    editBranchCircle.setLatLng([lat, lng]);
                    editBranchCircle.setRadius(radius);
                }
                editBranchMap.invalidateSize();
            }, 200);
        }

        function closeEditBranchModal() {
            document.getElementById('editBranchModal').classList.add('hidden');
        }

        // --- SHIFT MODAL CRUD ---
        function openAddShiftModal() {
            document.getElementById('addShiftModal').classList.remove('hidden');
        }
        function closeAddShiftModal() {
            document.getElementById('addShiftModal').classList.add('hidden');
        }
        function openEditShiftModal(button) {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const startTime = button.dataset.startTime.substring(0, 5);
            const endTime = button.dataset.endTime.substring(0, 5);

            document.getElementById('edit_shift_name').value = name;
            document.getElementById('edit_shift_start').value = startTime;
            document.getElementById('edit_shift_end').value = endTime;
            document.getElementById('editShiftForm').action = `/admin/shifts/${id}`;
            document.getElementById('editShiftModal').classList.remove('hidden');
        }
        function closeEditShiftModal() {
            document.getElementById('editShiftModal').classList.add('hidden');
        }

        // Initialize view
        document.addEventListener('DOMContentLoaded', () => {
            // Check if there is an active tab stored in Laravel session
            const activeTab = "{{ session('active_tab', 'settings-tab') }}";
            switchSettingsTab(activeTab);
        });
    </script>
</x-app-layout>
