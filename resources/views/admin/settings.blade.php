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

            <div class="space-y-6">
                @include('admin.partials.settings-form')
            </div>

        </div>
    </div>

    @include('admin.partials.modals')

    <script>
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

        // Initialize clock & charts on load
        document.addEventListener('DOMContentLoaded', () => {
            initConfigMap();
        });
    </script>
</x-app-layout>
