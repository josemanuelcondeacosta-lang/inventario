<div class="flex h-full flex-col bg-nav-fondo">

    {{-- Logo: cuando lo tengas, ponlo en public/img/logo.png y reemplaza
         el recuadro punteado por:
         <img src="{{ asset('img/logo.png') }}" alt="" class="h-11 w-auto"> --}}
    <div class="flex h-20 shrink-0 items-center gap-3 border-b border-white/[0.06] px-5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-dashed border-brand-400/40 text-[9px] font-medium text-brand-400/60">
            LOGO
        </div>
        <div class="min-w-0 leading-tight">
            <p class="truncate font-display text-base font-semibold text-white">
                {{ config('app.name', 'Inventario') }}
            </p>
            <p class="text-xs text-nav-texto">Inventario y ventas</p>
        </div>
    </div>

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

        <p class="px-3 pb-1.5 pt-5 text-[11px] font-semibold tracking-wide text-nav-texto">Análisis</p>

        <x-sidebar-link :href="route('reportes.index')" :active="request()->routeIs('reportes.*')" icon="chart-bar">
            Reportes
        </x-sidebar-link>
    </nav>

    <div class="shrink-0 border-t border-white/[0.06] p-3">
        <div class="flex items-center gap-3 rounded-lg px-2 py-2">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-400/15 text-brand-400">
                <x-icon name="user-circle" class="h-6 w-6" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                <p class="truncate text-xs text-nav-texto">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</div>