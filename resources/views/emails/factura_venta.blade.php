<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Compra | Inventory System</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 24px;
        }
        .email-container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #0f172a;
            padding: 28px 24px;
            text-align: center;
            color: #ffffff;
        }
        .brand-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
            font-weight: 600;
        }
        .content {
            padding: 32px 28px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .text {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 20px;
        }
        .invoice-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            margin: 20px 0;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        .invoice-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }
        .invoice-number {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 10px;
        }
        .items-table th {
            text-align: left;
            padding: 8px 4px;
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table td {
            padding: 10px 4px;
            border-bottom: 1px dashed #e2e8f0;
            vertical-align: top;
        }
        .total-row td {
            border-top: 2px solid #0f172a;
            border-bottom: none;
            padding-top: 12px;
            font-weight: 800;
            font-size: 15px;
            color: #0f172a;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            font-size: 12px;
            font-weight: 700;
            border-radius: 9999px;
            margin-bottom: 20px;
        }
        .attachment-notice {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 13px;
            color: #1e40af;
            margin-top: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <div class="email-container">
        <!-- Encabezado -->
        <div class="header">
            <h1 class="brand-title">Inventory System</h1>
            <div class="brand-subtitle">Comprobante Oficial de Venta</div>
        </div>

        <!-- Contenido -->
        <div class="content">
            <div class="badge">✓ Compra Registrada Exitosamente</div>

            <div class="greeting">¡Gracias por tu compra, {{ $venta->cliente->nombre ?? 'Estimado Cliente' }}!</div>
            <p class="text">
                Adjuntamos tu comprobante de compra en formato digital (PDF tipo ticket POS). A continuación encontrarás el resumen de tu transacción:
            </p>

            <!-- Caja de Detalles de Factura -->
            <div class="invoice-card">
                <table style="width: 100%; margin-bottom: 12px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 10px;">
                    <tr>
                        <td>
                            <div class="invoice-title">Factura POS</div>
                            <div class="invoice-number">#VEN-{{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td style="text-align: right;">
                            <div class="invoice-title">Fecha</div>
                            <div style="font-size: 13px; font-weight: 600; color: #334155;">
                                {{ $venta->fecha ? \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A') }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 8px;">
                            <div class="invoice-title">Atendido por</div>
                            <div style="font-size: 13px; font-weight: 600; color: #334155;">
                                {{ $venta->usuario->nombre ?? 'Cajero' }}
                            </div>
                        </td>
                        <td style="text-align: right; padding-top: 8px;">
                            <div class="invoice-title">NIT Negocio</div>
                            <div style="font-size: 13px; font-weight: 600; color: #334155;">
                                900.123.456-7
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Tabla de Productos -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Cant</th>
                            <th style="width: 55%;">Producto</th>
                            <th style="width: 30%; text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->detalles as $detalle)
                        <tr>
                            <td style="font-weight: 700; color: #0f172a;">{{ $detalle->cantidad }}</td>
                            <td>
                                <strong style="color: #0f172a;">{{ $detalle->producto->nombre ?? 'Producto #' . $detalle->id_producto }}</strong><br>
                                <span style="font-size: 11px; color: #64748b;">
                                    ${{ number_format($detalle->precio_unitario, 2) }} c/u
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #0f172a;">
                                ${{ number_format($detalle->subtotal, 2) }}
                            </td>
                        </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="2" style="padding-top: 14px;">TOTAL PAGADO:</td>
                            <td style="text-align: right; padding-top: 14px; color: #0f172a; font-size: 17px;">
                                ${{ number_format($venta->total, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Aviso de archivo adjunto -->
            <div class="attachment-notice">
                📎 <span>Encuentra tu <strong>factura oficial en formato PDF adjunta</strong> a este correo lista para descargar o imprimir.</span>
            </div>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <strong>Inventory System</strong> • Av. Principal #100 - Local 1<br>
            Tel: (601) 789-0123 • NIT: 900.123.456-7<br>
            Conserva este correo como comprobante para cualquier garantía o devolución.
        </div>
    </div>

</body>
</html>
