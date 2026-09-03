<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    protected $fillable = [ 'venta_id', 'producto_id', 'cantidad', 'precio_venta', 'costo_unitario', 'subtotal', 'garantia_meses'];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
