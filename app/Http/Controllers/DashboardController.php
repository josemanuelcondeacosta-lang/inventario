<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Reclamo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes    = Carbon::now()->endOfMonth();

        $ventasMes = Venta::where('anulada', false)
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->sum('total');

        $gananciaMes = VentaDetalle::whereHas('venta', function ($q) use ($inicioMes, $finMes) {
                $q->where('anulada', false)->whereBetween('fecha', [$inicioMes, $finMes]);
            })
            ->select(DB::raw('SUM((precio_venta - costo_unitario) * cantidad) as ganancia'))
            ->value('ganancia') ?? 0;

        $perdidaGarantias = Reclamo::whereBetween('fecha', [$inicioMes, $finMes])->sum('perdida');

        $gananciaNeta = $gananciaMes - $perdidaGarantias;

        $unidadesMes = VentaDetalle::whereHas('venta', function ($q) use ($inicioMes, $finMes) {
                $q->where('anulada', false)->whereBetween('fecha', [$inicioMes, $finMes]);
            })
            ->sum('cantidad');

        $valorInventario = Producto::where('activo', true)
            ->select(DB::raw('SUM(stock * precio_compra) as valor'))
            ->value('valor') ?? 0;

        $stockBajo = Producto::where('activo', true)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->orderBy('stock')
            ->get();

        $masVendidos = VentaDetalle::whereHas('venta', function ($q) use ($inicioMes, $finMes) {
            $q->where('anulada', false)->whereBetween('fecha', [$inicioMes, $finMes]);
        })
        ->select('producto_id', DB::raw('SUM(cantidad) as unidades'))
        ->groupBy('producto_id')
        ->orderByDesc('unidades')
        ->with('producto')
        ->limit(5)
        ->get();

        $ultimasVentas = Venta::with('cliente')
            ->where('anulada', false)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        
        $porCobrar = Venta::with('cliente')
            ->where('anulada', false)
            ->where('saldo', '>', 0)
            ->orderBy('fecha')
            ->get();

        $totalPorCobrar = $porCobrar->sum('saldo');

        $porEntregar = Venta::with('cliente')
            ->where('anulada', false)
            ->where('entregado', false)
            ->orderBy('fecha')
            ->get();

        return view('dashboard', compact(
            'ventasMes', 'gananciaMes', 'unidadesMes', 'valorInventario',
            'stockBajo', 'masVendidos', 'ultimasVentas',
            'porCobrar', 'totalPorCobrar', 'porEntregar',
            'perdidaGarantias', 'gananciaNeta'
        ));
    }
}