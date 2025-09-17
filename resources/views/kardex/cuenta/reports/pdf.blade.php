<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORTE CUENTAS</title>
    <link rel="icon" href="{{ asset('img/gas.ico') }}" type="image/x-icon">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            height: 100%;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 30px auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .details p, .totals p {
            margin: 5px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td {
            padding: 6px;
            vertical-align: top;
        }

        .header-table td {
            border: none;
        }

        .info-table-custom {
            margin-top: 20px;
            width: 100%;
        }

        .info-table-custom td {
            font-size: 12px;
            border: 1px solid #d4f1ff;
        }

        .info-table-custom .label {
            font-weight: bold;
            background-color: #f5f5f5;
            font-size: 11px;
        }



        .tbl-report-sale {
            margin-top: 20px;
            width: 100%;
            border: 1px solid #ccc;
        }

        .tbl-report-sale th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: left;
            padding: 6px;
            border: 1px solid #ccc;
            font-size: 12px;
        }

        .tbl-report-sale td {
            padding: 6px;
            border: 1px solid #ccc;
            font-size: 12px;
        }


        /*======== FOOTER ==========*/
        @page {
            margin: 30px 50px 90px 50px;
        }

    #footer {
        position: fixed;
        left: 0px;
        bottom: -180px;
        right: 0px;
        height: 130px;
        background-color: #e1e1e1;
        color: #3a5068;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 400;
        font-size: 11px;
        line-height: 1.5;
        text-align: center;
        box-shadow: inset 0 1px 3px rgb(58 80 104 / 0.1);
        border-top: 1px solid #c7d6e2;
    }


    </style>
</head>
<body>

    <div id="footer">
        <p>&copy; {{ now()->year }} {{ $empresa->razon_social }} - Todos los derechos reservados</p>
    </div>

    <div class="container">

        <!-- Encabezado con logo e información de la empresa -->
        <table class="header-table">
            <tr>
                <!-- Columna 1: Imagen -->
                <td style="width: 20%; text-align: left;">
                    <img src="{{ $empresa->logo_ruta }}" alt="Logo" style="height: 100px; object-fit: contain; max-width: 120px;">
                </td>

                <!-- Columna 2: Información de la empresa -->
                <td style="width: 80%; text-align: left;">
                    <h2 style="margin: 0; font-size: 11px; color: #3a6ea5;">{{ $empresa->razon_social }}</h2>
                    <p style="margin: 0; font-size: 11px; color: #555;">RUC: {{ $empresa->ruc }}</p>
                    <p style="margin: 0; font-size: 11px; color: #555;">{{ $empresa->direccion }}</p>
                    <p style="margin: 0; font-size: 11px; color: #555;">Teléfono: {{ $empresa->telefono }}</p>
                    <p style="margin: 0; font-size: 11px; color: #555;">EMAIL: {{ $empresa->correo }}</p>
                </td>


            </tr>
        </table>

        <div style="text-align: right; font-size: 12px; font-weight: bold; margin-top: 20px; margin-bottom: 10px;">
            REPORTE CUENTAS
        </div>

        <!-- Segunda tabla: Información adicional -->
        <table class="info-table-custom">
            <tr>
                <td class="label">USUARIO IMPRESIÓN:</td>
                <td>{{ Auth::user()->name }}</td>
            </tr>
            <tr>
                <td class="label">FECHA IMPRESIÓN:</td>
                <td>{{ now()->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <td class="label">FECHA INICIO:</td>
                <td>{{ $filters->get('fecha_inicio')}}</td>
            </tr>
            <tr>
                <td class="label">FECHA FIN:</td>
                <td>{{ $filters->get('fecha_fin')}}</td>
            </tr>
            <tr>
                <td class="label">MONEDA:</td>
                <td>{{ $filters->get('moneda') }}</td>
            </tr>
            <tr>
                <td class="label">BANCO:</td>
                <td>{{ $filters->get('banco_nombre') }}</td>
            </tr>
            <tr>
                <td class="label">N° CUENTA:</td>
                <td>{{ $filters->get('nro_cuenta') }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <table style="width:100%; border:none; margin-top:8px;">
                        <tr>
                            <td style="width:33%; text-align:center; font-weight:bold; border:none; font-size:11px; padding:1px 0;">INGRESOS<br><span style="font-weight:normal; font-size:11px;">{{ number_format($reporte->total_ingresos, 2, '.', ',') }}</span></td>
                            <td style="width:33%; text-align:center; font-weight:bold; border:none; font-size:11px; padding:1px 0;">EGRESOS<br><span style="font-weight:normal; font-size:11px;">{{ number_format($reporte->total_egresos, 2, '.', ',') }}</span></td>
                            <td style="width:33%; text-align:center; font-weight:bold; border:none; font-size:11px; padding:1px 0;">SALDO<br><span style="font-weight:normal; font-size:11px;">{{ number_format($reporte->saldo, 2, '.', ',') }}</span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Tercera tabla: Reporte Kardex -->
        <table class="tbl-report-sale">
            <thead>
                <tr>
                    <th scope="col">FECHA</th>
                    <th scope="col">CUENTA</th>
                    <th scope="col">MET PAGO</th>
                    <th scope="col">TIPO</th>
                    <th scope="col">DOC</th>
                    <th scope="col">ENTRADA</th>
                    <th scope="col">SALIDA</th>
                    <th scope="col">SALDO</th>
                    <th scope="col">REGISTRADOR</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reporte->kardex as $item)
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
    </div>


</body>
</html>
