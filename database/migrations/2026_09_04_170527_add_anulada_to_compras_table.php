<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->boolean('anulada')->default(false)->after('total');
            $table->timestamp('anulada_at')->nullable()->after('anulada');
            $table->string('motivo_anulacion', 200)->nullable()->after('anulada_at');
        });
    }

    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn(['anulada', 'anulada_at', 'motivo_anulacion']);
        });
    }
};
