<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">Registrar compra</h2>
    </x-slot>

    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="card card-body">

            @if ($errors->any())
                <x-alert variant="error" class="mb-4">
                    <ul class="list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <form action="{{ route('compras.store') }}" method="POST" id="formCompra">
                @csrf

                <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label for="fecha" class="field-label">Fecha</label>
                        <input type="date" id="fecha" name="fecha"
                               value="{{ old('fecha', date('Y-m-d')) }}"
                               class="field-input" required>
                    </div>
                    <div>
                        <label for="proveedor" class="field-label">Proveedor</label>
                        <input type="text" id="proveedor" name="proveedor" value="{{ old('proveedor') }}"
                               class="field-input">
                    </div>
                    <div>
                        <label for="factura" class="field-label">Factura</label>
                        <input type="text" id="factura" name="factura" value="{{ old('factura') }}"
                               class="field-input">
                    </div>
                </div>

                <h3 class="mb-2 text-sm font-semibold text-slate-800">Productos comprados</h3>

                <div class="table-wrap mb-4">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="w-28 text-center">Cantidad</th>
                                <th class="w-40 text-right">Precio compra</th>
                                <th class="w-32 text-right">Subtotal</th>
                                <th class="w-16"></th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoDetalles"></tbody>
                        <tfoot>
                            <tr class="border-t border-slate-200 bg-slate-50 font-semibold text-slate-700">
                                <td colspan="3" class="px-4 py-2.5 text-right">Total</td>
                                <td class="px-4 py-2.5 text-right" id="totalGeneral">$0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <button type="button" id="btnAgregar" class="btn-secondary btn-sm">
                    <x-icon name="plus" class="h-4 w-4" /> Agregar producto
                </button>

                <div class="mt-6 flex gap-3 border-t border-slate-100 pt-6">
                    <button type="submit" class="btn-primary">
                        Guardar compra
                    </button>
                    <a href="{{ route('compras.index') }}" class="btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @php
        $productosJs = $productos->map(fn($p) => [
            'id'     => $p->id,
            'nombre' => $p->nombre . ' — ' . $p->talla . ' ' . $p->color,
            'precio' => (float) $p->precio_compra,
        ]);
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const PRODUCTOS = @json($productosJs);
            let fila = 0;

            const cuerpo = document.getElementById('cuerpoDetalles');

            function opciones() {
                return PRODUCTOS.map(p =>
                    `<option value="${p.id}">${p.nombre}</option>`
                ).join('');
            }

            function agregarFila() {
                const i = fila++;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <select name="detalles[${i}][producto_id]" class="w-full text-sm" required>
                            <option value="">-- Seleccione --</option>
                            ${opciones()}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="detalles[${i}][cantidad]" min="1" value="1"
                            class="field-input text-center" required>
                    </td>
                    <td>
                        <input type="number" name="detalles[${i}][precio_compra]" min="0" step="1" value="0"
                            class="field-input text-right" required>
                    </td>
                    <td class="text-right subtotal font-medium text-slate-700">$0</td>
                    <td class="text-center">
                        <button type="button" class="text-red-600 hover:text-red-700 btnQuitar" aria-label="Quitar producto">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 inline"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </td>
                `;
                cuerpo.appendChild(tr);

                const select = tr.querySelector('select');

                new TomSelect(select, {
                    placeholder: 'Buscar producto...',
                    maxOptions: null,
                });

                select.addEventListener('change', function () {
                    const producto = PRODUCTOS.find(p => p.id == this.value);
                    if (producto) {
                        tr.querySelector('[name*="[precio_compra]"]').value = producto.precio;
                    }
                    calcular();
                });
            }

            function calcular() {
                let total = 0;
                cuerpo.querySelectorAll('tr').forEach(tr => {
                    const cant = parseFloat(tr.querySelector('[name*="[cantidad]"]').value) || 0;
                    const prec = parseFloat(tr.querySelector('[name*="[precio_compra]"]').value) || 0;
                    const sub = cant * prec;
                    total += sub;
                    tr.querySelector('.subtotal').textContent = '$' + sub.toLocaleString('es-CO');
                });
                document.getElementById('totalGeneral').textContent = '$' + total.toLocaleString('es-CO');
            }

            document.getElementById('btnAgregar').addEventListener('click', () => {
                agregarFila();
                calcular();
            });

            cuerpo.addEventListener('input', calcular);

            cuerpo.addEventListener('click', e => {
                if (e.target.closest('.btnQuitar')) {
                    e.target.closest('tr').remove();
                    calcular();
                }
            });

            document.getElementById('formCompra').addEventListener('submit', function () {
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'Guardando...';
            });

            agregarFila();

        });
        </script>
</x-app-layout>
