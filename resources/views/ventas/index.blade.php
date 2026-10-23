<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-texto">Ventas</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-4 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <x-alert variant="success">{{ session('success') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert variant="error">{{ session('error') }}</x-alert>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('ventas.create') }}" class="btn-primary">
                    <x-icon name="plus" class="h-4 w-4" />
                    Registrar venta
                </a>

                <div class="ml-2 flex flex-wrap gap-1 rounded-lg bg-elevado p-1">
                    <a href="{{ route('ventas.index') }}"
                       class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ !request('estado') ? 'bg-superficie text-texto shadow-soft' : 'text-tenue hover:text-suave' }}">
                        Todas
                    </a>
                    <a href="{{ route('ventas.index', ['estado' => 'pendientes']) }}"
                       class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ request('estado') == 'pendientes' ? 'bg-superficie text-texto shadow-soft' : 'text-tenue hover:text-suave' }}">
                        Con saldo
                    </a>
                    <a href="{{ route('ventas.index', ['estado' => 'por_entregar']) }}"
                       class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ request('estado') == 'por_entregar' ? 'bg-superficie text-texto shadow-soft' : 'text-tenue hover:text-suave' }}">
                        Por entregar
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('ventas.index') }}" class="relative">
                @if (request('estado'))
                    <input type="hidden" name="estado" value="{{ request('estado') }}">
                @endif
                <x-icon name="magnifying-glass" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-tenue" />
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Buscar por cédula o nombre..."
                       class="field-input w-64 py-2 pl-9 pr-3">
                @if (request('buscar'))
                    <a href="{{ route('ventas.index', array_filter(['estado' => request('estado')])) }}"
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-tenue hover:text-suave">
                        <x-icon name="x-mark" class="h-4 w-4" />
                    </a>
                @endif
            </form>
        </div>

        @if ($ventas->isEmpty())
            <div class="card flex flex-col items-center gap-3 px-4 py-14 text-center">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-elevado text-tenue">
                    <x-icon name="banknotes" class="h-6 w-6" />
                </span>
                <p class="font-medium text-suave">No hay ventas que coincidan</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Saldo</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ventas as $venta)
                            <tr class="{{ $venta->anulada ? 'opacity-50' : '' }}">
                                <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                                <td>
                                    {{ $venta->cliente->nombre ?? 'Sin datos' }}
                                    <span class="block text-xs text-tenue">
                                        {{ $venta->cliente->cedula ?? '' }}
                                    </span>
                                </td>
                                <td class="capitalize">{{ $venta->tipo_pago }}</td>
                                <td>
                                    @php $estado = $venta->estadoTexto(); @endphp
                                    <x-badge :variant="match($estado) {
                                        'Pagada' => 'success',
                                        'Debe' => 'danger',
                                        'Separado' => 'warning',
                                        'Anulada' => 'neutral',
                                        default => 'info',
                                    }">
                                        {{ $estado }}
                                    </x-badge>
                                </td>
                                <td class="text-right">${{ number_format($venta->total, 0) }}</td>
                                <td class="text-right {{ $venta->saldo > 0 ? 'font-medium text-alerta' : 'text-tenue' }}">
                                    {{ $venta->saldo > 0 ? '$' . number_format($venta->saldo, 0) : '-' }}
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('ventas.show', $venta) }}"
                                           class="inline-flex items-center gap-1 text-brand hover:text-brand">
                                            <x-icon name="eye" class="h-4 w-4" /> Ver
                                        </a>
                                        <a href="{{ route('ventas.factura', $venta) }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-tenue hover:text-suave">
                                            Factura
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($ventas->hasPages())
            <div>{{ $ventas->links() }}</div>
        @endif
    </div>
</x-app-layout>
