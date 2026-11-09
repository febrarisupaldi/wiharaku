@extends('layouts.app')

@section('title', 'Wiharaku - Temukan Vihara di Indonesia')

@section('description', 'Temukan vihara di seluruh Indonesia. Jelajahi lokasi, informasi, dan kegiatan vihara melalui Wiharaku.')

@section('content')

{{-- HERO SECTION --}}
<section class="relative isolate overflow-hidden bg-gradient-to-br from-emerald-50 via-white to-amber-50">

    {{-- Background Decoration --}}
    <div class="pointer-events-none absolute -right-32 -top-32 -z-10 h-96 w-96 rounded-full bg-emerald-100/60 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-40 -left-32 -z-10 h-96 w-96 rounded-full bg-amber-100/50 blur-3xl"></div>

    <div class="mx-auto grid max-w-7xl items-center gap-14 px-6 py-20 md:grid-cols-2 md:py-28">

        {{-- Hero Content --}}
        <div>
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white/80 px-4 py-2 text-sm font-medium text-emerald-800">
                <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                Direktori Vihara Indonesia
            </span>

            <h1 class="mt-7 max-w-xl text-4xl font-bold leading-tight tracking-tight text-emerald-950 sm:text-5xl lg:text-6xl">
                Temukan
                <span class="text-emerald-700">Vihara</span>
                di Seluruh Indonesia
            </h1>

            <p class="mt-6 max-w-lg text-base leading-8 text-gray-600 md:text-lg">
                Jelajahi berbagai vihara di Indonesia.
                Temukan lokasi, kenali informasi, dan
                lebih dekat dengan tempat untuk beribadah
                serta belajar Dhamma.
            </p>

            {{-- Search Form --}}
            <form action="{{ route('explore') }}"
                method="GET"
                class="mt-9 rounded-2xl border border-gray-200 bg-white p-2 shadow-lg shadow-emerald-900/5">

                <div class="flex flex-col gap-2 sm:flex-row">

                    <div class="flex flex-1 items-center gap-3 px-3">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.8"
                            stroke="currentColor"
                            class="h-6 w-6 shrink-0 text-emerald-700">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.197 5.197a7.5 7.5 0 0 0 10.606 10.606Z" />
                        </svg>

                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari nama vihara atau lokasi..."
                            class="w-full border-0 bg-transparent py-3 text-sm text-gray-800 outline-none placeholder:text-gray-400 focus:ring-0">
                    </div>

                    <button
                        type="submit"
                        class="rounded-xl bg-emerald-800 px-7 py-3 text-sm font-semibold text-white transition hover:bg-emerald-900">
                        Cari Vihara
                    </button>

                </div>
            </form>

            <p class="mt-4 text-xs text-gray-500">
                Temukan vihara berdasarkan nama atau wilayah.
            </p>

            {{-- CTA --}}
            <div class="mt-8 flex flex-wrap items-center gap-5">
                <a href="#jelajahi"
                    class="inline-flex items-center gap-2 font-semibold text-emerald-800 transition hover:text-emerald-950">
                    Mulai Jelajahi

                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="h-5 w-5">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Hero Visual --}}
        <div class="relative mx-auto w-full max-w-lg">

            <div class="relative overflow-hidden rounded-[2rem] bg-emerald-900 p-8 shadow-2xl shadow-emerald-950/20 sm:p-12">

                {{-- Decorative Circles --}}
                <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full border border-white/10"></div>
                <div class="absolute -right-10 -top-10 h-52 w-52 rounded-full border border-white/10"></div>
                <div class="absolute -bottom-24 -left-20 h-64 w-64 rounded-full bg-emerald-800"></div>

                <div class="relative z-10">

                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-white backdrop-blur">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.4"
                            stroke="currentColor"
                            class="h-9 w-9">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 21s7-4.438 7-11a7 7 0 1 0-14 0c0 6.562 7 11 7 11Z" />
                            <circle cx="12" cy="10" r="2.5" />
                        </svg>
                    </div>

                    <p class="mt-10 text-sm font-medium tracking-widest text-emerald-200">
                        JELAJAHI INDONESIA
                    </p>

                    <h2 class="mt-3 max-w-xs text-3xl font-bold leading-snug text-white sm:text-4xl">
                        Satu tempat untuk menemukan vihara.
                    </h2>

                    <p class="mt-5 max-w-xs text-sm leading-7 text-emerald-100/70">
                        Temukan informasi vihara dari berbagai
                        wilayah Indonesia dengan lebih mudah.
                    </p>

                    <div class="mt-10 flex items-center gap-3 border-t border-white/15 pt-6">
                        <div class="flex -space-x-2">
                            <span class="h-8 w-8 rounded-full border-2 border-emerald-900 bg-emerald-300"></span>
                            <span class="h-8 w-8 rounded-full border-2 border-emerald-900 bg-amber-200"></span>
                            <span class="h-8 w-8 rounded-full border-2 border-emerald-900 bg-teal-200"></span>
                        </div>

                        <p class="text-xs text-emerald-100/80">
                            Menjelajahi vihara di Indonesia
                        </p>
                    </div>

                </div>
            </div>

            {{-- Floating Label --}}
            <div class="absolute -bottom-5 -left-4 rounded-xl border border-gray-100 bg-white px-5 py-4 shadow-xl sm:-left-8">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-800">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.8"
                            stroke="currentColor"
                            class="h-6 w-6">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 21s7-4.438 7-11a7 7 0 1 0-14 0c0 6.562 7 11 7 11Z" />
                            <circle cx="12" cy="10" r="2.5" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Cakupan wilayah</p>
                        <p class="text-sm font-bold text-gray-800">Seluruh Indonesia</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>


