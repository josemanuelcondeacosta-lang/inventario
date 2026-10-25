<div class="flex h-full flex-col bg-nav-fondo">

    <a href="{{ route('dashboard') }}" class="flex h-20 shrink-0 items-center gap-3 border-b border-white/[0.06] px-5">
        <img src="{{ asset('images/yoyi-sport-logo-256.png') }}" alt="Yoyi Sport"
             class="h-11 w-11 shrink-0 rounded-full ring-2 ring-white/10">
        <div class="min-w-0 leading-tight">
            <p class="truncate font-display text-base font-semibold text-white">Yoyi Sport</p>
            <p class="text-xs text-nav-texto">Dios Es Amor</p>
        </div>
    </a>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
            Dashboard
        </x-sidebar-link>

        <p class="px-3 pb-1.5 pt-5 text-[11px] font-semibold tracking-wide text-nav-texto">Inventario</p>

        <x-sidebar-link :href="route('productos.index')" :active="request()->routeIs('productos.*')" icon="cube">
            Productos
        </x-sidebar-link>

        <x-sidebar-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')" icon="tag">
            Categorías
        </x-sidebar-link>

        <p class="px-3 pb-1.5 pt-5 text-[11px] font-semibold tracking-wide text-nav-texto">Operación</p>

        <x-sidebar-link :href="route('compras.index')" :active="request()->routeIs('compras.*')" icon="truck">
            Compras
        </x-sidebar-link>

        <x-sidebar-link :href="route('ventas.index')" :active="request()->routeIs('ventas.*')" icon="banknotes">
            Ventas
        </x-sidebar-link>

        <x-sidebar-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')" icon="user-circle">
            Clientes
        </x-sidebar-link>

        <x-sidebar-link :href="route('reclamos.index')" :active="request()->routeIs('reclamos.*')" icon="exclamation-triangle">
            Garantías
        </x-sidebar-link>

        <p class="px-3 pb-1.5 pt-5 text-[11px] font-semibold tracking-wide text-nav-texto">Análisis</p>

        <x-sidebar-link :href="route('reportes.index')" :active="request()->routeIs('reportes.*')" icon="chart-bar">
            Reportes
        </x-sidebar-link>
    </nav>

    <div class="shrink-0 border-t border-white/[0.06] p-3">
        <div class="flex items-center gap-3 rounded-lg px-2 py-2">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand/15 text-brand">
                <x-icon name="user-circle" class="h-6 w-6" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                <p class="truncate text-xs text-nav-texto">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</div>