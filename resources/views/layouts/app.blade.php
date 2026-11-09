<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="@yield('description', 'Wiharaku - Temukan vihara di seluruh Indonesia.')">

    <title>@yield('title', 'Wiharaku - Temukan Vihara di Indonesia')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body class="min-h-screen bg-white text-gray-800 antialiased">

    {{-- Navbar --}}
    <x-public.navbar />

    {{-- Konten halaman --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <x-public.footer />
    @yield('scripts')
</body>


</html>