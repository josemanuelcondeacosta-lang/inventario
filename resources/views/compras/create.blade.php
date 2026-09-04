<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Registrar compra</h2>
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

            <form action="{{ route('compras.store') }}" method="POST" id="formCompra">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block mb-1 font-medium">Fecha</label>
                        <input type="date" name="fecha"
                               value="{{ old('fecha', date('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded" required>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Proveedor</label>
                        <input type="text" name="proveedor" value="{{ old('proveedor') }}"
                               class="w-full border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Factura</label>
                        <input type="text" name="factura" value="{{ old('factura') }}"
                               class="w-full border-gray-300 rounded">
                    </div>
                </div>

                <h3 class="font-semibold mb-2">Productos comprados</h3>

                <table class="w-full text-sm mb-4">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Producto</th>
                            <th class="p-2 text-center w-28">Cantidad</th>
                            <th class="p-2 text-right w-40">Precio compra</th>
                            <th class="p-2 text-right w-32">Subtotal</th>
                            <th class="p-2 w-16"></th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoDetalles"></tbody>
                    <tfoot>
                        <tr class="border-t font-semibold">
                            <td colspan="3" class="p-2 text-right">Total</td>
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
                        Guardar compra
                    </button>
                    <a href="{{ route('compras.index') }}"
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
                            class="w-full border-gray-300 rounded text-sm text-center" required>
                    </td>
                    <td class="p-2">
                        <input type="number" name="detalles[${i}][precio_compra]" min="0" step="1" value="0"
                            class="w-full border-gray-300 rounded text-sm text-right" required>
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
                if (e.target.classList.contains('btnQuitar')) {
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