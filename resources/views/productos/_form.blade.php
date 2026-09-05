<div class="grid grid-cols-1 gap-5 md:grid-cols-2">

    <div>
        <label for="referencia" class="field-label">Referencia / Código</label>
        <input type="text" id="referencia" name="referencia"
               value="{{ old('referencia', $producto->referencia ?? '') }}"
               placeholder="Opcional — ej: GOR-001"
               class="field-input">
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
        <label for="selectTalla" class="field-label">Talla</label>
        @php
            $tallas = ['XS','S','M','L','XL','XXL','Única','Ajustable'];
            $tallaActual = old('talla', $producto->talla ?? '');
            $esOtra = $tallaActual && !in_array($tallaActual, $tallas);
        @endphp
        <select id="selectTalla" class="field-input">
            <option value="">-- Sin talla --</option>
            @foreach ($tallas as $t)
                <option value="{{ $t }}" {{ $tallaActual === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
            <option value="__otra" {{ $esOtra ? 'selected' : '' }}>Otra...</option>
        </select>
        <label for="inputTalla" class="sr-only">Talla personalizada</label>
        <input type="text" name="talla" id="inputTalla"
               value="{{ $tallaActual }}"
               class="field-input mt-2 {{ $esOtra ? '' : 'hidden' }}"
               placeholder="Escriba la talla">
        @error('talla') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="color" class="field-label">Color</label>
        <input type="text" id="color" name="color"
               value="{{ old('color', $producto->color ?? '') }}"
               class="field-input">
        @error('color') <p class="field-error">{{ $message }}</p> @enderror
    </div>

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

        const selectTalla = document.getElementById('selectTalla');
    const inputTalla = document.getElementById('inputTalla');

    selectTalla.addEventListener('change', function () {
        if (this.value === '__otra') {
            inputTalla.classList.remove('hidden');
            inputTalla.value = '';
            inputTalla.focus();
        } else {
            inputTalla.classList.add('hidden');
            inputTalla.value = this.value;
        }
    });
});
</script>
