<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-texto">Reclamos de garantía</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-4 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <x-alert variant="success">{{ session('success') }}</x-alert>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="card card-body">
                <p class="cifra-label">Pérdida del mes</p>
                <p class="cifra mt-2 {{ $perdidaMes > 0 ? 'text-alerta' : 'text-tenue' }}">
                    ${{ number_format($perdidaMes, 0) }}
                </p>
                <p class="mt-1 text-xs text-tenue">por garantías atendidas</p>
            </div>
            <div class="card card-body">
                <p class="cifra-label">Reclamos registrados</p>
                <p class="cifra mt-2">{{ $reclamos->total() }}</p>
                <p class="mt-1 text-xs text-tenue">en total</p>
            </div>
            <div class="card card-body flex items-center justify-center">
                <p class="text-center text-xs text-tenue">
                    Los reclamos se registran desde el detalle de cada venta.
                </p>
            </div>
        </div>

        <div class="flex justify-end">
            <form method="GET" action="{{ route('reclamos.index') }}" class="relative">
                <x-icon name="magnifying-glass" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-tenue" />
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Buscar por cédula o nombre..."
                       class="field-input w-72 py-2 pl-9 pr-3">
            </form>
        </div>

        @if ($reclamos->isEmpty())
            <div class="card flex flex-col items-center gap-3 px-4 py-14 text-center">
                <p class="font-medium text-suave">No hay reclamos registrados</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Prenda reclamada</th>
                            <th>Resultado</th>
                            <th>Se entregó</th>
                            <th class="text-right">Pérdida</th>
                            <th class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reclamos as $r)
                            <tr>
                                <td>{{ $r->fecha->format('d/m/Y') }}</td>
                                <td>
                                    <span class="font-medium text-texto">
                                        {{ $r->venta->cliente->nombre ?? 'Sin datos' }}
                                    </span>
                                    <span class="block text-xs text-tenue">
                                        {{ $r->venta->cliente->cedula ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $r->detalle->producto->nombre }}
                                    <span class="text-tenue">
                                        {{ $r->detalle->producto->talla }} {{ $r->detalle->producto->color }}
                                    </span>
                                    <span class="block text-xs text-tenue">{{ $r->cantidad }} und.</span>
                                </td>
                                <td>
                                    <x-badge :variant="match($r->resultado) {
                                        'proveedor' => 'success',
                                        'rechazado' => 'neutral',
                                        'devolucion' => 'warning',
                                        default => 'info',
                                    }">
                                        {{ $r->resultadoTexto() }}
                                    </x-badge>
                                </td>
                                <td>
                                    @if ($r->productoEntregado)
                                        {{ $r->productoEntregado->nombre }}
                                        <span class="text-tenue">{{ $r->productoEntregado->talla }}</span>
                                    @else
                                        <span class="text-tenue">-</span>
                                    @endif
                                </td>
                                <td class="text-right {{ $r->perdida > 0 ? 'font-display font-semibold text-alerta' : 'text-tenue' }}">
                                    {{ $r->perdida > 0 ? '$' . number_format($r->perdida, 0) : '-' }}
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('ventas.show', $r->venta) }}"
                                       class="text-xs font-medium text-brand hover:opacity-80">Ver venta</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($reclamos->hasPages())
            <div>{{ $reclamos->links() }}</div>
        @endif
    </div>
</x-app-layout>