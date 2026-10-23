<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-texto">Reportes</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        <div class="card card-body no-print">
            <form method="GET" action="{{ route('reportes.index') }}"
                  class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="desde" class="field-label">Desde</label>
                    <input type="date" id="desde" name="desde" value="{{ $desde->format('Y-m-d') }}"
                           class="field-input">
                </div>
                <div>
                    <label for="hasta" class="field-label">Hasta</label>
                    <input type="date" id="hasta" name="hasta" value="{{ $hasta->format('Y-m-d') }}"
                           class="field-input">
                </div>
                <button type="submit" class="btn-primary">
                    Consultar
                </button>
                <button type="button" onclick="window.print()" class="btn-secondary">
                    Imprimir / PDF
                </button>
            </form>
        </div>

        <p class="text-sm text-tenue">
            Período: <span class="font-medium text-suave">{{ $desde->format('d/m/Y') }} — {{ $hasta->format('d/m/Y') }}</span>
        </p>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
            <div class="card card-body">
                <p class="text-xs font-medium text-tenue">Ventas</p>
                <p class="text-xl font-semibold text-texto">${{ number_format($totalVentas, 0) }}</p>
            </div>
            <div class="card card-body">
                <p class="cifra-label">Ganancia</p>
                <p class="cifra mt-2 text-brand">${{ number_format($gananciaNeta, 0) }}</p>
            </div>
            <div class="card card-body">
                <p class="cifra-label">Garantías</p>
                <p class="cifra mt-2 {{ $perdidaGarantias > 0 ? 'text-alerta' : 'text-tenue' }}">
                    ${{ number_format($perdidaGarantias, 0) }}
                </p>
            </div>
            <div class="card card-body">
                <p class="text-xs font-medium text-tenue">Compras</p>
                <p class="text-xl font-semibold text-aviso">${{ number_format($totalCompras, 0) }}</p>
            </div>
            <div class="card card-body">
                <p class="text-xs font-medium text-tenue">N° de ventas</p>
                <p class="text-xl font-semibold text-texto">{{ $numVentas }}</p>
            </div>
            <div class="card card-body">
                <p class="text-xs font-medium text-tenue">Unidades</p>
                <p class="text-xl font-semibold text-texto">{{ $unidades }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ventas por producto</h3>
            </div>
            <div class="table-wrap border-0 shadow-none">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-center">Unidades</th>
                            <th class="text-right">Ingresos</th>
                            <th class="text-right">Ganancia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($porProducto as $item)
                            <tr>
                                <td>
                                    {{ $item->producto->nombre }}
                                    <span class="text-tenue">
                                        {{ $item->producto->talla }} {{ $item->producto->color }}
                                    </span>
                                </td>
                                <td>{{ $item->producto->categoria->nombre }}</td>
                                <td class="text-center">{{ $item->unidades }}</td>
                                <td class="text-right">${{ number_format($item->ingresos, 0) }}</td>
                                <td class="text-right text-brand">
                                    ${{ number_format($item->ganancia, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-tenue">
                                    No hay ventas en este período.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($porProducto->count())
                        <tfoot>
                            <tr class="border-t border-borde bg-elevado font-semibold text-suave">
                                <td colspan="2" class="px-4 py-2.5 text-right">Totales</td>
                                <td class="px-4 py-2.5 text-center">{{ $porProducto->sum('unidades') }}</td>
                                <td class="px-4 py-2.5 text-right">${{ number_format($porProducto->sum('ingresos'), 0) }}</td>
                                <td class="px-4 py-2.5 text-right text-brand">
                                    ${{ number_format($porProducto->sum('ganancia'), 0) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Inventario actual</h3>
            </div>
            <div class="table-wrap border-0 shadow-none">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-center">Stock</th>
                            <th class="text-right">Costo unit.</th>
                            <th class="text-right">Valor total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventario as $producto)
                            <tr>
                                <td>
                                    {{ $producto->nombre }}
                                    <span class="text-tenue">
                                        {{ $producto->talla }} {{ $producto->color }}
                                    </span>
                                </td>
                                <td>{{ $producto->categoria->nombre }}</td>
                                <td class="text-center">
                                    @if ($producto->stock <= $producto->stock_minimo)
                                        <x-badge variant="danger">{{ $producto->stock }}</x-badge>
                                    @else
                                        <x-badge variant="success">{{ $producto->stock }}</x-badge>
                                    @endif
                                </td>
                                <td class="text-right">${{ number_format($producto->precio_compra, 0) }}</td>
                                <td class="text-right">
                                    ${{ number_format($producto->stock * $producto->precio_compra, 0) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-borde bg-elevado font-semibold text-suave">
                            <td colspan="4" class="px-4 py-2.5 text-right">Valor total del inventario</td>
                            <td class="px-4 py-2.5 text-right">
                                ${{ number_format($inventario->sum(fn($p) => $p->stock * $p->precio_compra), 0) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print, aside, header { display: none !important; }
            body { background: white; }
        }
    </style>
</x-app-layout>
