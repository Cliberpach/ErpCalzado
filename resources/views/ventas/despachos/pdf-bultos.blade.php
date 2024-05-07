<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DESPACHO</title>
    <style>
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
          display: inline-block;
          width:42%;
          margin: 0px; /* Elimina el margen */
          padding: 0px; /* Elimina el padding */
          border: 1px blue solid;
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
        }
        .linea-individual{
          border-bottom: 4px solid #000; 
        }

        .ruc-text{
          font-size: 30px; /* Tamaño del texto "REMITENTE" */
          display: inline-block;
          width:15%;
          margin: 20px 0 0 0; /* Elimina el margen */
          padding: 0; /* Elimina el padding */
          border: 1px blue solid;
          
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
          display: inline-block;
          width:30%;
          margin: 20px 0 0 0; /* Elimina el margen */
          padding: 0; /* Elimina el padding */
          border: 1px blue solid;
        }
        .linea-costado-celular {
          display: inline-block; /* Mostrar como elemento en línea */
          border-bottom: 4px solid #000; /* Borde inferior como un guion bajo */
          width: 68%; /* Ancho de la línea */
          margin:0px;
          padding:0px;
        }
        .domicilio-check{
          width: 15%;
          height: 50px;
          border: 1px solid black;
          display: inline-block;
          margin-top: 20px;
        }
        .domicilio-text{
          width: 50%;
          display: inline-block;
          font-weight: bold;
          font-size: 17px;
        }

        .destino-text{
          font-size: 55px; /* Tamaño del texto "REMITENTE" */
          display: inline-block;
          width:48%;
          margin: 0px; /* Elimina el margen */
          padding: 0px; /* Elimina el padding */
          border: 1px blue solid;
        }
        .linea-costado-destino {
          display: inline-block; /* Mostrar como elemento en línea */
          border-bottom: 4px solid #000; /* Borde inferior como un guion bajo */
          width: 50%; /* Ancho de la línea */
          margin:0px;
          padding:0px;
        }

    </style>
</head>
<body>
  @for ($i = 0; $i < $nro_bultos; $i++)
  <table class="cargo-table" style="width: 100%;">
    <tbody>
      <tr style="width: 100%;">
        <td class="logo-cell logo" style="border: solid 1px black;">
          @if($empresa->ruta_logo)
            <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}" class="img-fluid" style="width:50%;">
          @else
            <img src="{{ public_path() . '/img/default.png' }}" class="img-fluid">
          @endif
        </td>
        <td class="nro-doc-cell">
          <div class="nro-doc-box">NRO DOC</div>
        </td>
      </tr>
      <tr style="width: 100%;">
        <td style="border: solid 1px black;" style="width: 50%;">
          <table style="width:100%;">
            <tr>
              <td style="border: solid 1px black;padding:0;margin:0;">
                  <p class="origen-text">ORIGEN</p>
                  <span class="linea-costado"></span>
              </td>
            </tr>
            <tr>
              <td style="border:solid 1px black;padding:0;" class="remitente-text">REMITENTE:</td>
            </tr>
            <tr >
              <td  class="linea-individual" style="margin-top: 10px;height:20px;"></td>
            </tr>
            <tr>
              <td style="border:solid 1px black;padding:0;">
                <p class="ruc-text">RUC:</p> <span class="linea-costado-ruc"></span>
              </td> 
            </tr>
            <tr>
              <td style="border:solid 1px black;padding:0;">
                <p class="celular-text">CELULAR:</p> <span class="linea-costado-celular"></span>
              </td> 
            </tr>
            <tr>
              <td style="text-align: center;">
                <div class="cuadro-numeracion" style="text-align: center;vertical-align:middle;font-size:50px;">
                  <p>{{'1/'.($i+1)}}</p>
                </div>
              </td>
            </tr>
          </table>
        </td>
        <td style="border: solid 1px black;" style="width: 50%;">
          <table style="width:100%;">
            <tr>
              <td style="border: solid 1px black;padding:0;margin:0;">
                  <p class="destino-text">DESTINO</p>
                  <span class="linea-costado-destino"></span>
              </td>
            </tr>
            <tr>
              <td style="border:solid 1px black;padding:0;" class="remitente-text">CONSIGNADO:</td>
            </tr>
            <tr >
              <td  class="linea-individual" style="margin-top: 10px;height:20px;"></td>
            </tr>
            <tr>
              <td style="border:solid 1px black;padding:0;">
                <p class="ruc-text">DNI:</p> <span class="linea-costado-ruc"></span>
              </td> 
            </tr>
            <tr>
              <td style="border:solid 1px black;padding:0;">
                <p class="celular-text">CELULAR:</p> <span class="linea-costado-celular"></span>
              </td> 
            </tr>
            <tr>
              <td style="border:solid 1px black;padding:0;">
                <div class="domicilio-check"></div> <p class="domicilio-text">ENTREGA EN DOMICILIO</p>
              </td> 
            </tr>
            <tr>
              <td class="linea-individual" style="margin-top: 10px;height:20px;"></td>
          </tr>
          <tr>
              <td class="linea-individual" style="margin-top: 10px;height:20px;"></td>
          </tr>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
  @endfor

</body>
</html>
