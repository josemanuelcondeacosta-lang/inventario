<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-texto">Clientes</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-4 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <x-alert variant="success">{{ session('success') }}</x-alert>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-tenue">
                Los clientes se registran automáticamente al hacer una venta con cédula.
            </p>

            <form method="GET" action="{{ route('clientes.index') }}" class="relative">
                <x-icon name="magnifying-glass" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-tenue" />
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Buscar por cédula, nombre o teléfono..."
                       class="field-input w-72 py-2 pl-9 pr-3">
            </form>
        </div>

        @if ($clientes->isEmpty())
            <div class="card flex flex-col items-center gap-3 px-4 py-14 text-center">
                <p class="font-medium text-suave">No hay clientes registrados</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th class="text-center">Compras</th>
                            <th class="text-right">Total comprado</th>
                            <th class="text-right">Saldo</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->cedula }}</td>
                                <td>{{ $cliente->nombre }}</td>
                                <td>{{ $cliente->telefono ?? '-' }}</td>
                                <td class="text-center">{{ $cliente->ventas_count }}</td>
                                <td class="text-right">${{ number_format($cliente->ventas_sum_total ?? 0, 0) }}</td>
                                <td class="text-right {{ ($cliente->ventas_sum_saldo ?? 0) > 0 ? 'font-medium text-alerta' : 'text-tenue' }}">
                                    {{ ($cliente->ventas_sum_saldo ?? 0) > 0
                                       ? '$' . number_format($cliente->ventas_sum_saldo, 0)
                                       : '-' }}
                                </td>
                                <td class="whitespace-nowrap text-right">
                                    <a href="{{ route('clientes.show', $cliente) }}"
                                       class="inline-flex items-center gap-1 text-brand hover:text-brand">
                                        <x-icon name="eye" class="h-4 w-4" /> Ver historial
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($clientes->hasPages())
            <div>{{ $clientes->links() }}</div>
        @endif
    </div>
</x-app-layout>