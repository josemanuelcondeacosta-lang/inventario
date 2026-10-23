<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-texto">Editar categoría</h2>
    </x-slot>

    <div class="mx-auto max-w-lg px-4 py-6 sm:px-6 lg:px-8">
        <div class="card card-body">
            <form action="{{ route('categorias.update', $categoria) }}" method="POST">
                @csrf
                @method('PUT')

                <label for="nombre" class="field-label">Nombre</label>
                <input type="text" id="nombre" name="nombre"
                       value="{{ old('nombre', $categoria->nombre) }}"
                       class="field-input" required autofocus>

                @error('nombre')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <div class="mt-6 flex gap-3 border-t border-borde pt-6">
                    <button type="submit" class="btn-primary">
                        Actualizar categoría
                    </button>
                    <a href="{{ route('categorias.index') }}" class="btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
