<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\CompraDetalle;
use App\Models\Abono;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $ventas = Venta::with('cliente')
            ->withCount('detalles')
            ->when($request->buscar, function ($q) use ($request) {
                $q->whereHas('cliente', function ($c) use ($request) {
                    $c->where('cedula', 'like', '%' . $request->buscar . '%')
                    ->orWhere('nombre', 'like', '%' . $request->buscar . '%');
                });
            })
            ->when($request->estado === 'pendientes', fn($q) => $q->where('saldo', '>', 0))
            ->when($request->estado === 'por_entregar', fn($q) => $q->where('entregado', false))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $productos = Producto::where('activo', true)
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();

        return view('ventas.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'fecha'                     => 'required|date',
            'tipo_pago'                 => 'required|in:contado,fiado,separado',
            'metodo_pago'               => 'required|in:efectivo,transferencia,mixto',
            'abono_inicial'             => 'nullable|numeric|min:0',
            'nota'                      => 'nullable|string|max:500',
            'cedula'                    => 'nullable|string|max:20',
            'nombre_cliente'            => 'nullable|string|max:150',
            'telefono'                  => 'nullable|string|max:20',
            'detalles'                  => 'required|array|min:1',
            'detalles.*.producto_id'    => 'required|exists:productos,id',
            'detalles.*.cantidad'       => 'required|integer|min:1',
            'detalles.*.precio_venta'   => 'required|numeric|min:0',
            'detalles.*.garantia_meses' => 'nullable|integer|min:0',
        ]);

        if ($datos['tipo_pago'] !== 'contado' && empty($datos['cedula'])) {
            throw ValidationException::withMessages([
                'cedula' => 'Para ventas fiadas o separadas debe registrar la cédula del cliente.',
            ]);
        }

        DB::transaction(function () use ($datos) {

            $clienteId = null;

            if (!empty($datos['cedula'])) {
                $cliente = Cliente::firstOrCreate(
                    ['cedula' => $datos['cedula']],
                    [
                        'nombre'   => $datos['nombre_cliente'] ?? 'Sin nombre',
                        'telefono' => $datos['telefono'] ?? null,
                    ]
                );
                $clienteId = $cliente->id;
            }

            $venta = Venta::create([
                'cliente_id'  => $clienteId,
                'fecha'       => $datos['fecha'],
                'tipo_pago'   => $datos['tipo_pago'],
                'metodo_pago' => $datos['metodo_pago'],
                'total'       => 0,
                'abonado'     => 0,
                'saldo'       => 0,
                'entregado'   => $datos['tipo_pago'] !== 'separado',
                'nota'        => $datos['nota'] ?? null,
            ]);

            $total = 0;

            foreach ($datos['detalles'] as $linea) {

                $producto = Producto::lockForUpdate()->find($linea['producto_id']);

                if ($producto->stock < $linea['cantidad']) {
                    throw ValidationException::withMessages([
                        'detalles' => "Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock}",
                    ]);
                }

                $subtotal = $linea['cantidad'] * $linea['precio_venta'];

                VentaDetalle::create([
                    'venta_id'       => $venta->id,
                    'producto_id'    => $producto->id,
                    'cantidad'       => $linea['cantidad'],
                    'precio_venta'   => $linea['precio_venta'],
                    'costo_unitario' => $producto->precio_compra,
                    'subtotal'       => $subtotal,
                    'garantia_meses' => $linea['garantia_meses'] ?? 0,
                ]);

                $producto->decrement('stock', $linea['cantidad']);

                $total += $subtotal;
            }

            if ($datos['tipo_pago'] === 'contado') {
                $abonado = $total;
            } else {
                $abonado = min($datos['abono_inicial'] ?? 0, $total);
            }

            $venta->update([
                'total'   => $total,
                'abonado' => $abonado,
                'saldo'   => $total - $abonado,
            ]);

            if ($abonado > 0) {
                Abono::create([
                    'venta_id'    => $venta->id,
                    'fecha'       => $datos['fecha'],
                    'monto'       => $abonado,
                    'metodo_pago' => $datos['metodo_pago'] === 'mixto' ? 'efectivo' : $datos['metodo_pago'],
                    'nota'        => $datos['tipo_pago'] === 'contado' ? 'Pago completo' : 'Abono inicial',
                ]);
            }
        });

        return redirect()->route('ventas.index')
            ->with('success', 'Venta registrada correctamente.');
    }

    public function show(Venta $venta)
    {
        $venta->load('detalles.producto', 'cliente', 'abonos');

        $productoIds = $venta->detalles->pluck('producto_id');

        $comprasPorProducto = CompraDetalle::whereIn('producto_id', $productoIds)
            ->whereHas('compra', function ($q) use ($venta) {
                $q->where('fecha', '<=', $venta->fecha);
            })
            ->with('compra')
            ->orderByDesc('id')
            ->get()
            ->groupBy('producto_id');

        return view('ventas.show', compact('venta', 'comprasPorProducto'));
    }

    public function factura(Venta $venta)
    {
        $venta->load('detalles.producto', 'cliente');
        return view('ventas.factura', compact('venta'));
    }

    public function buscarCliente(Request $request)
    {
        $cliente = Cliente::where('cedula', $request->cedula)->first();

        if (!$cliente) {
            return response()->json(['encontrado' => false]);
        }

        return response()->json([
            'encontrado' => true,
            'nombre'     => $cliente->nombre,
            'telefono'   => $cliente->telefono,
        ]);
    }

    public function registrarAbono(Request $request, Venta $venta)
    {
        $datos = $request->validate([
            'monto'       => 'required|numeric|min:1|max:' . $venta->saldo,
            'fecha'       => 'required|date',
            'metodo_pago' => 'required|in:efectivo,transferencia',
            'nota'        => 'nullable|string|max:200',
        ], [
            'monto.max' => 'El abono no puede superar el saldo pendiente de $' . number_format($venta->saldo, 0),
        ]);

        DB::transaction(function () use ($datos, $venta) {

            Abono::create([
                'venta_id'    => $venta->id,
                'fecha'       => $datos['fecha'],
                'monto'       => $datos['monto'],
                'metodo_pago' => $datos['metodo_pago'],
                'nota'        => $datos['nota'] ?? null,
            ]);

            $venta->increment('abonado', $datos['monto']);
            $venta->decrement('saldo', $datos['monto']);
        });

        return back()->with('success', 'Abono registrado correctamente.');
    }

    public function marcarEntregado(Venta $venta)
    {
        if ($venta->saldo > 0) {
            return back()->with('error', 'No se puede entregar: aún tiene saldo pendiente de $' . number_format($venta->saldo, 0));
        }

        $venta->update(['entregado' => true]);

        return back()->with('success', 'Producto marcado como entregado.');
    }

    public function anular(Request $request, Venta $venta)
    {
        $datos = $request->validate([
            'motivo' => 'required|string|max:200',
        ]);

        if ($venta->anulada) {
            return back()->with('error', 'Esta venta ya fue anulada.');
        }

        $venta->load('detalles');

        DB::transaction(function () use ($venta, $datos) {
            foreach ($venta->detalles as $detalle) {
                Producto::where('id', $detalle->producto_id)
                    ->increment('stock', $detalle->cantidad);
            }

            $venta->update([
                'anulada'          => true,
                'anulada_at'       => now(),
                'motivo_anulacion' => $datos['motivo'],
                'saldo'            => 0,
            ]);
        });

        return redirect()->route('ventas.index')
            ->with('success', 'Venta anulada y stock devuelto al inventario.');
    }
}