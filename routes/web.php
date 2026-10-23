<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ReclamoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('categorias', CategoriaController::class)->except('show');

    Route::resource('productos', ProductoController::class)->except('show');

    Route::get('compras', [CompraController::class, 'index'])->name('compras.index');
    Route::get('compras/create', [CompraController::class, 'create'])->name('compras.create');
    Route::post('compras', [CompraController::class, 'store'])->name('compras.store');
    Route::get('compras/{compra}', [CompraController::class, 'show'])->name('compras.show');
    Route::post('compras/{compra}/anular', [CompraController::class, 'anular'])->name('compras.anular');

    Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('ventas/create', [VentaController::class, 'create'])->name('ventas.create');
    Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('ventas/buscar-cliente', [VentaController::class, 'buscarCliente'])->name('ventas.buscarCliente');
    Route::get('ventas/{venta}/factura', [VentaController::class, 'factura'])->name('ventas.factura');
    Route::post('ventas/{venta}/abono', [VentaController::class, 'registrarAbono'])->name('ventas.abono');
    Route::post('ventas/{venta}/entregar', [VentaController::class, 'marcarEntregado'])->name('ventas.entregar');
    Route::post('ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
    Route::get('ventas/{venta}/reclamo', [ReclamoController::class, 'create'])->name('reclamos.create');
    Route::post('ventas/{venta}/reclamo', [ReclamoController::class, 'store'])->name('reclamos.store');
    Route::get('ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');

    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');

    Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::get('clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');

    Route::get('reclamos', [ReclamoController::class, 'index'])->name('reclamos.index');
    
});

require __DIR__.'/auth.php';