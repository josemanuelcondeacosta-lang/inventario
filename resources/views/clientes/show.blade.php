<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">Historial del cliente</h2>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <x-alert variant="success">{{ session('success') }}</x-alert>
        @endif

        <div class="card card-body">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="grid grid-cols-2 gap-6 text-sm md:grid-cols-3">
                    <div>
                        <span class="text-slate-500">Nombre</span>
                        <p class="font-medium text-slate-800">{{ $cliente->nombre }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Cédula</span>
                        <p class="font-medium text-slate-800">{{ $cliente->cedula }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Teléfono</span>
                        <p class="font-medium text-slate-800">{{ $cliente->telefono ?? '-' }}</p>
                    </div>
                </div>

                <a href="{{ route('clientes.edit', $cliente) }}" class="btn-secondary">
                    Editar datos
                </a>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4 border-t border-slate-100 pt-4 text-sm md:grid-cols-3">
                <div>
                    <span class="text-slate-500">Compras realizadas</span>
                    <p class="text-lg font-semibold text-slate-800">
                        {{ $cliente->ventas->where('anulada', false)->count() }}
                    </p>
                </div>
                <div>
                    <span class="text-slate-500">Total comprado</span>
                    <p class="text-lg font-semibold text-slate-800">${{ number_format($totalComprado, 0) }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Saldo pendiente</span>
                    <p class="text-lg font-semibold {{ $saldoTotal > 0 ? 'text-red-700' : 'text-slate-400' }}">
                        ${{ number_format($saldoTotal, 0) }}
                    </p>
                </div>
            </div>
        </div>

        @forelse ($cliente->ventas as $venta)
            <div class="card {{ $venta->anulada ? 'opacity-60' : '' }}">
                <div class="card-header flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <h3 class="card-title">Venta del {{ $venta->fecha->format('d/m/Y') }}</h3>
                        @php $estado = $venta->estadoTexto(); @endphp
                        <x-badge :variant="match($estado) {
                            'Pagada' => 'success',
                            'Debe' => 'danger',
                            'Separado' => 'warning',
                            'Anulada' => 'neutral',
                            default => 'info',
                        }">{{ $estado }}</x-badge>
                    </div>

                    <div class="flex items-center gap-3 text-sm">
                        <span class="font-semibold text-slate-800">${{ number_format($venta->total, 0) }}</span>
                        <a href="{{ route('ventas.show', $venta) }}"
                           class="text-brand-600 hover:text-brand-700">Ver venta</a>
                    </div>
                </div>

                <div class="table-wrap border-0 shadow-none">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-right">Precio</th>
                                <th class="text-center">Garantía</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($venta->detalles as $detalle)
                                <tr>
                                    <td>
                                        {{ $detalle->producto->nombre }}
                                        <span class="text-slate-400">
                                            {{ $detalle->producto->talla }} {{ $detalle->producto->color }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td class="text-right">${{ number_format($detalle->precio_venta, 0) }}</td>
                                    <td class="text-center">
                                        @if ($detalle->garantia_meses > 0)
                                            @php
                                                $vence = $venta->fecha->copy()->addMonths($detalle->garantia_meses);
                                                $vigente = $vence->isFuture() && !$venta->anulada;
                                            @endphp
                                            <x-badge :variant="$vigente ? 'success' : 'neutral'">
                                                {{ $vigente ? 'Vigente hasta' : 'Venció' }} {{ $vence->format('d/m/Y') }}
                                            </x-badge>
                                        @else
                                            <span class="text-slate-400">Sin garantía</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="card card-body text-center text-slate-500">
                Este cliente no tiene ventas registradas.
            </div>
        @endforelse

        <div>
            <a href="{{ route('clientes.index') }}" class="btn-secondary">
                <x-icon name="chevron-left" class="h-4 w-4" /> Volver
            </a>
        </div>
    </div>
</x-app-layout>