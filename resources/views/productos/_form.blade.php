@php
    $esNuevo = !isset($producto);
    $tallasDisponibles = ['XS','S','M','L','XL','XXL','Única','Ajustable'];
    $tallasViejas = old('tallas', []);
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">

    <div>
        <label for="referencia" class="field-label">Referencia / Código</label>
        <input type="text" id="referencia" name="referencia"
               value="{{ old('referencia', $producto->referencia ?? '') }}"
               placeholder="Opcional — ej: POL-001"
               class="field-input">
        @if ($esNuevo)
            <p class="mt-1 text-xs text-tenue">
                Si elige varias tallas, se le agrega la talla al final (POL-001-S, POL-001-M...).
            </p>
        @endif
        @error('referencia') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="nombre" class="field-label">Nombre</label>
        <input type="text" id="nombre" name="nombre"
               value="{{ old('nombre', $producto->nombre ?? '') }}"
               class="field-input" required>
        @error('nombre') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="categoria_id" class="field-label">Categoría</label>
        <select id="categoria_id" name="categoria_id" class="field-input" required>
            <option value="">-- Seleccione --</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}"
                    {{ old('categoria_id', $producto->categoria_id ?? '') == $categoria->id ? 'selected' : '' }}>
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>
        @error('categoria_id') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="color" class="field-label">Color</label>
        <input type="text" id="color" name="color"
               value="{{ old('color', $producto->color ?? '') }}"
               class="field-input">
        @error('color') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    @if ($esNuevo)
        <div class="md:col-span-2">
            <label class="field-label">Tallas</label>
            <p class="mb-2 text-xs text-tenue">
                Marque todas las que aplican. Se crea un producto por cada talla, con los mismos precios.
            </p>

            <div class="flex flex-wrap gap-2">
                @foreach ($tallasDisponibles as $t)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="tallas[]" value="{{ $t }}"
                               class="peer sr-only"
                               {{ in_array($t, $tallasViejas) ? 'checked' : '' }}>
                        <span class="inline-flex min-w-[3.2rem] items-center justify-center rounded-lg border border-borde bg-superficie px-3 py-2 text-sm font-medium text-suave transition-colors hover:border-slate-400 peer-checked:border-brand-600 peer-checked:bg-brand-600 peer-checked:text-texto">
                            {{ $t }}
                        </span>
                    </label>
                @endforeach
            </div>

            <div class="mt-3">
                <input type="text" name="talla_otra" value="{{ old('talla_otra') }}"
                       placeholder="Otra talla (opcional) — ej: 38, 40"
                       class="field-input md:w-1/2">
            </div>
            @error('tallas') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    @else
        <div>
            <label for="talla" class="field-label">Talla</label>
            <input type="text" id="talla" name="talla"
                   value="{{ old('talla', $producto->talla) }}"
                   class="field-input">
            @error('talla') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    @endif

    <div>
        <label for="stock_minimo" class="field-label">Stock mínimo</label>
        <input type="number" id="stock_minimo" name="stock_minimo" min="0"
               value="{{ old('stock_minimo', $producto->stock_minimo ?? 5) }}"
               class="field-input" required>
        @error('stock_minimo') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="precio_compra" class="field-label">Precio de compra</label>
        <input type="number" id="precio_compra" name="precio_compra" step="1" min="0"
               value="{{ old('precio_compra', $producto->precio_compra ?? '') }}"
               class="field-input" required>
        @error('precio_compra') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="precio_venta" class="field-label">Precio de venta</label>
        <input type="number" id="precio_venta" name="precio_venta" step="1" min="0"
               value="{{ old('precio_venta', $producto->precio_venta ?? '') }}"
               class="field-input" required>
        @error('precio_venta') <p class="field-error">{{ $message }}</p> @enderror
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    new TomSelect('select[name="categoria_id"]', {
        placeholder: 'Buscar categoría...',
        maxOptions: null,
    });
});
</script>