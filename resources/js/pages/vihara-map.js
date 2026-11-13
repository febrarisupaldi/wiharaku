import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

const mapElement = document.getElementById('vihara-map');

// Modul ini hanya berjalan pada halaman yang memiliki elemen peta.
if (mapElement && mapElement.dataset.initialized !== 'true') {
    mapElement.dataset.initialized = 'true';
    initializeViharaMap();
}

function initializeViharaMap() {
    const API_URL = '/api/v1/viharas/map';

    const searchForm = document.getElementById('map-search-form');
    const searchInput = document.getElementById('map-search');
    const countElement = document.getElementById('map-count');
    const loadingElement = document.getElementById('map-loading');
    const errorElement = document.getElementById('map-error');
    const errorMessageElement = document.getElementById('map-error-message');
    const retryButton = document.getElementById('map-retry');

    if (!searchForm || !searchInput || !countElement) {
        console.error('Elemen pencarian atau informasi peta tidak ditemukan.');
        return;
    }

    const map = L.map(mapElement, {
        zoomControl: false,
        preferCanvas: true,
        minZoom: 4,
        maxZoom: 19,
    }).setView([-2.5, 118], 5);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    const markerGroup = L.markerClusterGroup({
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
    }).addTo(map);

    let requestController = null;
    let requestId = 0;
    let searchTimer = null;
    let isProgrammaticMove = false;
    let userMarker = null;
    let accuracyCircle = null;

    // Kata umum ini tidak ikut dikirim sebagai kata kunci pencarian.
    // Tambahkan kata lain ke daftar ini jika memang diperlukan.
    const ignoredSearchWords = new Set(['vihara', 'cetiya', 'arama']);

    function normalizeKeyword(value) {
        return value
            .trim()
            .split(/\s+/)
            .filter(word => !ignoredSearchWords.has(
                word.toLocaleLowerCase('id-ID').replace(/^[^\p{L}\p{N}]+|[^\p{L}\p{N}]+$/gu, '')
            ))
            .join(' ')
            .trim();
    }

    function showLoading() {
        loadingElement?.classList.remove('hidden');
        errorElement?.classList.add('hidden');
    }

    function hideLoading() {
        loadingElement?.classList.add('hidden');
    }

    function showError(message) {
        hideLoading();
        countElement.textContent = 'Data vihara gagal dimuat';
        errorElement?.classList.remove('hidden');

        if (errorMessageElement) {
            errorMessageElement.textContent = message;
        }
    }

    function createPopup(vihara) {
        const container = document.createElement('div');

        const title = document.createElement('strong');
        title.textContent = vihara.name ?? 'Nama vihara tidak tersedia';

        const address = document.createElement('p');
        address.className = 'my-1';
        address.textContent = vihara.address ?? 'Alamat belum tersedia';

        const region = document.createElement('small');
        region.textContent = [
            vihara.district_name,
            vihara.city_name,
            vihara.province_name,
        ].filter(Boolean).join(', ');
        region.innerHTML += '<br>';

        container.append(title, address, region);

        if (vihara.slug) {
            const detailLink = document.createElement('a');
            detailLink.href = `/vihara/${encodeURIComponent(vihara.slug)}`;
            detailLink.className = 'mt-2 inline-block font-semibold text-emerald-800';
            detailLink.textContent = 'Lihat detail vihara';
            container.append(detailLink);
        }

        return container;
    }

    function createMarker(vihara) {
        const latitude = Number(vihara.latitude);
        const longitude = Number(vihara.longitude);

        if (
            !Number.isFinite(latitude) ||
            !Number.isFinite(longitude) ||
            latitude < -90 || latitude > 90 ||
            longitude < -180 || longitude > 180
        ) {
            return null;
        }

        const marker = L.circleMarker([latitude, longitude], {
            radius: 8,
            color: '#ffffff',
            weight: 2,
            fillColor: '#047857',
            fillOpacity: 0.95,
        }).bindPopup(createPopup(vihara));

        return { marker, latitude, longitude };
    }

    function renderMarkers(viharas) {
        markerGroup.clearLayers();
        const coordinates = [];

        viharas.forEach((vihara) => {
            const markerData = createMarker(vihara);
            if (!markerData) return;

            markerGroup.addLayer(markerData.marker);
            coordinates.push({
                latitude: markerData.latitude,
                longitude: markerData.longitude,
            });
        });

        return coordinates;
    }

    // Gerakan yang dipicu hasil pencarian tidak boleh dianggap sebagai
    // gerakan manual pengguna yang memicu pencarian bounding box.
    function focusMapOnResults(coordinates) {
        if (coordinates.length === 0) return;

        if (coordinates.length === 1) {
            const target = L.latLng(
                coordinates[0].latitude,
                coordinates[0].longitude
            );

            const alreadyFocused =
                map.getCenter().distanceTo(target) < 5 && map.getZoom() === 16;

            if (alreadyFocused) return;

            isProgrammaticMove = true;
            map.setView(target, 16, { animate: false });
            return;
        }

        const bounds = L.latLngBounds(
            coordinates.map(({ latitude, longitude }) => [latitude, longitude])
        );

        if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
            const target = bounds.getCenter();
            if (map.getCenter().distanceTo(target) < 5 && map.getZoom() === 15) return;

            isProgrammaticMove = true;
            map.setView(target, 15, { animate: false });
            return;
        }

        const targetZoom = Math.min(map.getBoundsZoom(bounds, false, L.point(40, 40)), 15);
        const alreadyFocused =
            map.getCenter().distanceTo(bounds.getCenter()) < 5 &&
            map.getZoom() === targetZoom;

        if (alreadyFocused) return;

        isProgrammaticMove = true;
        map.fitBounds(bounds, {
            padding: [40, 40],
            maxZoom: 15,
            animate: false,
        });
    }

    async function loadViharas() {
        const currentRequestId = ++requestId;

        requestController?.abort();
        const controller = new AbortController();
        requestController = controller;

        showLoading();

        const rawKeyword = searchInput.value.trim();
        const keyword = normalizeKeyword(rawKeyword);

        // Jangan mengirim pencarian kosong jika input hanya berisi kata umum,
        // misalnya "vihara" atau "cetiya". Marker dan posisi peta tetap.
        if (rawKeyword && !keyword) {
            requestController = null;
            hideLoading();
            errorElement?.classList.add('hidden');
            countElement.textContent = 'Tambahkan nama khusus vihara setelah kata "vihara" atau "cetiya".';
            return;
        }

        const params = new URLSearchParams({ limit: '500' });

        if (keyword) {
            // Pencarian keyword dilakukan secara global tanpa bounding box.
            params.set('keyword', keyword);
        } else {
            // Tanpa keyword, data mengikuti area peta saat ini.
            const bounds = map.getBounds();
            params.set('south', bounds.getSouth().toFixed(6));
            params.set('west', bounds.getWest().toFixed(6));
            params.set('north', bounds.getNorth().toFixed(6));
            params.set('east', bounds.getEast().toFixed(6));
        }

        try {
            const response = await fetch(`${API_URL}?${params.toString()}`, {
                method: 'GET',
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.json();

            if (result.success !== true || !Array.isArray(result.data)) {
                throw new Error('Format respons API tidak sesuai.');
            }

            // Abaikan respons lama jika pengguna sudah mengirim pencarian baru.
            if (currentRequestId !== requestId) return;

            let coordinates = [];

            // Saat pencarian global tidak menemukan hasil, pertahankan marker
            // sebelumnya dan jangan mengubah posisi peta.
            if (!(keyword && result.data.length === 0)) {
                coordinates = renderMarkers(result.data);
            }

            const markerCount = keyword && result.data.length === 0
                ? 0
                : coordinates.length;

            if (keyword) {
                countElement.textContent = markerCount > 0
                    ? `${markerCount} vihara ditemukan untuk "${keyword}"`
                    : `Vihara "${keyword}" tidak ditemukan`;

                // Jika kosong, posisi peta sengaja tidak diubah.
                if (markerCount > 0) {
                    focusMapOnResults(coordinates);
                }
            } else {
                countElement.textContent = `${markerCount} vihara ditemukan pada area peta ini`;
            }
        } catch (error) {
            if (error.name === 'AbortError') return;
            if (currentRequestId !== requestId) return;

            console.error('Gagal mengambil data peta:', error);
            showError(error.message || 'Terjadi kesalahan saat memuat data.');
        } finally {
            if (currentRequestId === requestId) {
                hideLoading();
                requestController = null;
            }
        }
    }

    function initializeMapByUserLocation() {
        if (!navigator.geolocation) {
            countElement.textContent =
                'Browser tidak mendukung fitur lokasi. Silakan geser peta secara manual.';
            return;
        }

        countElement.textContent = 'Mencari lokasi Anda...';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                const location = [latitude, longitude];

                // Marker lokasi pengguna.
                userMarker = L.circleMarker(location, {
                    radius: 9,
                    color: '#ffffff',
                    weight: 3,
                    fillColor: '#2563eb',
                    fillOpacity: 1,
                }).addTo(map);

                userMarker.bindPopup('Lokasi Anda');

                // Lingkaran estimasi akurasi lokasi.
                accuracyCircle = L.circle(location, {
                    radius: accuracy,
                    color: '#2563eb',
                    fillColor: '#2563eb',
                    fillOpacity: 0.08,
                    weight: 1,
                }).addTo(map);

                // Pindahkan peta ke lokasi pengguna.
                isProgrammaticMove = true;

                map.once('moveend', () => {
                    isProgrammaticMove = false;

                    // Muat vihara berdasarkan bounding box lokasi pengguna.
                    loadViharas();
                });

                map.setView(location, 13, {
                    animate: false,
                });
            },
            (error) => {
                console.warn('Geolocation error:', error);

                const messages = {
                    1: 'Izin lokasi ditolak. Silakan geser peta untuk mencari vihara.',
                    2: 'Lokasi tidak tersedia. Silakan geser peta untuk mencari vihara.',
                    3: 'Waktu pencarian lokasi habis. Silakan coba lagi.',
                };

                countElement.textContent =
                    messages[error.code] ??
                    'Tidak dapat memperoleh lokasi Anda.';
            },
            {
                enableHighAccuracy: false,
                timeout: 10000,
                maximumAge: 60000,
            }
        );
    }

    function scheduleSearch() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadViharas(), 350);
    }

    map.on('movestart', () => {
        if (!isProgrammaticMove && searchInput.value.trim()) {
            searchInput.value = '';
        }
    });

    map.on('moveend', () => {
        if (isProgrammaticMove) {
            isProgrammaticMove = false;
            return;
        }

        // Peta digeser/zoom manual: kembali ke pencarian berdasarkan area.
        scheduleSearch();
    });

    searchForm.addEventListener('submit', (event) => {
        event.preventDefault();
        clearTimeout(searchTimer);
        loadViharas();
    });

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadViharas(), 500);
    });

    retryButton?.addEventListener('click', () => loadViharas());

    // Saat pertama dibuka, tampilkan peta Indonesia tanpa meminta data vihara.
    // Data baru dimuat setelah pengguna mencari atau menggeser/zoom peta.
    map.whenReady(() => {
        map.invalidateSize();
        initializeMapByUserLocation();
    });
}


