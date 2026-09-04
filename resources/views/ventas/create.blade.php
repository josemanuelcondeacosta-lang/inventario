<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Registrar venta</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6">

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block mb-1 font-medium text-sm">Fecha</label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}"
                               class="w-full border-gray-300 rounded text-sm" required>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-sm">Tipo de pago</label>
                        <select name="tipo_pago" id="tipo_pago" class="w-full border-gray-300 rounded text-sm" required>
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
                        <label class="block mb-1 font-medium text-sm">Método de pago</label>
                        <select name="metodo_pago" class="w-full border-gray-300 rounded text-sm" required>
                            <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="mixto" {{ old('metodo_pago') == 'mixto' ? 'selected' : '' }}>Mixto</option>
                        </select>
                    </div>

                    <div id="cajaAbono" class="hidden">
                        <label class="block mb-1 font-medium text-sm">Abono inicial</label>
                        <input type="number" name="abono_inicial" min="0" step="1"
                               value="{{ old('abono_inicial', 0) }}"
                               class="w-full border-gray-300 rounded text-sm text-right">
                        <p class="text-xs text-gray-500 mt-1">Deje 0 si no abonó nada.</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium text-sm">Nota (opcional)</label>
                    <input type="text" name="nota" value="{{ old('nota') }}"
                           placeholder="Ej: promete pagar el viernes"
                           class="w-full border-gray-300 rounded text-sm">
                </div>

                <div class="border rounded p-4 mb-6 bg-gray-50">
                    <h3 class="font-semibold mb-3 text-sm">
                        Datos del cliente
                        <span class="font-normal text-gray-500">(para garantías y cobros)</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-1 text-sm">Cédula</label>
                            <input type="text" name="cedula" id="cedula"
                                   value="{{ old('cedula') }}"
                                   class="w-full border-gray-300 rounded text-sm">
                            <p id="avisoCliente" class="text-xs text-green-700 mt-1 hidden">
                                Cliente encontrado
                            </p>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm">Nombre</label>
                            <input type="text" name="nombre_cliente" id="nombre_cliente"
                                   value="{{ old('nombre_cliente') }}"
                                   class="w-full border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block mb-1 text-sm">Teléfono</label>
                            <input type="text" name="telefono" id="telefono"
                                   value="{{ old('telefono') }}"
                                   class="w-full border-gray-300 rounded text-sm">
                        </div>
                    </div>
                </div>

                <h3 class="font-semibold mb-2">Productos vendidos</h3>

                <table class="w-full text-sm mb-4">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Producto</th>
                            <th class="p-2 text-center w-24">Cant.</th>
                            <th class="p-2 text-right w-36">Precio</th>
                            <th class="p-2 text-center w-28">Garantía</th>
                            <th class="p-2 text-right w-32">Subtotal</th>
                            <th class="p-2 w-16"></th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoDetalles"></tbody>
                    <tfoot>
                        <tr class="border-t font-semibold">
                            <td colspan="4" class="p-2 text-right">Total</td>
                            <td class="p-2 text-right" id="totalGeneral">$0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <button type="button" id="btnAgregar"
                        class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm">
                    + Agregar producto
                </button>

                <div class="mt-6 flex gap-3 border-t pt-4">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Guardar venta
                    </button>
                    <a href="{{ route('ventas.index') }}"
                       class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @php
        $productosJs = $productos->map(fn($p) => [
            'id'     => $p->id,
            'nombre' => trim($p->nombre . ' — ' . $p->talla . ' ' . $p->color) . " (stock: {$p->stock})",
            'precio' => (float) $p->precio_venta,
            'stock'  => $p->stock,
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
            tr.className = 'border-t';
            tr.innerHTML = `
                <td class="p-2">
                    <select name="detalles[${i}][producto_id]" class="w-full text-sm" required>
                        <option value="">-- Seleccione --</option>
                        ${opciones()}
                    </select>
                </td>
                <td class="p-2">
                    <input type="number" name="detalles[${i}][cantidad]" min="1" value="1"
                           class="w-full border-gray-300 rounded text-sm text-center cantidad" required>
                    <p class="text-xs text-red-600 mt-1 hidden alertaStock">Sin stock</p>
                </td>
                <td class="p-2">
                    <input type="number" name="detalles[${i}][precio_venta]" min="0" step="1" value="0"
                           class="w-full border-gray-300 rounded text-sm text-right" required>
                </td>
                <td class="p-2">
                    <input type="number" name="detalles[${i}][garantia_meses]" min="0" value="0"
                           class="w-full border-gray-300 rounded text-sm text-center">
                </td>
                <td class="p-2 text-right subtotal">$0</td>
                <td class="p-2 text-center">
                    <button type="button" class="text-red-600 text-sm btnQuitar">Quitar</button>
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
            if (e.target.classList.contains('btnQuitar')) {
                e.target.closest('tr').remove();
                calcular();
            }
        });

        // Mostrar abono inicial solo en fiado y separado
        const tipoPago = document.getElementById('tipo_pago');
        const cajaAbono = document.getElementById('cajaAbono');
        const inputCedula = document.getElementById('cedula');

        const avisoCedula = document.createElement('p');
        avisoCedula.className = 'text-xs text-orange-700 mt-1';
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