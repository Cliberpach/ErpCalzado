<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$despacho->distrito.'-'.$despacho->cliente_nombre.'-'.$despacho->created_at}}</title>
    <link rel="stylesheet" href="styles.css">
   
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cedarville+Cursive&family=Vast+Shadow&display=swap" rel="stylesheet">
    
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin: 0 auto 0 auto;
            text-align: center;
        }

        .row {
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 200px;
            padding:0;
            margin:0;
        }

        .company-name{
            font-family: "Roboto", sans-serif;
            font-weight: 700;
            font-style: normal;
            color: rgb(186, 7, 7);
            font-size: 24px;
            padding:0;
            margin:0;
        }

        .phone-number {
            font-size: 24px;
            padding:0;
            margin:0;
            color: rgb(186, 7, 7);
        }



        .destino-text{
            font-size: 75px;
            font-family: "Vast Shadow", serif;
            padding:0;
            margin:0;
        }

    
        .cuadro-content {
            width: 98%;
            vertical-align: middle;
            display: inline-block;
        }

        .nombre_destinatario{
            font-size: 40px;
            margin-top:15px;
           margin-bottom: -20px;
        }

        .doc_destinatario{
            font-size: 40px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .redes-text{
            font-size: 27px;
            font-family: "Cedarville Cursive", cursive;
            margin:0;
            padding:0;
        }

        .empresa-envio{
            font-size: 29px;
        }
        

    </style>
</head>
<body>
    @for ($i = 0; $i < $nro_bultos; $i++)
    <div class="container">
        <div class="row" style="margin-bottom: -45px;margin-top:0px;">
            <div class="logo">
                @if($empresa->ruta_logo)
                    <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}" class="img-fluid" style="width:60%;">
                @else
                    <img src="{{ public_path() . '/img/default.png' }}" class="img-fluid">
                @endif
            </div>
        </div>
        <div class="row" style="margin-bottom: 7px;" >
            <div>
                <p class="company-name" >{{$empresa->razon_social_abreviada}}</p>
            </div>
        </div>
        <div class="row">
            <div class="phone-number">
                <img style="vertical-align: middle;" width="35px;" src="{{ public_path() . '/img/whatsapp_blanco_negro.jpg' }}" class="img-fluid">
                <span style="vertical-align: middle;">{{$empresa->celular}}</span>
            </div>
        </div>

        <div class="row" style="width:100%;">
            <div class="cuadro-content" style="border:1px black solid;">
                <p class="nombre_destinatario" >{{$despacho->destinatario_nombre}}</p>
                <p class="doc_destinatario">{{$despacho->destinatario_dni}}  
                    {{$despacho->cliente_celular? ' - '.$despacho->cliente_celular:''}}</p> 
            </div>
        </div>

        <div class="row" style="margin-top:-50px;">
            <div>
                <p class="destino-text" style="vertical-align: middle;">{{$despacho->distrito}}</p>
            </div>
        </div>

        <div class="row" style="margin-top:-15px;">
            <p style="margin:0;padding:0;" class="empresa-envio">{{$despacho->empresa_envio_nombre.' - '.$despacho->tipo_pago_envio}}</p>
        </div>

      

        <div class="row">
            <p class="redes-text">Nuestras Redes Sociales</p>
        </div>

        <div class="row" style="margin-bottom:0px;margin-top:0;">
            <img style="vertical-align: middle;margin:0;padding:0;" width="35px;" src="{{ public_path() . '/img/facebook.jpg' }}" class="img-fluid">
            <img style="vertical-align: middle;margin:0;padding:0;" width="35px;" src="{{ public_path() . '/img/instagram.png' }}" class="img-fluid">
            <img style="vertical-align: middle;margin:0;padding:0;" width="35px;" src="{{ public_path() . '/img/tiktok.png' }}" class="img-fluid">
            <span>{{$empresa->correo}}</span>
        </div>
    </div>
    @endfor
</body>
</html>
