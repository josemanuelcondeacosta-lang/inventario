<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nueva categoría</h2>
    </x-slot>

    <div class="py-6 max-w-lg mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6">
            <form action="{{ route('categorias.store') }}" method="POST">
                @csrf

                <label class="block mb-2 font-medium">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}"
                       class="w-full border-gray-300 rounded" required>

                @error('nombre')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror

                <div class="mt-6 flex gap-3">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Guardar
                    </button>
                    <a href="{{ route('categorias.index') }}"
                       class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>