<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::with('categoria')
            ->when($request->buscar, function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                ->orWhere('referencia', 'like', '%' . $request->buscar . '%');
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        Producto::create($datos);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $this->validar($request);

        $producto->update($datos);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        $producto->update(['activo' => false]);

        return redirect()->route('productos.index')
            ->with('success', 'Producto desactivado.');
    }

    private function validar(Request $request)
    {
        return $request->validate([
            'categoria_id'  => 'required|exists:categorias,id',
            'nombre'        => 'required|string|max:150',
            'talla'         => 'nullable|string|max:20',
            'color'         => 'nullable|string|max:50',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0|gte:precio_compra',
            'stock_minimo'  => 'required|integer|min:0',
            'referencia' => 'nullable|string|max:50|unique:productos,referencia,' . ($request->route('producto')?->id ?? 'NULL'),
        ]);
    }
}