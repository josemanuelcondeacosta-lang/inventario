<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Reclamo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->desde
            ? Carbon::parse($request->desde)
            : Carbon::now()->startOfMonth();

        $hasta = $request->hasta
            ? Carbon::parse($request->hasta)
            : Carbon::now()->endOfMonth();

        $rango = [$desde->format('Y-m-d'), $hasta->format('Y-m-d')];

        $totalVentas = Venta::where('anulada', false)
            ->whereBetween('fecha', $rango)
            ->sum('total');

        $numVentas = Venta::where('anulada', false)
            ->whereBetween('fecha', $rango)
            ->count();

        $totalCompras = Compra::where('anulada', false)
            ->whereBetween('fecha', $rango)
            ->sum('total');

        $detallesDelRango = VentaDetalle::whereHas('venta', function ($q) use ($rango) {
            $q->where('anulada', false)->whereBetween('fecha', $rango);
        });

        $ganancia = (clone $detallesDelRango)
            ->select(DB::raw('SUM((precio_venta - costo_unitario) * cantidad) as g'))
            ->value('g') ?? 0;

        $unidades = (clone $detallesDelRango)->sum('cantidad');

        $perdidaGarantias = Reclamo::whereBetween('fecha', $rango)->sum('perdida');
        $gananciaNeta = $ganancia - $perdidaGarantias;

        $porProducto = VentaDetalle::whereHas('venta', function ($q) use ($rango) {
                $q->where('anulada', false)->whereBetween('fecha', $rango);
            })
            ->select(
                'producto_id',
                DB::raw('SUM(cantidad) as unidades'),
                DB::raw('SUM(subtotal) as ingresos'),
                DB::raw('SUM((precio_venta - costo_unitario) * cantidad) as ganancia')
            )
            ->groupBy('producto_id')
            ->orderByDesc('unidades')
            ->with('producto.categoria')
            ->get();

        $inventario = Producto::where('activo', true)
            ->with('categoria')
            ->orderBy('nombre')
            ->get();

        return view('reportes.index', compact(
            'desde', 'hasta', 'totalVentas', 'numVentas', 'totalCompras',
            'ganancia', 'unidades', 'porProducto', 'inventario', 'perdidaGarantias', 'gananciaNeta'
        ));
    }
}