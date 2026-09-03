<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div class="md:col-span-2">
        <label class="block mb-1 font-medium">Nombre</label>
        <input type="text" name="nombre"
               value="{{ old('nombre', $producto->nombre ?? '') }}"
               class="w-full border-gray-300 rounded" required>
        @error('nombre') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium">Categoría</label>
        <select name="categoria_id" class="w-full border-gray-300 rounded" required>
            <option value="">-- Seleccione --</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}"
                    {{ old('categoria_id', $producto->categoria_id ?? '') == $categoria->id ? 'selected' : '' }}>
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>
        @error('categoria_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium">Talla</label>
        <input type="text" name="talla"
               value="{{ old('talla', $producto->talla ?? '') }}"
               placeholder="S, M, L, XL..."
               class="w-full border-gray-300 rounded">
        @error('talla') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium">Color</label>
        <input type="text" name="color"
               value="{{ old('color', $producto->color ?? '') }}"
               class="w-full border-gray-300 rounded">
        @error('color') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium">Stock mínimo</label>
        <input type="number" name="stock_minimo" min="0"
               value="{{ old('stock_minimo', $producto->stock_minimo ?? 5) }}"
               class="w-full border-gray-300 rounded" required>
        @error('stock_minimo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium">Precio de compra</label>
        <input type="number" name="precio_compra" step="1" min="0"
               value="{{ old('precio_compra', $producto->precio_compra ?? '') }}"
               class="w-full border-gray-300 rounded" required>
        @error('precio_compra') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block mb-1 font-medium">Precio de venta</label>
        <input type="number" name="precio_venta" step="1" min="0"
               value="{{ old('precio_venta', $producto->precio_venta ?? '') }}"
               class="w-full border-gray-300 rounded" required>
        @error('precio_venta') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
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