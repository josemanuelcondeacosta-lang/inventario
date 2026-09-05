<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">Nuevo producto</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="card card-body">
            <form action="{{ route('productos.store') }}" method="POST">
                @csrf
                @include('productos._form')

                <div class="mt-6 flex gap-3 border-t border-slate-100 pt-6">
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
