<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detalle de venta</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6">

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
                <div>
                    <span class="text-gray-500">Fecha</span>
                    <p class="font-medium">{{ $venta->fecha->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Cliente</span>
                    <p class="font-medium">{{ $venta->cliente->nombre ?? 'Sin datos' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Cédula</span>
                    <p class="font-medium">{{ $venta->cliente->cedula ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Teléfono</span>
                    <p class="font-medium">{{ $venta->cliente->telefono ?? '-' }}</p>
                </div>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Producto</th>
                        <th class="p-2 text-center">Cantidad</th>
                        <th class="p-2 text-right">Precio</th>
                        <th class="p-2 text-center">Garantía</th>
                        <th class="p-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($venta->detalles as $detalle)
                        <tr class="border-t">
                            <td class="p-2">
                                {{ $detalle->producto->nombre }}
                                <span class="text-gray-500">
                                    {{ $detalle->producto->talla }} {{ $detalle->producto->color }}
                                </span>
                            </td>
                            <td class="p-2 text-center">{{ $detalle->cantidad }}</td>
                            <td class="p-2 text-right">${{ number_format($detalle->precio_venta, 0) }}</td>
                            <td class="p-2 text-center">
                                @if ($detalle->garantia_meses > 0)
                                    @php
                                        $vence = $venta->fecha->copy()->addMonths($detalle->garantia_meses);
                                        $vigente = $vence->isFuture();
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs
                                        {{ $vigente ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $vigente ? 'Vigente hasta' : 'Venció' }} {{ $vence->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="p-2 text-right">${{ number_format($detalle->subtotal, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t font-semibold">
                        <td colspan="4" class="p-2 text-right">Total</td>
                        <td class="p-2 text-right">${{ number_format($venta->total, 0) }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-6">
                <a href="{{ route('ventas.index') }}"
                   class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    Volver
                </a>
            </div>

            <div class="mt-8 border-t pt-6">
                <h3 class="font-semibold mb-3 text-sm">Origen de la mercancía (para garantías)</h3>

                @foreach ($venta->detalles as $detalle)
                    <div class="mb-4">
                        <p class="text-sm font-medium">
                            {{ $detalle->producto->nombre }}
                            <span class="text-gray-500">
                                {{ $detalle->producto->talla }} {{ $detalle->producto->color }}
                            </span>
                        </p>

                        @php
                            $compras = $comprasPorProducto[$detalle->producto_id] ?? collect();
                        @endphp

                        @if ($compras->count())
                            <table class="w-full text-xs mt-1">
                                <thead class="text-gray-500">
                                    <tr>
                                        <th class="text-left py-1">Fecha compra</th>
                                        <th class="text-left py-1">Proveedor</th>
                                        <th class="text-left py-1">Factura</th>
                                        <th class="text-center py-1">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($compras as $c)
                                        <tr class="border-t">
                                            <td class="py-1">{{ $c->compra->fecha->format('d/m/Y') }}</td>
                                            <td class="py-1">{{ $c->compra->proveedor ?? 'Sin registrar' }}</td>
                                            <td class="py-1">{{ $c->compra->factura ?? '-' }}</td>
                                            <td class="py-1 text-center">{{ $c->cantidad }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <p class="text-xs text-gray-500 mt-1">
                                La compra más reciente antes de esta venta es la primera de la lista.
                            </p>
                        @else
                            <p class="text-xs text-gray-500">Sin compras registradas para este producto.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>