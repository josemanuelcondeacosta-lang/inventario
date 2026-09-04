<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = [
        'fecha', 'proveedor', 'factura', 'total',
        'anulada', 'anulada_at', 'motivo_anulacion'
    ];

    protected $casts = [
        'fecha'      => 'date',
        'anulada'    => 'boolean',
        'anulada_at' => 'datetime',
    ];

    public function detalles()
    {
        return $this->hasMany(CompraDetalle::class);
    }
}
