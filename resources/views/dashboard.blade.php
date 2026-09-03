<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Resumen del mes</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white shadow rounded p-5">
                <p class="text-sm text-gray-500">Ventas del mes</p>
                <p class="text-2xl font-bold text-gray-800">${{ number_format($ventasMes, 0) }}</p>
            </div>
            <div class="bg-white shadow rounded p-5">
                <p class="text-sm text-gray-500">Ganancia del mes</p>
                <p class="text-2xl font-bold text-green-700">${{ number_format($gananciaMes, 0) }}</p>
            </div>
            <div class="bg-white shadow rounded p-5">
                <p class="text-sm text-gray-500">Unidades vendidas</p>
                <p class="text-2xl font-bold text-gray-800">{{ $unidadesMes }}</p>
            </div>
            <div class="bg-white shadow rounded p-5">
                <p class="text-sm text-gray-500">Valor del inventario</p>
                <p class="text-2xl font-bold text-gray-800">${{ number_format($valorInventario, 0) }}</p>
            </div>
        </div>

        @if ($stockBajo->count())
            <div class="bg-white shadow rounded p-5 border-l-4 border-red-500">
                <h3 class="font-semibold mb-3">Productos por agotarse</h3>
                <table class="w-full text-sm">
                    <thead class="text-gray-500">
                        <tr>
                            <th class="text-left pb-2">Producto</th>
                            <th class="text-center pb-2">Stock</th>
                            <th class="text-center pb-2">Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stockBajo as $producto)
                            <tr class="border-t">
                                <td class="py-2">
                                    {{ $producto->nombre }}
                                    <span class="text-gray-500">{{ $producto->talla }} {{ $producto->color }}</span>
                                </td>
                                <td class="py-2 text-center">
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">
                                        {{ $producto->stock }}
                                    </span>
                                </td>
                                <td class="py-2 text-center text-gray-500">{{ $producto->stock_minimo }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white shadow rounded p-5">
                <h3 class="font-semibold mb-3">Más vendidos este mes</h3>
                @forelse ($masVendidos as $item)
                    <div class="flex justify-between py-2 border-t text-sm">
                        <span>{{ $item->producto->nombre }}
                            <span class="text-gray-500">{{ $item->producto->talla }}</span>
                        </span>
                        <span class="font-medium">{{ $item->unidades }} und.</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Sin ventas este mes.</p>
                @endforelse
            </div>

            <div class="bg-white shadow rounded p-5">
                <h3 class="font-semibold mb-3">Últimas ventas</h3>
                @forelse ($ultimasVentas as $venta)
                    <a href="{{ route('ventas.show', $venta) }}"
                       class="flex justify-between py-2 border-t text-sm hover:bg-gray-50">
                        <span>
                            {{ $venta->fecha->format('d/m/Y') }}
                            <span class="text-gray-500">
                                {{ $venta->cliente->nombre ?? 'Sin datos' }}
                            </span>
                        </span>
                        <span class="font-medium">${{ number_format($venta->total, 0) }}</span>
                    </a>
                @empty
                    <p class="text-gray-500 text-sm">Sin ventas registradas.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout> 