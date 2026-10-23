<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-texto">Registrar compra</h2>
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
                               max="{{ date('Y-m-d') }}"
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

                <h3 class="mb-1 text-sm font-semibold text-texto">Productos comprados</h3>
                <p class="mb-4 text-xs text-tenue">
                    Elija el producto y escriba cuántas unidades compró de cada talla. Deje en blanco las que no compró.
                </p>

                <div id="bloques" class="space-y-4"></div>

                <button type="button" id="btnAgregar" class="btn-secondary btn-sm mt-4">
                    <x-icon name="plus" class="h-4 w-4" /> Agregar otro producto
                </button>

                <div class="mt-6 flex items-center justify-between rounded-lg bg-elevado px-4 py-3">
                    <span class="text-sm font-semibold text-suave">Total de la compra</span>
                    <span class="text-xl font-semibold text-texto" id="totalGeneral">$0</span>
                </div>

                <div class="mt-6 flex gap-3 border-t border-borde pt-6">
                    <button type="submit" class="btn-primary">Guardar compra</button>
                    <a href="{{ route('compras.index') }}" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    @php
        $grupos = $productos
            ->groupBy(fn($p) => $p->categoria_id . '|' . $p->nombre . '|' . ($p->color ?? ''))
            ->map(function ($items, $clave) {
                $primero = $items->first();
                $etiqueta = $primero->nombre;
                if ($primero->color) {
                    $etiqueta .= ' — ' . $primero->color;
                }
                $refs = $items->pluck('referencia')->filter()->implode(' ');
                return [
                    'clave'    => $clave,
                    'etiqueta' => $etiqueta,
                    'buscar'   => trim($etiqueta . ' ' . $refs),
                    'items'    => $items->map(fn($p) => [
                        'id'     => $p->id,
                        'talla'  => $p->talla ?: 'Única',
                        'stock'  => $p->stock,
                        'precio' => (float) $p->precio_compra,
                    ])->values(),
                ];
            })->values();
    @endphp

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const GRUPOS = @json($grupos);
        const cont = document.getElementById('bloques');
        let n = 0;

        function opciones() {
            return GRUPOS.map((g, i) =>
                `<option value="${i}">${g.buscar}</option>`
            ).join('');
        }

        function agregarBloque() {
            const div = document.createElement('div');
            div.className = 'bloque rounded-lg border border-borde p-4';
            div.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="flex-1">
                        <select class="selectGrupo w-full text-sm">
                            <option value="">-- Buscar por nombre o referencia --</option>
                            ${opciones()}
                        </select>
                    </div>
                    <button type="button" class="btnQuitar mt-1 text-tenue hover:text-alerta" aria-label="Quitar producto">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="tallas mt-4 hidden"></div>
                <div class="mt-3 hidden justify-end text-sm subtotalLinea">
                    <span class="text-tenue">Subtotal:&nbsp;</span>
                    <span class="font-semibold text-texto valorSub">$0</span>
                </div>
            `;
            cont.appendChild(div);

            const select = div.querySelector('.selectGrupo');
            new TomSelect(select, { placeholder: 'Buscar por nombre o referencia...', maxOptions: null });

            select.addEventListener('change', function () {
                pintarTallas(div, this.value);
                calcular();
            });
        }

        function pintarTallas(div, indice) {
            const zona = div.querySelector('.tallas');
            const pie = div.querySelector('.subtotalLinea');

            if (indice === '') {
                zona.innerHTML = '';
                zona.classList.add('hidden');
                pie.classList.add('hidden');
                return;
            }

            const grupo = GRUPOS[indice];

            zona.innerHTML = `
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    ${grupo.items.map(it => {
                        const i = n++;
                        return `
                        <div class="rounded-lg bg-elevado p-3">
                            <input type="hidden" name="detalles[${i}][producto_id]" value="${it.id}">
                            <div class="mb-2 flex items-baseline justify-between">
                                <span class="text-sm font-semibold text-texto">${it.talla}</span>
                                <span class="text-xs text-tenue">hay ${it.stock}</span>
                            </div>
                            <label class="mb-1 block text-xs text-tenue">Cantidad</label>
                            <input type="number" min="0" step="1" value="0"
                                   name="detalles[${i}][cantidad]"
                                   class="cant field-input mb-2 py-1.5 text-center text-sm">
                            <label class="mb-1 block text-xs text-tenue">Precio</label>
                            <input type="number" min="0" step="1" value="${it.precio}"
                                   name="detalles[${i}][precio_compra]"
                                   class="prec field-input py-1.5 text-right text-sm">
                        </div>`;
                    }).join('')}
                </div>
            `;
            zona.classList.remove('hidden');
            pie.classList.remove('hidden');
            pie.classList.add('flex');
        }

        function calcular() {
            let total = 0;
            cont.querySelectorAll('.bloque').forEach(div => {
                let sub = 0;
                div.querySelectorAll('.tallas .rounded-lg').forEach(celda => {
                    const c = parseFloat(celda.querySelector('.cant')?.value) || 0;
                    const p = parseFloat(celda.querySelector('.prec')?.value) || 0;
                    sub += c * p;
                });
                const destino = div.querySelector('.valorSub');
                if (destino) destino.textContent = '$' + sub.toLocaleString('es-CO');
                total += sub;
            });
            document.getElementById('totalGeneral').textContent = '$' + total.toLocaleString('es-CO');
        }

        document.getElementById('btnAgregar').addEventListener('click', agregarBloque);

        cont.addEventListener('input', calcular);

        cont.addEventListener('click', e => {
            if (e.target.closest('.btnQuitar')) {
                e.target.closest('.bloque').remove();
                calcular();
            }
        });

        document.getElementById('formCompra').addEventListener('submit', function (e) {
            let hayAlgo = false;

            cont.querySelectorAll('.tallas .rounded-lg').forEach(celda => {
                const cant = celda.querySelector('.cant');
                if (!cant) return;
                if ((parseFloat(cant.value) || 0) > 0) {
                    hayAlgo = true;
                } else {
                    celda.querySelectorAll('input').forEach(i => i.disabled = true);
                }
            });

            if (!hayAlgo) {
                e.preventDefault();
                alert('Escriba la cantidad de al menos una talla.');
                cont.querySelectorAll('input').forEach(i => i.disabled = false);
                return;
            }

            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Guardando...';
        });

        agregarBloque();
    });
    </script>
</x-app-layout>