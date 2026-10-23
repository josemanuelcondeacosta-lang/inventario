<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas');
            $table->foreignId('venta_detalle_id')->constrained('venta_detalles');
            $table->date('fecha');
            $table->enum('resultado', [
                'cambio_mismo',
                'cambio_otro',
                'devolucion',
                'rechazado',
                'proveedor',
            ]);
            // Producto entregado al cliente (en los cambios)
            $table->foreignId('producto_entregado_id')->nullable()->constrained('productos');
            $table->integer('cantidad')->default(1);
            // Si la prenda devuelta sirve para revender
            $table->boolean('reingresa_stock')->default(false);
            // Pérdida calculada por el sistema
            $table->decimal('perdida', 12, 2)->default(0);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamos');
    }
};
