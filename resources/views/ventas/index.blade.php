<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Ventas</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex justify-between items-center gap-4">
            <a href="{{ route('ventas.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Registrar venta
            </a>

            <form method="GET" action="{{ route('ventas.index') }}" class="flex gap-2">
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                    placeholder="Buscar por cédula o nombre..."
                    class="border-gray-300 rounded w-64">
                <button type="submit" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    Buscar
                </button>
                @if (request('buscar'))
                    <a href="{{ route('ventas.index') }}"
                    class="px-4 py-2 text-gray-600 hover:underline">Limpiar</a>
                @endif
            </form>
        </div>

        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Cliente</th>
                        <th class="p-3">Cédula</th>
                        <th class="p-3 text-center">Productos</th>
                        <th class="p-3 text-right">Total</th>
                        <th class="p-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $venta)
                        <tr class="border-t">
                            <td class="p-3">{{ $venta->fecha->format('d/m/Y') }}</td>
                            <td class="p-3">{{ $venta->cliente->nombre ?? 'Sin datos' }}</td>
                            <td class="p-3">{{ $venta->cliente->cedula ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $venta->detalles_count }}</td>
                            <td class="p-3 text-right">${{ number_format($venta->total, 0) }}</td>
                            <td class="p-3 text-right whitespace-nowrap">
                                <a href="{{ route('ventas.show', $venta) }}"
                                class="text-blue-600 hover:underline">Ver detalle</a>
                                <a href="{{ route('ventas.factura', $venta) }}" target="_blank"
                                class="text-gray-700 hover:underline ml-3">Factura</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">
                                No hay ventas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $ventas->links() }}
        </div>
    </div>
</x-app-layout>