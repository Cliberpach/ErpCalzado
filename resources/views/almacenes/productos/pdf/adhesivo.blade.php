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

<link href="https://fonts.googleapis.com/css2?family=Bevan:ital@0;1&display=swap" rel="stylesheet">
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
        font-family: "Bevan", serif;
        text-align: left;
        font-size: 80px;
        line-height: 45px;
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
        height: 100px;
        object-fit: contain;
        padding:0;
        margin:0;
        width:95%;
    }
    .div_img_barras{
        margin:0;
        padding:0;
        border: solid 1px pink; 
        border-radius: 5px; 
    }
    .talla_nombre{
        font-family: "Righteous", sans-serif;
        margin:0;
        padding:0;
        font-size: 240px;
        padding-right:5px;
        text-align: right;
    }
    .descripcion_span{
        font-weight: bold;
        font-family: "Roboto Condensed", sans-serif;
        font-size: 22px;
        text-align: start;
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

    @if ($cantidad >= 300 )
        @php
            $tipo_impresion = "LIMITADA"
        @endphp
    @endif 

    @if ($tipo_impresion == "LIBRE")
        @foreach ($nota_detalle as $producto)
            @for ($i = 0; $i < $producto->cantidad; $i++)
            <table>
                <tr>
                    <td width="55%">
                        
                            <div>
                                <span class="descripcion_span">TIPO: </span>
                                <p class="producto" >{{$producto->categoria_nombre}}</p>
                            </div>
                            <div style="margin:15px 0;">
                                <span class="descripcion_span">MODELO: </span>
                                <p class="producto" >{{$producto->modelo_nombre}}</p>
                            </div>
                            <div>
                                <span class="descripcion_span">COLOR: </span>
                                <p class="producto" >{{$producto->color_nombre}}</p>
                            </div>
                    </td>
                    <td width="45%" style="border-left:solid 1px black;height:100%;" >
                       
                        <div style="padding:5px;vertical-align: top;text-align:center;height:32px;">
                            <div style="border: .5px dashed black; border-radius: 4px; width:90%;margin:0 auto;padding:1.7px;" >
                                @if ($producto->ruta_cod_barras)
                                    <img src="{{ base_path() . '/storage/app/'.$producto->ruta_cod_barras }}" class="img_cod_barras">
                                @else
                                    <img src="#" alt="SIN CÓDIGO DE BARRAS">
                                @endif
                                <p style="font-size: 10px;margin:0;padding:0;">{{'775'.$producto->modelo_id.$producto->producto_id.$producto->color_id.$producto->talla_id}}</p>    
                            </div>
                        </div>

                        <div style="vertical-align: bottom;"> 
                            <p class="talla_nombre" >{{$producto->talla_nombre}}</p>
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
                    <td width="55%">
                        
                            <div>
                                <span class="descripcion_span">TIPO: </span>
                                <p class="producto" >{{$producto->categoria_nombre}}</p>
                            </div>
                            <div style="margin:15px 0;">
                                <span class="descripcion_span">MODELO: </span>
                                <p class="producto" >{{$producto->modelo_nombre}}</p>
                            </div>
                            <div>
                                <span class="descripcion_span">COLOR: </span>
                                <p class="producto" >{{$producto->color_nombre}}</p>
                            </div>
                    </td>
                    <td width="45%" style="border-left:solid 1px black;height:100%;" >
                       
                        <div style="padding:5px;vertical-align: top;text-align:center;height:32px;">
                            <div style="border: .5px dashed black; border-radius: 4px; width:90%;margin:0 auto;padding:1.7px;" >
                                <img src="{{ base_path() . '/storage/app/'.$producto->ruta_cod_barras }}" class="img_cod_barras">
                                <p style="font-size: 10px;margin:0;padding:0;">{{'775'.$producto->modelo_id.$producto->producto_id.$producto->color_id.$producto->talla_id}}</p>    
                            </div>
                        </div>

                        <div style="vertical-align: bottom;"> 
                            <p class="talla_nombre" >{{$producto->talla_nombre}}</p>
                        </div>
                        
                    </td>
                </tr>
            </table>
            @endfor
        @endforeach
    @endif 
   
</body>
</html>
