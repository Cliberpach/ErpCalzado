<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Recibo Egreso</title>
    <style>
        body{
            font-size: 6pt;
            font-family: Arial, Helvetica, sans-serif;
            color: black;
        }

        .cabecera {
            align-content: center;
            text-align: center;
        }

        .logo{
            width: 100%;
            margin: 0px;
            padding: 0px;
        }

        .img-fluid {
            width: 60%;
            height: 70px;
            margin-bottom: 10px;
        }

        .empresa {
            position: relative;
            align-content: center;
        }

        .comprobante {
            width: 100%;
        }

        .numero-documento {
            margin: 1px;
            padding-top: 20px;
            padding-bottom: 20px;
            border: 1px solid #8f8f8f;
        }

        .informacion{
            width: 100%;
            position: relative;
        }

        .tbl-informacion {
            width: 100%;
        }

        .cuerpo{
            width: 100%;
            position: relative;
            margin-bottom: 10px;
        }

        .tbl-detalles {
            width: 100%;
        }

        .tbl-detalles thead{
            border-top: 1px solid;
            background-color: rgb(241, 239, 239);
        }

        .tbl-detalles tbody{
            border-top: 1px solid;
            border-bottom: 1px solid;
        }

        .tbl-qr {
            width: 100%;
        }

        .qr {
            position: relative;
            width: 100%;
            align-content: center;
            text-align: center;
            margin-top: 10px;
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
            @if($empresa->ruta_logo)
            <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}" class="img-fluid">
            @else
            <img src="{{ public_path() . '/img/default.png' }}" class="img-fluid">
            @endif
        </div>
        <div class="empresa">
            <p class="m-0 p-0 text-uppercase nombre-empresa">{{ DB::table('empresas')->count() == 0 ? 'SISCOM ' : DB::table('empresas')->first()->razon_social }}</p>
            <p class="m-0 p-0 text-uppercase ruc-empresa">RUC {{ DB::table('empresas')->count() == 0 ? '- ' : DB::table('empresas')->first()->ruc }}</p>
            <p class="m-0 p-0 text-uppercase direccion-empresa">{{ DB::table('empresas')->count() == 0 ? '- ' : DB::table('empresas')->first()->direccion_fiscal }}</p>

            <p class="m-0 p-0 text-info-empresa">Central telefónica: {{ DB::table('empresas')->count() == 0 ? '-' : DB::table('empresas')->first()->celular }}</p>
            <p class="m-0 p-0 text-info-empresa">Email: {{ DB::table('empresas')->count() == 0 ? '-' : DB::table('empresas')->first()->correo }}</p>
        </div>
    </div><br>
    <br>
    <table class="tbl-detalles text-uppercase" cellpadding="2" cellspacing="0">
        <thead>
            <tr>
                <th style="text-align: center">CAJERO</th>
                <th style="text-align: center">CUENTA</th>
                <th style="text-align: center">DESCRIPCION</th>
                <th style="text-align: center">IMPORTE</th>
            </tr>
        </thead>
        <tbody class="cuerpoTabla">
            <tr>
                <td style="text-align: center">-</td>
                <td style="text-align: center">{{$egreso->cuenta->descripcion}}</td>
                <td style="text-align: center">{{$egreso->descripcion}}</td>
                <td style="text-align: center">{{$egreso->importe}}</td>

            </tr>
       </tbody>
    </table><br><br><br>
    <div style="width: 100%; border-top: 1px dashed #3333; margin: 0px; padding: 0px"></div>
    <br>
    <div style="margin-top: 5pt; text-align: center">
        <span>!!! RECIBO IMPRESO!!!</span>
    </div>


</body>

</html>
