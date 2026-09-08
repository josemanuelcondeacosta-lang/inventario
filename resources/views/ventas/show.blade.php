<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">Detalle de venta</h2>
    </x-slot>

    <div class="mx-auto max-w-4xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <x-alert variant="success">{{ session('success') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert variant="error">{{ session('error') }}</x-alert>
        @endif

        @if ($errors->any())
            <x-alert variant="error">
                <ul class="list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <div class="card card-body">

            <div class="mb-6 grid grid-cols-2 gap-4 text-sm md:grid-cols-4">
                <div>
                    <span class="text-slate-500">Fecha</span>
                    <p class="font-medium text-slate-800">{{ $venta->fecha->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Cliente</span>
                    <p class="font-medium text-slate-800">{{ $venta->cliente->nombre ?? 'Sin datos' }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Cédula</span>
                    <p class="font-medium text-slate-800">{{ $venta->cliente->cedula ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Teléfono</span>
                    <p class="font-medium text-slate-800">{{ $venta->cliente->telefono ?? '-' }}</p>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-2 gap-4 border-t border-slate-100 pt-4 text-sm md:grid-cols-4">
                <div>
                    <span class="text-slate-500">Tipo</span>
                    <p class="font-medium capitalize text-slate-800">{{ $venta->tipo_pago }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Método</span>
                    <p class="font-medium capitalize text-slate-800">{{ $venta->metodo_pago }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Abonado</span>
                    <p class="font-medium text-emerald-700">${{ number_format($venta->abonado, 0) }}</p>
                </div>
                <div>
                    <span class="text-slate-500">Saldo</span>
                    <p class="font-medium {{ $venta->saldo > 0 ? 'text-red-700' : 'text-slate-400' }}">
                        ${{ number_format($venta->saldo, 0) }}
                    </p>
                </div>
            </div>

            @if ($venta->nota)
                <x-alert variant="info" class="mb-4">
                    <strong>Nota:</strong> {{ $venta->nota }}
                </x-alert>
            @endif

            <div class="table-wrap">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Precio</th>
                            <th class="text-center">Garantía</th>
                            <th class="text-right">Subtotal</th>
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
                                            $vigente = $vence->isFuture();
                                        @endphp
                                        <x-badge :variant="$vigente ? 'success' : 'neutral'">
                                            {{ $vigente ? 'Vigente hasta' : 'Venció' }} {{ $vence->format('d/m/Y') }}
                                        </x-badge>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="text-right">${{ number_format($detalle->subtotal, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-slate-200 bg-slate-50 font-semibold text-slate-700">
                            <td colspan="4" class="px-4 py-2.5 text-right">Total</td>
                            <td class="px-4 py-2.5 text-right">${{ number_format($venta->total, 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if ($venta->saldo > 0 && !$venta->anulada)
            <div class="card card-body border-l-4 border-l-red-500">
                <h3 class="font-semibold text-slate-800">Registrar abono</h3>
                <p class="mb-4 text-sm text-slate-500">
                    Saldo pendiente: <strong class="text-red-700">${{ number_format($venta->saldo, 0) }}</strong>
                </p>

                <form action="{{ route('ventas.abono', $venta) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <label for="monto" class="field-label">Monto</label>
                            <input type="number" id="monto" name="monto" min="1" max="{{ $venta->saldo }}" step="1"
                                   value="{{ old('monto') }}"
                                   class="field-input text-right" required>
                        </div>
                        <div>
                            <label for="fecha_abono" class="field-label">Fecha</label>
                            <input type="date" id="fecha_abono" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}"
                                   class="field-input" required>
                        </div>
                        <div>
                            <label for="metodo_pago_abono" class="field-label">Método</label>
                            <select id="metodo_pago_abono" name="metodo_pago" class="field-input" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                        <div>
                            <label for="nota_abono" class="field-label">Nota</label>
                            <input type="text" id="nota_abono" name="nota" value="{{ old('nota') }}" class="field-input">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary mt-4">
                        Registrar abono
                    </button>
                </form>
            </div>
        @endif

        @if (!$venta->entregado && !$venta->anulada)
            <div class="card card-body border-l-4 border-l-amber-500">
                <h3 class="font-semibold text-slate-800">Producto separado</h3>
                @if ($venta->saldo > 0)
                    <p class="mt-1 text-sm text-slate-600">
                        Se podrá entregar cuando termine de pagar.
                    </p>
                @else
                    <p class="mb-3 mt-1 text-sm text-slate-600">
                        Ya está totalmente pagado. Puede entregarse.
                    </p>
                    <form action="{{ route('ventas.entregar', $venta) }}" method="POST"
                          onsubmit="return confirm('¿Confirmar la entrega del producto?')">
                        @csrf
                        <button type="submit" class="btn-primary bg-amber-600 hover:bg-amber-700 focus:ring-amber-500">
                            Marcar como entregado
                        </button>
                    </form>
                @endif
            </div>
        @endif

        @if ($venta->abonos->count())
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Historial de pagos</h3>
                </div>
                <div class="table-wrap border-0 shadow-none">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Método</th>
                                <th>Nota</th>
                                <th class="text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($venta->abonos->sortBy('fecha') as $abono)
                                <tr>
                                    <td>{{ $abono->fecha->format('d/m/Y') }}</td>
                                    <td class="capitalize">{{ $abono->metodo_pago }}</td>
                                    <td class="text-slate-400">{{ $abono->nota ?? '-' }}</td>
                                    <td class="text-right">${{ number_format($abono->monto, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-slate-200 bg-slate-50 font-semibold text-slate-700">
                                <td colspan="3" class="px-4 py-2.5 text-right">Total abonado</td>
                                <td class="px-4 py-2.5 text-right">${{ number_format($venta->abonado, 0) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Origen de la mercancía (para garantías)</h3>
            </div>
            <div class="card-body space-y-5">
                @foreach ($venta->detalles as $detalle)
                    <div>
                        <p class="text-sm font-medium text-slate-800">
                            {{ $detalle->producto->nombre }}
                            <span class="font-normal text-slate-400">
                                {{ $detalle->producto->talla }} {{ $detalle->producto->color }}
                            </span>
                        </p>

                        @php
                            $compras = $comprasPorProducto[$detalle->producto_id] ?? collect();
                        @endphp

                        @if ($compras->count())
                            <div class="mt-2 overflow-x-auto rounded-lg border border-slate-100">
                                <table class="table-base">
                                    <thead>
                                        <tr>
                                            <th>Fecha compra</th>
                                            <th>Proveedor</th>
                                            <th>Factura</th>
                                            <th class="text-center">Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($compras as $c)
                                            <tr>
                                                <td>{{ $c->compra->fecha->format('d/m/Y') }}</td>
                                                <td>{{ $c->compra->proveedor ?? 'Sin registrar' }}</td>
                                                <td>{{ $c->compra->factura ?? '-' }}</td>
                                                <td class="text-center">{{ $c->cantidad }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mt-1 text-xs text-slate-500">Sin compras registradas para este producto.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

                @if ($venta->anulada)
            <div class="card card-body border-l-4 border-l-slate-500">
                <h3 class="font-semibold text-slate-800">Venta anulada</h3>
                <p class="mt-1 text-sm text-slate-600">
                    Fecha: {{ $venta->anulada_at->format('d/m/Y H:i') }}<br>
                    Motivo: {{ $venta->motivo_anulacion }}
                </p>
            </div>
        @else
            <div class="card card-body">
                <details>
                    <summary class="cursor-pointer text-sm text-red-600 hover:underline">
                        Anular esta venta
                    </summary>

                    <div class="mt-3 rounded-lg bg-red-50 p-4">
                        <p class="mb-3 text-sm text-slate-700">
                            Al anular, los productos volverán al inventario y el saldo pendiente se cancelará.
                            La venta quedará registrada como anulada.
                        </p>

                        <form action="{{ route('ventas.anular', $venta) }}" method="POST"
                              onsubmit="return confirm('¿Seguro que desea anular esta venta?')">
                            @csrf
                            <label for="motivo" class="field-label">Motivo de la anulación</label>
                            <input type="text" id="motivo" name="motivo" required maxlength="200"
                                   placeholder="Ej: el cliente devolvió la mercancía"
                                   class="field-input mb-3">

                            <button type="submit" class="btn-primary bg-red-600 hover:bg-red-700 focus:ring-red-500">
                                Confirmar anulación
                            </button>
                        </form>
                    </div>
                </details>
            </div>
        @endif

        <div>
            <a href="{{ route('ventas.index') }}" class="btn-secondary">
                <x-icon name="chevron-left" class="h-4 w-4" /> Volver
            </a>
        </div>
    </div>
</x-app-layout>
