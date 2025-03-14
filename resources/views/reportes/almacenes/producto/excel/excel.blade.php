<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Productos</title>
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
            REPORTE PRODUCTOS
        </div>

        <!-- Información adicional -->
        <table class="info-table">
            <tr>
                <td style="width:160px;"><strong>USUARIO IMPRESIÓN:</strong></td>
                <td>{{ Auth::user()->usuario }}</td>
            </tr>
            <tr>
                <td style="width:160px;"><strong>FECHA IMPRESIÓN:</strong></td>
                <td>{{ now()->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <td style="width:160px;"><strong>SEDE:</strong></td>
                <td>{{ $filters->get('sede_nombre') }}</td>
            </tr> 
            <tr>
                <td style="width:160px;"><strong>ALMACÉN:</strong></td>
                <td>{{ $filters->get('almacen_nombre') }}</td>
            </tr> 
        </table>

        <!-- Tabla del reporte -->
        <table>
            <thead>
                <tr>
                    <th width="30" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">SEDE</th>
                    <th width="30" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">ALMACÉN</th>
                    <th width="30" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">PRODUCTO</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">COLOR</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">TALLA</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">MODELO</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">CATEGORÍA</th>
                    <th width="15" style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">STOCK</th>
                </tr>
            </thead>
            <tbody>
               

                @foreach($productos as $item)
                <tr>
                    <td>{{ $item->sede }}</td>
                    <td>{{ $item->almacen }}</td>
                    <td>{{ $item->producto }}</td>
                    <td>{{ $item->color }}</td>
                    <td>{{ $item->talla }}</td>
                    <td>{{ $item->modelo }}</td>
                    <td>{{ $item->categoria }}</td>
                    <td>{{ $item->stock }}</td>
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
