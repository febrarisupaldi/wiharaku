<footer class="bg-emerald-950 text-white">

    <div class="mx-auto grid max-w-7xl gap-10 px-6 py-14 md:grid-cols-3">

        {{-- Brand --}}
        <div>
            <h2 class="text-2xl font-bold">Wiharaku</h2>

            <p class="mt-4 max-w-sm text-sm leading-7 text-emerald-100/70">
                Temukan dan jelajahi vihara di seluruh Indonesia.
                Membantu masyarakat menemukan informasi vihara
                dengan lebih mudah.
            </p>
        </div>

        {{-- Navigation --}}
        <div>
            <h3 class="font-semibold">Navigasi</h3>

            <ul class="mt-4 space-y-3 text-sm text-emerald-100/70">
                <li>
                    <a href="{{ route('home') }}"
                        class="transition hover:text-white">
                        Beranda
                    </a>
                </li>

                <li>
                    <a href="{{ route('explore') }}"
                        class="transition hover:text-white">
                        Jelajahi Vihara
                    </a>
                </li>

                <li>
                    <a href="{{ route('map') }}"
                        class="transition hover:text-white">
                        Peta Vihara
                    </a>
                </li>
            </ul>
        </div>

        {{-- About --}}
        <div>
            <h3 class="font-semibold">Tentang Wiharaku</h3>

            <p class="mt-4 text-sm leading-7 text-emerald-100/70">
                Wiharaku merupakan platform informasi
                untuk membantu menemukan lokasi vihara
                di berbagai wilayah Indonesia.
            </p>
        </div>

    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto max-w-7xl px-6 py-6">
            <p class="text-center text-xs text-emerald-100/50">
                &copy; {{ date('Y') }} Wiharaku. All rights reserved.
            </p>
        </div>
    </div>

</footer>