<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reclamo extends Model
{
    protected $fillable = [
        'venta_id', 'venta_detalle_id', 'fecha', 'resultado',
        'producto_entregado_id', 'cantidad', 'reingresa_stock',
        'perdida', 'descripcion',
    ];

    protected $casts = [
        'fecha'           => 'date',
        'reingresa_stock' => 'boolean',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function detalle()
    {
        return $this->belongsTo(VentaDetalle::class, 'venta_detalle_id');
    }

    public function productoEntregado()
    {
        return $this->belongsTo(Producto::class, 'producto_entregado_id');
    }

    public function resultadoTexto()
    {
        return match ($this->resultado) {
            'cambio_mismo' => 'Cambio por la misma prenda',
            'cambio_otro'  => 'Cambio por otra prenda',
            'devolucion'   => 'Devolución del dinero',
            'rechazado'    => 'Rechazado',
            'proveedor'    => 'Respondió el proveedor',
        };
    }
}