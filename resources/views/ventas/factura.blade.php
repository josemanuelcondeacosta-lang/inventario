<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #{{ $venta->id }}</title>
    <style>
        * { font-family: Arial, sans-serif; }
        body { max-width: 420px; margin: 20px auto; font-size: 13px; color: #000; }
        h1 { font-size: 18px; margin: 0; }
        .centro { text-align: center; }
        .der { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        th, td { padding: 4px 2px; }
        thead th { border-bottom: 1px solid #000; text-align: left; font-size: 12px; }
        tfoot td { border-top: 1px solid #000; font-weight: bold; padding-top: 6px; }
        .datos { margin: 12px 0; line-height: 1.6; }
        .linea { border-top: 1px dashed #999; margin: 12px 0; }
        .pie { font-size: 11px; text-align: center; color: #444; margin-top: 16px; }
        .botones { text-align: center; margin: 20px 0; }
        button { padding: 8px 16px; cursor: pointer; }
        @media print { .botones { display: none; } body { margin: 0; } }
    </style>
</head>
<body>

    <div class="botones">
        <button onclick="window.print()">Imprimir</button>
    </div>

    <div class="centro">
        <h1>NOMBRE DE LA TIENDA</h1>
        <div>NIT / Cédula: 000000000</div>
        <div>Barranquilla — Tel. 000 000 0000</div>
    </div>

    <div class="linea"></div>

    <div class="datos">
        <strong>Factura N°:</strong> {{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}<br>
        <strong>Fecha:</strong> {{ $venta->fecha->format('d/m/Y') }}<br>
        <strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Consumidor final' }}<br>
        @if ($venta->cliente)
            <strong>Cédula:</strong> {{ $venta->cliente->cedula }}<br>
            @if ($venta->cliente->telefono)
                <strong>Teléfono:</strong> {{ $venta->cliente->telefono }}
            @endif
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="der">Cant</th>
                <th class="der">V. Unit</th>
                <th class="der">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->detalles as $d)
                <tr>
                    <td>
                        {{ $d->producto->nombre }}<br>
                        <small>{{ $d->producto->talla }} {{ $d->producto->color }}</small>
                    </td>
                    <td class="der">{{ $d->cantidad }}</td>
                    <td class="der">{{ number_format($d->precio_venta, 0) }}</td>
                    <td class="der">{{ number_format($d->subtotal, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="der">TOTAL</td>
                <td class="der">${{ number_format($venta->total, 0) }}</td>
            </tr>
        </tfoot>
    </table>

    @php
        $conGarantia = $venta->detalles->where('garantia_meses', '>', 0);
    @endphp

    @if ($conGarantia->count())
        <div class="linea"></div>
        <strong>Garantías</strong>
        <div class="datos">
            @foreach ($conGarantia as $d)
                {{ $d->producto->nombre }} — {{ $d->garantia_meses }} meses
                (hasta {{ $venta->fecha->copy()->addMonths($d->garantia_meses)->format('d/m/Y') }})<br>
            @endforeach
        </div>
    @endif

    <div class="linea"></div>

    <div class="pie">
        Conserve esta factura para efectos de garantía.<br>
        ¡Gracias por su compra!
    </div>

</body>
</html>