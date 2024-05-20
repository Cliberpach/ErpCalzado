<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>{{'RCC'.'-'.$recibo_caja->id}}</title>
        <link rel="icon" href="{{ base_path() . '/img/siscom.ico' }}" />
        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                color: black;
            }

            .cabecera{
                width: 100%;
                position: relative;
                height: 100px;
                max-height: 150px;
            }

            .logo {
                width: 25%;
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
                width: 45%;
                position: absolute;
                left: 25%;
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
                border: 1px solid #8f8f8f;
                font-size: 14px;
            }

            .informacion{
                width: 100%;
                position: relative;
            }

            .tbl-informacion {
                width: 100%;
                font-size: 12px;
            }

            .cuerpo{
                width: 100%;
                position: relative;
            }

            .tbl-detalles {
                width: 100%;
                font-size: 12px;
            }

            .tbl-detalles thead{
                border-top: 1px solid;
                background-color: rgb(241, 239, 239);
            }

            .tbl-detalles tbody{
                border-top: 1px solid;
                border-bottom: 1px solid;
            }

            .text-cuerpo{
                font-size: 12px
            }

            .tbl-qr {
                width: 100%;
            }

            .tbl-info-credito {
                width: 100%;
                font-size: 12px;
                border: 1px solid black;
            }

            .tbl-info-retencion {
                width: 100%;
                font-size: 12px;
                border: 1px solid black;
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
        @php
            use Carbon\Carbon;
        @endphp
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
                    <p class="m-0 p-0 text-uppercase nombre-empresa">{{ DB::table('empresas')->count() == 0 ? 'SISCOM ' : DB::table('empresas')->first()->razon_social }}</p>
                    <p class="m-0 p-0 text-uppercase ruc-empresa">RUC {{ DB::table('empresas')->count() == 0 ? '- ' : DB::table('empresas')->first()->ruc }}</p>
                    <p class="m-0 p-0 text-uppercase direccion-empresa">{{ DB::table('empresas')->count() == 0 ? '- ' : DB::table('empresas')->first()->direccion_fiscal }}</p>

                    <p class="m-0 p-0 text-info-empresa">Central telefónica: {{ DB::table('empresas')->count() == 0 ? '-' : DB::table('empresas')->first()->celular }}</p>
                    <p class="m-0 p-0 text-info-empresa">Email: {{ DB::table('empresas')->count() == 0 ? '-' : DB::table('empresas')->first()->correo }}</p>
                </div>
            </div>
            <div class="comprobante">
                <div class="comprobante-info">
                    <div class="numero-documento">
                        <p class="m-0 p-0 text-uppercase">{{ 'RECIBO DE CAJA' }}</p>
                        <p class="m-0 p-0 text-uppercase">{{'RCC'.'-'.$recibo_caja->id}}</p>
                    </div>
                </div>
            </div>
        </div><br>
        <div class="informacion">
            <table class="tbl-informacion">
                <tr>
                    <td>F. EMISIÓN</td>
                    <td>:</td>
                    <td>
                        {{ Carbon::parse($recibo_caja->created_at)->format('d/m/Y') }} {{ Carbon::parse($recibo_caja->created_at)->format('H:i') }}
                    </td>
                </tr>
                <tr>
                    <td>CLIENTE</td>
                    <td>:</td>
                    <td>{{ $recibo_caja->cliente_nombre }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase">{{ $recibo_caja->cliente_tipo_doc}}</td>
                    <td>:</td>
                    <td>{{ $recibo_caja->cliente_documento }}</td>
                </tr>
                <tr>
                    <td>DIRECCIÓN</td>
                    <td>:</td>
                    <td>{{ $recibo_caja->cliente_direccion }}</td>
                </tr>
                <tr>
                    <td>MODO DE PAGO</td>
                    <td>:</td>
                    <td class="text-uppercase">{{ $recibo_caja->metodo_pago }}</td>
                </tr>
                @if ($recibo_caja->observacion)
                <tr>
                    <td>OBSERVACIÓN</td>
                    <td>:</td>
                    <td class="text-uppercase">{{ $recibo_caja->observacion }}</td>
                </tr>
                @endif
                <tr>
                    <td>ATENDIDO POR</td>
                    <td>:</td>
                    <td class="text-uppercase">{{ $recibo_caja->usuario_nombre }}</td>
                </tr>
            </table>
        </div><br>
        <div class="cuerpo">
            <table class="tbl-detalles text-uppercase" cellpadding="2" cellspacing="0">
                <thead>
                    <tr >
                        <th style="text-align: center; width: 50%;">MONTO</th>
                        <th style="text-align: center; width: 50%;">SALDO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center">{{ $recibo_caja->monto }}</td>
                        <td style="text-align: center">{{$recibo_caja->saldo}}</td>
                    </tr>
                </tbody>
               
            </table>
            <br>
            <div style="width: 100%; border-top: 1px dashed #3333; margin: 0px; padding: 0px"></div>
        </div><br>
    </body>
</html>