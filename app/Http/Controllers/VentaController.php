<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\CompraDetalle;
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
            'cedula'                    => 'nullable|string|max:20',
            'nombre_cliente'            => 'nullable|string|max:150',
            'telefono'                  => 'nullable|string|max:20',
            'detalles'                  => 'required|array|min:1',
            'detalles.*.producto_id'    => 'required|exists:productos,id',
            'detalles.*.cantidad'       => 'required|integer|min:1',
            'detalles.*.precio_venta'   => 'required|numeric|min:0',
            'detalles.*.garantia_meses' => 'nullable|integer|min:0',
        ]);

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
                'cliente_id' => $clienteId,
                'fecha'      => $datos['fecha'],
                'total'      => 0,
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

            $venta->update(['total' => $total]);
        });

        return redirect()->route('ventas.index')
            ->with('success', 'Venta registrada y stock descontado.');
    }

    public function show(Venta $venta)
    {
        $venta->load('detalles.producto', 'cliente');

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
}