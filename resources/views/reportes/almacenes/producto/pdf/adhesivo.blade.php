<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Etiqueta Adhesiva</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Jersey+25&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: inherit;
            margin: 0;
            padding: 0;
        }
        body, html {
            width: 100%;
            height: 100%; 
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 0;
            margin: 0;
        }
        .producto{
            font-family: "Roboto Condensed", sans-serif;
        }
        p {
            margin: 0;
            padding: 0;
        }
        .datos-empresa{
            font-size: 5px;
            margin-left: -15px;
        }
        .empresa_nombre{
            font-family: "Jersey 25", sans-serif;
            font-size: 7px;
            font-weight: 400;
            font-style: normal;
            margin-left: -15px;

        }
        .img_cod_barras{
            height: 20px;
            object-fit: contain;
            padding:0;
        }
        .div_img_barras{
            margin:0;
            padding:0;
            border: solid 1px pink; 
            border-radius: 5px; 
        }
      
    </style>
</head>
<body>
    @php
        $cantidad           =   $producto->cantidad;
    @endphp

 
    @if ($cantidad >100)
        @php
            $cantidad    =   50;
        @endphp
    @endif

            @for ($i = 0; $i < $cantidad; $i++)
                <table>
                    <tr>
                        <td width="40%">
                            <table>
                                <tr height="20%">
                                    <td style="border-bottom:solid 1px black;" >
                                        @if($empresa->ruta_logo)
                                            <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}" class="img-fluid" width="50px;">
                                        @else
                                            <img src="{{ public_path() . '/img/default.png' }}" class="img-fluid">
                                        @endif
                                    </td>
                                    <td style="border-bottom:solid 1px black;">
                                        <p class="datos-empresa">
                                            <span style="font-weight:bold;">RUC: </span>{{$empresa->ruc}}
                                        </p>
                                        <p class="empresa_nombre">{{$empresa->razon_social}}</p>
                                        <p class="datos-empresa">{{$empresa->direccion_fiscal}}</p>
                                        <p class="datos-empresa">{{$empresa->correo}}</p>
                                    </td>
                                </tr>
                                <tr height="80%">
                                    <td colspan="2" style="padding-left:5px;">
                                        <p class="producto" style="font-size: 6px;text-align: left;">{{$producto->modelo_nombre}}</p>
                                        <p class="producto" style="font-size: 6px;text-align: left;">{{$producto->producto_nombre}}</p>
                                        <p class="producto" style="font-size: 6px;text-align: left;">{{$producto->color_nombre}}</p>
                                        <p class="producto" style="font-size: 6px;text-align: left;">{{$producto->talla_nombre}}</p>
                                    </td>
                                    
                                </tr>
                            </table>
                        </td>
                        <td width="60%" style="vertical-align: middle;text-align:center;border-left:solid 1px black;height:100%;" >
                            <img src="{{ base_path() . '/storage/app/'.$producto->ruta_cod_barras }}" class="img-fluid img_cod_barras">
                            <p style="font-size: 10px;">{{'775'.$producto->modelo_id.$producto->producto_id.$producto->color_id.$producto->talla_id}}</p>
                        </td>
                    </tr>
                </table>
            @endfor
  
</body>
</html>