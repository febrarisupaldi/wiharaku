import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

const mapElement = document.getElementById('vihara-map');

if (mapElement) {
    initializeViharaMap();
}

function initializeViharaMap() {
    const statusElement = document.getElementById('map-status');
    const locateButton = document.getElementById('locate-me');

    // Posisi awal: Indonesia
    const map = L.map('vihara-map', {
        zoomControl: false,
        preferCanvas: true,
    }).setView([-2.5, 118], 5);

    L.control.zoom({
        position: 'bottomright',
    }).addTo(map);

    // Tile OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    // Marker cluster
    const markerGroup = L.markerClusterGroup({
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
    });

    map.addLayer(markerGroup);

    let userMarker = null;
    let accuracyCircle = null;
    let requestController = null;
    let debounceTimer = null;

    function setStatus(message) {
        if (statusElement) {
            statusElement.textContent = message;
        }
    }

    // Membuat popup menggunakan DOM agar data API tidak
    // langsung dimasukkan sebagai HTML mentah.
    function createPopup(vihara) {
        const container = document.createElement('div');

        const title = document.createElement('strong');
        title.textContent = vihara.name ?? 'Nama vihara tidak tersedia';

        const address = document.createElement('p');
        address.textContent = vihara.address ?? '-';

        const location = document.createElement('small');
        location.textContent = [
            vihara.district_name,
            vihara.city_name,
            vihara.province_name,
        ].filter(Boolean).join(', ');

        container.append(title, address, location);

        return container;
    }

    function renderMarkers(viharas) {
        markerGroup.clearLayers();

        viharas.forEach((vihara) => {
            const latitude = Number(vihara.latitude);
            const longitude = Number(vihara.longitude);

            if (
                !Number.isFinite(latitude) ||
                !Number.isFinite(longitude) ||
                latitude < -90 ||
                latitude > 90 ||
                longitude < -180 ||
                longitude > 180
            ) {
                return;
            }

            const marker = L.circleMarker(
                [latitude, longitude],
                {
                    radius: 8,
                    color: '#ffffff',
                    weight: 2,
                    fillColor: '#16a34a',
                    fillOpacity: 1,
                }
            );

            marker.bindPopup(createPopup(vihara));

            markerGroup.addLayer(marker);
        });
    }

    async function loadViharas() {
        if (requestController) {
            requestController.abort();
        }

        requestController = new AbortController();

        const bounds = map.getBounds();

        const params = new URLSearchParams({
            south: bounds.getSouth(),
            west: bounds.getWest(),
            north: bounds.getNorth(),
            east: bounds.getEast(),
            limit: 500,
        });

        setStatus('Memuat vihara...');

        try {
            const response = await fetch(
                `/api/v1/viharas/map?${params.toString()}`,
                {
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                    },
                    credentials: 'same-origin',
                    signal: requestController.signal,
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.json();

            if (result.success !== true) {
                throw new Error('API mengembalikan respons gagal.');
            }

            // Mendukung response berupa array langsung maupun
            // response yang dibungkus pagination.
            const viharas = Array.isArray(result.data)
                ? result.data
                : (result.data?.data ?? []);

            renderMarkers(viharas);

            setStatus(`${viharas.length} vihara ditemukan`);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error('Gagal memuat vihara:', error);

            setStatus('Gagal memuat data vihara. Silakan coba lagi.');
        }
    }

    function scheduleSearch() {
        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(() => {
            loadViharas();
        }, 350);
    }

    // Setiap peta selesai digeser atau di-zoom,
    // cari ulang berdasarkan bounding box terbaru.
    map.on('moveend', scheduleSearch);

    // Lokasi pengguna
    function locateUser() {
        if (!navigator.geolocation) {
            setStatus('Browser tidak mendukung geolocation.');
            return;
        }

        setStatus('Mencari lokasi Anda...');

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                const location = [latitude, longitude];

                if (userMarker) {
                    map.removeLayer(userMarker);
                }

                if (accuracyCircle) {
                    map.removeLayer(accuracyCircle);
                }

                userMarker = L.circleMarker(location, {
                    radius: 9,
                    color: '#ffffff',
                    weight: 3,
                    fillColor: '#2563eb',
                    fillOpacity: 1,
                }).addTo(map);

                userMarker.bindPopup('Lokasi Anda');

                accuracyCircle = L.circle(location, {
                    radius: accuracy,
                    color: '#2563eb',
                    fillColor: '#2563eb',
                    fillOpacity: 0.08,
                    weight: 1,
                }).addTo(map);

                map.setView(location, 14);

                setStatus('Lokasi berhasil ditemukan.');
            },
            (error) => {
                console.warn('Geolocation error:', error);

                const messages = {
                    1: 'Izin lokasi ditolak. Anda tetap dapat menjelajahi peta.',
                    2: 'Lokasi tidak tersedia.',
                    3: 'Waktu pencarian lokasi habis.',
                };

                setStatus(
                    messages[error.code] ??
                    'Tidak dapat memperoleh lokasi Anda.'
                );
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000,
            }
        );
    }

    locateButton?.addEventListener('click', locateUser);

    // Pencarian awal tanpa harus menunggu izin lokasi.
    loadViharas();

    // Minta lokasi setelah peta selesai diinisialisasi.
    locateUser();
}