let viharaDetailMap = null;

export function initViharaDetailMap(vihara) {
    const mapElement = document.getElementById('vihara-detail-map');

    if (!mapElement) {
        return;
    }

    const latitude = Number(vihara.latitude);
    const longitude = Number(vihara.longitude);

    // Pastikan koordinat tersedia dan valid.
    if (
        vihara.latitude === null ||
        vihara.latitude === undefined ||
        vihara.longitude === null ||
        vihara.longitude === undefined ||
        !Number.isFinite(latitude) ||
        !Number.isFinite(longitude) ||
        latitude < -90 ||
        latitude > 90 ||
        longitude < -180 ||
        longitude > 180
    ) {
        mapElement.dataset.mapState = 'unavailable';
        mapElement.textContent = 'Lokasi Vihara belum tersedia.';
        return;
    }

    // Hindari inisialisasi ulang pada instance sebelumnya.
    if (viharaDetailMap) {
        viharaDetailMap.remove();
        viharaDetailMap = null;
    }

    mapElement.replaceChildren();
    mapElement.dataset.mapState = 'ready';

    viharaDetailMap = L.map(mapElement, {
        scrollWheelZoom: false,
    }).setView([latitude, longitude], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(viharaDetailMap);

    const marker = L.circleMarker([latitude, longitude], {
        radius: 9,
        color: '#ffffff',
        weight: 3,
        fillColor: '#2563eb',
        fillOpacity: 1,
    }).addTo(viharaDetailMap);

    // Gunakan textContent agar nama dan alamat tidak
    // diperlakukan sebagai HTML dari API.
    const popupContent = document.createElement('div');

    const nameElement = document.createElement('strong');
    nameElement.textContent = vihara.name ?? 'Vihara';

    const addressElement = document.createElement('p');
    addressElement.textContent = vihara.address ?? '';
    addressElement.className = 'mt-1 text-sm';

    popupContent.append(nameElement, addressElement);
    marker.bindPopup(popupContent);

    // Link navigasi Google Maps.
    const googleMapsLink = document.getElementById(
        'vihara-google-maps'
    );

    if (googleMapsLink) {
        googleMapsLink.href =
            `https://www.google.com/maps?q=${latitude},${longitude}`;

        googleMapsLink.hidden = false;
        googleMapsLink.classList.remove('hidden');
    }

    // Memastikan ukuran peta sesuai dengan container.
    requestAnimationFrame(() => {
        viharaDetailMap?.invalidateSize();
    });
}

// Agar dapat dipanggil dari script halaman detail.
window.initViharaDetailMap = initViharaDetailMap;