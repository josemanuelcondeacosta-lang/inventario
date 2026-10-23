<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-texto">Detalle de compra</h2>
    </x-slot>

    <div class="mx-auto max-w-4xl space-y-4 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('error'))
            <x-alert variant="error">{{ session('error') }}</x-alert>
        @endif

        <div class="card card-body">

            <div class="mb-6 grid grid-cols-2 gap-4 text-sm md:grid-cols-4">
                <div>
                    <span class="text-tenue">Fecha</span>
                    <p class="font-medium text-texto">{{ $compra->fecha->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-tenue">Proveedor</span>
                    <p class="font-medium text-texto">{{ $compra->proveedor ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-tenue">Factura</span>
                    <p class="font-medium text-texto">{{ $compra->factura ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-tenue">Total</span>
                    <p class="font-medium text-texto">${{ number_format($compra->total, 0) }}</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Precio compra</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compra->detalles as $detalle)
                            <tr>
                                <td>
                                    {{ $detalle->producto->nombre }}
                                    <span class="text-tenue">
                                        {{ $detalle->producto->talla }} {{ $detalle->producto->color }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $detalle->cantidad }}</td>
                                <td class="text-right">${{ number_format($detalle->precio_compra, 0) }}</td>
                                <td class="text-right">${{ number_format($detalle->subtotal, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-borde bg-elevado font-semibold text-suave">
                            <td colspan="3" class="px-4 py-2.5 text-right">Total</td>
                            <td class="px-4 py-2.5 text-right">${{ number_format($compra->total, 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($compra->anulada)
                <div class="mt-6 rounded-lg border border-borde bg-elevado p-4">
                    <p class="font-semibold text-suave">Compra anulada</p>
                    <p class="mt-1 text-sm text-suave">
                        Fecha: {{ $compra->anulada_at->format('d/m/Y H:i') }}<br>
                        Motivo: {{ $compra->motivo_anulacion }}
                    </p>
                </div>
            @else
                <div class="mt-6 border-t border-borde pt-4">
                    <details class="group">
                        <summary class="cursor-pointer text-sm font-medium text-alerta hover:text-alerta">
                            Anular esta compra
                        </summary>

                        <div class="mt-3 rounded-lg border border-red-200 bg-alerta-suave p-4">
                            <p class="mb-3 text-sm text-suave">
                                Al anular, el stock de estos productos se descontará del inventario.
                                La compra quedará registrada como anulada.
                            </p>

                            <form action="{{ route('compras.anular', $compra) }}" method="POST"
                                  onsubmit="return confirm('¿Seguro que desea anular esta compra?')">
                                @csrf
                                <label for="motivo" class="field-label">Motivo de la anulación</label>
                                <input type="text" id="motivo" name="motivo" required maxlength="200"
                                       placeholder="Ej: se registró la cantidad equivocada"
                                       class="field-input mb-3">

                                <button type="submit" class="btn-danger">
                                    Confirmar anulación
                                </button>
                            </form>
                        </div>
                    </details>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('compras.index') }}" class="btn-secondary">
                    <x-icon name="chevron-left" class="h-4 w-4" /> Volver
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
