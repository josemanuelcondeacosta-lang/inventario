<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('categorias', CategoriaController::class)->except('show');

    Route::resource('productos', ProductoController::class)->except('show');

    Route::get('compras', [CompraController::class, 'index'])->name('compras.index');
    Route::get('compras/create', [CompraController::class, 'create'])->name('compras.create');
    Route::post('compras', [CompraController::class, 'store'])->name('compras.store');
    Route::get('compras/{compra}', [CompraController::class, 'show'])->name('compras.show');

    Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('ventas/create', [VentaController::class, 'create'])->name('ventas.create');
    Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::get('ventas/buscar-cliente', [VentaController::class, 'buscarCliente'])->name('ventas.buscarCliente');
    Route::get('ventas/{venta}/factura', [VentaController::class, 'factura'])->name('ventas.factura');
    Route::get('ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');

    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
});

require __DIR__.'/auth.php';
