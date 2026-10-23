<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-texto">Categorías</h2>
    </x-slot>

    <div class="mx-auto max-w-4xl space-y-4 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('success'))
            <x-alert variant="success">{{ session('success') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert variant="error">{{ session('error') }}</x-alert>
        @endif

        <div class="flex items-center justify-between gap-4">
            <p class="text-sm text-tenue">
                <span class="font-semibold text-texto">{{ $categorias->total() }}</span>
                {{ Str::plural('categoría', $categorias->total()) }}
            </p>
            <a href="{{ route('categorias.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Nueva categoría
            </a>
        </div>

        @if ($categorias->isEmpty())
            <div class="card flex flex-col items-center gap-3 px-4 py-14 text-center">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-elevado text-tenue">
                    <x-icon name="tag" class="h-6 w-6" />
                </span>
                <div>
                    <p class="font-medium text-suave">No hay categorías registradas</p>
                    <p class="mt-1 text-sm text-tenue">Comienza creando tu primera categoría.</p>
                </div>
                <a href="{{ route('categorias.create') }}" class="btn-primary">
                    <x-icon name="plus" class="h-4 w-4" /> Nueva categoría
                </a>
            </div>
        @else
            <div class="table-wrap">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Productos</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categorias as $categoria)
                            <tr>
                                <td class="font-medium text-texto">{{ $categoria->nombre }}</td>
                                <td>
                                    <x-badge variant="neutral">{{ $categoria->productos()->count() }}</x-badge>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('categorias.edit', $categoria) }}"
                                           class="inline-flex items-center gap-1 text-brand hover:text-brand">
                                            <x-icon name="pencil" class="h-4 w-4" /> Editar
                                        </a>

                                        <form action="{{ route('categorias.destroy', $categoria) }}"
                                              method="POST"
                                              onsubmit="return confirm('¿Eliminar esta categoría?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 text-alerta hover:text-alerta">
                                                <x-icon name="trash" class="h-4 w-4" /> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($categorias->hasPages())
            <div>{{ $categorias->links() }}</div>
        @endif
    </div>
</x-app-layout>
