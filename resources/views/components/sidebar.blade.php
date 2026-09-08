<div class="flex h-full flex-col bg-slate-900">
    <!-- Branding -->
    <div class="flex h-16 shrink-0 items-center gap-2.5 px-5">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-white shadow-soft">
            <x-application-logo class="h-5 w-5 fill-current" />
        </div>
        <div class="leading-tight">
            <p class="text-sm font-semibold text-white">{{ config('app.name', 'Inventario') }}</p>
            <p class="text-xs text-slate-400">Panel de gestión</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
            Dashboard
        </x-sidebar-link>

        <p class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Inventario</p>

        <x-sidebar-link :href="route('productos.index')" :active="request()->routeIs('productos.*')" icon="cube">
            Productos
        </x-sidebar-link>

        <x-sidebar-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')" icon="tag">
            Categorías
        </x-sidebar-link>

        <p class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Operación</p>

        <x-sidebar-link :href="route('compras.index')" :active="request()->routeIs('compras.*')" icon="truck">
            Compras
        </x-sidebar-link>

        <x-sidebar-link :href="route('ventas.index')" :active="request()->routeIs('ventas.*')" icon="banknotes">
            Ventas
        </x-sidebar-link>

        <x-sidebar-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')" icon="user-circle">
            Clientes
        </x-sidebar-link>

        <p class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wider text-slate-500">Análisis</p>

        <x-sidebar-link :href="route('reportes.index')" :active="request()->routeIs('reportes.*')" icon="chart-bar">
            Reportes
        </x-sidebar-link>
    </nav>

    <!-- Footer / user -->
    <div class="shrink-0 border-t border-white/10 p-3">
        <div class="flex items-center gap-3 rounded-lg px-2 py-2">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/10 text-white">
                <x-icon name="user-circle" class="h-6 w-6" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</div>
