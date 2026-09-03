<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Productos</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex justify-between items-center gap-4">
            <a href="{{ route('productos.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Nuevo producto
            </a>

            <form method="GET" action="{{ route('productos.index') }}" class="flex gap-2">
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Buscar producto..."
                       class="border-gray-300 rounded">
                <button type="submit" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    Buscar
                </button>
            </form>
        </div>

        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Producto</th>
                        <th class="p-3">Categoría</th>
                        <th class="p-3">Talla</th>
                        <th class="p-3">Color</th>
                        <th class="p-3 text-right">P. Venta</th>
                        <th class="p-3 text-center">Stock</th>
                        <th class="p-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $producto)
                        <tr class="border-t {{ $producto->activo ? '' : 'opacity-50' }}">
                            <td class="p-3">{{ $producto->nombre }}</td>
                            <td class="p-3">{{ $producto->categoria->nombre }}</td>
                            <td class="p-3">{{ $producto->talla ?? '-' }}</td>
                            <td class="p-3">{{ $producto->color ?? '-' }}</td>
                            <td class="p-3 text-right">${{ number_format($producto->precio_venta, 0) }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-1 rounded text-xs
                                    {{ $producto->stock <= $producto->stock_minimo
                                       ? 'bg-red-100 text-red-800'
                                       : 'bg-green-100 text-green-800' }}">
                                    {{ $producto->stock }}
                                </span>
                            </td>
                            <td class="p-3 text-right whitespace-nowrap">
                                <a href="{{ route('productos.edit', $producto) }}"
                                   class="text-blue-600 hover:underline">Editar</a>

                                @if ($producto->activo)
                                    <form action="{{ route('productos.destroy', $producto) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('¿Desactivar este producto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline ml-3">
                                            Desactivar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">
                                No hay productos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $productos->links() }}
        </div>
    </div>
</x-app-layout>