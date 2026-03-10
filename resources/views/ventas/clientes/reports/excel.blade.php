<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Clientes</title>
</head>

<body>
    <div>
        <table>
            <tr>
                <td style="width: 220px; font-weight: bold;">EMPRESA</td>
                <td style="font-size: 12px;">{{ $company->razon_social }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">RUC</td>
                <td style="font-size: 10px;">{{ $company->ruc }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">DIRECCIÓN</td>
                <td style="font-size: 10px;">{{ $company->direccion_fiscal }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">TELÉFONO</td>
                <td style="font-size: 10px;">{{ $company->telefono }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">EMAIL</td>
                <td style="font-size: 10px;">{{ $company->correo }}</td>
            </tr>
        </table>

        <div class="header-title">
            LISTA CLIENTES
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
        </table>

        <!-- Tabla del reporte -->
        <table>
            <thead>
                <tr>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        TIPO DOC
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        TIPO CLIENTE
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        N° DOC
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        NOMBRE
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        TELÉFONO
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        DEPARTAMENTO
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        PROVINCIA
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        DISTRITO
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        ZONA
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        UBIGEO
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $customer)
                    <tr>
                        <td>{{ $customer->tipo_documento }}</td>
                        <td>{{ $customer->tipo_cliente_nombre }}</td>
                        <td>{{ $customer->documento }}</td>
                        <td>{{ $customer->nombre }}</td>
                        <td>{{ $customer->telefono_movil }}</td>
                        <td>{{ $customer->departamento }}</td>
                        <td>{{ $customer->provincia }}</td>
                        <td>{{ $customer->distrito }}</td>

                        <td>{{ $customer->zona }}</td>
                        <td>{{ $customer->distrito_id }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ now()->year }} {{ $company->razon_social }} - Todos los derechos reservados</p>
        </div>
    </div>
</body>

</html>
