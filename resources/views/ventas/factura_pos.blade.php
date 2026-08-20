<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura POS #{{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }} | Inventory System</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        .pos-container {
            width: 100%;
            max-width: 330px;
            margin: 0 auto;
        }

        .ticket-card {
            width: 100%;
            max-width: 330px;
            margin: 0 auto;
            background: #ffffff;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .ticket-divider {
            border-top: 1px dashed #cbd5e1;
            margin: 10px 0;
        }

        .ticket-divider-double {
            border-top: 2px dashed #0f172a;
            margin: 12px 0;
        }

        @media print {
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .pos-container,
            .ticket-card {
                width: 76mm !important;
                max-width: 76mm !important;
                padding: 2mm !important;
                margin: 0 auto !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-6 px-3 flex flex-col items-center justify-start text-slate-900 font-sans antialiased">

    {{-- Barra Superior de Botones --}}
    <div class="pos-container no-print flex items-center justify-between gap-2 mb-4">
        <a href="{{ auth()->user()->esAdmin() ? route('admin.ventas') : route('vendedor.ventas') }}" 
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold shadow-xs transition-colors">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ auth()->user()->esAdmin() ? route('admin.ventas.factura.pdf', $venta->id_venta) : route('vendedor.ventas.factura.pdf', $venta->id_venta) }}" 
               target="_blank" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-colors">
                <i class="bi bi-file-earmark-pdf-fill"></i> PDF
            </a>
            <button onclick="window.print()" 
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition-colors cursor-pointer">
                <i class="bi bi-printer-fill"></i> Imprimir
            </button>
        </div>
    </div>

    {{-- Ticket POS Térmico Vertical --}}
    <div class="ticket-card p-6 rounded-2xl shadow-xl border border-slate-200 text-xs leading-normal">
        
        {{-- Encabezado del Comercio --}}
        <div class="text-center space-y-0.5">
            <h1 class="text-base font-extrabold text-slate-900 tracking-wide uppercase">INVENTORY SYSTEM</h1>
            <p class="text-[10px] text-slate-500 font-semibold tracking-tight">SISTEMA INTEGRAL DE VENTAS</p>
            <p class="text-[10px] text-slate-600">NIT: 900.123.456-7</p>
            <p class="text-[10px] text-slate-500">Av. Principal #100 - Local 1</p>
            <p class="text-[10px] text-slate-500">Tel: (601) 789-0123 • Cel: 300 123 4567</p>
        </div>

        <div class="ticket-divider"></div>

        {{-- Información de la Venta --}}
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between items-center">
                <span class="font-bold text-slate-700 text-[11px]">FACTURA POS:</span>
                <span class="font-extrabold text-slate-900">#VEN-{{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-bold text-slate-700 text-[11px]">FECHA:</span>
                <span class="text-slate-800">{{ $venta->fecha ? \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-bold text-slate-700 text-[11px]">CAJERO/A:</span>
                <span class="text-slate-800 font-semibold">{{ $venta->usuario->nombre ?? auth()->user()->nombre }}</span>
            </div>
        </div>

        <div class="ticket-divider"></div>

        {{-- Datos del Cliente --}}
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between items-start gap-2">
                <span class="font-bold text-slate-700 text-[11px] flex-shrink-0">CLIENTE:</span>
                <span class="font-bold text-slate-900 text-right break-words">{{ $venta->cliente->nombre ?? 'Cliente General' }}</span>
            </div>
            @if(!empty($venta->cliente->telefono))
            <div class="flex justify-between items-center">
                <span class="font-bold text-slate-700 text-[11px]">TELÉFONO:</span>
                <span class="text-slate-800">{{ $venta->cliente->telefono }}</span>
            </div>
            @endif
            @if(!empty($venta->cliente->correo))
            <div class="flex justify-between items-center gap-2">
                <span class="font-bold text-slate-700 text-[11px] flex-shrink-0">CORREO:</span>
                <span class="text-[10px] text-slate-600 truncate text-right">{{ $venta->cliente->correo }}</span>
            </div>
            @endif
        </div>

        <div class="ticket-divider"></div>

        {{-- Tabla de Productos con formato POS estándar (CANT, DESCRIPCIÓN con P.UNIT, TOTAL) --}}
        <table class="w-full text-xs border-collapse">
            <thead>
                <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-dashed border-slate-300">
                    <th class="text-left pb-1.5" style="width: 14%;">CANT</th>
                    <th class="text-left pb-1.5" style="width: 52%;">DESCRIPCIÓN</th>
                    <th class="text-right pb-1.5" style="width: 34%;">TOTAL</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dashed divide-slate-100">
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td class="align-top py-2 text-slate-900 font-bold">
                        {{ $detalle->cantidad }}
                    </td>
                    <td class="align-top py-2 pr-2">
                        <div class="font-bold text-slate-900 leading-tight break-words">
                            {{ $detalle->producto->nombre ?? 'Producto #' . $detalle->id_producto }}
                        </div>
                        <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                            {{ $detalle->cantidad }} x ${{ number_format($detalle->precio_unitario, 2) }}
                        </div>
                    </td>
                    <td class="align-top py-2 text-right font-black text-slate-900 text-xs whitespace-nowrap">
                        ${{ number_format($detalle->subtotal, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ticket-divider"></div>

        {{-- Totales --}}
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between items-center text-slate-600">
                <span>TOTAL ARTÍCULOS:</span>
                <span class="font-semibold">{{ $venta->detalles->sum('cantidad') }} unid.</span>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>SUBTOTAL:</span>
                <span>${{ number_format($venta->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between items-center text-slate-600">
                <span>IVA (0%):</span>
                <span>$0.00</span>
            </div>

            <div class="ticket-divider-double"></div>

            <div class="flex justify-between items-center font-sans text-slate-900 font-black text-sm pt-0.5">
                <span class="tracking-tight">TOTAL A PAGAR:</span>
                <span class="text-base font-black text-slate-900">${{ number_format($venta->total, 2) }}</span>
            </div>
        </div>

        <div class="ticket-divider" style="margin-top: 14px; margin-bottom: 12px;"></div>

        {{-- Pie del Ticket --}}
        <div class="text-center space-y-1 text-slate-600">
            <p class="font-extrabold text-slate-900 text-xs">¡GRACIAS POR SU COMPRA!</p>
            <p class="text-[10px] text-slate-500 leading-tight">Conserve este comprobante para cualquier cambio, devolución o garantía.</p>
            
            <div class="font-mono text-sm tracking-widest font-bold text-slate-800 pt-1">
                |||| | |||||| || ||||| ||||
            </div>
            <p class="text-[8px] text-slate-400">AUTORIZACIÓN DIAN No. 18760000001</p>
        </div>

    </div>

</body>
</html>
