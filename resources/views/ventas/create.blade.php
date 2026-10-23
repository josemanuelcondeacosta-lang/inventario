<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-texto">Registrar venta</h2>
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
                        <input type="date" id="fecha" name="fecha"
                               value="{{ old('fecha', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}"
                               class="field-input" required>
                    </div>

                    <div>
                        <label for="tipo_pago" class="field-label">Tipo de pago</label>
                        <select name="tipo_pago" id="tipo_pago" class="field-input" required>
                            <option value="contado" {{ old('tipo_pago') == 'contado' ? 'selected' : '' }}>Contado</option>
                            <option value="fiado" {{ old('tipo_pago') == 'fiado' ? 'selected' : '' }}>Fiado</option>
                            <option value="separado" {{ old('tipo_pago') == 'separado' ? 'selected' : '' }}>Separado</option>
                        </select>
                    </div>

                    <div>
                        <label for="metodo_pago" class="field-label">Método de pago</label>
                        <select name="metodo_pago" id="metodo_pago" class="field-input" required>
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
                    </div>
                </div>

                <div class="mb-6">
                    <label for="nota" class="field-label">Nota (opcional)</label>
                    <input type="text" id="nota" name="nota" value="{{ old('nota') }}"
                           placeholder="Ej: promete pagar el viernes"
                           class="field-input">
                </div>

                <div class="mb-6 rounded-lg border border-borde bg-elevado p-4">
                    <h3 class="mb-3 text-sm font-semibold text-texto">
                        Datos del cliente
                        <span class="font-normal text-tenue">— para garantías y cobros</span>
                    </h3>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label for="cedula" class="field-label">Cédula</label>
                            <input type="text" id="cedula" name="cedula" value="{{ old('cedula') }}"
                                   class="field-input">
                            <p id="avisoCliente" class="mt-1 hidden text-xs font-medium text-brand">
                                Cliente encontrado
                            </p>
                        </div>
                        <div>
                            <label for="nombre_cliente" class="field-label">Nombre</label>
                            <input type="text" id="nombre_cliente" name="nombre_cliente"
                                   value="{{ old('nombre_cliente') }}" class="field-input">
                        </div>
                        <div>
                            <label for="telefono" class="field-label">Teléfono</label>
                            <input type="text" id="telefono" name="telefono"
                                   value="{{ old('telefono') }}" class="field-input">
                        </div>
                    </div>
                </div>

                <h3 class="mb-3 text-sm font-semibold text-texto">Productos vendidos</h3>

                <div id="bloques" class="space-y-3"></div>

                <button type="button" id="btnAgregar" class="btn-secondary btn-sm mt-3">
                    <x-icon name="plus" class="h-4 w-4" /> Agregar producto
                </button>

                <div class="mt-6 flex items-center justify-between rounded-lg bg-elevado px-4 py-3">
                    <span class="text-sm font-semibold text-suave">Total de la venta</span>
                    <span class="font-display text-xl font-semibold text-texto" id="totalGeneral">$0</span>
                </div>

                <div class="mt-6 flex gap-3 border-t border-borde pt-6">
                    <button type="submit" class="btn-primary">Guardar venta</button>
                    <a href="{{ route('ventas.index') }}" class="btn-secondary">Cancelar</a>
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
                'precio' => (float) $p->precio_venta,
                'stock'  => $p->stock,
            ];
        })->values();

        $modelosJs = $productos
            ->groupBy(fn($p) => $p->nombre . ($p->color ? ' — ' . $p->color : ''))
            ->keys()->values();
    @endphp

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const PRODUCTOS = @json($productosJs);
        const MODELOS = @json($modelosJs);
        const cont = document.getElementById('bloques');
        let n = 0;

        function opciones() {
            return PRODUCTOS.map(p =>
                `<option value="${p.id}" data-modelo="${p.modelo}">${p.talla}${p.ref ? ' · ' + p.ref : ''} — hay ${p.stock}</option>`
            ).join('');
        }

        function agregarBloque() {
            const i = n++;
            const div = document.createElement('div');
            div.className = 'bloque rounded-lg border border-borde p-4';
            div.innerHTML = `
                <div class="mb-3 flex items-start gap-3">
                    <div class="flex-1">
                        <label class="field-label">Producto</label>
                        <select name="detalles[${i}][producto_id]" class="w-full text-sm" required>
                            <option value="">-- Buscar por nombre, talla o referencia --</option>
                            ${opciones()}
                        </select>
                    </div>
                    <button type="button" class="btnQuitar mt-7 text-tenue hover:text-alerta" aria-label="Quitar producto">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div>
                        <label class="field-label">Cantidad</label>
                        <input type="number" name="detalles[${i}][cantidad]" min="1" value="1"
                               class="cantidad field-input text-center" required>
                        <p class="mt-1 hidden text-xs font-medium text-alerta alertaStock"></p>
                    </div>
                    <div>
                        <label class="field-label">Precio</label>
                        <input type="number" name="detalles[${i}][precio_venta]" min="0" step="1" value="0"
                               class="field-input text-right" required>
                    </div>
                    <div>
                        <label class="field-label">Garantía (meses)</label>
                        <input type="number" name="detalles[${i}][garantia_meses]" min="0" value="0"
                               class="field-input text-center">
                    </div>
                    <div>
                        <label class="field-label">Subtotal</label>
                        <p class="subtotal flex h-[38px] items-center justify-end font-display font-semibold text-texto">$0</p>
                    </div>
                </div>
            `;
            cont.appendChild(div);

            const select = div.querySelector('select');

            new TomSelect(select, {
                placeholder: 'Buscar por nombre, talla o referencia...',
                maxOptions: null,
                optgroupField: 'modelo',
                searchField: ['text', 'modelo'],
                optgroups: MODELOS.map(m => ({ value: m, label: m })),
                lockOptgroupOrder: true,
                render: {
                    optgroup_header: (data, escape) =>
                        `<div class="px-3 py-1.5 text-xs font-semibold opacity-70">${escape(data.label)}</div>`,
                },
            });

            select.addEventListener('change', function () {
                const producto = PRODUCTOS.find(p => p.id == this.value);
                if (producto) {
                    div.querySelector('[name*="[precio_venta]"]').value = producto.precio;
                    div.dataset.stock = producto.stock;
                }
                validarStock(div);
                calcular();
            });
        }

        function validarStock(div) {
            const stock = parseInt(div.dataset.stock ?? 0);
            const cant = parseInt(div.querySelector('.cantidad').value) || 0;
            const alerta = div.querySelector('.alertaStock');

            if (div.dataset.stock && cant > stock) {
                alerta.textContent = `Solo hay ${stock}`;
                alerta.classList.remove('hidden');
            } else {
                alerta.classList.add('hidden');
            }
        }

        function calcular() {
            let total = 0;
            cont.querySelectorAll('.bloque').forEach(div => {
                const c = parseFloat(div.querySelector('.cantidad').value) || 0;
                const p = parseFloat(div.querySelector('[name*="[precio_venta]"]').value) || 0;
                const sub = c * p;
                total += sub;
                div.querySelector('.subtotal').textContent = '$' + sub.toLocaleString('es-CO');
            });
            document.getElementById('totalGeneral').textContent = '$' + total.toLocaleString('es-CO');
        }

        document.getElementById('btnAgregar').addEventListener('click', () => {
            agregarBloque();
            calcular();
        });

        cont.addEventListener('input', e => {
            if (e.target.classList.contains('cantidad')) {
                validarStock(e.target.closest('.bloque'));
            }
            calcular();
        });

        cont.addEventListener('click', e => {
            if (e.target.closest('.btnQuitar')) {
                e.target.closest('.bloque').remove();
                calcular();
            }
        });

        // Abono inicial solo en fiado y separado
        const tipoPago = document.getElementById('tipo_pago');
        const cajaAbono = document.getElementById('cajaAbono');
        const inputCedula = document.getElementById('cedula');

        function actualizarTipoPago() {
            cajaAbono.classList.toggle('hidden', tipoPago.value === 'contado');
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

        agregarBloque();
    });
    </script>
</x-app-layout>