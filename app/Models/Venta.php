<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'cliente_id', 'fecha', 'tipo_pago', 'metodo_pago',
        'total', 'abonado', 'saldo', 'entregado', 'nota',
        'anulada', 'anulada_at', 'motivo_anulacion'
    ];

    protected $casts = [
        'fecha'      => 'date',
        'entregado'  => 'boolean',
        'anulada'    => 'boolean',
        'anulada_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function abonos()
    {
        return $this->hasMany(Abono::class);
    }

    public function tienePendiente()
    {
        return $this->saldo > 0;
    }

    public function estadoTexto()
    {
        if ($this->anulada) return 'Anulada';
        if ($this->saldo > 0 && !$this->entregado) return 'Separado';
        if ($this->saldo > 0) return 'Debe';
        if (!$this->entregado) return 'Por entregar';
        return 'Pagada';
    }

        public function reclamos()
    {
        return $this->hasMany(Reclamo::class);
    }
}