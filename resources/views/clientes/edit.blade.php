<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">Editar cliente</h2>
    </x-slot>

    <div class="mx-auto max-w-lg space-y-4 px-4 py-6 sm:px-6 lg:px-8">

        @if ($errors->any())
            <x-alert variant="error">
                <ul class="list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <div class="card card-body">
            <form action="{{ route('clientes.update', $cliente) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="cedula" class="field-label">Cédula</label>
                        <input type="text" id="cedula" name="cedula"
                               value="{{ old('cedula', $cliente->cedula) }}"
                               class="field-input" required>
                    </div>

                    <div>
                        <label for="nombre" class="field-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre"
                               value="{{ old('nombre', $cliente->nombre) }}"
                               class="field-input" required>
                    </div>

                    <div>
                        <label for="telefono" class="field-label">Teléfono</label>
                        <input type="text" id="telefono" name="telefono"
                               value="{{ old('telefono', $cliente->telefono) }}"
                               class="field-input">
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="btn-primary">Actualizar</button>
                    <a href="{{ route('clientes.show', $cliente) }}" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>