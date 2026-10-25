<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Reclamo;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReclamoController extends Controller
{
    public function index(Request $request)
    {
        $reclamos = Reclamo::with(['venta.cliente', 'detalle.producto', 'productoEntregado'])
            ->when($request->buscar, function ($q) use ($request) {
                $q->whereHas('venta.cliente', function ($c) use ($request) {
                    $c->whereLike('cedula', '%' . $request->buscar . '%')
                      ->orWhereLike('nombre', '%' . $request->buscar . '%');
                });
            })
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $perdidaMes = Reclamo::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('perdida');

        return view('reclamos.index', compact('reclamos', 'perdidaMes'));
    }

    public function create(Venta $venta)
    {
        $venta->load('detalles.producto', 'cliente');

        $productos = Producto::where('activo', true)
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();

        return view('reclamos.create', compact('venta', 'productos'));
    }

    public function store(Request $request, Venta $venta)
    {
        $datos = $request->validate([
            'venta_detalle_id'      => 'required|exists:venta_detalles,id',
            'fecha'                 => 'required|date',
            'resultado'             => 'required|in:cambio_mismo,cambio_otro,devolucion,rechazado,proveedor',
            'cantidad'              => 'required|integer|min:1',
            'producto_entregado_id' => 'nullable|exists:productos,id',
            'reingresa_stock'       => 'nullable|boolean',
            'descripcion'           => 'nullable|string|max:500',
        ]);

        $detalle = VentaDetalle::with('producto')->findOrFail($datos['venta_detalle_id']);

        if ($detalle->venta_id !== $venta->id) {
            abort(404);
        }

        if ($datos['cantidad'] > $detalle->cantidad) {
            throw ValidationException::withMessages([
                'cantidad' => "Solo se vendieron {$detalle->cantidad} unidades de ese producto.",
            ]);
        }

        $requiereEntrega = in_array($datos['resultado'], ['cambio_mismo', 'cambio_otro']);

        if ($requiereEntrega && empty($datos['producto_entregado_id'])) {
            throw ValidationException::withMessages([
                'producto_entregado_id' => 'Indique qué producto se le entregó al cliente.',
            ]);
        }

        DB::transaction(function () use ($datos, $venta, $detalle, $requiereEntrega) {

            $perdida = 0;
            $reingresa = (bool) ($datos['reingresa_stock'] ?? false);

            if ($requiereEntrega) {
                $entregado = Producto::lockForUpdate()->find($datos['producto_entregado_id']);

                if ($entregado->stock < $datos['cantidad']) {
                    throw ValidationException::withMessages([
                        'producto_entregado_id' => "Stock insuficiente de {$entregado->nombre}. Disponible: {$entregado->stock}",
                    ]);
                }

                // Sale la unidad que se le entrega al cliente
                $entregado->decrement('stock', $datos['cantidad']);

                // La pérdida es lo que costó esa unidad
                $perdida = $entregado->precio_compra * $datos['cantidad'];
            }

            if ($datos['resultado'] === 'devolucion') {
                if ($reingresa) {
                    // La prenda sirve: vuelve al inventario, no hay pérdida de mercancía
                    Producto::where('id', $detalle->producto_id)
                        ->increment('stock', $datos['cantidad']);
                    $perdida = 0;
                } else {
                    // No sirve: se pierde el costo de la unidad
                    $perdida = $detalle->costo_unitario * $datos['cantidad'];
                }

                // Se le devolvió la plata al cliente: se descuenta de la venta
                $venta->decrement('total', $detalle->precio_venta * $datos['cantidad']);
                $venta->decrement('abonado', min($venta->abonado, $detalle->precio_venta * $datos['cantidad']));
            }

            // rechazado y proveedor: sin movimiento de stock ni pérdida

            Reclamo::create([
                'venta_id'              => $venta->id,
                'venta_detalle_id'      => $detalle->id,
                'fecha'                 => $datos['fecha'],
                'resultado'             => $datos['resultado'],
                'producto_entregado_id' => $requiereEntrega ? $datos['producto_entregado_id'] : null,
                'cantidad'              => $datos['cantidad'],
                'reingresa_stock'       => $reingresa,
                'perdida'               => $perdida,
                'descripcion'           => $datos['descripcion'] ?? null,
            ]);
        });

        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Reclamo de garantía registrado.');
    }
}