<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Inventario') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50">

            <!-- Sidebar: escritorio -->
            <aside class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-40 lg:flex lg:w-72 lg:flex-col">
                <x-sidebar />
            </aside>

            <!-- Sidebar: móvil (off-canvas) -->
            <div x-show="sidebarOpen" x-cloak class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
                <div x-show="sidebarOpen"
                     x-transition:enter="transition-opacity ease-linear duration-200"
                     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-150"
                     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/60"
                     @click="sidebarOpen = false"></div>

                <div class="fixed inset-y-0 left-0 flex w-72 max-w-[85%]">
                    <div x-show="sidebarOpen"
                         x-transition:enter="transition ease-in-out duration-200"
                         x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in-out duration-150"
                         x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                         class="relative flex w-full flex-col">
                        <button type="button" @click="sidebarOpen = false"
                                class="absolute -right-11 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white"
                                aria-label="Cerrar menú">
                            <x-icon name="x-mark" class="h-5 w-5" />
                        </button>
                        <x-sidebar />
                    </div>
                </div>
            </div>

            <!-- Contenido -->
            <div class="flex min-h-screen flex-col lg:pl-72">
                <x-topbar>
                    @isset($header)
                        {{ $header }}
                    @endisset
                </x-topbar>

                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
