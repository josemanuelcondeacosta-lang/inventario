<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">Compras</h2>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-4 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <x-alert variant="success">{{ session('success') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert variant="error">{{ session('error') }}</x-alert>
        @endif

        <div class="flex items-center justify-between gap-4">
            <p class="text-sm text-slate-500">
                <span class="font-semibold text-slate-800">{{ $compras->total() }}</span>
                {{ Str::plural('compra', $compras->total()) }} registradas
            </p>
            <a href="{{ route('compras.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Registrar compra
            </a>
        </div>

        @if ($compras->isEmpty())
            <div class="card flex flex-col items-center gap-3 px-4 py-14 text-center">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <x-icon name="truck" class="h-6 w-6" />
                </span>
                <div>
                    <p class="font-medium text-slate-700">No hay compras registradas</p>
                    <p class="mt-1 text-sm text-slate-500">Registra tu primera compra de mercancía.</p>
                </div>
                <a href="{{ route('compras.create') }}" class="btn-primary">
                    <x-icon name="plus" class="h-4 w-4" /> Registrar compra
                </a>
            </div>
        @else
            <div class="table-wrap">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Factura</th>
                            <th>Estado</th>
                            <th class="text-center">Productos</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compras as $compra)
                            <tr class="{{ $compra->anulada ? 'opacity-50' : '' }}">
                                <td>{{ $compra->fecha->format('d/m/Y') }}</td>
                                <td>{{ $compra->proveedor ?? '-' }}</td>
                                <td>{{ $compra->factura ?? '-' }}</td>
                                <td>
                                    @if ($compra->anulada)
                                        <x-badge variant="neutral">Anulada</x-badge>
                                    @else
                                        <x-badge variant="success">Activa</x-badge>
                                    @endif
                                </td>
                                <td class="text-center">{{ $compra->detalles_count }}</td>
                                <td class="text-right">${{ number_format($compra->total, 0) }}</td>
                                <td class="text-right">
                                    <a href="{{ route('compras.show', $compra) }}"
                                       class="inline-flex items-center gap-1 text-brand-600 hover:text-brand-700">
                                        <x-icon name="eye" class="h-4 w-4" /> Ver detalle
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($compras->hasPages())
            <div>{{ $compras->links() }}</div>
        @endif
    </div>
</x-app-layout>
