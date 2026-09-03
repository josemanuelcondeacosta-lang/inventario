<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detalle de venta</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-100 text-red-800 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm border-t pt-4">
                <div>
                    <span class="text-gray-500">Tipo</span>
                    <p class="font-medium capitalize">{{ $venta->tipo_pago }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Método</span>
                    <p class="font-medium capitalize">{{ $venta->metodo_pago }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Abonado</span>
                    <p class="font-medium text-green-700">${{ number_format($venta->abonado, 0) }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Saldo</span>
                    <p class="font-medium {{ $venta->saldo > 0 ? 'text-red-700' : 'text-gray-400' }}">
                        ${{ number_format($venta->saldo, 0) }}
                    </p>
                </div>
            </div>

            @if ($venta->nota)
                <div class="mb-4 p-3 bg-yellow-50 text-sm rounded">
                    <strong>Nota:</strong> {{ $venta->nota }}
                </div>
            @endif

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
        </div>

        @if ($venta->saldo > 0)
            <div class="bg-white shadow rounded p-6 border-l-4 border-red-500">
                <h3 class="font-semibold mb-1">Registrar abono</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Saldo pendiente: <strong class="text-red-700">${{ number_format($venta->saldo, 0) }}</strong>
                </p>

                <form action="{{ route('ventas.abono', $venta) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium">Monto</label>
                            <input type="number" name="monto" min="1" max="{{ $venta->saldo }}" step="1"
                                   value="{{ old('monto') }}"
                                   class="w-full border-gray-300 rounded text-sm text-right" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Fecha</label>
                            <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}"
                                   class="w-full border-gray-300 rounded text-sm" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Método</label>
                            <select name="metodo_pago" class="w-full border-gray-300 rounded text-sm" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium">Nota</label>
                            <input type="text" name="nota" value="{{ old('nota') }}"
                                   class="w-full border-gray-300 rounded text-sm">
                        </div>

                        
                    </div>

                    <button type="submit"
                            class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                        Registrar abono
                    </button>
                </form>
            </div>
        @endif

        @if (!$venta->entregado)
            <div class="bg-white shadow rounded p-6 border-l-4 border-orange-500">
                <h3 class="font-semibold mb-1">Producto separado</h3>
                @if ($venta->saldo > 0)
                    <p class="text-sm text-gray-600">
                        Se podrá entregar cuando termine de pagar.
                    </p>
                @else
                    <p class="text-sm text-gray-600 mb-3">
                        Ya está totalmente pagado. Puede entregarse.
                    </p>
                    <form action="{{ route('ventas.entregar', $venta) }}" method="POST"
                          onsubmit="return confirm('¿Confirmar la entrega del producto?')">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm">
                            Marcar como entregado
                        </button>
                    </form>
                @endif
            </div>
        @endif

        @if ($venta->abonos->count())
            <div class="bg-white shadow rounded p-6">
                <h3 class="font-semibold mb-3">Historial de pagos</h3>
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Fecha</th>
                            <th class="p-2 text-left">Método</th>
                            <th class="p-2 text-left">Nota</th>
                            <th class="p-2 text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta->abonos->sortBy('fecha') as $abono)
                            <tr class="border-t">
                                <td class="p-2">{{ $abono->fecha->format('d/m/Y') }}</td>
                                <td class="p-2 capitalize">{{ $abono->metodo_pago }}</td>
                                <td class="p-2 text-gray-500">{{ $abono->nota ?? '-' }}</td>
                                <td class="p-2 text-right">${{ number_format($abono->monto, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t font-semibold">
                            <td colspan="3" class="p-2 text-right">Total abonado</td>
                            <td class="p-2 text-right">${{ number_format($venta->abonado, 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        <div class="bg-white shadow rounded p-6">
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
                    @else
                        <p class="text-xs text-gray-500">Sin compras registradas para este producto.</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div>
            <a href="{{ route('ventas.index') }}"
               class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                Volver
            </a>
        </div>
    </div>
</x-app-layout>