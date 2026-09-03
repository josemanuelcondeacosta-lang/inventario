<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Detalle de compra</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
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

            <div class="mt-6">
                <a href="{{ route('compras.index') }}"
                   class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    Volver
                </a>
            </div>
        </div>
    </div>
</x-app-layout>