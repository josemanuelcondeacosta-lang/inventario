<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-texto">Nuevo producto</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-4 px-4 py-6 sm:px-6 lg:px-8">

        @if (session('error'))
            <x-alert variant="error">{{ session('error') }}</x-alert>
        @endif

        <div class="card card-body">
            <form action="{{ route('productos.store') }}" method="POST">
                @csrf
                @include('productos._form')

                <div class="mt-6 flex gap-3 border-t border-borde pt-6">
                    <button type="submit" class="btn-primary">
                        Guardar producto
                    </button>
                    <a href="{{ route('productos.index') }}" class="btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>