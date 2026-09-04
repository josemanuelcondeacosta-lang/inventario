<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detalle de compra</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif
        <div class="bg-white shadow rounded p-6">

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
                <div>
                    <span class="text-gray-500">Fecha</span>
                    <p class="font-medium">{{ $compra->fecha->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Proveedor</span>
                    <p class="font-medium">{{ $compra->proveedor ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Factura</span>
                    <p class="font-medium">{{ $compra->factura ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Total</span>
                    <p class="font-medium">${{ number_format($compra->total, 0) }}</p>
                </div>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Producto</th>
                        <th class="p-2 text-center">Cantidad</th>
                        <th class="p-2 text-right">Precio compra</th>
                        <th class="p-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compra->detalles as $detalle)
                        <tr class="border-t">
                            <td class="p-2">
                                {{ $detalle->producto->nombre }}
                                <span class="text-gray-500">
                                    {{ $detalle->producto->talla }} {{ $detalle->producto->color }}
                                </span>
                            </td>
                            <td class="p-2 text-center">{{ $detalle->cantidad }}</td>
                            <td class="p-2 text-right">${{ number_format($detalle->precio_compra, 0) }}</td>
                            <td class="p-2 text-right">${{ number_format($detalle->subtotal, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t font-semibold">
                        <td colspan="3" class="p-2 text-right">Total</td>
                        <td class="p-2 text-right">${{ number_format($compra->total, 0) }}</td>
                    </tr>
                </tfoot>
            </table>

                        @if ($compra->anulada)
                <div class="mt-6 p-4 bg-gray-100 rounded border-l-4 border-gray-500">
                    <p class="font-semibold text-gray-700">Compra anulada</p>
                    <p class="text-sm text-gray-600 mt-1">
                        Fecha: {{ $compra->anulada_at->format('d/m/Y H:i') }}<br>
                        Motivo: {{ $compra->motivo_anulacion }}
                    </p>
                </div>
            @else
                <div class="mt-6 border-t pt-4">
                    <details>
                        <summary class="cursor-pointer text-sm text-red-600 hover:underline">
                            Anular esta compra
                        </summary>

                        <div class="mt-3 p-4 bg-red-50 rounded">
                            <p class="text-sm text-gray-700 mb-3">
                                Al anular, el stock de estos productos se descontará del inventario.
                                La compra quedará registrada como anulada.
                            </p>

                            <form action="{{ route('compras.anular', $compra) }}" method="POST"
                                  onsubmit="return confirm('¿Seguro que desea anular esta compra?')">
                                @csrf
                                <label class="block mb-1 text-sm font-medium">Motivo de la anulación</label>
                                <input type="text" name="motivo" required maxlength="200"
                                       placeholder="Ej: se registró la cantidad equivocada"
                                       class="w-full border-gray-300 rounded text-sm mb-3">

                                <button type="submit"
                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                    Confirmar anulación
                                </button>
                            </form>
                        </div>
                    </details>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('compras.index') }}"
                   class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    Volver
                </a>
            </div>
        </div>
    </div>
</x-app-layout>