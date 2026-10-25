<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Yoyi Sport</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|space-grotesk:500,600,700&display=swap" rel="stylesheet" />
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            (function () {
                const t = localStorage.getItem('tema');
                if (t === 'oscuro' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
    </head>
    <body class="font-sans text-texto antialiased">
        <div class="grid min-h-screen lg:grid-cols-2">

            <!-- Panel de marca (solo escritorio) -->
            <div class="relative hidden flex-col justify-between overflow-hidden bg-nav-fondo p-12 text-white lg:flex">
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_15%_15%,rgba(198,242,78,0.16),transparent_40%),radial-gradient(circle_at_85%_85%,rgba(198,242,78,0.08),transparent_45%)]"></div>

                <p class="relative font-display text-xl font-semibold">Yoyi Sport</p>

                <div class="relative flex flex-col items-start">
                    <img src="{{ asset('images/yoyi-sport-logo.webp') }}" alt="Yoyi Sport"
                         class="h-48 w-48 rounded-full shadow-2xl ring-4 ring-white/10">
                    <h1 class="mt-10 max-w-md text-4xl font-semibold leading-tight text-white">
                        Tu tienda deportiva, <span class="text-[#C6F24E]">bajo control.</span>
                    </h1>
                    <p class="mt-4 max-w-md text-sm text-nav-texto">
                        Productos, ventas, compras, clientes, garantías y reportes de Yoyi Sport en un solo lugar.
                    </p>
                </div>

                <div class="relative flex items-center justify-between text-xs text-nav-texto">
                    <span>&copy; {{ date('Y') }} Yoyi Sport &middot; Dios Es Amor</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 font-semibold text-white">Cel. 301 742 6254</span>
                </div>
            </div>

            <!-- Panel del formulario -->
            <div class="flex flex-col items-center justify-center bg-elevado px-6 py-12 sm:px-10">
                <div class="w-full max-w-sm">
                    <div class="mb-8 flex flex-col items-center gap-3 lg:hidden">
                        <img src="{{ asset('images/yoyi-sport-logo-256.png') }}" alt="Yoyi Sport"
                             class="h-20 w-20 rounded-full shadow-card">
                        <span class="font-display text-lg font-semibold text-texto">Yoyi Sport</span>
                    </div>

                    <div class="card p-6 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
