<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Compras</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4">
            <a href="{{ route('compras.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Registrar compra
            </a>
        </div>

        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Proveedor</th>
                        <th class="p-3">Factura</th>
                        <th class="p-3 text-center">Productos</th>
                        <th class="p-3 text-right">Total</th>
                        <th class="p-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($compras as $compra)
                        <tr class="border-t">
                            <td class="p-3">{{ $compra->fecha->format('d/m/Y') }}</td>
                            <td class="p-3">{{ $compra->proveedor ?? '-' }}</td>
                            <td class="p-3">{{ $compra->factura ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $compra->detalles_count }}</td>
                            <td class="p-3 text-right">${{ number_format($compra->total, 0) }}</td>
                            <td class="p-3 text-right">
                                <a href="{{ route('compras.show', $compra) }}"
                                   class="text-blue-600 hover:underline">Ver detalle</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">
                                No hay compras registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $compras->links() }}
        </div>
    </div>
</x-app-layout>