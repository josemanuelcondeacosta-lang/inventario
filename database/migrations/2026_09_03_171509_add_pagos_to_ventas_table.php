<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('tipo_pago', ['contado', 'fiado', 'separado'])
                ->default('contado')->after('fecha');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'mixto'])
                ->default('efectivo')->after('tipo_pago');
            $table->decimal('abonado', 12, 2)->default(0)->after('total');
            $table->decimal('saldo', 12, 2)->default(0)->after('abonado');
            $table->boolean('entregado')->default(true)->after('saldo');
            $table->text('nota')->nullable()->after('entregado');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_pago', 'metodo_pago', 'abonado', 'saldo', 'entregado', 'nota'
            ]);
        });
    }
};
