<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">Resumen del mes</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        <!-- KPIs -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="card card-body">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                        <x-icon name="banknotes" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium text-slate-500">Ventas del mes</p>
                        <p class="text-xl font-semibold text-slate-800">${{ number_format($ventasMes, 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="card card-body">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <x-icon name="chart-bar" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium text-slate-500">Ganancia del mes</p>
                        <p class="text-xl font-semibold text-emerald-700">${{ number_format($gananciaMes, 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="card card-body">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $totalPorCobrar > 0 ? 'bg-red-50 text-red-600' : 'bg-slate-100 text-slate-400' }}">
                        <x-icon name="exclamation-triangle" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium text-slate-500">Por cobrar</p>
                        <p class="text-xl font-semibold {{ $totalPorCobrar > 0 ? 'text-red-700' : 'text-slate-400' }}">
                            ${{ number_format($totalPorCobrar, 0) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card card-body">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                        <x-icon name="cube" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium text-slate-500">Unidades vendidas</p>
                        <p class="text-xl font-semibold text-slate-800">{{ $unidadesMes }}</p>
                    </div>
                </div>
            </div>

            <div class="card card-body">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                        <x-icon name="inbox" class="h-5 w-5" />
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium text-slate-500">Valor del inventario</p>
                        <p class="text-xl font-semibold text-slate-800">${{ number_format($valorInventario, 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($porCobrar->count())
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pagos pendientes</h3>
                    <x-badge variant="danger">{{ $porCobrar->count() }}</x-badge>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Tipo</th>
                                <th class="text-center">Días</th>
                                <th class="text-right">Saldo</th>
                                <th class="text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($porCobrar as $venta)
                                @php $dias = (int) $venta->fecha->startOfDay()->diffInDays(now()->startOfDay()); @endphp
                                <tr>
                                    <td>
                                        {{ $venta->cliente->nombre ?? 'Sin datos' }}
                                        <span class="block text-xs text-slate-400">
                                            {{ $venta->cliente->cedula ?? '' }}
                                        </span>
                                    </td>
                                    <td>{{ $venta->cliente->telefono ?? '-' }}</td>
                                    <td class="capitalize">{{ $venta->tipo_pago }}</td>
                                    <td class="text-center">
                                        <x-badge :variant="$dias > 30 ? 'danger' : 'neutral'">{{ $dias }}</x-badge>
                                    </td>
                                    <td class="text-right font-medium text-red-700">
                                        ${{ number_format($venta->saldo, 0) }}
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('ventas.show', $venta) }}"
                                           class="text-xs font-medium text-brand-600 hover:text-brand-700">Abonar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-slate-200 bg-slate-50 font-semibold text-slate-700">
                                <td colspan="4" class="px-4 py-2.5 text-right">Total por cobrar</td>
                                <td class="px-4 py-2.5 text-right text-red-700">${{ number_format($totalPorCobrar, 0) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        @if ($porEntregar->count())
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Productos separados sin entregar</h3>
                    <x-badge variant="warning">{{ $porEntregar->count() }}</x-badge>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th class="text-right">Saldo</th>
                                <th class="text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($porEntregar as $venta)
                                <tr>
                                    <td>{{ $venta->cliente->nombre ?? 'Sin datos' }}</td>
                                    <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                                    <td class="text-right">
                                        {{ $venta->saldo > 0
                                           ? '$' . number_format($venta->saldo, 0)
                                           : 'Pagado — listo para entregar' }}
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('ventas.show', $venta) }}"
                                           class="text-xs font-medium text-brand-600 hover:text-brand-700">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if ($stockBajo->count())
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Productos por agotarse</h3>
                    <x-badge variant="danger">{{ $stockBajo->count() }}</x-badge>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockBajo as $producto)
                                <tr>
                                    <td>
                                        {{ $producto->nombre }}
                                        <span class="text-slate-400">{{ $producto->talla }} {{ $producto->color }}</span>
                                    </td>
                                    <td class="text-center">
                                        <x-badge variant="danger">{{ $producto->stock }}</x-badge>
                                    </td>
                                    <td class="text-center text-slate-500">{{ $producto->stock_minimo }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Más vendidos este mes</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($masVendidos as $item)
                        <div class="flex items-center justify-between px-5 py-3 text-sm">
                            <span class="text-slate-600">{{ $item->producto->nombre }}
                                <span class="text-slate-400">{{ $item->producto->talla }}</span>
                            </span>
                            <span class="font-medium text-slate-800">{{ $item->unidades }} und.</span>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-sm text-slate-500">Sin ventas este mes.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Últimas ventas</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($ultimasVentas as $venta)
                        <a href="{{ route('ventas.show', $venta) }}"
                           class="flex items-center justify-between px-5 py-3 text-sm transition-colors hover:bg-slate-50">
                            <span class="text-slate-600">
                                {{ $venta->fecha->format('d/m/Y') }}
                                <span class="text-slate-400">
                                    {{ $venta->cliente->nombre ?? 'Sin datos' }}
                                </span>
                            </span>
                            <span class="font-medium text-slate-800">${{ number_format($venta->total, 0) }}</span>
                        </a>
                    @empty
                        <p class="px-5 py-6 text-center text-sm text-slate-500">Sin ventas registradas.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
