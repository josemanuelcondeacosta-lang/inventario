<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->string('nombre', 150);
            $table->string('talla', 20)->nullable();
            $table->string('color', 50)->nullable();
            $table->decimal('precio_compra', 12, 2);
            $table->decimal('precio_venta', 12, 2);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
