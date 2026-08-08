<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Recuperación | Inventory System</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 24px;
        }
        .email-container {
            max-width: 520px;
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
            font-size: 20px;
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
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .text {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 20px;
        }
        .code-box {
            background-color: #f1f5f9;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .code-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 8px;
        }
        .code-number {
            font-size: 36px;
            font-weight: 900;
            letter-spacing: 10px;
            color: #0f172a;
            font-family: 'Courier New', Courier, monospace;
        }
        .expiry-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 10px;
            background-color: #fef3c7;
            color: #92400e;
            font-size: 11px;
            font-weight: 700;
            border-radius: 9999px;
        }
        .warning-box {
            background-color: #f8fafc;
            border-left: 4px solid #0f172a;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
            margin-top: 24px;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 24px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="email-container">
        <!-- Encabezado -->
        <div class="header">
            <h1 class="brand-title">Inventory System</h1>
            <div class="brand-subtitle">Seguridad y Verificación</div>
        </div>

        <!-- Contenido -->
        <div class="content">
            <div class="greeting">¡Hola, {{ $nombre }}!</div>
            <p class="text">
                Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en <strong>Inventory System</strong>. Usa el siguiente código de seguridad de 6 dígitos para completar el proceso:
            </p>

            <!-- Caja del Código -->
            <div class="code-box">
                <div class="code-title">Tu Código de Verificación</div>
                <div class="code-number">{{ $codigo }}</div>
                <div class="expiry-badge">⏱ Válido por 15 minutos</div>
            </div>

            <p class="text" style="font-size: 13px;">
                Ingresa este código en la pantalla de verificación para poder establecer tu nueva contraseña.
            </p>

            <div class="warning-box">
                <strong>¿No solicitaste este cambio?</strong> Si no realizaste esta solicitud, puedes ignorar este mensaje de forma segura. Tu contraseña actual no cambiará sin este código.
            </div>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            &copy; {{ date('Y') }} Inventory System. Todos los derechos reservados.<br>
            Este es un mensaje automático de seguridad, por favor no respondas a este correo.
        </div>
    </div>

</body>
</html>
