<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-white">Resumen del mes</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        <!-- Cifras principales -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">

            <div class="card card-body sm:col-span-1">
                <p class="cifra-label">Ventas del mes</p>
                <p class="cifra mt-2">${{ number_format($ventasMes, 0) }}</p>
                <p class="mt-1 text-xs text-ink-400">{{ $unidadesMes }} unidades vendidas</p>
            </div>

            <div class="card card-body relative overflow-hidden">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-brand-400/10"></div>
                <p class="cifra-label">Ganancia del mes</p>
                <p class="cifra-brand mt-2">${{ number_format($gananciaMes, 0) }}</p>
                <p class="mt-1 text-xs text-ink-400">ya descontado el costo</p>
            </div>

            <div class="card card-body">
                <p class="cifra-label">Por cobrar</p>
                <p class="cifra mt-2 {{ $totalPorCobrar > 0 ? 'text-coral-400' : 'text-ink-400' }}">
                    ${{ number_format($totalPorCobrar, 0) }}
                </p>
                <p class="mt-1 text-xs text-ink-400">
                    {{ $porCobrar->count() }} {{ $porCobrar->count() == 1 ? 'cliente' : 'clientes' }}
                </p>
            </div>

            <div class="card card-body">
                <p class="cifra-label">Valor del inventario</p>
                <p class="cifra mt-2">${{ number_format($valorInventario, 0) }}</p>
                <p class="mt-1 text-xs text-ink-400">al costo de compra</p>
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
                                        <span class="font-medium text-white">{{ $venta->cliente->nombre ?? 'Sin datos' }}</span>
                                        <span class="block text-xs text-ink-400">
                                            {{ $venta->cliente->cedula ?? '' }}
                                        </span>
                                    </td>
                                    <td>{{ $venta->cliente->telefono ?? '-' }}</td>
                                    <td class="capitalize">{{ $venta->tipo_pago }}</td>
                                    <td class="text-center">
                                        <x-badge :variant="$dias > 30 ? 'danger' : 'neutral'">
                                            {{ $dias == 0 ? 'Hoy' : $dias . ' días' }}
                                        </x-badge>
                                    </td>
                                    <td class="text-right font-display font-semibold text-coral-400">
                                        ${{ number_format($venta->saldo, 0) }}
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('ventas.show', $venta) }}"
                                           class="text-xs font-medium text-brand-400 hover:text-brand-300">Abonar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right">Total por cobrar</td>
                                <td class="px-4 py-3 text-right text-coral-400">${{ number_format($totalPorCobrar, 0) }}</td>
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
                    <h3 class="card-title">Separados sin entregar</h3>
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
                                    <td class="font-medium text-white">{{ $venta->cliente->nombre ?? 'Sin datos' }}</td>
                                    <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                                    <td class="text-right">
                                        @if ($venta->saldo > 0)
                                            <span class="font-display font-semibold text-coral-400">
                                                ${{ number_format($venta->saldo, 0) }}
                                            </span>
                                        @else
                                            <span class="text-brand-400">Pagado — listo para entregar</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('ventas.show', $venta) }}"
                                           class="text-xs font-medium text-brand-400 hover:text-brand-300">Ver</a>
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
                                        <span class="font-medium text-white">{{ $producto->nombre }}</span>
                                        <span class="text-ink-400">{{ $producto->talla }} {{ $producto->color }}</span>
                                    </td>
                                    <td class="text-center">
                                        <x-badge variant="danger">{{ $producto->stock }}</x-badge>
                                    </td>
                                    <td class="text-center text-ink-400">{{ $producto->stock_minimo }}</td>
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
                <div class="divide-y divide-ink-700">
                    @forelse ($masVendidos as $item)
                        @php
                            $tope = $masVendidos->max('unidades') ?: 1;
                            $pct = round($item->unidades / $tope * 100);
                        @endphp
                        <div class="px-5 py-3.5">
                            <div class="mb-2 flex items-baseline justify-between text-sm">
                                <span class="text-white">{{ $item->producto->nombre }}
                                    <span class="text-ink-400">{{ $item->producto->talla }}</span>
                                </span>
                                <span class="font-display font-semibold text-brand-400">{{ $item->unidades }}</span>
                            </div>
                            <div class="h-1 w-full overflow-hidden rounded-full bg-ink-700">
                                <div class="h-full rounded-full bg-brand-400" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-sm text-ink-400">Sin ventas este mes.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Últimas ventas</h3>
                </div>
                <div class="divide-y divide-ink-700">
                    @forelse ($ultimasVentas as $venta)
                        <a href="{{ route('ventas.show', $venta) }}"
                           class="flex items-center justify-between px-5 py-3.5 text-sm transition-colors hover:bg-white/[0.03]">
                            <span>
                                <span class="text-white">{{ $venta->cliente->nombre ?? 'Sin datos' }}</span>
                                <span class="block text-xs text-ink-400">{{ $venta->fecha->format('d/m/Y') }}</span>
                            </span>
                            <span class="font-display font-semibold text-white">${{ number_format($venta->total, 0) }}</span>
                        </a>
                    @empty
                        <p class="px-5 py-8 text-center text-sm text-ink-400">Sin ventas registradas.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>