<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$despacho->distrito.'-'.$despacho->cliente_nombre.'-'.$despacho->created_at}}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    
    <style>
        .documento_nro_text{
            font-family: "Lato", sans-serif;
            font-weight: 700;
            font-size: 23px;
        }

        .cargo-table {
            width: 100%;
            border-collapse: collapse; /* Combina los bordes de las celdas en una sola línea */
        }
        .cargo-table th, .cargo-table td {
          
            padding: 8px;
        }
        .cargo-table tr {
            border: 1px solid #000; /* Borde alrededor de toda la fila */
        }
        .logo-cell {
            width: 50%; /* Ajusta el ancho de la celda del logo según sea necesario */
            border: none; /* Elimina el borde de la celda */
            vertical-align: bottom; /* Alinea el contenido en la parte superior de la celda */
        }
        .nro-doc-cell {
          width: 50%; /* Ajusta el ancho de la celda del número de documento según sea necesario */
          text-align: center; /* Alinea el texto al centro */
          border: none; /* Elimina el borde de la celda */  
        }
        .nro-doc-box {
            border: 1px solid #000; /* Borde del cuadro */
            border-radius: 8px; /* Bordes redondeados */
            padding: 8px; /* Espacio interno */
        }
        .logo img {
            max-width: 100%; /* Ajusta el tamaño máximo de la imagen del logo */
            height:170px;
            object-fit: cover;
        }

        .origen-text{
          font-size: 55px; /* Tamaño del texto "REMITENTE" */
          display: inline;
          width:42%;
          margin: 0px; /* Elimina el margen */
          padding: 0px; /* Elimina el padding */
        }
        .linea-costado {
          display: inline-block; /* Mostrar como elemento en línea */
          border-bottom: 4px solid #000; /* Borde inferior como un guion bajo */
          width: 56%; /* Ancho de la línea */
          margin:0px;
          padding:0px;
        }

        .remitente-text{
          font-size: 30px;
          font-weight: bold;
        }
        .linea-individual{
          border-bottom: 4px solid #000; 
        }

        .ruc-text{
          font-size: 30px; /* Tamaño del texto "REMITENTE" */
          display: inline;
          width:15%;
          margin: 0; /* Elimina el margen */
          padding: 0; /* Elimina el padding */
          font-weight: bold;
          
        }
        .linea-costado-ruc {
          display: inline-block; /* Mostrar como elemento en línea */
          border-bottom: 4px solid #000; /* Borde inferior como un guion bajo */
          width: 83.4%; /* Ancho de la línea */
          margin:0px;
          padding:0px;
        }
        .cuadro-numeracion{
          border: 1px solid black;
          height: 150px;
          width: 65%;
          margin:auto;
        }


        .celular-text{
          font-size: 30px; /* Tamaño del texto "REMITENTE" */
          display: inline;
          width:30%;
          margin: 0; /* Elimina el margen */
          padding: 0; /* Elimina el padding */
          font-weight: bold;
        }
        .linea-costado-celular {
          display: inline-block; /* Mostrar como elemento en línea */
          border-bottom: 4px solid #000; /* Borde inferior como un guion bajo */
          width: 68%; /* Ancho de la línea */
          margin:0px;
          padding:0px;
        }
    
    

        .domicilio-text{
          width: 50%;
          display: inline-block;
          font-weight: bold;
          font-size: 17px;
          margin:0;
          padding:0;
          vertical-align: middle;
        }

        .destino-text{
          font-size: 55px; /* Tamaño del texto "REMITENTE" */
          display: inline;
          width:48%;
          margin: 0px; /* Elimina el margen */
          padding: 0px; /* Elimina el padding */
        }
        .linea-costado-destino {
          display: inline-block; /* Mostrar como elemento en línea */
          border-bottom: 4px solid #000; /* Borde inferior como un guion bajo */
          width: 50%; /* Ancho de la línea */
          margin:0px;
          padding:0px;
        }


        .razon-text,
        .consignado-text{
            font-family: "Roboto", sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 30px;
            margin:7px 0;
            padding:0;
        }

        .empresa_ruc,
        .destinatario_dni,
        .empresa_celular,
        .direccion_entrega{
            font-family: "Roboto", sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 22px;
            margin:7px 0;
            padding:0;
        }

        .empresa_origen{
            font-family: "Roboto", sans-serif;
            font-weight: 400;
            font-style: normal;
            font-size: 22px;
            margin:7px 0;
            padding:0;
        }

    </style>
