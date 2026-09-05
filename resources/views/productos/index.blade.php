<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">Productos</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-4 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <x-alert variant="success">{{ session('success') }}</x-alert>
        @endif

        <!-- Encabezado de la página -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">
                    <span class="font-semibold text-slate-800">{{ $productos->total() }}</span>
                    {{ Str::plural('producto', $productos->total()) }} registrados
                </p>
            </div>

            <div class="flex flex-1 flex-wrap items-center justify-end gap-3 sm:flex-none">
                <form method="GET" action="{{ route('productos.index') }}" class="relative flex-1 sm:flex-none">
                    <x-icon name="magnifying-glass" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           placeholder="Buscar por nombre o referencia..."
                           class="field-input w-full py-2 pl-9 pr-3 sm:w-72">
                    @if (request('buscar'))
                        <a href="{{ route('productos.index') }}"
                           class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                           aria-label="Limpiar búsqueda">
                            <x-icon name="x-mark" class="h-4 w-4" />
                        </a>
                    @endif
                </form>

                <a href="{{ route('productos.create') }}" class="btn-primary whitespace-nowrap">
                    <x-icon name="plus" class="h-4 w-4" />
                    Nuevo producto
                </a>
            </div>
        </div>

        @if ($productos->isEmpty())
            <!-- Empty state -->
            <div class="card flex flex-col items-center gap-3 px-4 py-14 text-center">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <x-icon name="inbox" class="h-6 w-6" />
                </span>
                <div>
                    <p class="font-medium text-slate-700">
                        {{ request('buscar') ? 'Sin resultados para tu búsqueda' : 'No hay productos registrados' }}
                    </p>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ request('buscar') ? 'Prueba con otro nombre o referencia.' : 'Comienza agregando tu primer producto.' }}
                    </p>
                </div>
                @if (!request('buscar'))
                    <a href="{{ route('productos.create') }}" class="btn-primary">
                        <x-icon name="plus" class="h-4 w-4" /> Nuevo producto
                    </a>
                @endif
            </div>
        @else
        <!-- Tabla -->
        <div class="table-wrap">
            <table class="table-base">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Talla</th>
                        <th>Color</th>
                        <th class="text-right">P. Venta</th>
                        <th class="text-center">Stock</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $producto)
                        <tr class="{{ $producto->activo ? '' : 'opacity-50' }}">
                            <td class="font-medium text-slate-800">
                                {{ $producto->nombre }}
                                @if ($producto->referencia)
                                    <span class="block text-xs font-normal text-slate-400">{{ $producto->referencia }}</span>
                                @endif
                            </td>
                            <td>{{ $producto->categoria->nombre }}</td>
                            <td>{{ $producto->talla ?? '-' }}</td>
                            <td>{{ $producto->color ?? '-' }}</td>
                            <td class="text-right">${{ number_format($producto->precio_venta, 0) }}</td>
                            <td class="text-center">
                                @if ($producto->stock <= 0)
                                    <x-badge variant="danger">Agotado</x-badge>
                                @elseif ($producto->stock <= $producto->stock_minimo)
                                    <x-badge variant="warning">Stock bajo ({{ $producto->stock }})</x-badge>
                                @else
                                    <x-badge variant="success">{{ $producto->stock }} disponibles</x-badge>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('productos.edit', $producto) }}"
                                       class="inline-flex items-center gap-1 text-brand-600 hover:text-brand-700"
                                       aria-label="Editar {{ $producto->nombre }}">
                                        <x-icon name="pencil" class="h-4 w-4" /> Editar
                                    </a>

                                    @if ($producto->activo)
                                        <form action="{{ route('productos.destroy', $producto) }}"
                                              method="POST"
                                              onsubmit="return confirm('¿Desactivar este producto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 text-red-600 hover:text-red-700"
                                                    aria-label="Desactivar {{ $producto->nombre }}">
                                                <x-icon name="trash" class="h-4 w-4" /> Desactivar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if ($productos->hasPages())
            <div>
                {{ $productos->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
