<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Inventario') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-texto antialiased">
        <div class="grid min-h-screen lg:grid-cols-2">

            <!-- Panel de marca (solo escritorio) -->
            <div class="relative hidden flex-col justify-between overflow-hidden bg-slate-900 p-12 text-white lg:flex">
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,theme(colors.brand.600)/35,transparent_45%),radial-gradient(circle_at_80%_70%,theme(colors.brand.500)/25,transparent_40%)]"></div>

                <div class="relative flex items-center gap-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 shadow-soft">
                        <x-application-logo class="h-5 w-5 fill-current text-white" />
                    </div>
                    <span class="text-sm font-semibold">{{ config('app.name', 'Inventario') }}</span>
                </div>

                <div class="relative max-w-md">
                    <h1 class="text-3xl font-semibold leading-tight">
                        Gestiona tu inventario con claridad.
                    </h1>
                    <p class="mt-4 text-sm text-tenue">
                        Controla productos, ventas, compras y reportes desde un solo lugar,
                        con una vista clara del estado de tu negocio.
                    </p>
                </div>

                <p class="relative text-xs text-tenue">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Inventario') }}. Todos los derechos reservados.
                </p>
            </div>

            <!-- Panel del formulario -->
            <div class="flex flex-col items-center justify-center bg-elevado px-6 py-12 sm:px-10">
                <div class="w-full max-w-sm">
                    <div class="mb-8 flex flex-col items-center gap-3 lg:hidden">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-600 shadow-soft">
                            <x-application-logo class="h-7 w-7 fill-current text-white" />
                        </div>
                        <span class="text-base font-semibold text-texto">{{ config('app.name', 'Inventario') }}</span>
                    </div>

                    <div class="card p-6 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
