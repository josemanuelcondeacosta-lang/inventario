<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-slate-800">Registrar venta</h2>
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

            <form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
                @csrf

                <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label for="fecha" class="field-label">Fecha</label>
                        <input type="date" id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}"
                               class="field-input" required>
                    </div>

                    <div>
                        <label for="tipo_pago" class="field-label">Tipo de pago</label>
                        <select name="tipo_pago" id="tipo_pago" class="field-input" required>
                            <option value="contado" {{ old('tipo_pago') == 'contado' ? 'selected' : '' }}>
                                Contado (pago completo)
                            </option>
                            <option value="fiado" {{ old('tipo_pago') == 'fiado' ? 'selected' : '' }}>
                                Fiado (se lleva, paga después)
                            </option>
                            <option value="separado" {{ old('tipo_pago') == 'separado' ? 'selected' : '' }}>
                                Separado (se aparta, paga por cuotas)
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="metodo_pago" class="field-label">Método de pago</label>
                        <select id="metodo_pago" name="metodo_pago" class="field-input" required>
                            <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="mixto" {{ old('metodo_pago') == 'mixto' ? 'selected' : '' }}>Mixto</option>
                        </select>
                    </div>

                    <div id="cajaAbono" class="hidden">
                        <label for="abono_inicial" class="field-label">Abono inicial</label>
                        <input type="number" id="abono_inicial" name="abono_inicial" min="0" step="1"
                               value="{{ old('abono_inicial', 0) }}"
                               class="field-input text-right">
                        <p class="field-hint">Deje 0 si no abonó nada.</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="nota" class="field-label">Nota (opcional)</label>
                    <input type="text" id="nota" name="nota" value="{{ old('nota') }}"
                           placeholder="Ej: promete pagar el viernes"
                           class="field-input">
                </div>

                <div class="mb-6 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <h3 class="mb-3 text-sm font-semibold text-slate-800">
                        Datos del cliente
                        <span class="font-normal text-slate-500">(para garantías y cobros)</span>
                    </h3>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label for="cedula" class="field-label">Cédula</label>
                            <input type="text" name="cedula" id="cedula"
                                   value="{{ old('cedula') }}"
                                   class="field-input">
                            <p id="avisoCliente" class="mt-1.5 hidden text-xs font-medium text-emerald-700">
                                Cliente encontrado
                            </p>
                        </div>
                        <div>
                            <label for="nombre_cliente" class="field-label">Nombre</label>
                            <input type="text" name="nombre_cliente" id="nombre_cliente"
                                   value="{{ old('nombre_cliente') }}"
                                   class="field-input">
                        </div>
                        <div>
                            <label for="telefono" class="field-label">Teléfono</label>
                            <input type="text" name="telefono" id="telefono"
                                   value="{{ old('telefono') }}"
                                   class="field-input">
                        </div>
                    </div>
                </div>

                <h3 class="mb-2 text-sm font-semibold text-slate-800">Productos vendidos</h3>

                <div class="table-wrap mb-4">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="w-24 text-center">Cant.</th>
                                <th class="w-36 text-right">Precio</th>
                                <th class="w-28 text-center">Garantía</th>
                                <th class="w-32 text-right">Subtotal</th>
                                <th class="w-16"></th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoDetalles"></tbody>
                        <tfoot>
                            <tr class="border-t border-slate-200 bg-slate-50 font-semibold text-slate-700">
                                <td colspan="4" class="px-4 py-2.5 text-right">Total</td>
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
                        Guardar venta
                    </button>
                    <a href="{{ route('ventas.index') }}" class="btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @php
        $productosJs = $productos->map(function ($p) {
            $modelo = $p->nombre . ($p->color ? ' — ' . $p->color : '');
            return [
                'id'     => $p->id,
                'talla'  => $p->talla ?: 'Única',
                'modelo' => $modelo,
                'ref'    => $p->referencia,
                'buscar' => trim($modelo . ' ' . $p->talla . ' ' . $p->referencia),
                'precio' => (float) $p->precio_venta,
                'stock'  => $p->stock,
            ];
        })->values();

        $modelosJs = $productos->groupBy(fn($p) => $p->nombre . ($p->color ? ' — ' . $p->color : ''))
            ->keys()->values();
    @endphp

       <script>
        document.addEventListener('DOMContentLoaded', function () {

        const PRODUCTOS = @json($productosJs);
        const MODELOS = @json($modelosJs);
        let fila = 0;
        const cuerpo = document.getElementById('cuerpoDetalles');

        function opciones() {
            return PRODUCTOS.map(p =>
                `<option value="${p.id}" data-modelo="${p.modelo}">${p.talla}${p.ref ? ' · ' + p.ref : ''} — hay ${p.stock}</option>`
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
                           class="field-input text-center cantidad" required>
                    <p class="mt-1 hidden text-xs font-medium text-red-600 alertaStock">Sin stock</p>
                </td>
                <td>
                    <input type="number" name="detalles[${i}][precio_venta]" min="0" step="1" value="0"
                           class="field-input text-right" required>
                </td>
                <td>
                    <input type="number" name="detalles[${i}][garantia_meses]" min="0" value="0"
                           class="field-input text-center">
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
                placeholder: 'Buscar por nombre, talla o referencia...',
                maxOptions: null,
                optgroupField: 'modelo',
                searchField: ['text', 'modelo'],
                optgroups: MODELOS.map(m => ({ value: m, label: m })),
                lockOptgroupOrder: true,
                render: {
                    optgroup_header: (data, escape) =>
                        `<div class="px-3 py-1.5 text-xs font-semibold text-slate-500 bg-slate-50">${escape(data.label)}</div>`,
                },
            });

            select.addEventListener('change', function () {
                const producto = PRODUCTOS.find(p => p.id == this.value);
                if (producto) {
                    tr.querySelector('[name*="[precio_venta]"]').value = producto.precio;
                    tr.dataset.stock = producto.stock;
                }
                validarStock(tr);
                calcular();
            });
        }

        function validarStock(tr) {
            const stock = parseInt(tr.dataset.stock ?? 0);
            const cant = parseInt(tr.querySelector('.cantidad').value) || 0;
            const alerta = tr.querySelector('.alertaStock');

            if (tr.dataset.stock && cant > stock) {
                alerta.textContent = `Solo hay ${stock}`;
                alerta.classList.remove('hidden');
            } else {
                alerta.classList.add('hidden');
            }
        }

        function calcular() {
            let total = 0;
            cuerpo.querySelectorAll('tr').forEach(tr => {
                const cant = parseFloat(tr.querySelector('[name*="[cantidad]"]').value) || 0;
                const prec = parseFloat(tr.querySelector('[name*="[precio_venta]"]').value) || 0;
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

        cuerpo.addEventListener('input', e => {
            if (e.target.classList.contains('cantidad')) {
                validarStock(e.target.closest('tr'));
            }
            calcular();
        });

        cuerpo.addEventListener('click', e => {
            if (e.target.closest('.btnQuitar')) {
                e.target.closest('tr').remove();
                calcular();
            }
        });

        // Mostrar abono inicial solo en fiado y separado
        const tipoPago = document.getElementById('tipo_pago');
        const cajaAbono = document.getElementById('cajaAbono');
        const inputCedula = document.getElementById('cedula');

        const avisoCedula = document.createElement('p');
        avisoCedula.className = 'mt-1.5 text-xs font-medium text-amber-700';
        avisoCedula.textContent = 'Obligatorio para fiados y separados';

        function actualizarTipoPago() {
            if (tipoPago.value === 'contado') {
                cajaAbono.classList.add('hidden');
                avisoCedula.remove();
            } else {
                cajaAbono.classList.remove('hidden');
                inputCedula.parentElement.appendChild(avisoCedula);
            }
        }

        tipoPago.addEventListener('change', actualizarTipoPago);
        actualizarTipoPago();

        // Autocompletar cliente por cédula
        let timer;

        inputCedula.addEventListener('input', function () {
            clearTimeout(timer);
            const valor = this.value.trim();
            const aviso = document.getElementById('avisoCliente');

            if (valor.length < 5) {
                aviso.classList.add('hidden');
                return;
            }

            timer = setTimeout(() => {
                fetch(`{{ route('ventas.buscarCliente') }}?cedula=${valor}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.encontrado) {
                            document.getElementById('nombre_cliente').value = data.nombre;
                            document.getElementById('telefono').value = data.telefono ?? '';
                            aviso.classList.remove('hidden');
                        } else {
                            aviso.classList.add('hidden');
                        }
                    });
            }, 400);
        });

        document.getElementById('formVenta').addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Guardando...';
        });

        agregarFila();

    });
    </script>
</x-app-layout>
