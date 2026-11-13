@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
            <a href="/" class="transition hover:text-green-700">
                Beranda
            </a>

            <span>/</span>

            <a href="/jelajahi" class="transition hover:text-green-700">
                Jelajahi
            </a>

            <span>/</span>

            <span id="breadcrumb-name" class="font-medium text-gray-800">
                Detail Vihara
            </span>
        </nav>

        {{-- Loading --}}
        <div id="vihara-loading"
            class="rounded-2xl border border-gray-200 bg-white py-20 text-center">
            <div class="mx-auto mb-4 h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-green-700"></div>
            <p class="text-sm text-gray-500">Memuat informasi vihara...</p>
        </div>

        {{-- Error --}}
        <div id="vihara-error" class="hidden rounded-2xl border border-gray-200 bg-white px-6 py-20 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M12 9v3m0 4h.01M10.29 3.86l-8.1 14.02A2 2 0 003.92 21h16.16a2 2 0 001.73-3.12l-8.1-14.02a2 2 0 00-3.42 0z" />
                </svg>
            </div>

            <h1 class="text-xl font-bold text-gray-900">
                Vihara tidak ditemukan
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Data vihara tidak tersedia atau belum dipublikasikan.
            </p>

            <a href="/jelajahi"
                class="mt-6 inline-flex items-center rounded-xl bg-green-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-green-800">
                Kembali ke Jelajahi
            </a>
        </div>

        {{-- Content --}}
        <div id="vihara-content" class="hidden space-y-6">

            {{-- Header Vihara --}}
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <div class="h-2 bg-green-700"></div>

                <div class="p-6 sm:p-8">
                    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-start">

                        <div class="flex gap-4">
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-green-50 text-green-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 10l9-7 9 7M5 10v10h14V10M9 20v-6h6v6" />
                                </svg>
                            </div>

                            <div>
                                <h1 id="vihara-name"
                                    class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                                </h1>

                                <p id="vihara-address"
                                    class="mt-2 text-sm leading-6 text-gray-500">
                                </p>
                            </div>
                        </div>

                        <div id="vihara-verification"
                            class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full bg-green-50 px-3 py-2 text-xs font-semibold text-green-700">
                            <span class="h-2 w-2 rounded-full bg-green-600"></span>
                            Terverifikasi
                        </div>
                    </div>
                </div>
            </section>


            {{-- Vihara Photo Gallery --}}
            <section id="vihara-gallery"
                class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">

                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">
                            Galeri Foto
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Dokumentasi dan suasana vihara
                        </p>
                    </div>

                    <span id="gallery-photo-count"
                        class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                        0 Foto
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-4">

                    {{-- Foto utama --}}
                    <button
                        type="button"
                        id="gallery-main-button"
                        class="group relative aspect-[16/10] overflow-hidden rounded-xl bg-gray-100 text-left md:col-span-3 md:aspect-[16/9]"
                        aria-label="Lihat foto utama">
                        <img
                            id="gallery-main-image"
                            src=""
                            alt=""
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105">

                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>

                        <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-3 p-4">
                            <p id="gallery-main-caption"
                                class="text-sm font-medium text-white">
                            </p>

                            <span class="shrink-0 rounded-lg bg-black/40 px-3 py-2 text-xs font-medium text-white backdrop-blur">
                                Lihat foto
                            </span>
                        </div>
                    </button>

                    {{-- Thumbnail --}}
                    <div id="gallery-thumbnails"
                        class="grid grid-cols-3 gap-3 md:grid-cols-1">
                    </div>
                </div>
            </section>

            {{-- Empty Gallery --}}
            <section id="vihara-gallery-empty"
                class="hidden rounded-2xl border border-gray-200 bg-white p-8 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M3 7h18M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm2 14l4-4 3 3 2-2 3 3" />
                    </svg>
                </div>

                <h3 class="mt-4 font-semibold text-gray-800">
                    Belum ada foto
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Foto Vihara ini belum tersedia.
                </p>
            </section>

            {{-- Lightbox --}}
            <div id="gallery-lightbox"
                class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/95 p-4">

                <button
                    type="button"
                    id="gallery-close"
                    class="absolute right-5 top-5 z-10 rounded-full bg-white/10 p-3 text-white transition hover:bg-white/20"
                    aria-label="Tutup gallery">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <button
                    type="button"
                    id="gallery-previous"
                    class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition hover:bg-white/20"
                    aria-label="Foto sebelumnya">
                    &#10094;
                </button>

                <div class="flex max-h-full w-full max-w-5xl flex-col items-center">
                    <img
                        id="gallery-lightbox-image"
                        src=""
                        alt=""
                        class="max-h-[80vh] max-w-full rounded-lg object-contain">

                    <p id="gallery-lightbox-caption"
                        class="mt-4 text-center text-sm text-white">
                    </p>

                    <p id="gallery-lightbox-counter"
                        class="mt-2 text-xs text-gray-400">
                    </p>
                </div>

                <button
                    type="button"
                    id="gallery-next"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition hover:bg-white/20"
                    aria-label="Foto berikutnya">
                    &#10095;
                </button>
            </div>

            {{-- Main Content --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- Left Content --}}
                <div class="space-y-6 lg:col-span-2">

                    {{-- Description --}}
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                        d="M12 6.25c-2.5-1.5-5-1.5-8-1v13c3-.5 5.5-.5 8 1m0-13c2.5-1.5 5-1.5 8-1v13c-3-.5-5.5-.5-8 1m0-13v13" />
                                </svg>
                            </div>

                            <h2 class="text-lg font-bold text-gray-900">
                                Tentang Vihara
                            </h2>
                        </div>

                        <p id="vihara-description"
                            class="text-sm leading-7 whitespace-pre-line text-gray-600">
                        </p>
                    </section>

                    {{-- Address --}}
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                        d="M12 21s7-5.2 7-12a7 7 0 10-14 0c0 6.8 7 12 7 12z" />
                                    <circle cx="12" cy="9" r="2.5" />
                                </svg>
                            </div>

                            <h2 class="text-lg font-bold text-gray-900">
                                Informasi Alamat
                            </h2>
                        </div>

                        <div class="space-y-5">
                            <div class="mb-2">
                                <p class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                    Alamat Lengkap
                                </p>
                                <p id="detail-address" class="mt-1 text-sm font-medium text-gray-800"></p>
                            </div>

                            <div class="grid grid-cols-1 gap-5 border-t border-gray-100 pt-5 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                        Provinsi
                                    </p>
                                    <p id="vihara-province" class="mt-1 text-sm font-medium text-gray-800"></p>
                                </div>

                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                        Kota / Kabupaten
                                    </p>
                                    <p id="vihara-city" class="mt-1 text-sm font-medium text-gray-800"></p>
                                </div>

                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                        Kecamatan
                                    </p>
                                    <p id="vihara-district" class="mt-1 text-sm font-medium text-gray-800"></p>
                                </div>

                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                        Kelurahan
                                    </p>
                                    <p id="vihara-subdistrict" class="mt-1 text-sm font-medium text-gray-800"></p>
                                </div>

                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                        Kode Pos
                                    </p>
                                    <p id="vihara-postal-code" class="mt-1 text-sm font-medium text-gray-800"></p>
                                </div>
                            </div>
                        </div>
                    </section>


                    {{-- Map --}}
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                        d="M9 18l-6 3V6l6-3m0 15l6 3m-6-3V3m6 18l6-3V3l-6 3m0 15V6m0 0L9 3" />
                                </svg>
                            </div>

                            <h2 class="text-lg font-bold text-gray-900">
                                Lokasi Vihara
                            </h2>
                        </div>

                        <div
                            id="vihara-detail-map"
                            class="relative z-0 isolate h-[350px] w-full overflow-hidden rounded-xl bg-gray-100"
                            data-map-state="loading">
                            <div class="flex h-full items-center justify-center text-sm text-gray-500">
                                Memuat peta...
                            </div>
                        </div>

                        <a
                            id="vihara-google-maps"
                            href="#"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-4 hidden text-sm font-semibold text-green-700 hover:text-green-800 hover:underline"
                            hidden>
                            Buka di Google Maps
                        </a>
                    </section>

                </div>

                {{-- Right Sidebar --}}
                <aside class="space-y-6">

                    {{-- Contact --}}
                    <section class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h2 class="text-base font-bold text-gray-900">
                            Informasi Kontak
                        </h2>

                        <div class="mt-5">
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                Website Resmi
                            </p>

                            <a id="vihara-website"
                                href="#"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-2 hidden break-all text-sm font-medium text-green-700 hover:underline">
                            </a>

                            <p id="vihara-no-website" class="mt-2 text-sm text-gray-500">
                                Belum tersedia
                            </p>
                        </div>
                    </section>

                    {{-- Coordinates --}}
                    <section class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h2 class="text-base font-bold text-gray-900">
                            Koordinat Lokasi
                        </h2>

                        <div class="mt-5 space-y-4">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                    Latitude
                                </p>
                                <p id="vihara-latitude" class="mt-1 text-sm font-semibold text-gray-800"></p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                    Longitude
                                </p>
                                <p id="vihara-longitude" class="mt-1 text-sm font-semibold text-gray-800"></p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-gray-200 bg-white p-6">
                        <h2 class="text-base font-bold text-gray-900">
                            Mazhab
                        </h2>

                        <div class="mt-5 space-y-4">
                            <p id="vihara-tradition" class="mt-1 text-sm font-semibold text-gray-800"></p>
                        </div>
                    </section>

                    {{-- Back Button --}}
                    <a href="/jelajahi"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-green-700 hover:text-green-700">
                        Kembali ke Jelajahi
                    </a>
                </aside>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const API = '/api/v1';
        const slug = @json($slug);

        const loading = document.getElementById('vihara-loading');
        const error = document.getElementById('vihara-error');
        const content = document.getElementById('vihara-content');

        const setText = (id, value) => {
            document.getElementById(id).textContent = value || '-';
        };


        function renderViharaGallery(photos) {
            const gallery = document.getElementById('vihara-gallery');
            const emptyGallery = document.getElementById('vihara-gallery-empty');

            const mainImage = document.getElementById('gallery-main-image');
            const mainButton = document.getElementById('gallery-main-button');
            const mainCaption = document.getElementById('gallery-main-caption');
            const photoCount = document.getElementById('gallery-photo-count');
            const thumbnails = document.getElementById('gallery-thumbnails');

            const lightbox = document.getElementById('gallery-lightbox');
            const lightboxImage = document.getElementById('gallery-lightbox-image');
            const lightboxCaption = document.getElementById('gallery-lightbox-caption');
            const lightboxCounter = document.getElementById('gallery-lightbox-counter');

            const closeButton = document.getElementById('gallery-close');
            const previousButton = document.getElementById('gallery-previous');
            const nextButton = document.getElementById('gallery-next');

            // Hanya gunakan foto dengan URL HTTP/HTTPS yang valid.
            const validPhotos = (Array.isArray(photos) ? photos : [])
                .filter(photo => {
                    if (!photo.photo_url) return false;

                    try {
                        const url = new URL(photo.photo_url, window.location.origin);
                        return ['http:', 'https:'].includes(url.protocol);
                    } catch {
                        return false;
                    }
                })
                .sort((a, b) => {
                    return Number(b.is_primary) - Number(a.is_primary) ||
                        Number(a.sort_order ?? 0) - Number(b.sort_order ?? 0);
                });

            if (validPhotos.length === 0) {
                gallery.classList.add('hidden');
                emptyGallery.classList.remove('hidden');
                return;
            }

            emptyGallery.classList.add('hidden');
            gallery.classList.remove('hidden');

            let currentIndex = 0;

            photoCount.textContent = `${validPhotos.length} Foto`;

            function updateMainImage() {
                const photo = validPhotos[currentIndex];

                mainImage.src = photo.photo_url;
                mainImage.alt = photo.caption || 'Foto Vihara';
                mainCaption.textContent = photo.caption || '';

                mainImage.onerror = () => {
                    mainImage.alt = 'Foto tidak dapat dimuat';
                    mainImage.removeAttribute('src');
                };
            }

            function updateLightbox() {
                const photo = validPhotos[currentIndex];

                lightboxImage.src = photo.photo_url;
                lightboxImage.alt = photo.caption || 'Foto Vihara';
                lightboxCaption.textContent = photo.caption || '';
                lightboxCounter.textContent =
                    `${currentIndex + 1} / ${validPhotos.length}`;

                previousButton.disabled = validPhotos.length <= 1;
                nextButton.disabled = validPhotos.length <= 1;
            }

            function selectPhoto(index) {
                currentIndex = index;
                updateMainImage();
                updateThumbnails();
            }

            function updateThumbnails() {
                thumbnails.replaceChildren();

                // Tampilkan maksimal tiga thumbnail.
                // Foto lainnya tetap bisa dilihat melalui lightbox.
                const visiblePhotos = validPhotos.slice(1, 4);

                visiblePhotos.forEach((photo, index) => {
                    const photoIndex = index + 1;
                    const isLast = index === visiblePhotos.length - 1;
                    const remaining = validPhotos.length - 4;

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className =
                        'group relative aspect-[16/9] overflow-hidden rounded-xl bg-gray-100';

                    button.setAttribute(
                        'aria-label',
                        `Lihat foto ${photoIndex + 1}`
                    );

                    const image = document.createElement('img');
                    image.src = photo.photo_url;
                    image.alt = photo.caption || 'Foto Vihara';
                    image.loading = 'lazy';
                    image.className =
                        'h-full w-full object-cover transition duration-300 group-hover:scale-105';

                    button.appendChild(image);

                    // Indikator foto yang sedang dipilih.
                    if (currentIndex === photoIndex) {
                        button.classList.add('ring-2', 'ring-green-600');
                    }

                    // Indikator jumlah foto yang masih tersedia.
                    if (isLast && remaining > 0) {
                        const overlay = document.createElement('span');
                        overlay.className =
                            'absolute inset-0 flex items-center justify-center bg-black/60 text-lg font-bold text-white';

                        overlay.textContent = `+${remaining}`;

                        button.appendChild(overlay);
                    }

                    button.addEventListener('click', () => {
                        selectPhoto(photoIndex);
                        openLightbox();
                    });

                    thumbnails.appendChild(button);
                });
            }

            function openLightbox() {
                updateLightbox();

                lightbox.classList.remove('hidden');
                lightbox.classList.add('flex');

                document.body.style.overflow = 'hidden';
                document.addEventListener('keydown', handleKeyboard);
            }

            function closeLightbox() {
                lightbox.classList.add('hidden');
                lightbox.classList.remove('flex');

                document.body.style.overflow = '';
                document.removeEventListener('keydown', handleKeyboard);
            }

            function showPrevious() {
                currentIndex =
                    (currentIndex - 1 + validPhotos.length) % validPhotos.length;

                updateMainImage();
                updateThumbnails();
                updateLightbox();
            }

            function showNext() {
                currentIndex = (currentIndex + 1) % validPhotos.length;

                updateMainImage();
                updateThumbnails();
                updateLightbox();
            }

            function handleKeyboard(event) {
                if (event.key === 'Escape') closeLightbox();
                if (event.key === 'ArrowLeft') showPrevious();
                if (event.key === 'ArrowRight') showNext();
            }

            mainButton.addEventListener('click', openLightbox);
            closeButton.addEventListener('click', closeLightbox);
            previousButton.addEventListener('click', showPrevious);
            nextButton.addEventListener('click', showNext);

            lightbox.addEventListener('click', event => {
                if (event.target === lightbox) {
                    closeLightbox();
                }
            });

            updateMainImage();
            updateThumbnails();
        }


        try {
            const response = await fetch(
                `${API}/viharas/${encodeURIComponent(slug)}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                }
            );

            if (!response.ok) {
                throw new Error('Vihara tidak ditemukan.');
            }

            const result = await response.json();

            if (!result.success || !result.data) {
                throw new Error('Data vihara tidak tersedia.');
            }

            const vihara = result.data;
            // Render gallery foto yang sudah approved.
            renderViharaGallery(vihara.photos);

            setText('vihara-name', vihara.name);
            setText('breadcrumb-name', vihara.name);
            setText('vihara-address', vihara.address);
            setText('detail-address', vihara.address);
            setText('vihara-description', vihara.description);

            setText('vihara-name', vihara.name);
            setText('breadcrumb-name', vihara.name);
            setText('vihara-address', vihara.address);
            setText('detail-address', vihara.address);
            setText('vihara-description', vihara.description);

            setText('vihara-province', vihara.province_name);
            setText('vihara-city', vihara.city_name);
            setText('vihara-district', vihara.district_name);
            setText('vihara-subdistrict', vihara.subdistrict_name);
            setText('vihara-postal-code', vihara.postal_code);

            setText('vihara-latitude', vihara.latitude);
            setText('vihara-longitude', vihara.longitude);

            setText('vihara-tradition', vihara.traditions.name);

            const website = document.getElementById('vihara-website');
            const noWebsite = document.getElementById('vihara-no-website');

            if (vihara.website) {
                const websiteUrl = new URL(vihara.website, window.location.origin);

                if (['http:', 'https:'].includes(websiteUrl.protocol)) {
                    website.href = websiteUrl.href;
                    website.textContent = vihara.website;
                    website.classList.remove('hidden');
                    noWebsite.classList.add('hidden');
                }
            }

            document.title = `${vihara.name} - Wiharaku`;

            loading.classList.add('hidden');
            content.classList.remove('hidden');

            // Inisialisasi peta setelah container terlihat.
            window.initViharaDetailMap?.(vihara);

        } catch (exception) {
            console.error(exception);

            loading.classList.add('hidden');
            error.classList.remove('hidden');
        }
    });
</script>
@endsection