{{-- INTRODUCTION SECTION --}}
<section id="jelajahi" class="bg-white py-20">

    <div class="mx-auto max-w-7xl px-6">

        <div class="mx-auto max-w-2xl text-center">
            <span class="text-sm font-semibold uppercase tracking-widest text-emerald-700">
                Jelajahi Wiharaku
            </span>

            <h2 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Temukan vihara dengan lebih mudah
            </h2>

            <p class="mt-5 leading-8 text-gray-600">
                Wiharaku membantu kamu menemukan informasi vihara
                melalui pencarian, wilayah, dan peta lokasi.
            </p>
        </div>

        <div class="mt-14 grid gap-6 md:grid-cols-3">

            {{-- Feature 1 --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-7 transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="h-6 w-6">
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" d="m16 16 4 4" />
                    </svg>
                </div>

                <h3 class="mt-6 text-lg font-bold text-gray-900">
                    Pencarian Vihara
                </h3>

                <p class="mt-3 text-sm leading-7 text-gray-600">
                    Cari vihara berdasarkan nama dan informasi
                    yang tersedia.
                </p>
            </div>

            {{-- Feature 2 --}}
            <div class="rounded-2xl border border-gray-100 bg-white p-7 transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="h-6 w-6">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 6.75 9 4.5l6 2.25 6-2.25v13.5L15 20.25 9 18 3 20.25V6.75Z" />
                        <path stroke-linecap="round" d="M9 4.5v13.5m6-11.25v13.5" />
                    </svg>
                </div>

                <h3 class="mt-6 text-lg font-bold text-gray-900">
                    Berdasarkan Wilayah
                </h3>

                <p class="mt-3 text-sm leading-7 text-gray-600">
                    Jelajahi vihara berdasarkan provinsi,
                    kota, kabupaten, dan kecamatan.
                </p>
            </div>

            {{-- Feature 3 --}}
            <div id="peta" class="rounded-2xl border border-gray-100 bg-white p-7 transition hover:-translate-y-1 hover:shadow-lg">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="h-6 w-6">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 21s7-4.438 7-11a7 7 0 1 0-14 0c0 6.562 7 11 7 11Z" />
                        <circle cx="12" cy="10" r="2.5" />
                    </svg>
                </div>

                <h3 class="mt-6 text-lg font-bold text-gray-900">
                    Peta Lokasi
                </h3>

                <p class="mt-3 text-sm leading-7 text-gray-600">
                    Temukan lokasi vihara dan lihat persebarannya
                    melalui peta interaktif.
                </p>
            </div>

        </div>
    </div>
</section>


{{-- ABOUT SECTION --}}
<section id="tentang" class="bg-emerald-50 py-16">
    <div class="mx-auto max-w-4xl px-6 text-center">

        <h2 class="text-2xl font-bold text-emerald-950 sm:text-3xl">
            Mengenal Wiharaku
        </h2>

        <p class="mt-5 leading-8 text-gray-600">
            Wiharaku hadir sebagai direktori informasi vihara
            di Indonesia. Kami ingin memudahkan masyarakat
            menemukan lokasi dan mengenal vihara di berbagai
            daerah melalui satu platform.
        </p>

    </div>
</section>

@endsection