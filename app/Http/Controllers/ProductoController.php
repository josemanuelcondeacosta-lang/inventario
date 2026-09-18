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
        $datos = $request->validate([
            'categoria_id'  => 'required|exists:categorias,id',
            'nombre'        => 'required|string|max:150',
            'referencia'    => 'nullable|string|max:50',
            'color'         => 'nullable|string|max:50',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0|gte:precio_compra',
            'stock_minimo'  => 'required|integer|min:0',
            'tallas'        => 'nullable|array',
            'tallas.*'      => 'nullable|string|max:20',
            'talla_otra'    => 'nullable|string|max:20',
        ]);

        $tallas = collect($datos['tallas'] ?? []);

        if (!empty($datos['talla_otra'])) {
            $tallas->push($datos['talla_otra']);
        }

        $tallas = $tallas->filter()->unique()->values();

        if ($tallas->isEmpty()) {
            $tallas = collect([null]);
        }

        $varias = $tallas->count() > 1;
        $creados = 0;

        foreach ($tallas as $talla) {

            $referencia = $datos['referencia'] ?? null;

            if ($referencia && $varias && $talla) {
                $referencia = $referencia . '-' . strtoupper($talla);
            }

            if ($referencia && Producto::where('referencia', $referencia)->exists()) {
                return back()->withInput()
                    ->with('error', "La referencia {$referencia} ya existe.");
            }

            $existe = Producto::where('nombre', $datos['nombre'])
                ->where('categoria_id', $datos['categoria_id'])
                ->where('talla', $talla)
                ->where('color', $datos['color'] ?? null)
                ->exists();

            if ($existe) {
                continue;
            }

            Producto::create([
                'referencia'    => $referencia,
                'categoria_id'  => $datos['categoria_id'],
                'nombre'        => $datos['nombre'],
                'talla'         => $talla,
                'color'         => $datos['color'] ?? null,
                'precio_compra' => $datos['precio_compra'],
                'precio_venta'  => $datos['precio_venta'],
                'stock_minimo'  => $datos['stock_minimo'],
            ]);

            $creados++;
        }

        if ($creados === 0) {
            return back()->withInput()
                ->with('error', 'Esos productos ya estaban registrados.');
        }

        return redirect()->route('productos.index')
            ->with('success', $creados === 1
                ? 'Producto creado correctamente.'
                : "Se crearon {$creados} productos, uno por cada talla.");
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $request->validate([
            'categoria_id'  => 'required|exists:categorias,id',
            'nombre'        => 'required|string|max:150',
            'referencia'    => 'nullable|string|max:50|unique:productos,referencia,' . $producto->id,
            'talla'         => 'nullable|string|max:20',
            'color'         => 'nullable|string|max:50',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta'  => 'required|numeric|min:0|gte:precio_compra',
            'stock_minimo'  => 'required|integer|min:0',
        ]);

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
}