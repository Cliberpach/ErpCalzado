<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORTE CUENTAS</title>
</head>
<body>
    <div>
        <table >
            <tr>
                <td style="width: 220px; font-weight: bold;">EMPRESA</td>
                <td style="font-size: 12px;">{{ $empresa->razon_social }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">RUC</td>
                <td style="font-size: 10px;">{{ $empresa->ruc }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">DIRECCIÓN</td>
                <td style="font-size: 10px;">{{ $empresa->direccion }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">TELÉFONO</td>
                <td style="font-size: 10px;">{{ $empresa->telefono }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">EMAIL</td>
                <td style="font-size: 10px;">{{ $empresa->correo }}</td>
            </tr>
        </table>

        <div class="header-title">
            REPORTE KARDEX VALORIZADO
        </div>

        <!-- Información adicional -->
        <table class="info-table">
            <tr>
                <td style="width:160px;"><strong>USUARIO IMPRESIÓN:</strong></td>
                <td>{{ Auth::user()->name }}</td>
            </tr>
            <tr>
                <td style="width:160px;"><strong>FECHA IMPRESIÓN:</strong></td>
                <td>{{ now()->format('Y-m-d H:i:s') }}</td>
            </tr>
             <tr>
                <td class="label"><strong>FECHA INICIO:</strong></td>
                <td>{{ $filters->get('fecha_inicio')}}</td>
            </tr>
            <tr>
                <td class="label"><strong>FECHA FIN:</strong></td>
                <td>{{ $filters->get('fecha_fin')}}</td>
            </tr>
            <tr>
                <td class="label"><strong>MONEDA:</strong></td>
                <td>{{ $filters->get('moneda') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>BANCO:</strong></td>
                <td>{{ $filters->get('banco_nombre') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>N° CUENTA:</strong></td>
                <td>{{ $filters->get('nro_cuenta') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>INGRESOS:</strong></td>
                <td>{{ number_format($reporte->total_ingresos, 2, '.', ',') }}</td>
                <td class="label"><strong>EGRESOS:</strong></td>
                <td>{{ number_format($reporte->total_egresos, 2, '.', ',') }}</td>
                <td class="label"><strong>SALDO:</strong></td>
                <td>{{ number_format($reporte->saldo, 2, '.', ',') }}</td>
            </tr>
        </table>

        <!-- Tabla del reporte -->
        <table>
            <thead>
                <tr>
                    <th width="30" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">FECHA</th>
                    <th width="30" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">CUENTA</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">MÉTODO PAGO</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">TIPO</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">DOC</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">ENTRADA</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">SALIDA</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">SALDO</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">REGISTRADOR</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte->kardex as $item)
                    <tr>
                       <td>{{ $item->fecha_registro }}</td>
                        <td>{{ $item->banco_abreviatura.':'.$item->nro_cuenta }}</td>
                        <td>{{ $item->metodo_pago_nombre }}</td>
                        <td>{{ $item->tipo_documento }}</td>
                        <td>{{ $item->documento }}</td>
                        <td style="text-align: right;">{{ number_format($item->entrada, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($item->salida, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($item->stock_posterior, 2) }}</td>
                        <td>{{ $item->registrador_nombre }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ now()->year }} {{ $empresa->razon_social }} - Todos los derechos reservados</p>
        </div>
    </div>
</body>
</html>
