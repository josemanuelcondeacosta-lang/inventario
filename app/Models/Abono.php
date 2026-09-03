<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    protected $fillable = ['venta_id', 'fecha', 'monto', 'metodo_pago', 'nota'];
    protected $casts = ['fecha' => 'date'];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}