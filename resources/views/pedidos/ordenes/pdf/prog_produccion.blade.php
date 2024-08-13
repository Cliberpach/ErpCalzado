<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PROGRAMACIÓN DE PRODUCCIÓN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bevan:ital@0;1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">    <style>

        body {
            padding: 0;
            font-family: Arial, sans-serif;
            text-align: left;
        }

        /*===== CABEZERA ======*/
        .cabecera{
            width: 100%;
            position: relative;
            height: 100px;
            max-height: 150px;
        }

        .logo {
            width: 15%;
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
            height: 100%;
            object-fit: cover;
        }

        .empresa {
            width: 40%;
            position: absolute;
            left: 10%;
        }

            .empresa .empresa-info {
                position: relative;
                width: 100%;
            }

            .nombre-empresa {
                font-size: 16px;
                font-family: "Archivo Black", sans-serif;
                font-weight: 400;
                font-style: normal;
                margin:0;
                padding:0;
            }

            .ruc-empresa {
                font-size: 15px;
            }

            .direccion-empresa {
                font-size: 12px;
                font-family: "Roboto", sans-serif;
                font-weight: 400;
                font-style: normal;
                margin:0;
                padding:0;
            }

            .text-info-empresa {
                font-size: 12px;
                font-family: "Roboto", sans-serif;
                margin:0;
                padding:0;
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

            .numero-cotizacion {
                margin: 1px;
                padding-top: 20px;
                padding-bottom: 20px;
                border: 2px solid #52BE80;
                font-size: 14px;
            }

            .nombre-cotizacion{
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


        /*====== TABLES ======*/
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            font-size: 10px;
            text-align: center;
        }

        thead {
            background-color: #f4f4f4;
        }

        th, td {
            padding: 5px 7px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #2b79cd;
            color: #fff;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        th, td {
            text-align: center;
        }
    </style>
</head>
<body>
    
    <div class="cabecera">
        <div class="logo">
            <div class="logo-img" >
                <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}" class="img-fluid">
            </div>
        </div>
        <div class="empresa">
            <div class="empresa-info">
                <p class="m-0 p-0 text-uppercase nombre-empresa">{{ DB::table('empresas')->count() == 0 ? 'SISCOM ' : DB::table('empresas')->first()->razon_social }}</p>
                <p class="m-0 p-0 text-uppercase direccion-empresa"><span style="font-weight: bold;">Dirección:</span> {{ DB::table('empresas')->count() == 0 ? '- ' : DB::table('empresas')->first()->direccion_fiscal }}</p>

                <p class="m-0 p-0 text-info-empresa"><span style="font-weight: bold;">Central telefónica:</span> {{ DB::table('empresas')->count() == 0 ? '-' : DB::table('empresas')->first()->telefono }}</p>
                <p class="m-0 p-0 text-info-empresa"><span style="font-weight: bold;">Email:</span> {{ DB::table('empresas')->count() == 0 ? '-' : DB::table('empresas')->first()->correo }}</p>
                <p class="m-0 p-0 text-info-empresa"><span style="font-weight: bold;">Fecha PDF:</span> {{ $fecha_actual }}</p>
                <p class="m-0 p-0 text-info-empresa"><span style="font-weight: bold;">Usuario de Impresión:</span> {{ $usuario_impresion_nombre }}</p>

            </div>
        </div>
        <div class="comprobante">
            <div class="comprobante-info">
                <div class="numero-cotizacion">
                    <p class="m-0 p-0 text-uppercase ruc-empresa">RUC {{ DB::table('empresas')->count() == 0 ? '- ' : DB::table('empresas')->first()->ruc }}</p>
                    <div class="nombre-cotizacion">
                        <p class="m-0 p-0 text-uppercase">PROGRAMACIÓN DE PRODUCCIÓN</p>
                    </div>
                    {{-- <p class="m-0 p-0 text-uppercase">{{ 'PROGRAMACIÓN'}}</p> --}}
                </div>
            </div>
        </div>
    </div>

    <table style="margin-top:60px;">
        <thead>
            <tr>
                <th>MODELO</th>
                <th>PRODUCTO</th>
                <th>COLOR</th>
                @foreach ($tallasBD as $tallaBD)
                    <th>{{$tallaBD->descripcion}}</th>
                @endforeach

            </tr>
        </thead>
        <tbody>
            @foreach ($lstProgramacionProduccion as $producto)
                @foreach ($producto->colores as $color)
                <tr>
                    <td>{{$producto->modelo->nombre}}</td>
                    <td>{{$producto->nombre}}</td>
                    <td>{{$color->nombre}}</td>

                    @foreach ($tallasBD as $tallaBD)
                        @php
                            $tallas =   $color->tallas;

                            $tallasFiltradas = array_filter($tallas, function($talla) use ($tallaBD) {
                                return $talla->id == $tallaBD->id;
                            });
                            
                            $cantidad_pendiente = !empty($tallasFiltradas) ? reset($tallasFiltradas)->cantidad_pendiente : '-';
                        @endphp
                        <td>{{$cantidad_pendiente}}</td>
                    @endforeach
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
    


</body>
</html>