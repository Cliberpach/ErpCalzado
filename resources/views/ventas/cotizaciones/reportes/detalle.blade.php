<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ecovalle | Sistema de Producción</title>
    {{-- <link rel="stylesheet" href="{{asset('css/informe.css')}}" /> --}}
    {{-- <link rel="icon" href="{{asset('img/ecologo.ico')}}" /> --}}
    <style>
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        a {
            color: #0f243e;
            text-decoration: none;
        }
        body {
            position: relative;
            height: 29.7cm;
            margin: 0 auto;
            font-size: 12px;
            font-family: Helvetica;
            text-transform: uppercase;
            /*Mayuscula en todo el reporte*/
        }
        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }
        #logo {
            float: left;
            /* margin-top: 8px; */
            padding-left: 20px;
            width: 40%;
            /* padding: 10px 10px 10px 10px; */
        }
        #logo img {
            height: 90px;
            width: 100px;
        }
        #company {
            float: right;
            text-align: right;
            /* max-width: 40%; */
            width: 60%;
            margin-top: 15px;
            font-size: 12px;
        }
        #company .name {
            font-size: 13px;
        }
        #details {
            /* margin-bottom: 10px; */
            width: 100%;
        }
        h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
        }
        /* ///////ORDEN DE COMPRA//////// */
        #invoice {
            float: left;
            text-align: left;
            width: 40%;
        }
        #tabla-orden {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-top: 5px;
            margin-bottom: 15px;
            background: #EEEEEE;
        }
        #tabla-orden th {
            white-space: nowrap;
            font-weight: normal;
        }
        #tabla-orden thead th {
            background: #0f243e;
            color: #FFFFFF;
            padding: 8px;
            border: 2px solid #0f243e;
        }
        #tabla-orden tbody tr th {
            padding: 5px;
        }
        #tabla-orden tbody tr .datos-orden-titulo {
            text-align: left;
            font-weight: 700;
            font-size: 10px;
            width: 70%;
            border: 2px solid #0f243e;
        }
        #tabla-orden tbody tr .datos-orden {
            text-align: center;
            font-size: 10px;
            width: 30%;
            font-weight: normal;
            white-space: initial;
            word-wrap: break-word;
            border: 2px solid #0f243e;
        }
        #client {
            padding-right: 6px;
            border-right: 6px solid #0f243e;
            float: right;
            text-align: right;
            width: 60%;
        }
        #client .to {
            color: #777777;
        }
        /* ///////////////////////////////////// */
        #tabla-proveedor {
            width: 100%;
            border-spacing: 0;
            margin-top: 5px;
            margin-bottom: 15px;
            color: #000;
            background: #EEEEEE;
        }
        #tabla-proveedor thead th {
            background: #0f243e;
            color: #ffff;
            font-size: 11px;
            font-weight: 700;
            padding: 8px;
            border: 2px solid #0f243e;
        }
        #tabla-proveedor tbody tr th {
            padding: 8px;
            border: 2px solid #0f243e;
        }
        #tabla-proveedor tbody tr .datos-proveedor-titulo {
            text-align: left;
            font-weight: 700;
            font-size: 10px;
            width: 30%;
        }
        #tabla-proveedor tbody tr .datos-proveedor {
            text-align: left;
            font-size: 10px;
            width: 70%;
            white-space: initial;
            word-wrap: break-word;
            font-weight: normal;
        }
        /* //////////TABLA DE PRODUCTOS///////// */
        #tabla-productos {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
            color: #000;
        }
        #tabla-productos th,
        #tabla-productos td {
            padding: 8px;
            background: #EEEEEE;
            text-align: center;
            font-size: 11px;
        }
        #tabla-productos th {
            white-space: nowrap;
            font-weight: normal;
        }
        #tabla-productos thead tr th {
            /* border: 2px solid #fff; */
            border: 2px solid #0f243e;
        }
        #tabla-productos td {
            text-align: right;
        }
        #tabla-productos .no {
            font-size: 10px;
            text-align: center;
        }
        #tabla-productos thead th {
            background: #0f243e;
            color: #ffff;
            font-size: 11px;
            font-weight: 700;
        }
        /* Presentacion */
        #tabla-productos .desc {
            text-align: center;
        }
        /* Producto */
        #tabla-productos .unit {
            text-align: left;
        }
        /* Precio */
        #tabla-productos .qty {
            text-align: center;
        }
        /* Total */
        #tabla-productos .total {
            text-align: center;
        }
        #tabla-productos td.no,
        #tabla-productos td.desc,
        #tabla-productos td.unit,
        #tabla-productos td.qty,
        #tabla-productos td.total {
            font-size: 11px;
            /* border: 2px solid #fff; */
            border: 2px solid #0f243e;
        }
        #tabla-productos tbody tr:last-child td {
            border: none;
        }
        #tabla-productos tfoot td {
            /* padding: 10px 20px; */
            padding: 8px;
            background: #FFFFFF;
            /* border-bottom: none; */
            font-size: 12px;
            white-space: nowrap;
            /* border: 2px solid #fff; */
            /* border: 2px solid #0f243e; */
            /* border-top: 1px solid #AAAAAA;  */
        }
        #tabla-productos tfoot tr:first-child td {
            border-top: none;
        }
        #tabla-productos tfoot tr td {
            text-align: center;
        }
        #tabla-productos tfoot tr .sub {
            background: #0f243e;
            color: #ffff;
            font-weight: 700;
            font-size: 11px;
            border: 2px solid #0f243e;
        }
        #tabla-productos tfoot tr .sub-monto {
            background: #EEEEEE;
            border: 2px solid #0f243e;
            /* background: #0f243e;
  color: #ffff; */
            font-weight: 700;
            font-size: 11px;
        }
        /* ////////////////////////////////////// */
        /* /////////////TRANSPORTISTA//////////// */
        #tabla-transporte {
            width: 100%;
            border-spacing: 0;
            margin-top: 5px;
            margin-bottom: 15px;
            color: #000;
            background: #EEEEEE;
        }
        #tabla-transporte thead th {
            background: #0f243e;
            color: #ffff;
            font-size: 11px;
            font-weight: 700;
            padding: 10px;
            border: 2px solid #0f243e;
        }
        #tabla-transporte tbody tr th {
            padding: 8px;
            border: 2px solid #0f243e;
        }
        #tabla-transporte tbody tr .datos-transporte-titulo {
            text-align: left;
            font-weight: 700;
            font-size: 10px;
            width: 30%;
        }
        #tabla-transporte tbody tr .datos-transporte {
            text-align: left;
            font-size: 10px;
            width: 70%;
            white-space: initial;
            word-wrap: break-word;
            font-weight: normal;
        }
        /* ///////////ADICIONAL///////////// */
        #tabla-adicional {
            width: 100%;
            border-spacing: 0;
            margin-top: 5px;
            margin-bottom: 15px;
            color: #000;
            background: #EEEEEE;
        }
        #tabla-adicional tbody tr th {
            padding: 8px;
            border: 2px solid #0f243e;
        }
        #tabla-adicional tbody tr .datos-adicional-titulo {
            text-align: left;
            font-weight: 700;
            font-size: 10px;
            width: 30%;
            color: #FFFFFF;
            background: #0f243e;
        }
        #tabla-adicional tbody tr .datos-adicional {
            text-align: left;
            font-size: 10px;
            width: 70%;
            white-space: initial;
            word-wrap: break-word;
            font-weight: normal;
        }
        #notices {
            padding-left: 6px;
            border-left: 6px solid #0f243e;
        }
        #notices .notice {
            font-size: 1.2em;
        }
        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="clearfix">
        <div>
            <div id="logo">
                {{-- @if ($cotizacion->empresa->ruta_logo)
                <img src="{{ base_path() . '/storage/app/'.$cotizacion->empresa->ruta_logo }}">
                @else
                <img src="{{asset('storage/empresas/logos/default.png')}}">
                @endif --}}
            </div>
            <div id="company">
                <h2 class="name">{{ $cotizacion->empresa->razon_social }}</h2>
                <div>RUC:{{ $cotizacion->empresa->ruc }}</div>
                <div>{{ $cotizacion->empresa->direccion_fiscal }}</div>
            </div>
        </div>
    </header>
    <main>
        <div id="details" class="clearfix">
            <div id="invoice">
                <table cellspacing="0" cellpadding="0" id="tabla-orden">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">COTIZACION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="datos-orden-titulo">
                                N°:
                            </th>
                            <th class="datos-orden">
                                CO - {{ $cotizacion->id }}
                            </th>
                        </tr>
                        <tr>
                            <th class="datos-orden-titulo">
                                FECHA DOCUMENTO:
                            </th>
                            <th class="datos-orden">
                                {{ Carbon\Carbon::parse($cotizacion->fecha_documento)->format('d/m/y') }}
                            </th>
                        </tr>
                        <tr>
                            <th class="datos-orden-titulo">
                                FECHA ATENCION:
                            </th>
                            <th class="datos-orden">
                                {{ Carbon\Carbon::parse($cotizacion->fecha_atencion)->format('d/m/y') }}
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="client">
                <div class="to">CONTACTO:</div>
                <h2 class="name">{{ $nombre_completo }}</h2>
                <div class="address">{{ $cotizacion->user->persona->telefono_movil }}</div>
                <div class="email"><a
                        href="mailto:{{ $cotizacion->user->persona->correo_electronico }}">{{ $cotizacion->user->persona->correo_electronico }}</a>
                </div>
            </div>
        </div>
        <table border="0" cellspacing="0" cellpadding="0" id="tabla-proveedor">
            <thead>
                <tr>
                    <th colspan="2" class="text-center">DATOS DEL CLIENTE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th class="datos-proveedor-titulo">
                        TIPO DE DOCUMENTO:
                    </th>
                    <th class="datos-proveedor">
                        {{ $cotizacion->cliente->tipo_documento }}
                    </th>
                </tr>
                <tr>
                    <th class="datos-proveedor-titulo">
                        DOCUMENTO:
                    </th>
                    <th class="datos-proveedor">
                        {{ $cotizacion->cliente->documento }}
                    </th>
                </tr>
                <tr>
                    <th class="datos-proveedor-titulo">
                        NOMBRE:
                    </th>
                    <th class="datos-proveedor">
                        {{ $cotizacion->cliente->nombre }}
                    </th>
                </tr>
                <tr>
                    <th class="datos-proveedor-titulo">
                        DIRECCION:
                    </th>
                    <th class="datos-proveedor">
                        {{ $cotizacion->cliente->direccion }}
                    </th>
                </tr>
                <tr>
                    <th class="datos-proveedor-titulo">
                        CELULAR:
                    </th>
                    <th class="datos-proveedor">
                        @if ($cotizacion->cliente->telefono_movil)
                            {{ $cotizacion->cliente->telefono_movil }}
                        @else
                            -
                        @endif
                    </th>
                </tr>
                <tr>
                    <th class="datos-proveedor-titulo">
                        TELEFONO:
                    </th>
                    <th class="datos-proveedor">
                        @if ($cotizacion->cliente->telefono_fijo)
                            {{ $cotizacion->cliente->telefono_fijo }}
                        @else
                            -
                        @endif
                    </th>
                </tr>
                <tr>
                    <th class="datos-proveedor-titulo">
                        CORREO:
                    </th>
                    <th class="datos-proveedor">
                        <a href="mailto:{{ $cotizacion->cliente->correo_electronico }}">{{ $cotizacion->cliente->correo_electronico }}
                        </a>
                    </th>
                </tr>
            </tbody>
        </table>
        <table border="0" cellspacing="0" cellpadding="0" id="tabla-productos">
            <thead>
                <tr>
                    <th class="no">CANT.</th>
                    <th class="desc">UNIDAD DE MEDIDA</th>
                    <th class="unit">DESCRIPCION DEL PRODUCTO</th>
                    <th class="qty">PRECIO</th>
                    <th class="total">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detalles as $detalle)
                    <tr>
                        <td class="no">{{ $detalle->cantidad }}</td>
                        <td class="desc">
                            {{ $detalle->producto->tabladetalle->simbolo . ' - ' . $detalle->producto->tabladetalle->descripcion }}
                        </td>
                        <td class="unit">{{ $detalle->producto->codigo . ' - ' . $detalle->producto->nombre }}
                        </td>
                        <td class="qty">{{ 'S/. ' . $detalle->precio_nuevo }}</td>
                        <td class="total">{{ 'S/. ' . $detalle->valor_venta }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td class="sub" colspan="1">SUBTOTAL</td>
                    <td class="sub-monto">{{ 'S. ' . $cotizacion->sub_total }}</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td class="sub" colspan="1">IGV
                        @if ($cotizacion->igv)
                            {{ $cotizacion->igv }} %
                        @else
                            18 %
                        @endif
                    </td>
                    <td class="sub-monto">{{ 'S/. ' . $cotizacion->total_igv }}</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td class="sub" colspan="1">TOTAL</td>
                    <td class="sub-monto">{{ 'S/. ' . $cotizacion->total }}</td>
                </tr>
            </tfoot>
            </tfoot>
        </table>
    </main>
    <footer>
        SISCOM SAC
    </footer>
</body>
</html>
