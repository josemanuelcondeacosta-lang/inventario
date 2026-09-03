<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Ventas</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-4 flex flex-wrap justify-between items-center gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('ventas.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Registrar venta
                </a>

                <a href="{{ route('ventas.index') }}"
                   class="px-3 py-2 rounded text-sm {{ !request('estado') ? 'bg-gray-800 text-white' : 'bg-gray-200 hover:bg-gray-300' }}">
                    Todas
                </a>
                <a href="{{ route('ventas.index', ['estado' => 'pendientes']) }}"
                   class="px-3 py-2 rounded text-sm {{ request('estado') == 'pendientes' ? 'bg-gray-800 text-white' : 'bg-gray-200 hover:bg-gray-300' }}">
                    Con saldo
                </a>
                <a href="{{ route('ventas.index', ['estado' => 'por_entregar']) }}"
                   class="px-3 py-2 rounded text-sm {{ request('estado') == 'por_entregar' ? 'bg-gray-800 text-white' : 'bg-gray-200 hover:bg-gray-300' }}">
                    Por entregar
                </a>
            </div>

            <form method="GET" action="{{ route('ventas.index') }}" class="flex gap-2">
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Buscar por cédula o nombre..."
                       class="border-gray-300 rounded w-64 text-sm">
                <button type="submit" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm">
                    Buscar
                </button>
                @if (request('buscar'))
                    <a href="{{ route('ventas.index') }}"
                       class="px-4 py-2 text-gray-600 hover:underline text-sm">Limpiar</a>
                @endif
            </form>
        </div>

        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Cliente</th>
                        <th class="p-3">Tipo</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3 text-right">Total</th>
                        <th class="p-3 text-right">Saldo</th>
                        <th class="p-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $venta)
                        <tr class="border-t">
                            <td class="p-3">{{ $venta->fecha->format('d/m/Y') }}</td>
                            <td class="p-3">
                                {{ $venta->cliente->nombre ?? 'Sin datos' }}
                                <span class="block text-xs text-gray-500">
                                    {{ $venta->cliente->cedula ?? '' }}
                                </span>
                            </td>
                            <td class="p-3 capitalize">{{ $venta->tipo_pago }}</td>
                            <td class="p-3">
                                @php $estado = $venta->estadoTexto(); @endphp
                                <span class="px-2 py-1 rounded text-xs
                                    @if ($estado === 'Pagada') bg-green-100 text-green-800
                                    @elseif ($estado === 'Debe') bg-red-100 text-red-800
                                    @elseif ($estado === 'Separado') bg-orange-100 text-orange-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $estado }}
                                </span>
                            </td>
                            <td class="p-3 text-right">${{ number_format($venta->total, 0) }}</td>
                            <td class="p-3 text-right {{ $venta->saldo > 0 ? 'text-red-700 font-medium' : 'text-gray-400' }}">
                                {{ $venta->saldo > 0 ? '$' . number_format($venta->saldo, 0) : '-' }}
                            </td>
                            <td class="p-3 text-right whitespace-nowrap">
                                <a href="{{ route('ventas.show', $venta) }}"
                                   class="text-blue-600 hover:underline">Ver detalle</a>
                                <a href="{{ route('ventas.factura', $venta) }}" target="_blank"
                                   class="text-gray-700 hover:underline ml-3">Factura</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500">
                                No hay ventas que coincidan.
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