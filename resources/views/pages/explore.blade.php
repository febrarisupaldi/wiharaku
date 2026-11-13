@extends('layouts.app')

@section('title', 'Jelajahi Vihara - Wiharaku')

@section('description', 'Cari dan temukan vihara berdasarkan nama, provinsi, dan kota di seluruh Indonesia.')

@section('content')

<section class="bg-emerald-50">
    <div class="mx-auto max-w-7xl px-6 py-14 md:py-20">
        <span class="text-sm font-semibold uppercase tracking-widest text-emerald-700">
            Direktori Vihara
        </span>

        <h1 class="mt-4 text-3xl font-bold tracking-tight text-emerald-950 sm:text-5xl">
            Jelajahi Vihara
        </h1>

        <p class="mt-5 max-w-2xl leading-8 text-gray-600">
            Temukan vihara di berbagai wilayah Indonesia.
            Gunakan pencarian dan filter wilayah untuk menemukan
            vihara yang ingin kamu kunjungi.
        </p>
    </div>
</section>

<section class="bg-white py-12">
    <div class="mx-auto max-w-7xl px-6">

        {{-- Search and Filter --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-7">

            <form id="explore-form">
                <div class="grid gap-5 md:grid-cols-3">

                    {{-- Search --}}
                    <div class="md:col-span-3">
                        <label for="search"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Nama vihara
                        </label>

                        <div class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 focus-within:border-emerald-600 focus-within:ring-2 focus-within:ring-emerald-100">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.8"
                                stroke="currentColor"
                                class="h-5 w-5 text-gray-400">
                                <circle cx="11" cy="11" r="7" />
                                <path stroke-linecap="round" d="m16 16 4 4" />
                            </svg>

                            <input
                                id="search"
                                type="search"
                                value="{{ request('q') }}"
                                placeholder="Contoh: Vihara Dharma..."
                                class="w-full border-0 py-3.5 text-sm outline-none focus:ring-0">
                        </div>
                    </div>

                    {{-- Province --}}
                    <div>
                        <label for="province"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Provinsi
                        </label>

                        <select id="province"
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3.5 text-sm text-gray-700 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                            <option value="">Semua Provinsi</option>
                        </select>
                    </div>

                    {{-- City --}}
                    <div>
                        <label for="city"
                            class="mb-2 block text-sm font-semibold text-gray-700">
                            Kota / Kabupaten
                        </label>

                        <select id="city"
                            disabled
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3.5 text-sm text-gray-500 outline-none disabled:cursor-not-allowed">
                            <option value="">Semua Kota / Kabupaten</option>
                        </select>
                    </div>

                    {{-- Reset --}}
                    <div class="flex items-end">
                        <button type="button"
                            id="reset-filter"
                            class="w-full rounded-xl border border-gray-200 px-5 py-3.5 text-sm font-semibold text-gray-600 transition hover:border-emerald-700 hover:text-emerald-800">
                            Reset Filter
                        </button>
                    </div>

                </div>
            </form>
        </div>

        {{-- Result Heading --}}
        <div class="mt-10 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    Daftar Vihara
                </h2>

                <p id="result-count" class="mt-1 text-sm text-gray-500">
                    Memuat data vihara...
                </p>
            </div>
        </div>

        {{-- Loading --}}
        <div id="loading-state" class="py-20 text-center">
            <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-emerald-100 border-t-emerald-700"></div>

            <p class="mt-4 text-sm text-gray-500">
                Sedang mengambil data vihara...
            </p>
        </div>

        {{-- Error --}}
        <div id="error-state"
            class="hidden mt-8 rounded-xl border border-red-200 bg-red-50 p-6 text-center">
            <p class="font-semibold text-red-800">
                Gagal mengambil data vihara
            </p>

            <p id="error-message" class="mt-2 text-sm text-red-700"></p>

            <button id="retry-button"
                type="button"
                class="mt-4 rounded-lg bg-red-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-800">
                Coba Lagi
            </button>
        </div>

        {{-- Empty --}}
        <div id="empty-state"
            class="hidden py-20 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-800">
                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="h-8 w-8">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="m16 16 4 4" />
                </svg>
            </div>

            <h3 class="mt-5 text-lg font-bold text-gray-900">
                Vihara tidak ditemukan
            </h3>

            <p class="mt-2 text-sm text-gray-500">
                Coba gunakan kata kunci atau filter wilayah lainnya.
            </p>
        </div>

        {{-- Vihara List --}}
        <div id="vihara-list"
            class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        </div>

    </div>
</section>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const API = '/api/v1';

        const searchInput = document.getElementById('search');
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const resetButton = document.getElementById('reset-filter');
        const retryButton = document.getElementById('retry-button');

        const listElement = document.getElementById('vihara-list');
        const countElement = document.getElementById('result-count');
        const loadingElement = document.getElementById('loading-state');
        const errorElement = document.getElementById('error-state');
        const errorMessage = document.getElementById('error-message');
        const emptyElement = document.getElementById('empty-state');

        let searchTimer = null;
        let requestController = null;

        function hideStates() {
            loadingElement.classList.add('hidden');
            errorElement.classList.add('hidden');
            emptyElement.classList.add('hidden');
        }

        function showLoading() {
            hideStates();
            loadingElement.classList.remove('hidden');
            listElement.replaceChildren();
        }

        function showError(message) {
            hideStates();
            errorElement.classList.remove('hidden');
            errorMessage.textContent = message;
        }

        function createElement(tag, className, text = '') {
            const element = document.createElement(tag);
            element.className = className;
            element.textContent = text;

            return element;
        }

        async function fetchData(url, signal = undefined) {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
                signal,
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();

            if (!result.success || !Array.isArray(result.data)) {
                throw new Error('Format respons API tidak sesuai kontrak Wiharaku.');
            }

            return result.data;
        }

        async function loadProvinces() {
            try {
                const provinces = await fetchData(`${API}/regions/provinces`);

                provinceSelect.replaceChildren(
                    new Option('Semua Provinsi', '')
                );

                provinces.forEach(province => {
                    provinceSelect.add(
                        new Option(province.province_name, province.id)
                    );
                });

            } catch (error) {
                console.error('Gagal memuat provinsi:', error);
            }
        }

        async function loadCities(provinceId) {
            citySelect.replaceChildren(
                new Option('Semua Kota / Kabupaten', '')
            );

            citySelect.disabled = !provinceId;

            if (!provinceId) {
                return;
            }

            try {
                const params = new URLSearchParams({
                    province_id: provinceId,
                });

                const cities = await fetchData(
                    `${API}/regions/cities?${params.toString()}`
                );

                cities.forEach(city => {
                    const label =
                        city.city_name;

                    citySelect.add(new Option(label, city.id));
                });

            } catch (error) {
                console.error('Gagal memuat kota:', error);
            }
        }

        function createViharaCard(vihara) {
            const card = createElement(
                'article',
                'overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:-translate-y-1 hover:shadow-lg'
            );

            const top = createElement(
                'div',
                'flex h-36 items-center justify-center bg-gradient-to-br from-emerald-50 to-emerald-100'
            );

            const icon = createElement(
                'span',
                'flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-emerald-800 shadow-sm'
            );

            icon.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke-width="1.5"
                 stroke="currentColor"
                 class="h-8 w-8">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 21s7-4.438 7-11a7 7 0 1 0-14 0c0 6.562 7 11 7 11Z" />
                <circle cx="12" cy="10" r="2.5" />
            </svg>
        `;

            top.append(icon);

            const content = createElement(
                'div',
                'p-5'
            );

            const name = createElement(
                'h3',
                'text-lg font-bold text-gray-900',
                vihara.name || 'Tanpa nama'
            );

            const address = createElement(
                'p',
                'mt-3 text-sm leading-6 text-gray-600',
                vihara.address || 'Alamat belum tersedia'
            );

            const region = [
                vihara.district_name,
                vihara.city_name,
                vihara.province_name,
            ].filter(Boolean).join(', ');

            const regionElement = createElement(
                'p',
                'mt-3 border-t border-gray-100 pt-3 text-xs text-gray-500',
                region || 'Informasi wilayah belum tersedia'
            );

            content.append(name, address, regionElement);

            if (vihara.slug) {
                const detailLink = document.createElement('a');

                detailLink.href = `/vihara/${encodeURIComponent(vihara.slug)}`;
                detailLink.className = 'mt-5 inline-flex items-center gap-2 text-sm font-semibold text-emerald-800 hover:text-emerald-950';
                detailLink.textContent = 'Lihat detail vihara';

                content.append(detailLink);
            }

            card.append(top, content);

            return card;
        }

        async function loadViharas() {
            if (requestController) {
                requestController.abort();
            }

            requestController = new AbortController();

            showLoading();

            const params = new URLSearchParams();

            const search = searchInput.value.trim();
            const provinceId = provinceSelect.value;
            const cityId = citySelect.value;

            if (search) {
                params.set('search', search);
            }

            if (provinceId) {
                params.set('province_id', provinceId);
            }

            if (cityId) {
                params.set('city_id', cityId);
            }

            try {
                const query = params.toString();
                const url = query ?
                    `${API}/viharas?${query}` :
                    `${API}/viharas`;

                const viharas = await fetchData(
                    url,
                    requestController.signal
                );

                hideStates();
                listElement.replaceChildren();

                countElement.textContent =
                    `${viharas.length} vihara ditemukan`;

                if (viharas.length === 0) {
                    emptyElement.classList.remove('hidden');
                    return;
                }

                const cards = viharas.map(createViharaCard);

                listElement.append(...cards);

            } catch (error) {
                if (error.name === 'AbortError') {
                    return;
                }

                console.error('Gagal memuat vihara:', error);

                countElement.textContent = 'Data belum dapat dimuat';

                showError(error.message);
            }
        }

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);

            searchTimer = setTimeout(() => {
                loadViharas();
            }, 400);
        });

        provinceSelect.addEventListener('change', async () => {
            await loadCities(provinceSelect.value);
            loadViharas();
        });

        citySelect.addEventListener('change', () => {
            loadViharas();
        });

        resetButton.addEventListener('click', async () => {
            searchInput.value = '';
            provinceSelect.value = '';

            await loadCities('');
            loadViharas();
        });

        retryButton.addEventListener('click', loadViharas);

        async function initialize() {
            await loadProvinces();
            await loadViharas();
        }

        initialize();
    });
</script>
@endsection