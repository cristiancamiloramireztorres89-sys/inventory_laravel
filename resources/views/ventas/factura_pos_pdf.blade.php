<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factura POS #{{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 4px 6px;
            padding: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 7.5pt;
            line-height: 1.25;
            color: #111827;
            margin: 0;
            padding: 0;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .font-bold   { font-weight: bold; }

        .brand-name {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .subtitle {
            font-size: 6.5pt;
            color: #4b5563;
            margin-bottom: 1px;
        }

        .divider {
            border-top: 1px dashed #9ca3af;
            margin: 4px 0;
        }

        .double-divider {
            border-top: 1.5px dashed #111827;
            margin: 5px 0;
        }

        .info-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .info-table td {
            font-size: 7pt;
            padding: 1px 0;
            vertical-align: top;
        }

        .items-table {
            margin-top: 3px;
        }

        .items-table th {
            font-size: 6.5pt;
            font-weight: bold;
            color: #374151;
            border-bottom: 1px dashed #6b7280;
            padding: 2px 2px;
            text-align: left;
        }

        .items-table td {
            font-size: 7pt;
            padding: 2px 2px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .item-name {
            font-weight: bold;
            color: #111827;
        }

        .item-sub {
            font-size: 6pt;
            color: #6b7280;
            margin-top: 1px;
        }

        .totals-table td {
            font-size: 7.2pt;
            padding: 1.5px 0;
        }

        .total-label {
            font-size: 8.5pt;
            font-weight: bold;
        }

        .total-amount {
            font-size: 10pt;
            font-weight: bold;
        }

        .footer {
            margin-top: 6px;
            font-size: 6.5pt;
            text-align: center;
            color: #4b5563;
        }

        .barcode {
            font-family: 'Courier', monospace;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 3px;
            margin: 3px 0 1px 0;
            color: #111827;
        }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <div class="text-center">
        <div class="brand-name">INVENTORY SYSTEM</div>
        <div class="subtitle font-bold">SISTEMA INTEGRAL DE VENTAS</div>
        <div class="subtitle">NIT: 900.123.456-7</div>
        <div class="subtitle">Av. Principal #100 - Local 1</div>
        <div class="subtitle">Tel: (601) 789-0123 • Cel: 300 123 4567</div>
    </div>

    <div class="divider"></div>

    {{-- Datos de la Factura --}}
    <table class="info-table">
        <tr>
            <td class="font-bold" style="width: 40%;">FACTURA POS:</td>
            <td class="text-right font-bold" style="width: 60%;">#VEN-{{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td class="font-bold">FECHA:</td>
            <td class="text-right">{{ $venta->fecha ? \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A') }}</td>
        </tr>
        <tr>
            <td class="font-bold">CAJERO/A:</td>
            <td class="text-right">{{ $venta->usuario->nombre ?? 'Vendedor' }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- Datos del Cliente --}}
    <table class="info-table">
        <tr>
            <td class="font-bold" style="width: 32%;">CLIENTE:</td>
            <td class="text-right font-bold" style="width: 68%;">{{ $venta->cliente->nombre ?? 'Cliente Ocasional' }}</td>
        </tr>
        @if(!empty($venta->cliente->telefono))
        <tr>
            <td class="font-bold">TELÉFONO:</td>
            <td class="text-right">{{ $venta->cliente->telefono }}</td>
        </tr>
        @endif
        @if(!empty($venta->cliente->correo))
        <tr>
            <td class="font-bold">CORREO:</td>
            <td class="text-right" style="font-size: 6pt;">{{ $venta->cliente->correo }}</td>
        </tr>
        @endif
    </table>

    <div class="divider"></div>

    {{-- Tabla de Productos Formato Estándar POS --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 14%;">CANT</th>
                <th style="width: 54%;">DESCRIPCIÓN</th>
                <th style="width: 32%;" class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td class="font-bold">{{ $detalle->cantidad }}</td>
                <td>
                    <div class="item-name">{{ $detalle->producto->nombre ?? 'Producto #' . $detalle->id_producto }}</div>
                    <div class="item-sub">{{ $detalle->cantidad }} x ${{ number_format($detalle->precio_unitario, 2) }}</div>
                </td>
                <td class="text-right font-bold" style="font-size: 7.5pt;">${{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    {{-- Totales --}}
    <table class="totals-table">
        <tr>
            <td style="width: 50%;">TOTAL ARTÍCULOS:</td>
            <td class="text-right" style="width: 50%;">{{ $venta->detalles->sum('cantidad') }} unid.</td>
        </tr>
        <tr>
            <td>SUBTOTAL:</td>
            <td class="text-right">${{ number_format($venta->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>IVA (0%):</td>
            <td class="text-right">$0.00</td>
        </tr>
    </table>

    <div class="double-divider"></div>

    <table class="totals-table">
        <tr>
            <td class="total-label" style="width: 45%;">TOTAL A PAGAR:</td>
            <td class="text-right total-amount" style="width: 55%;">${{ number_format($venta->total, 2) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- Pie de Ticket --}}
    <div class="footer">
        <div class="font-bold" style="font-size: 7.5pt; color: #111827; margin-bottom: 2px;">¡GRACIAS POR SU COMPRA!</div>
        <div style="font-size: 6pt;">Conserve este comprobante para cualquier cambio, devolución o garantía.</div>
        
        <div class="barcode">
            |||| | |||||| || ||||| ||||
        </div>
        <div style="font-size: 5.5pt; color: #6b7280;">
            AUTORIZACIÓN DIAN No. 18760000001
        </div>
    </div>

</body>
</html>
