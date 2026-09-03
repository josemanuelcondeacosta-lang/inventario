<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id', 'nombre', 'talla', 'color',
        'precio_compra', 'precio_venta', 'stock', 'stock_minimo', 'activo'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
