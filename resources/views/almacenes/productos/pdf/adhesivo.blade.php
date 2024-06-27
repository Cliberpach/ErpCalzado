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
<link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
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
        text-align: left;
        font-size: 5px;
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
        height: 17px;
        object-fit: contain;
        padding:0;
        width:96%;
    }
    .div_img_barras{
        margin:0;
        padding:0;
        border: solid 1px pink; 
        border-radius: 5px; 
    }
    .talla_nombre{
        font-family: "Righteous", sans-serif;
    }
    .descripcion_span{
        font-weight: bold;
        font-family: "Roboto Condensed", sans-serif;
        font-size: 5px;
        text-align: left;
    }
  
</style>
</head>
<body>
    @php
        $cantidad           =   0;
        $tipo_impresion     =   "LIBRE";
    @endphp

    @foreach ($nota_detalle as $item)
        @php
            $cantidad+=$item->cantidad;
        @endphp
    @endforeach

    @if ($cantidad >100)
        @php
            $tipo_impresion = "LIMITADA"
        @endphp
    @endif

    @if ($tipo_impresion == "LIBRE")
        @foreach ($nota_detalle as $producto)
            @for ($i = 0; $i < $producto->cantidad; $i++)
            <table>
                <tr>
                    <td width="55%" style="vertical-align: top;">
                        <table>
                            <tr>
                                <td style="padding-left: 5px;padding-bottom:7px;padding-top:7px;">
                                    @if($empresa->ruta_logo)
                                        <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}"  class="img-fluid" style="height: 20px;object-fit:cover;"> 
                                    @else
                                        <img src="{{ public_path() . '/img/default.png' }}" class="img-fluid">
                                    @endif
                                </td>
                                <td style="padding-left: 8px;">
                                    <p class="datos-empresa">
                                        <span style="font-weight:bold;">RUC: </span>{{$empresa->ruc}}
                                    </p>
                                    <p class="empresa_nombre">{{$empresa->razon_social}}</p>
                                    <p class="datos-empresa">{{$empresa->direccion_fiscal}}</p>
                                    <p class="datos-empresa">{{$empresa->correo}}</p>
                                </td>
                            </tr>
                            <tr style="border-top:solid 1px black;">
                                <td colspan="2" style="padding-left:5px;">
                                    <p class="producto" ><span class="descripcion_span">TIPO: </span>{{$producto->categoria_nombre}}</p>
                                    <p class="producto" ><span class="descripcion_span">MODELO: </span>{{$producto->modelo_nombre}}</p>
                                    <p class="producto" ><span class="descripcion_span">COLOR: </span>{{$producto->color_nombre}}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="45%" style="vertical-align: middle;text-align:center;border-left:solid 1px black;height:100%;" >
                        <div >
                            <div style="border: .5px dashed black; border-radius: 5px;padding:5px 0; width:90%;margin:0 auto;" >
                                    <img src="{{ base_path() . '/storage/app/'.$producto->ruta_cod_barras }}" class="img_cod_barras">
                                    <p style="font-size: 10px;">{{'775'.$producto->modelo_id.$producto->producto_id.$producto->color_id.$producto->talla_id}}</p>    
                            </div>
                            
                            <div style="text-align:right;">
                                <p class="talla_nombre" style="font-size: 20px;padding-right:15px;">{{$producto->talla_nombre}}</p>
                            </div>

                        </div>
                    </td>
                </tr>
            </table>
            @endfor
        @endforeach
    @endif

    @if ($tipo_impresion == "LIMITADA")
        @foreach ($nota_detalle as $producto)
            @for ($i = 0; $i < 1; $i++)
            <table>
                <tr>
                    <td width="55%" style="vertical-align: top;">
                        <table>
                            <tr>
                                <td style="padding-left: 5px;padding-bottom:7px;padding-top:7px;">
                                    @if($empresa->ruta_logo)
                                        <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}"  class="img-fluid" style="height: 20px;object-fit:cover;"> 
                                    @else
                                        <img src="{{ public_path() . '/img/default.png' }}" class="img-fluid">
                                    @endif
                                </td>
                                <td style="padding-left: 8px;">
                                    <p class="datos-empresa">
                                        <span style="font-weight:bold;">RUC: </span>{{$empresa->ruc}}
                                    </p>
                                    <p class="empresa_nombre">{{$empresa->razon_social}}</p>
                                    <p class="datos-empresa">{{$empresa->direccion_fiscal}}</p>
                                    <p class="datos-empresa">{{$empresa->correo}}</p>
                                </td>
                            </tr>
                            <tr style="border-top:solid 1px black;">
                                <td colspan="2" style="padding-left:5px;">
                                    <p class="producto" ><span class="descripcion_span">TIPO: </span>{{$producto->categoria_nombre}}</p>
                                    <p class="producto" ><span class="descripcion_span">MODELO: </span>{{$producto->modelo_nombre}}</p>
                                    <p class="producto" ><span class="descripcion_span">COLOR: </span>{{$producto->color_nombre}}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="45%" style="vertical-align: middle;text-align:center;border-left:solid 1px black;height:100%;" >
                        <div >
                            <div style="border: .5px dashed black; border-radius: 5px;padding:5px 0; width:90%;margin:0 auto;" >
                                    <img src="{{ base_path() . '/storage/app/'.$producto->ruta_cod_barras }}" class="img_cod_barras">
                                    <p style="font-size: 10px;">{{'775'.$producto->modelo_id.$producto->producto_id.$producto->color_id.$producto->talla_id}}</p>    
                            </div>
                            
                            <div style="text-align:right;">
                                <p class="talla_nombre" style="font-size: 20px;padding-right:15px;">{{$producto->talla_nombre}}</p>
                            </div>

                        </div>
                    </td>
                </tr>
            </table>
            @endfor
        @endforeach
    @endif
   
</body>
</html>
