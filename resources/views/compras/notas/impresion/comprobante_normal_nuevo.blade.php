<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>NOTA DE CRÉDITO</title>
        <link rel="icon" href="{{ base_path() . '/img/siscom.ico' }}" />
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

            .logos-empresas {
                width: 100%;
                height: 105px;
            }

            .img-logo {
                width: 95%;
                height: 100px;
            }

            .logo-empresa {
                width: 14.2%;
                float: left;
            }

            .informacion{
                width: 100%;
                position: relative;
                border: 2px solid #52BE80;
            }

            .tbl-informacion {
                width: 100%;
                font-size: 12px;
            }

            .cuerpo{
                width: 100%;
                position: relative;
                border: 1px solid red;
            }

            .tbl-detalles {
                width: 100%;
                font-size: 12px;
            }

            .tbl-detalles thead{
                border-top: 2px solid #52BE80;
                border-left: 2px solid #52BE80;
                border-right: 2px solid #52BE80;
            }

            .tbl-detalles tbody{
                border-top: 2px solid #52BE80;
                border-bottom: 2px solid #52BE80;
                border-left: 2px solid #52BE80;
                border-right: 2px solid #52BE80;
            }

            .info-total-qr {
                position: relative;
                width: 100%;
            }

            .tbl-total {
                width: 100%;
                border: 2px solid #229954;
            }

            .qr-img {
                margin-top: 15px;
            }

            .text-cuerpo{
                font-size: 12px
            }

            .tbl-qr {
                width: 100%;
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
        </style>
    </head>

    <body>
        <div class="cabecera">
            <div class="logo">
                <div class="logo-img">
                    @if($empresa->ruta_logo)
                    <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}" class="img-fluid">
                    @else
                    <img src="{{ public_path() . '/img/default.png' }}" class="img-fluid">
                    @endif

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
                            <p class="m-0 p-0 text-uppercase">NOTA DE CRÉDITO COMPRA</p>
                        </div>
                        <p class="m-0 p-0 text-uppercase">{{$nota->serie.'-'.$nota->correlativo}}</p>
                    </div>
                </div>
            </div>
        </div><br>
        @if($empresa->condicion == 1)
        <div class="logos-empresas">
            <div class="logo-empresa">
                <img src="{{ public_path() . '/img/cifarelli_1.jpg' }}" class="img-logo">
            </div>
            <div class="logo-empresa">
                <img src="{{ public_path() . '/img/motor.png' }}" class="img-logo">
            </div>
            <div class="logo-empresa">
                <img src="{{ public_path() . '/img/motosierra.jpg' }}" class="img-logo">
            </div>
            <div class="logo-empresa">
                <img src="{{ public_path() . '/img/mochila.jpg' }}" class="img-logo">
            </div>
            <div class="logo-empresa">
                <img src="{{ public_path() . '/img/mochila_jacto.jpg' }}" class="img-logo">
            </div>
            <div class="logo-empresa">
                <img src="{{ public_path() . '/img/filtro.jpg' }}" class="img-logo">
            </div>
            <div class="logo-empresa">
                <img src="{{ public_path() . '/img/llaves.jpg' }}" class="img-logo">
            </div>
        </div><br>
        @endif
        <div class="informacion">
            <table class="tbl-informacion">
                <tbody style="padding-top: 5px; padding-bottom: 5px;">
                    <tr>
                        <td style="padding-left: 5px;">Razón Social</td>
                        <td>:</td>
                        <td>{{ $nota->proveedor }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 5px;">FECHA EMISIÓN</td>
                        <td>:</td>
                        <td>{{ getFechaFormato( $nota->fechaEmision ,'d/m/Y')}}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 5px;">Tipo moneda</td>
                        <td>:</td>
                        <td class="text-uppercase">{{ $nota->tipoMoneda }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 5px;">Documento Ref.</td>
                        <td>:</td>
                        <td class="text-uppercase">{{ $nota->numDocfectado }}</td>
                    </tr>
                </tbody>
            </table>
        </div><br>
        <div class="cuerpo">
            <table class="tbl-detalles text-uppercase" cellpadding="8" cellspacing="0">
                <thead>
                    <tr >
                        <th style="text-align: center; border-right: 2px solid #52BE80;">CANT</th>
                        <th style="text-align: center;border-right: 2px solid #52BE80">CODIGO</th>
                        <th style="text-align: center;border-right: 2px solid #52BE80">DESCRIPCIÓN</th>
                        <th style="text-align: center; border-right: 2px solid #52BE80">P. UNIT.</th>
                        <th style="text-align: right">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detalles as $item)
                    <tr>
                        <td style="text-align: center; border-right: 2px solid #52BE80">{{ number_format($item->cantidad, 2) }}</td>
                        <td style="text-align: center; border-right: 2px solid #52BE80">{{ $item->codProducto }}</td>
                        <td style="text-align: center; border-right: 2px solid #52BE80">{{ $item->descripcion }}</td>
                        <td style="text-align: center; border-right: 2px solid #52BE80">{{ $item->mtoPrecioUnitario }}</td>
                        <td style="text-align: right">{{ number_format($item->mtoPrecioUnitario * $item->cantidad, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" style="border-top: 2px solid #52BE80">
                            <p class="p-0 m-0 text-uppercase text-cuerpo">SON: <b>{{ $legends[0]['value'] }}</b></p><br>
                            <p class="m-0 p-0">
                                <b>Información adicional</b>
                                <hr>
                            </p>
                            <p class="m-0 p-0">
                                MOTIVO EMISIÓN: {{ $nota->desMotivo }}
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div><br>
        <div class="info-total-qr">
            <table class="tbl-qr">
                <tr>
                    <td style="width: 50%">
                        <table class="tbl-qr">
                            <tr>
                                <td>
                                    @if($nota->ruta_qr)
                                    <img src="{{ base_path() . '/storage/app/'.$nota->ruta_qr }}" class="qr-img">
                                    @endif
                                    @if($nota->hash)
                                    <p class="m-0 p-0" style="font-size: 9px;">{{ $nota->hash }}</p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 50%;font-size: 14px">
                        <table class="tbl-total text-uppercase">
                            <tr>
                                <td style="text-align:left; padding: 5px;"><p class="m-0 p-0">Sub Total: S/.</p></td>
                                <td style="text-align:right; padding: 5px;"><p class="p-0 m-0">{{ number_format($nota->mtoOperGravadas, 2) }}</p></td>
                            </tr>
                            <tr>
                                <td style="text-align:left; padding: 5px;"><p class="p-0 m-0">IGV {{$nota->documento->igv }}%: S/.</p></td>
                                <td style="text-align:right; padding: 5px;"><p class="p-0 m-0">{{ number_format($nota->mtoIGV, 2) }}</p></td>
                            </tr>
                            <tr>
                                <td style="text-align:left; padding: 5px;"><p class="p-0 m-0">Total a pagar: S/.</p></td>
                                <td style="text-align:right; padding: 5px;"><p class="p-0 m-0">{{ number_format($nota->mtoImpVenta, 2) }}</p></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
