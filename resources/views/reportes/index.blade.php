<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Reportes</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="bg-white shadow rounded p-5 no-print">
            <form method="GET" action="{{ route('reportes.index') }}"
                  class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block mb-1 text-sm font-medium">Desde</label>
                    <input type="date" name="desde" value="{{ $desde->format('Y-m-d') }}"
                           class="border-gray-300 rounded text-sm">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Hasta</label>
                    <input type="date" name="hasta" value="{{ $hasta->format('Y-m-d') }}"
                           class="border-gray-300 rounded text-sm">
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    Consultar
                </button>
                <button type="button" onclick="window.print()"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm">
                    Imprimir / PDF
                </button>
            </form>
        </div>

        <div class="text-sm text-gray-600">
            Período: {{ $desde->format('d/m/Y') }} — {{ $hasta->format('d/m/Y') }}
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white shadow rounded p-4">
                <p class="text-xs text-gray-500">Ventas</p>
                <p class="text-xl font-bold">${{ number_format($totalVentas, 0) }}</p>
            </div>
            <div class="bg-white shadow rounded p-4">
                <p class="text-xs text-gray-500">Ganancia</p>
                <p class="text-xl font-bold text-green-700">${{ number_format($ganancia, 0) }}</p>
            </div>
            <div class="bg-white shadow rounded p-4">
                <p class="text-xs text-gray-500">Compras</p>
                <p class="text-xl font-bold text-orange-700">${{ number_format($totalCompras, 0) }}</p>
            </div>
            <div class="bg-white shadow rounded p-4">
                <p class="text-xs text-gray-500">N° de ventas</p>
                <p class="text-xl font-bold">{{ $numVentas }}</p>
            </div>
            <div class="bg-white shadow rounded p-4">
                <p class="text-xs text-gray-500">Unidades</p>
                <p class="text-xl font-bold">{{ $unidades }}</p>
            </div>
        </div>

        <div class="bg-white shadow rounded p-5">
            <h3 class="font-semibold mb-3">Ventas por producto</h3>
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Producto</th>
                        <th class="p-2 text-left">Categoría</th>
                        <th class="p-2 text-center">Unidades</th>
                        <th class="p-2 text-right">Ingresos</th>
                        <th class="p-2 text-right">Ganancia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($porProducto as $item)
                        <tr class="border-t">
                            <td class="p-2">
                                {{ $item->producto->nombre }}
                                <span class="text-gray-500">
                                    {{ $item->producto->talla }} {{ $item->producto->color }}
                                </span>
                            </td>
                            <td class="p-2">{{ $item->producto->categoria->nombre }}</td>
                            <td class="p-2 text-center">{{ $item->unidades }}</td>
                            <td class="p-2 text-right">${{ number_format($item->ingresos, 0) }}</td>
                            <td class="p-2 text-right text-green-700">
                                ${{ number_format($item->ganancia, 0) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">
                                No hay ventas en este período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                 @if ($porProducto->count())
                    <tfoot>
                        <tr class="border-t font-semibold bg-gray-50">
                            <td colspan="2" class="p-2 text-right">Totales</td>
                            <td class="p-2 text-center">{{ $porProducto->sum('unidades') }}</td>
                            <td class="p-2 text-right">${{ number_format($porProducto->sum('ingresos'), 0) }}</td>
                            <td class="p-2 text-right text-green-700">
                                ${{ number_format($porProducto->sum('ganancia'), 0) }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="bg-white shadow rounded p-5">
            <h3 class="font-semibold mb-3">Inventario actual</h3>
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Producto</th>
                        <th class="p-2 text-left">Categoría</th>
                        <th class="p-2 text-center">Stock</th>
                        <th class="p-2 text-right">Costo unit.</th>
                        <th class="p-2 text-right">Valor total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventario as $producto)
                        <tr class="border-t">
                            <td class="p-2">
                                {{ $producto->nombre }}
                                <span class="text-gray-500">
                                    {{ $producto->talla }} {{ $producto->color }}
                                </span>
                            </td>
                            <td class="p-2">{{ $producto->categoria->nombre }}</td>
                            <td class="p-2 text-center">
                                <span class="px-2 py-1 rounded text-xs
                                    {{ $producto->stock <= $producto->stock_minimo
                                       ? 'bg-red-100 text-red-800'
                                       : 'bg-green-100 text-green-800' }}">
                                    {{ $producto->stock }}
                                </span>
                            </td>
                            <td class="p-2 text-right">${{ number_format($producto->precio_compra, 0) }}</td>
                            <td class="p-2 text-right">
                                ${{ number_format($producto->stock * $producto->precio_compra, 0) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    

                    <tr class="border-t font-semibold">
                        <td colspan="4" class="p-2 text-right">Valor total del inventario</td>
                        <td class="p-2 text-right">
                            ${{ number_format($inventario->sum(fn($p) => $p->stock * $p->precio_compra), 0) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <style>
        @media print {
            .no-print, nav, header { display: none !important; }
            body { background: white; }
        }
    </style>
</x-app-layout>