<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = ['fecha', 'proveedor', 'factura', 'total'];
    protected $casts = ['fecha' => 'date'];

    public function detalles()
    {
        return $this->hasMany(CompraDetalle::class);
    }
}
