<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-texto">Registrar reclamo de garantía</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-4 px-4 py-6 sm:px-6 lg:px-8">

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
            <div class="mb-5 grid grid-cols-2 gap-4 border-b border-borde pb-4 text-sm md:grid-cols-4">
                <div>
                    <span class="text-tenue">Venta</span>
                    <p class="font-medium text-texto">#{{ $venta->id }}</p>
                </div>
                <div>
                    <span class="text-tenue">Fecha</span>
                    <p class="font-medium text-texto">{{ $venta->fecha->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-tenue">Cliente</span>
                    <p class="font-medium text-texto">{{ $venta->cliente->nombre ?? 'Sin datos' }}</p>
                </div>
                <div>
                    <span class="text-tenue">Cédula</span>
                    <p class="font-medium text-texto">{{ $venta->cliente->cedula ?? '-' }}</p>
                </div>
            </div>

            <form action="{{ route('reclamos.store', $venta) }}" method="POST">
                @csrf

                <div class="space-y-5">

                    <div>
                        <label for="venta_detalle_id" class="field-label">¿Cuál prenda?</label>
                        <select id="venta_detalle_id" name="venta_detalle_id" class="field-input" required>
                            <option value="">-- Seleccione --</option>
                            @foreach ($venta->detalles as $d)
                                @php
                                    $vence = $d->garantia_meses > 0
                                        ? $venta->fecha->copy()->addMonths($d->garantia_meses)
                                        : null;
                                @endphp
                                <option value="{{ $d->id }}" data-max="{{ $d->cantidad }}"
                                    {{ old('venta_detalle_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->producto->nombre }} {{ $d->producto->talla }} {{ $d->producto->color }}
                                    — {{ $d->cantidad }} und.
                                    @if ($vence)
                                        (garantía {{ $vence->isFuture() ? 'vigente hasta' : 'vencida el' }} {{ $vence->format('d/m/Y') }})
                                    @else
                                        (sin garantía registrada)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="fecha" class="field-label">Fecha del reclamo</label>
                            <input type="date" id="fecha" name="fecha"
                                   value="{{ old('fecha', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}" class="field-input" required>
                        </div>
                        <div>
                            <label for="cantidad" class="field-label">Cantidad reclamada</label>
                            <input type="number" id="cantidad" name="cantidad" min="1"
                                   value="{{ old('cantidad', 1) }}" class="field-input text-center" required>
                        </div>
                    </div>

                    <div>
                        <label for="resultado" class="field-label">¿Qué se hizo?</label>
                        <select id="resultado" name="resultado" class="field-input" required>
                            <option value="cambio_mismo" {{ old('resultado') == 'cambio_mismo' ? 'selected' : '' }}>
                                Cambio por la misma prenda
                            </option>
                            <option value="cambio_otro" {{ old('resultado') == 'cambio_otro' ? 'selected' : '' }}>
                                Cambio por otra prenda
                            </option>
                            <option value="devolucion" {{ old('resultado') == 'devolucion' ? 'selected' : '' }}>
                                Se devolvió el dinero
                            </option>
                            <option value="proveedor" {{ old('resultado') == 'proveedor' ? 'selected' : '' }}>
                                Respondió el proveedor
                            </option>
                            <option value="rechazado" {{ old('resultado') == 'rechazado' ? 'selected' : '' }}>
                                No procede el reclamo
                            </option>
                        </select>
                        <p id="explicacion" class="mt-2 text-xs text-tenue"></p>
                    </div>

                    <div id="cajaEntrega" class="hidden">
                        <label for="producto_entregado_id" class="field-label">¿Qué se le entregó?</label>
                        <select id="producto_entregado_id" name="producto_entregado_id" class="field-input">
                            <option value="">-- Seleccione --</option>
                            @foreach ($productos as $p)
                                <option value="{{ $p->id }}" {{ old('producto_entregado_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombre }} {{ $p->talla }} {{ $p->color }}{{ $p->referencia ? ' · ' . $p->referencia : '' }} — hay {{ $p->stock }}
                                </option>
                            @endforeach
                        </select>
                        <p class="field-hint">Se descuenta del inventario y su costo se registra como pérdida.</p>
                    </div>

                    <div id="cajaReingreso" class="hidden">
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-borde bg-elevado p-4">
                            <input type="checkbox" name="reingresa_stock" value="1"
                                   class="mt-0.5 rounded border-borde text-brand focus:ring-brand"
                                   {{ old('reingresa_stock') ? 'checked' : '' }}>
                            <span class="text-sm">
                                <span class="font-medium text-texto">La prenda sirve para volver a venderse</span>
                                <span class="mt-0.5 block text-xs text-tenue">
                                    Si la marca, vuelve al inventario y no se registra pérdida.
                                    Si no, se pierde el costo de esa unidad.
                                </span>
                            </span>
                        </label>
                    </div>

                    <div>
                        <label for="descripcion" class="field-label">Qué pasó (opcional)</label>
                        <textarea id="descripcion" name="descripcion" rows="3" class="field-input"
                                  placeholder="Ej: se descosió la costura del hombro">{{ old('descripcion') }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-3 border-t border-borde pt-6">
                    <button type="submit" class="btn-primary">Registrar reclamo</button>
                    <a href="{{ route('ventas.show', $venta) }}" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const resultado = document.getElementById('resultado');
                const cajaEntrega = document.getElementById('cajaEntrega');
                const cajaReingreso = document.getElementById('cajaReingreso');
                const explicacion = document.getElementById('explicacion');

                const textos = {
                    cambio_mismo: 'Sale una unidad buena del inventario. Su costo se registra como pérdida.',
                    cambio_otro:  'Sale la prenda que elija del inventario. Su costo se registra como pérdida.',
                    devolucion:   'Se descuenta el valor de la venta. La pérdida depende de si la prenda se puede revender.',
                    proveedor:    'El proveedor repone o devuelve el dinero. No se registra pérdida.',
                    rechazado:    'Queda el registro del reclamo. No se mueve inventario ni se registra pérdida.',
                };

                function actualizar() {
                    const v = resultado.value;
                    const esCambio = v === 'cambio_mismo' || v === 'cambio_otro';

                    cajaEntrega.classList.toggle('hidden', !esCambio);
                    cajaReingreso.classList.toggle('hidden', v !== 'devolucion');
                    explicacion.textContent = textos[v] ?? '';
                }

                resultado.addEventListener('change', actualizar);
                actualizar();

                // Buscadores
                new TomSelect('#venta_detalle_id', {
                    placeholder: 'Seleccione la prenda reclamada...',
                    maxOptions: null,
                });

                new TomSelect('#producto_entregado_id', {
                    placeholder: 'Buscar producto...',
                    maxOptions: null,
                });

                // Limitar la cantidad a lo que se vendió
                const selDetalle = document.getElementById('venta_detalle_id');
                const cantidad = document.getElementById('cantidad');

                selDetalle.addEventListener('change', function () {
                    const opt = selDetalle.querySelector(`option[value="${this.value}"]`);
                    const max = opt?.dataset.max;
                    if (max) {
                        cantidad.max = max;
                        if (parseInt(cantidad.value) > parseInt(max)) cantidad.value = max;
                    }
                });
            });
    </script>
</x-app-layout>