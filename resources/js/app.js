import './bootstrap';
import './pages/vihara-map';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Agar dapat digunakan oleh script halaman Blade.
window.L = L;

document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('vihara-map');

    // Hanya dijalankan pada halaman peta.
    if (!mapElement) {
        return;
    }

    const API = '/api/v1/viharas/map';

    const map = L.map('vihara-map', {
        center: [-2.5, 118],
        zoom: 5,
        minZoom: 4,
        maxZoom: 19,
        scrollWheelZoom: true,
    });

    // OpenStreetMap tiles.
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    const markers = L.layerGroup().addTo(map);

    const loadingElement = document.getElementById('map-loading');
    const errorElement = document.getElementById('map-error');
    const countElement = document.getElementById('map-count');
    const searchInput = document.getElementById('map-search');
    const searchForm = document.getElementById('map-search-form');
    const retryButton = document.getElementById('map-retry');

    let requestController = null;
    let searchTimer = null;
    let initialized = false;

    function showLoading() {
        loadingElement.classList.remove('hidden');
        errorElement.classList.add('hidden');
    }

    function hideLoading() {
        loadingElement.classList.add('hidden');
    }

    function showError(message) {
        hideLoading();
        errorElement.classList.remove('hidden');

        document.getElementById('map-error-message').textContent = message;
    }

    function createPopup(vihara) {
        const container = document.createElement('div');

        const title = document.createElement('h3');
        title.className = 'mb-2 text-base font-bold text-gray-900';
        title.textContent = vihara.name || 'Tanpa nama';

        const address = document.createElement('p');
        address.className = 'text-sm text-gray-600';
        address.textContent = vihara.address || 'Alamat belum tersedia';

        const region = document.createElement('p');
        region.className = 'mt-2 text-xs text-gray-500';

        region.textContent = [
            vihara.district_name,
            vihara.city_name,
            vihara.province_name,
        ].filter(Boolean).join(', ');

        container.append(title, address, region);

        if (vihara.slug) {
            const detail = document.createElement('a');

            detail.href = `/vihara/${encodeURIComponent(vihara.slug)}`;
            detail.className = 'mt-3 inline-block text-sm font-semibold text-emerald-800';
            detail.textContent = 'Lihat detail vihara';

            container.append(detail);
        }

        return container;
    }

    function createMarker(vihara) {
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
            return null;
        }

        return L.circleMarker([latitude, longitude], {
            radius: 8,
            color: '#ffffff',
            weight: 2,
            fillColor: '#047857',
            fillOpacity: 0.95,
        }).bindPopup(createPopup(vihara));
    }

    async function loadViharas() {
        if (requestController) {
            requestController.abort();
        }

        requestController = new AbortController();

        showLoading();

        const bounds = map.getBounds();

        const params = new URLSearchParams({
            south: bounds.getSouth().toFixed(6),
            west: bounds.getWest().toFixed(6),
            north: bounds.getNorth().toFixed(6),
            east: bounds.getEast().toFixed(6),
            limit: '500',
        });

        const keyword = searchInput.value.trim();

        if (keyword) {
            params.set('keyword', keyword);
        }

        try {
            const response = await fetch(`${API}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
                signal: requestController.signal,
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();

            if (!result.success || !Array.isArray(result.data)) {
                throw new Error('Format respons API peta tidak sesuai.');
            }

            markers.clearLayers();

            let markerCount = 0;

            result.data.forEach(vihara => {
                const marker = createMarker(vihara);

                if (marker) {
                    marker.addTo(markers);
                    markerCount++;
                }
            });

            countElement.textContent =
                `${markerCount} vihara ditemukan pada area peta ini`;

            hideLoading();

        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error('Gagal mengambil data peta:', error);

            showError(error.message);
        }
    }

    // Ketika pengunjung menggeser atau memperbesar peta.
    map.on('moveend', () => {
        if (initialized) {
            loadViharas();
        }
    });

    // Pencarian nama vihara.
    searchForm.addEventListener('submit', event => {
        event.preventDefault();
        loadViharas();
    });

    // Tombol pencarian juga bisa dipicu melalui input.
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            loadViharas();
        }, 500);
    });

    retryButton.addEventListener('click', loadViharas);

    // Ambil data setelah peta siap.
    map.whenReady(() => {
        initialized = true;
        loadViharas();
    });
});
