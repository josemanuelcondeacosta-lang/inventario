<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Categorías</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">

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

        <div class="mb-4">
            <a href="{{ route('categorias.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Nueva categoría
            </a>
        </div>

        <div class="bg-white shadow rounded overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Nombre</th>
                        <th class="p-3">Productos</th>
                        <th class="p-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categorias as $categoria)
                        <tr class="border-t">
                            <td class="p-3">{{ $categoria->nombre }}</td>
                            <td class="p-3">{{ $categoria->productos()->count() }}</td>
                            <td class="p-3 text-right">
                                <a href="{{ route('categorias.edit', $categoria) }}"
                                   class="text-blue-600 hover:underline">Editar</a>

                                <form action="{{ route('categorias.destroy', $categoria) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('¿Eliminar esta categoría?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline ml-3">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-center text-gray-500">
                                No hay categorías registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $categorias->links() }}
        </div>
    </div>
</x-app-layout>