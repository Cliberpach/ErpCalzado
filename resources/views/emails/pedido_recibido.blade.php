<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recibimos tu pedido</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; max-width: 560px; margin: 0 auto; padding: 24px;">
    <h2 style="margin-top: 0;">¡Gracias por tu compra, {{ $reserva->cliente_nombre }}!</h2>

    <p>
        Recibimos tu pedido <strong>{{ $reserva->codigo_pedido_ecommerce }}</strong> y lo
        estamos procesando. Te avisaremos por correo apenas se confirme el pago
        y quede listo.
    </p>

    <table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
        <thead>
            <tr>
                <th style="text-align: left; border-bottom: 1px solid #ddd; padding: 6px 0;">Producto</th>
                <th style="text-align: center; border-bottom: 1px solid #ddd; padding: 6px 0;">Cant.</th>
                <th style="text-align: right; border-bottom: 1px solid #ddd; padding: 6px 0;">Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reserva->detalle as $item)
                <tr>
                    <td style="padding: 6px 0;">
                        {{ $item->producto->nombre ?? 'Producto' }}
                        @if ($item->color || $item->talla)
                            <br>
                            <span style="color: #666; font-size: 12px;">
                                {{ $item->color->descripcion ?? '' }}
                                {{ $item->talla ? '· Talla ' . $item->talla->descripcion : '' }}
                            </span>
                        @endif
                    </td>
                    <td style="text-align: center; padding: 6px 0;">{{ $item->cantidad }}</td>
                    <td style="text-align: right; padding: 6px 0;">S/ {{ number_format((float) $item->precio_venta_1, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin: 0 0 24px;">
        <tr>
            <td style="padding: 4px 0; color: #666;">Total</td>
            <td style="padding: 4px 0; text-align: right;"><strong>S/ {{ number_format((float) $reserva->total, 2) }}</strong></td>
        </tr>
    </table>

    @if ($reserva->sedeRecojo)
        <p>Elegiste recoger en <strong>{{ $reserva->sedeRecojo->nombre }}</strong>.</p>
    @endif

    <p style="color: #666; font-size: 13px;">
        Si tienes dudas, responde a este correo.
    </p>
</body>
</html>