</head>
<body>
  @for ($i = 0; $i < $nro_bultos; $i++)
  <table class="cargo-table" style="width: 100%;">
    <tbody>
      <tr style="width: 100%;">
        <td class="logo-cell logo" >
          @if($empresa->ruta_logo)
            <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}" class="img-fluid" style="width:50%;">
          @else
            <img src="{{ public_path() . '/img/default.png' }}" class="img-fluid">
          @endif
        </td>
        <td class="nro-doc-cell">
          <div class="nro-doc-box"><p style="margin:0;padding:0;" class="documento_nro_text">{{$despacho->documento_nro}}</p></div>
        </td>
      </tr>
      <tr style="width: 100%;">
        <td  style="width: 50%;">
          <table style="width:100%;">
            <tr>
              <td style="padding:0;margin:0;">
                <p class="origen-text">ORIGEN: </p>
                <span class="empresa_origen" style="font-size: 35px;display:inline;margin:0;padding:0;">{{$empresa->direccion_llegada}}</span>
              </td>
            </tr>
            <tr>
              <td style="padding:0;" class="remitente-text">REMITENTE: </td>
            </tr>
            <tr >
              <td style="margin-top: 10px;height:20px;"><p class="razon-text">{{$empresa->razon_social_abreviada}}</p></td>
            </tr>
            <tr>
              <td style="padding:0;">
                <p class="ruc-text">RUC:</p> <span class="empresa_ruc">{{$empresa->ruc}}</span>
              </td> 
            </tr>
            <tr>
              <td style="padding:0;">
                <p class="celular-text">CELULAR:</p> <span class="empresa_celular">{{$empresa->celular}}</span>
              </td> 
            </tr>
            <tr>
              <td style="text-align: center;">
                <div class="cuadro-numeracion" style="text-align: center;vertical-align:middle;font-size:50px;">
                  <p>{{($i+1).'/'.$nro_bultos}}</p>
                </div>
              </td>
            </tr>
          </table>
        </td>
        <td  style="width: 50%;">
          <table style="width:100%;">
            <tr>
              <td style="margin:0;">
                <p class="destino-text">DESTINO: </p>
                <span class="empresa_origen" style="font-size: 35px;display:inline;margin:0;padding:0;">{{$despacho->distrito}}</span>
              </td>
            </tr>
            <tr>
              <td style="padding:0;" class="remitente-text">CONSIGNADO:</td>
            </tr>
            <tr >
                <td style="margin-top: 10px;height:20px;"><p class="consignado-text">{{$despacho->destinatario_nombre}}</p></td>
            </tr>
            <tr>
              <td style="padding:0;">
                <p class="ruc-text">DNI:</p> <span class="destinatario_dni">{{$despacho->destinatario_dni}}</span>
              </td> 
            </tr>
            <tr>
              <td style="padding:0 0 20px 0;">
                <p class="celular-text">CELULAR:</p> <span class="empresa_celular">{{$despacho->cliente_celular}}</span>
              </td> 
            </tr>
            <tr>
              <td style="padding:0;">
                @if ($despacho->entrega_domicilio   === "SI")
                    <img style="width: 44px;vertical-align:middle;" src="{{ public_path() . '/img/check_true.png' }}" class="img-fluid">
                @endif
                @if ($despacho->entrega_domicilio === "NO")
                    <img style="width: 44px;vertical-align:middle;" src="{{ public_path() . '/img/check_false.png' }}" class="img-fluid">
                @endif
                <p class="domicilio-text">ENTREGA EN DOMICILIO</p>
              </td> 
            </tr>
          <tr>
                <td style="margin-top: 10px;height:85px;">
                    <p class="direccion_entrega">{{$despacho->direccion_entrega}}</p>
                </td>
          </tr>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
  @endfor

</body>
</html>
