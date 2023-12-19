<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>GUIA DE REMISION</title>
        <link rel="icon" href="{{ public_path() . '/img/siscom.ico' }}" />
        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                color: black;
            }

            .cabecera{
                width: 100%;
                position: relative;
                height: 120px;
                max-height: 200px;
            }

            .logo {
                width: 30%;
                position: absolute;
                left: 0%;
            }

            .logo .logo-img
            {
                position: relative;
                width: 95%;
                margin-right: 5%;
                height: 90px;
            }

            .img-fluid {
                width: 100%;
                height: 100%;
            }

            .empresa {
                width: 40%;
                position: absolute;
                left: 30%;
            }

            .empresa .empresa-info {
                position: relative;
                width: 100%;
            }

            .nombre-empresa {
                font-size: 16px;
            }

            .ruc-empresa {
                font-size: 15px;
            }

            .direccion-empresa {
                font-size: 12px;
            }

            .text-info-empresa {
                font-size: 12px;
            }

            .comprobante {
                width: 30%;
                position: absolute;
                left: 70%;
            }

            .comprobante .comprobante-info {
                position: relative;
                width: 100%;
                display: flex;
                align-content: center;
                align-items: center;
                text-align: center;
            }

            .numero-documento {
                margin: 1px;
                padding-top: 20px;
                padding-bottom: 20px;
                border: 2px solid #52BE80;
                font-size: 14px;
            }

            .nombre-documento{
                margin-top: 5px;
                margin-bottom: 5px;
                margin-left: 0px;
                margin-right: 0px;
                width: 100%;
                background-color: #7DCEA0 ;
            }

            .destinatario{
                width: 100%;
                position: relative;
            }

            .tbl{
                width: 100%;
                font-size: 12px;
            }

            .tbl thead tr th{
                border: 0.03cm solid #5f5f5f;
                text-align: left;
            }

            .tbl td{
                border: 0.03cm solid #5f5f5f;
                text-align: left;
            }

            .envio{
                width: 100%;
                position: relative;
            }

            .transporte{
                width: 100%;
                position: relative;
            }

            .detalles{
                width: 100%;
                position: relative;
            }

            .tbl-detalles{
                width: 100%;
                font-size: 12px;
            }

            .tbl-detalles thead{
                border: 0.03cm solid #5f5f5f;
                text-align: left;
            }

            .tbl-detalles tbody{
                border: 0.03cm solid #5f5f5f;
                text-align: left;
            }
            /*---------------------------------------------*/

            .m-0{
                margin:0;
            }

            .text-uppercase {
                text-transform: uppercase;
            }

            .p-0{
                padding:0;
            }

            .text-danger {
                color: #c90404;
            }
        </style>
    </head>

    <body>
        <div class="cabecera">
            <div class="logo">
                <div class="logo-img">
                    <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}" class="img-fluid">
                </div>
            </div>
            <div class="empresa">
                <div class="empresa-info">
                    <p class="m-0 p-0 text-uppercase nombre-empresa">DE: {{ DB::table('empresas')->count() == 0 ? 'SISCOM ' : DB::table('empresas')->first()->razon_social }}</p>
                    <p class="m-0 p-0 text-uppercase direccion-empresa">{{ DB::table('empresas')->count() == 0 ? '- ' : DB::table('empresas')->first()->direccion_fiscal }}</p>

                    <p class="m-0 p-0 text-info-empresa">Tlfn: {{ DB::table('empresas')->count() == 0 ? '-' : DB::table('empresas')->first()->telefono.' / '.DB::table('empresas')->first()->celular }}</p>
                    <p class="m-0 p-0 text-info-empresa">Email: {{ DB::table('empresas')->count() == 0 ? '-' : DB::table('empresas')->first()->correo }}</p>
                </div>
            </div>
            <div class="comprobante">
                <div class="comprobante-info">
                    <div class="numero-documento">
                        <p class="m-0 p-0 text-uppercase ruc-empresa">RUC {{ DB::table('empresas')->count() == 0 ? '- ' : DB::table('empresas')->first()->ruc }}</p>
                        <div class="nombre-documento">
                            <p class="m-0 p-0 text-uppercase">GUÍA DE REMISIÓN REMITENTE <small>ELECTRÓNICA</small></p>
                        </div>
                        <p class="m-0 p-0 text-uppercase {{ $guia->serie ? '' : 'text-danger' }}">{{$guia->serie ? $guia->serie.'-'.$guia->correlativo : 'No enviado a sunat'}}</p>
                    </div>
                </div>
            </div>
        </div><br>
        <div class="destinatario">
            <table class="tbl" cellpadding="2" cellspacing="0">
                <thead>
                    <tr>
                        <th>DESTINATARIO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <p class="m-0 p-0">{{ $guia->tipo_documento_cliente == 'RUC' ? 'Razón Social' : 'Nombre' }}: {{ $guia->clienteEntidad->nombre }}</p>
                            <p class="m-0 p-0">{{ $guia->tipo_documento_cliente }}: {{ $guia->clienteEntidad->documento }}</p>
                            <p class="m-0 p-0">Dirección: {{ $guia->direccion_llegada }}</p>
                            <p class="m-0 p-0">Vendedor: {{ $guia->user->usuario }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div><br>
        <div class="envio">
            <table class="tbl" cellpadding="2" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="2">ENVÍO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border-right: 0px !important; border-bottom: 0px !important;">
                            <p class="m-0 p-0">Fecha Emisión: {{ getFechaFormato($guia->created_at,'d/m/Y') }}</p>
                            <p class="m-0 p-0">Motivo Translado: {{ $guia->desTraslado() }}</p>
                            <p class="m-0 p-0">Peso Bruto Total(KGM): {{ $guia->peso_productos }}</p>
                        </td>
                        <td style="border-left: 0px !important; border-bottom: 0px !important;">
                            <p class="m-0 p-0">Fecha Inicio de Translado: {{ getFechaFormato($guia->created_at,'d/m/Y') }}</p>
                            <p class="m-0 p-0">Modalidad de Transporte: Transporte publico</p>
                            <p class="m-0 p-0">Numero de Bultos: {{ $guia->cantidad_productos }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-top: 0px !important;">
                            <p class="m-0 p-0">P. Partida: {{ $empresa->direccion_fiscal }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div><br>
        <div class="transporte">
            <table class="tbl" cellpadding="2" cellspacing="0">
                <thead>
                    <tr>
                        <th colspan="2">TRANSPORTE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border-right: 0px !important;">
                            <p class="m-0 p-0">Nombre y/o razón social: {{ $guia->nombre_transporte_domicilio }}</p>
                            <p class="m-0 p-0">Número de Placa del Vehículo: {{ $guia->placa_vehiculo }}</p>
                            <p class="m-0 p-0">Licencia del conductor: -</p>
                        </td>
                        <td style="border-left: 0px !important;">
                            <p class="m-0 p-0">DNI: {{ $guia->dni_conductor }}</p>
                            <p class="m-0 p-0">Conductor: {{ '-' }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div><br>
        <div class="detalles">
            <table class="tbl-detalles" cellpadding="2" cellspacing="0">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th>CÓDIGO</th>
                        <th>DESCRIPCION</th>
                        <th>UNI.</th>
                        <th>CANT.</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < count($guia->detalles); $i++)
                    <tr>
                        <td style="text-align: center"><p class="m-0 p-0">{{ $i + 1 }}</p></td>
                        <td style="text-align: center">{{ $guia->detalles[$i]->codigo_producto }}</td>
                        <td>{{ $guia->detalles[$i]->nombre_producto }}</td>
                        <td style="text-align: center">{{ $guia->detalles[$i]->unidad }}</td>
                        <td style="text-align: center">{{ $guia->detalles[$i]->cantidad }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div><br>
        <div class="comprobante-aux">
            <table class="tbl-detalles" cellpadding="2" cellspacing="0">
                <thead>
                    <tr>
                        <th style="text-align: left">{{ $guia->documento ? $guia->documento->nombreDocumento() : '-' }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $guia->documento ? $guia->documento->serie.'-'.$guia->documento->correlativo : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>
