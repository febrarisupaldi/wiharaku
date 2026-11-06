@extends('layouts.app')

@section('content')
<div class="relative h-[calc(100vh-64px)] min-h-[500px] w-full overflow-hidden">

    {{-- Map --}}
    <div
        id="vihara-map"
        class="absolute inset-0 z-0"></div>

    {{-- Search panel --}}
    <div class="absolute left-4 top-4 z-[1000] w-[min(360px,calc(100%-32px))]">

        <div class="rounded-2xl bg-white p-4 shadow-xl">

            <h1 class="text-lg font-bold text-gray-900">
                Temukan Vihara
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Temukan vihara Buddhis di sekitar Anda.
            </p>

            <div class="mt-4">
                <input
                    type="text"
                    id="vihara-keyword"
                    placeholder="Cari nama vihara..."
                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-green-600 focus:ring-2 focus:ring-green-100">
            </div>

            <div
                id="map-status"
                role="status"
                aria-live="polite"
                class="mt-3 text-xs text-gray-500">
                Memuat peta...
            </div>

        </div>

    </div>

    {{-- Locate user --}}
    <button
        type="button"
        id="locate-me"
        aria-label="Gunakan lokasi saya"
        title="Gunakan lokasi saya"
        class="absolute bottom-8 right-4 z-[1000] flex h-12 w-12 items-center justify-center rounded-xl bg-white text-gray-700 shadow-lg hover:bg-gray-50">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round">
            <circle cx="12" cy="12" r="3" />
            <path d="M12 2v2" />
            <path d="M12 20v2" />
            <path d="M20 12h2" />
            <path d="M2 12h2" />
            <circle cx="12" cy="12" r="9" />
        </svg>
    </button>

</div>
@endsection