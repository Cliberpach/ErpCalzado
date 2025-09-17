<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kardex Stock</title>
</head>

<body>
    <div>
        <table>
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
            REPORTE STOCK
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
                <td>{{ $filtros->get('fecha_inicio') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>FECHA FIN:</strong></td>
                <td>{{ $filtros->get('fecha_fin') }}</td>
            </tr>
             <tr>
            <td class="label"><strong>MODELO:</strong></td>
                <td>{{ $filtros->get('modelo_nombre') }}</td>
            </tr>
        </table>

        <!-- Tabla del reporte -->
        <table>
            <thead>
                <tr>
                    <th width="15"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        PRODUCTO</th>
                    <th width="15"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        COLOR</th>

                    @foreach ($tallas as $talla)
                        <th width="10"
                            style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                            {{ $talla->descripcion }}</th>
                    @endforeach

                </tr>
            </thead>
            <tbody>
                @foreach ($resultado as $item)
                    <tr>
                        <td>{{ $item->producto_nombre }}</td>
                        <td>{{ $item->color_nombre }}</td>
                        @foreach ($tallas as $talla)
                            <td style="text-align:center;">
                                {{ $item->{'talla_' . $talla->id} }}
                            </td>
                        @endforeach
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
