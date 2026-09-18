<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 border-b border-borde bg-superficie/85 px-4 backdrop-blur sm:px-6 lg:px-8">

    <button type="button" @click="sidebarOpen = true"
            class="-ml-1 rounded-lg p-2 text-suave hover:bg-elevado hover:text-texto lg:hidden"
            aria-label="Abrir menú de navegación">
        <x-icon name="bars-3" class="h-6 w-6" />
    </button>

    <div class="min-w-0 flex-1">
        {{ $slot }}
    </div>

    <div class="flex items-center gap-1">

        <button type="button" id="btnTema"
                class="rounded-lg p-2 text-suave transition-colors hover:bg-elevado hover:text-texto"
                aria-label="Cambiar entre modo claro y oscuro" title="Cambiar tema">
            <svg id="iconoLuna" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
            </svg>
            <svg id="iconoSol" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="hidden h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
            </svg>
        </button>

        <x-dropdown align="right" width="56">
            <x-slot name="trigger">
                <button class="flex items-center gap-2 rounded-lg py-1.5 pl-1.5 pr-2.5 text-sm text-texto hover:bg-elevado focus:outline-none">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-suave text-brand-texto">
                        <x-icon name="user-circle" class="h-5 w-5" />
                    </span>
                    <span class="hidden font-medium sm:block">{{ Auth::user()->name }}</span>
                    <x-icon name="chevron-down" class="hidden h-4 w-4 text-tenue sm:block" />
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const raiz = document.documentElement;
    const luna = document.getElementById('iconoLuna');
    const sol  = document.getElementById('iconoSol');

    function pintarIcono() {
        const oscuro = raiz.classList.contains('dark');
        luna.classList.toggle('hidden', oscuro);
        sol.classList.toggle('hidden', !oscuro);
    }

    document.getElementById('btnTema').addEventListener('click', function () {
        raiz.classList.toggle('dark');
        localStorage.setItem('tema', raiz.classList.contains('dark') ? 'oscuro' : 'claro');
        pintarIcono();
    });

    pintarIcono();
});
</script>