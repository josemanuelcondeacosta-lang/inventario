<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::withCount('detalles')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15);

        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('compras.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'fecha'                   => 'required|date',
            'proveedor'               => 'nullable|string|max:150',
            'factura'                 => 'nullable|string|max:50',
            'detalles'                => 'required|array|min:1',
            'detalles.*.producto_id'  => 'required|exists:productos,id',
            'detalles.*.cantidad'     => 'required|integer|min:1',
            'detalles.*.precio_compra'=> 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($datos) {

            $compra = Compra::create([
                'fecha'     => $datos['fecha'],
                'proveedor' => $datos['proveedor'] ?? null,
                'factura'   => $datos['factura'] ?? null,
                'total'     => 0,
            ]);

            $total = 0;

            foreach ($datos['detalles'] as $linea) {
                $subtotal = $linea['cantidad'] * $linea['precio_compra'];

                CompraDetalle::create([
                    'compra_id'     => $compra->id,
                    'producto_id'   => $linea['producto_id'],
                    'cantidad'      => $linea['cantidad'],
                    'precio_compra' => $linea['precio_compra'],
                    'subtotal'      => $subtotal,
                ]);

                Producto::where('id', $linea['producto_id'])
                    ->increment('stock', $linea['cantidad']);

                $total += $subtotal;
            }

            $compra->update(['total' => $total]);
        });

        return redirect()->route('compras.index')
            ->with('success', 'Compra registrada y stock actualizado.');
    }

    public function show(Compra $compra)
    {
        $compra->load('detalles.producto');
        return view('compras.show', compact('compra'));
    }

    public function anular(Request $request, Compra $compra)
    {
        $datos = $request->validate([
            'motivo' => 'required|string|max:200',
        ]);

        if ($compra->anulada) {
            return redirect()->route('compras.show', $compra)
                ->with('error', 'Esta compra ya fue anulada.');
        }

        $compra->load('detalles.producto');

        foreach ($compra->detalles as $detalle) {
            if ($detalle->producto->stock < $detalle->cantidad) {
                return redirect()->route('compras.show', $compra)
                    ->with('error',
                        "No se puede anular: el stock de {$detalle->producto->nombre} " .
                        "ya bajó de {$detalle->cantidad} unidades (quedan {$detalle->producto->stock}). " .
                        "Probablemente ya se vendieron.");
            }
        }

        DB::transaction(function () use ($compra, $datos) {
            foreach ($compra->detalles as $detalle) {
                Producto::where('id', $detalle->producto_id)
                    ->decrement('stock', $detalle->cantidad);
            }

            $compra->update([
                'anulada'          => true,
                'anulada_at'       => now(),
                'motivo_anulacion' => $datos['motivo'],
            ]);
        });

        return redirect()->route('compras.index')
            ->with('success', 'Compra anulada y stock revertido.');
    }
}