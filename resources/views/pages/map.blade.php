@extends('layouts.app')

@section('title', 'Peta Vihara - Wiharaku')

@section('description', 'Jelajahi persebaran vihara di Indonesia melalui peta interaktif Wiharaku.')

@section('content')

{{-- PAGE HEADER --}}
<section class="bg-emerald-50">
    <div class="mx-auto max-w-7xl px-6 py-12 md:py-16">

        <span class="text-sm font-semibold uppercase tracking-widest text-emerald-700">
            Peta Lokasi
        </span>

        <h1 class="mt-4 text-3xl font-bold tracking-tight text-emerald-950 sm:text-5xl">
            Peta Persebaran Vihara
        </h1>

        <p class="mt-5 max-w-2xl leading-8 text-gray-600">
            Jelajahi lokasi vihara di seluruh Indonesia.
            Geser dan perbesar peta untuk menemukan vihara
            di wilayah yang ingin kamu kunjungi.
        </p>

    </div>
</section>


{{-- MAP SECTION --}}
<section class="bg-white py-10">
    <div class="mx-auto max-w-7xl px-6">

        {{-- Search --}}
        <form id="map-search-form"
            class="mb-5 flex flex-col gap-3 sm:flex-row">

            <div class="flex flex-1 items-center gap-3 rounded-xl border border-gray-200 px-4 focus-within:border-emerald-600 focus-within:ring-2 focus-within:ring-emerald-100">

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
                    id="map-search"
                    type="search"
                    placeholder="Cari nama vihara..."
                    class="w-full border-0 py-3.5 text-sm outline-none focus:ring-0">

            </div>

            <button type="submit"
                class="rounded-xl bg-emerald-800 px-8 py-3.5 text-sm font-semibold text-white transition hover:bg-emerald-900">

                Cari Vihara

            </button>

        </form>


        {{-- Map Information --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

            <div>
                <h2 class="font-bold text-gray-900">
                    Jelajahi Peta
                </h2>

                <p id="map-count" class="mt-1 text-sm text-gray-500">
                    Menunggu data vihara...
                </p>
            </div>

            <div class="flex items-center gap-2 text-xs text-gray-500">
                <span class="h-3 w-3 rounded-full bg-emerald-700"></span>
                Lokasi Vihara
            </div>

        </div>


        {{-- Map Loading --}}
        <div id="map-loading"
            class="mb-3 flex items-center gap-3 rounded-xl bg-emerald-50 px-4 py-3">

            <div class="h-5 w-5 animate-spin rounded-full border-2 border-emerald-200 border-t-emerald-700"></div>

            <p class="text-sm text-emerald-900">
                Memuat lokasi vihara...
            </p>

        </div>


        {{-- Map Error --}}
        <div id="map-error"
            class="mb-3 hidden rounded-xl border border-red-200 bg-red-50 p-4">

            <p class="font-semibold text-red-800">
                Gagal memuat lokasi vihara
            </p>

            <p id="map-error-message"
                class="mt-1 text-sm text-red-700">
            </p>

            <button id="map-retry"
                type="button"
                class="mt-3 text-sm font-semibold text-red-800 underline">
                Coba lagi
            </button>

        </div>


        {{-- Leaflet Map --}}
        <div id="vihara-map"
            class="z-0 h-[500px] w-full overflow-hidden rounded-2xl border border-gray-200 shadow-sm md:h-[650px]">
        </div>


        {{-- Map Guide --}}
        <div class="mt-5 grid gap-4 sm:grid-cols-3">

            <div class="rounded-xl bg-gray-50 p-5">
                <h3 class="font-semibold text-gray-900">
                    Geser Peta
                </h3>

                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Geser peta untuk melihat vihara
                    di wilayah lainnya.
                </p>
            </div>

            <div class="rounded-xl bg-gray-50 p-5">
                <h3 class="font-semibold text-gray-900">
                    Perbesar Peta
                </h3>

                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Gunakan tombol zoom untuk melihat
                    lokasi secara lebih detail.
                </p>
            </div>

            <div class="rounded-xl bg-gray-50 p-5">
                <h3 class="font-semibold text-gray-900">
                    Klik Lokasi
                </h3>

                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Klik marker untuk melihat informasi
                    vihara yang tersedia.
                </p>
            </div>

        </div>

    </div>
</section>

@endsection