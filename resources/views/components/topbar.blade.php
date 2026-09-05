<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur sm:px-6 lg:px-8">
    <!-- Mobile: abrir sidebar -->
    <button type="button" @click="sidebarOpen = true"
            class="-ml-1 rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden"
            aria-label="Abrir menú de navegación">
        <x-icon name="bars-3" class="h-6 w-6" />
    </button>

    <!-- Título de la página / breadcrumb -->
    <div class="min-w-0 flex-1">
        {{ $slot }}
    </div>

    <!-- Usuario -->
    <div class="flex items-center">
        <x-dropdown align="right" width="56">
            <x-slot name="trigger">
                <button class="flex items-center gap-2 rounded-lg py-1.5 pl-1.5 pr-2.5 text-sm text-slate-700 hover:bg-slate-100 focus:outline-none">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                        <x-icon name="user-circle" class="h-5 w-5" />
                    </span>
                    <span class="hidden font-medium sm:block">{{ Auth::user()->name }}</span>
                    <x-icon name="chevron-down" class="hidden h-4 w-4 text-slate-400 sm:block" />
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    <span class="flex items-center gap-2">
                        <x-icon name="user-circle" class="h-4 w-4" /> Mi perfil
                    </span>
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        <span class="flex items-center gap-2">
                            <x-icon name="arrow-right-on-rectangle" class="h-4 w-4" /> Cerrar sesión
                        </span>
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
