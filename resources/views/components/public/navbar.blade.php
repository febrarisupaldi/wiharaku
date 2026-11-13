<header class="sticky top-0 z-[1100] z-50 border-b border-gray-100 bg-white/95 backdrop-blur">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-800 text-xl font-bold text-white">
                KW
            </div>

            <div>
                <span class="block text-xl font-bold tracking-tight text-emerald-900">
                    KeWihara
                </span>
                <span class="block text-xs text-gray-500">
                    Temukan Vihara Indonesia
                </span>
            </div>
        </a>

        {{-- Desktop Navigation --}}
        <div class="hidden items-center gap-8 md:flex">
            <a href="{{ route('home') }}"
                class="text-sm font-medium text-emerald-800">
                Beranda
            </a>

            <a href="{{ route('explore') }}"
                class="text-sm font-medium text-gray-600 transition hover:text-emerald-800">
                Jelajahi Vihara
            </a>

            <a href="{{ route('map') }}"
                class="text-sm font-medium text-gray-600 transition hover:text-emerald-800">
                Peta Vihara
            </a>

            <a href="{{ route('home') }}#tentang"
                class="text-sm font-medium text-gray-600 transition hover:text-emerald-800">
                Tentang
            </a>
        </div>

        {{-- Mobile Navigation --}}
        <details class="relative md:hidden">
            <summary class="flex cursor-pointer list-none items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="h-6 w-6">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </summary>

            <div class="absolute right-0 mt-3 w-56 rounded-xl border border-gray-100 bg-white p-3 shadow-lg">
                <a href="{{ route('home') }}"
                    class="block rounded-lg px-4 py-3 text-sm font-medium text-emerald-800 hover:bg-emerald-50">
                    Beranda
                </a>

                <a href="{{ route('explore') }}"
                    class="text-sm font-medium text-gray-600 transition hover:text-emerald-800">
                    Jelajahi Vihara
                </a>

                <a href="{{ route('map') }}"
                    class="text-sm font-medium text-gray-600 transition hover:text-emerald-800">
                    Peta Vihara
                </a>

                <a href="{{ route('home') }}#tentang"
                    class="block rounded-lg px-4 py-3 text-sm text-gray-600 hover:bg-emerald-50">
                    Tentang
                </a>
            </div>
        </details>

    </nav>
</header>