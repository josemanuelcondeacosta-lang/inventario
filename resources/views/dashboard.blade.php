<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Resumen del mes</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white shadow rounded p-5">
                <p class="text-sm text-gray-500">Ventas del mes</p>
                <p class="text-2xl font-bold text-gray-800">${{ number_format($ventasMes, 0) }}</p>
            </div>
            <div class="bg-white shadow rounded p-5">
                <p class="text-sm text-gray-500">Ganancia del mes</p>
                <p class="text-2xl font-bold text-green-700">${{ number_format($gananciaMes, 0) }}</p>
            </div>
            <div class="bg-white shadow rounded p-5">
                <p class="text-sm text-gray-500">Por cobrar</p>
                <p class="text-2xl font-bold {{ $totalPorCobrar > 0 ? 'text-red-700' : 'text-gray-400' }}">
                    ${{ number_format($totalPorCobrar, 0) }}
                </p>
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

        @if ($porCobrar->count())
            <div class="bg-white shadow rounded p-5 border-l-4 border-red-500">
                <h3 class="font-semibold mb-3">Pagos pendientes</h3>
                <table class="w-full text-sm">
                    <thead class="text-gray-500">
                        <tr>
                            <th class="text-left pb-2">Cliente</th>
                            <th class="text-left pb-2">Teléfono</th>
                            <th class="text-left pb-2">Tipo</th>
                            <th class="text-center pb-2">Días</th>
                            <th class="text-right pb-2">Saldo</th>
                            <th class="text-right pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($porCobrar as $venta)
                            @php $dias = (int) $venta->fecha->startOfDay()->diffInDays(now()->startOfDay()); @endphp
                            <tr class="border-t">
                                <td class="py-2">
                                    {{ $venta->cliente->nombre ?? 'Sin datos' }}
                                    <span class="block text-xs text-gray-500">
                                        {{ $venta->cliente->cedula ?? '' }}
                                    </span>
                                </td>
                                <td class="py-2">{{ $venta->cliente->telefono ?? '-' }}</td>
                                <td class="py-2 capitalize">{{ $venta->tipo_pago }}</td>
                                <td class="py-2 text-center">
                                    <span class="px-2 py-1 rounded text-xs
                                        {{ $dias > 30 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $dias }}
                                    </span>
                                </td>
                                <td class="py-2 text-right font-medium text-red-700">
                                    ${{ number_format($venta->saldo, 0) }}
                                </td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('ventas.show', $venta) }}"
                                       class="text-blue-600 hover:underline text-xs">Abonar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t font-semibold">
                            <td colspan="4" class="py-2 text-right">Total por cobrar</td>
                            <td class="py-2 text-right text-red-700">${{ number_format($totalPorCobrar, 0) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        @if ($porEntregar->count())
            <div class="bg-white shadow rounded p-5 border-l-4 border-orange-500">
                <h3 class="font-semibold mb-3">Productos separados sin entregar</h3>
                <table class="w-full text-sm">
                    <thead class="text-gray-500">
                        <tr>
                            <th class="text-left pb-2">Cliente</th>
                            <th class="text-left pb-2">Fecha</th>
                            <th class="text-right pb-2">Saldo</th>
                            <th class="text-right pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($porEntregar as $venta)
                            <tr class="border-t">
                                <td class="py-2">{{ $venta->cliente->nombre ?? 'Sin datos' }}</td>
                                <td class="py-2">{{ $venta->fecha->format('d/m/Y') }}</td>
                                <td class="py-2 text-right">
                                    {{ $venta->saldo > 0
                                       ? '$' . number_format($venta->saldo, 0)
                                       : 'Pagado — listo para entregar' }}
                                </td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('ventas.show', $venta) }}"
                                       class="text-blue-600 hover:underline text-xs">Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

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