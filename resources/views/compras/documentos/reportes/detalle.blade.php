<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Ecovalle | Sistema de Producción</title>
    <style>
      
        .clearfix:after {
        content: "";
        display: table;
        clear: both;
        }

        a {
        color: #0f243e;
        text-decoration: none;
        }

        body { 
        position: relative;
        height: 29.7cm; 
        margin: 0 auto; 
        font-size: 12px; 
        font-family: Helvetica; 
        text-transform: uppercase; /*Mayuscula en todo el reporte*/
        } 

        header {
        padding: 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #AAAAAA;
        }

        #logo {
        float: left;
        /* margin-top: 8px; */
        padding-left: 20px;
        width: 40%;
        /* padding: 10px 10px 10px 10px; */
        }

        #logo img {
        height: 90px;
        width: 100px;
        }

        #company {
        float: right;
        text-align: right;
        /* max-width: 40%; */
        width: 60%;
        margin-top: 15px;
        font-size: 12px;
        }

        #company .name{
        font-size: 13px;
        }


        #details {
        /* margin-bottom: 10px; */
        width: 100%;
        }



        h2.name {
        font-size: 1.4em;
        font-weight: normal;
        margin: 0;
        }


        /* ///////ORDEN DE COMPRA//////// */
        #invoice {
        float: left;
        text-align: left;
        width: 40%;
        }

        #tabla-orden {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-top: 5px;
        margin-bottom: 15px;
        background: #EEEEEE;
        
        }
        #tabla-orden th {
        white-space: nowrap;        
        font-weight: normal;
        }

        #tabla-orden thead th {
        background: #0f243e;
        color: #FFFFFF;
        padding: 8px;
        border: 2px solid #0f243e;
        }

        #tabla-orden tbody tr th {
        padding: 5px;
        }

        #tabla-orden tbody tr .datos-orden-titulo{
        text-align: left;
        font-weight: 700;
        font-size: 10px;
        width: 70%;
        border: 2px solid #0f243e;
        }
        #tabla-orden tbody tr .datos-orden{
        text-align: center;
        font-size: 10px;
        width: 30%;
        font-weight: normal;
        white-space: initial;
        word-wrap: break-word;
        border: 2px solid #0f243e;
        }


        #client {
        padding-right: 6px;
        border-right: 6px solid #0f243e;
        float: right;
        text-align: right;
        width: 60%;

        }

        #client .to {
        color: #777777;
        }


        /* ///////////////////////////////////// */
        #tabla-proveedor {
        width: 100%;
        border-spacing: 0;
        margin-top: 5px;
        margin-bottom: 15px;
        color: #000;
        background: #EEEEEE;
        }
        #tabla-proveedor thead th {
        background: #0f243e;
        color: #ffff;
        font-size: 11px;
        font-weight: 700;
        padding: 8px;
        border: 2px solid #0f243e;
        }

        #tabla-proveedor tbody tr th{
        padding: 8px;
        border: 2px solid #0f243e;
        }

        #tabla-proveedor tbody tr .datos-proveedor-titulo{
        text-align: left;
        font-weight: 700;
        font-size: 10px;
        width: 30%;
        }
        #tabla-proveedor tbody tr .datos-proveedor{
        text-align: left;
        font-size: 10px;
        width: 70%;
        white-space: initial;
        word-wrap: break-word;
        font-weight: normal;
        }







        /* //////////TABLA DE PRODUCTOS///////// */

        #tabla-productos {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20px;
        color: #000;
        }

        #tabla-productos th,
        #tabla-productos td {
        padding: 8px;
        background: #EEEEEE;
        text-align: center;
        font-size: 11px;
        }

        #tabla-productos th {
        white-space: nowrap;        
        font-weight: normal;
        }

        #tabla-productos thead tr th {
        /* border: 2px solid #fff; */
        border: 2px solid #0f243e;
        }

        #tabla-productos td {
        text-align: right;
        }


        #tabla-productos .no {
        font-size: 10px;
        text-align: center;
        }

        #tabla-productos thead th {
        background: #0f243e;
        color: #ffff;
        font-size: 11px;
        font-weight: 700;
        }

        /* Presentacion */
        #tabla-productos .desc {
        text-align: center;
        }

        /* Producto */
        #tabla-productos .unit {
        text-align: left;
        }

        /* Precio */
        #tabla-productos .qty {
        text-align: center;
        }

        /* Total */
        #tabla-productos .total {
        text-align: center;
        }


        #tabla-productos td.no,
        #tabla-productos td.desc,
        #tabla-productos td.unit,
        #tabla-productos td.qty,
        #tabla-productos td.total {
        font-size: 11px;
        /* border: 2px solid #fff; */
        border: 2px solid #0f243e;
        }

        #tabla-productos tbody tr:last-child td {
        border: none;
        }

        #tabla-productos tfoot td {
        /* padding: 10px 20px; */
        padding: 8px;
        background: #FFFFFF;
        /* border-bottom: none; */
        font-size: 12px;
        white-space: nowrap; 
        /* border: 2px solid #fff; */
        /* border: 2px solid #0f243e; */
        /* border-top: 1px solid #AAAAAA;  */
        }

        #tabla-productos tfoot tr:first-child td {
        border-top: none; 
        }



        #tabla-productos tfoot tr td{
        text-align: center;
        }

        #tabla-productos tfoot tr .sub{
        background: #0f243e;
        color: #ffff;
        font-weight: 700;
        font-size: 11px;
        border: 2px solid #0f243e;
        }

        #tabla-productos tfoot tr .sub-monto{
        background: #EEEEEE;
        border: 2px solid #0f243e;
        /* background: #0f243e;
        color: #ffff; */
        font-weight: 700;
        font-size: 11px;
        }


        /* ////////////////////////////////////// */
        /* /////////////TRANSPORTISTA//////////// */


        #tabla-transporte {
        width: 100%;
        border-spacing: 0;
        margin-top: 5px;
        margin-bottom: 15px;
        color: #000;
        background: #EEEEEE;
        }

        #tabla-transporte thead th {
        background: #0f243e;
        color: #ffff;
        font-size: 11px;
        font-weight: 700;
        padding: 10px;
        border: 2px solid #0f243e;
        }

        #tabla-transporte tbody tr th{
        padding: 8px;
        border: 2px solid #0f243e;
        }

        #tabla-transporte tbody tr .datos-transporte-titulo{
        text-align: left;
        font-weight: 700;
        font-size: 10px;
        width: 30%;
        }

        #tabla-transporte tbody tr .datos-transporte{
        text-align: left;
        font-size: 10px;
        width: 70%;
        white-space: initial;
        word-wrap: break-word;
        font-weight: normal;
        }

        /* ///////////ADICIONAL///////////// */


        #tabla-adicional {
        width: 100%;
        border-spacing: 0;
        margin-top: 5px;
        margin-bottom: 15px;
        color: #000;
        background: #EEEEEE;
        }

        #tabla-adicional tbody tr th{
        padding: 8px;
        border: 2px solid #0f243e;
        }

        #tabla-adicional tbody tr .datos-adicional-titulo{
        text-align: left;
        font-weight: 700;
        font-size: 10px;
        width: 30%;
        color: #FFFFFF;
        background: #0f243e;
        }

        #tabla-adicional tbody tr .datos-adicional{
        text-align: left;
        font-size: 10px;
        width: 70%;
        white-space: initial;
        word-wrap: break-word;
        font-weight: normal;
        }




        #notices{
        padding-left: 6px;
        border-left: 6px solid #0f243e;  
        }

        #notices .notice {
        font-size: 1.2em;
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
    <header class="clearfix">
    
        <div>
            <div id="logo">
                {{-- @if($documento->empresa->ruta_logo)
                <img src="{{ base_path() . '/storage/app/'.$documento->empresa->ruta_logo }}">
                @else
                <img src="{{asset('storage/empresas/logos/default.png')}}">
                @endif --}}
            </div>
            
            <div id="company">
                <h2 class="name">{{$documento->empresa->razon_social}}</h2>
                <div>RUC:{{$documento->empresa->ruc}}</div>
                <div>{{$documento->empresa->direccion_fiscal}}</div>
            </div>
      </div>
    
    </header>

    <main>
      <div id="details" class="clearfix">




        <div id="invoice">
            
          <table cellspacing="0" cellpadding="0" id="tabla-orden">
            <thead>
                <tr>
                    <th colspan="2" class="text-center">DOCUMENTO DE COMPRA</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <th class="datos-orden-titulo">
                        N°:
                    </th>

                    <th class="datos-orden">
                        DC - {{$documento->id}}
                    </th>

                </tr>
                <tr>
                    <th class="datos-orden-titulo">
                        FECHA EMISION:
                    </th>

                    <th class="datos-orden">
                        {{ Carbon\Carbon::parse($documento->fecha_emision)->format('d/m/y') }}
                    </th>

                </tr>
                <tr>
                    <th class="datos-orden-titulo">
                        FECHA ENTREGA:
                    </th>

                    <th class="datos-orden">
                        {{ Carbon\Carbon::parse($documento->fecha_entrega)->format('d/m/y') }}
                    </th>

                </tr>
            </tbody>

          </table>
          
        </div>

        <div id="client">

            <div class="to">CONTACTO:</div>
            <h2 class="name">{{$nombre_completo}}</h2>
            <div class="address">{{$documento->usuario->user->persona->telefono_movil}}</div>
            <div class="email"><a href="mailto:{{$documento->usuario->user->persona->correo_electronico}}">{{$documento->usuario->user->persona->correo_electronico}}</a></div>
        </div>



      </div>

      <table border="0" cellspacing="0" cellpadding="0" id="tabla-proveedor">
        <thead>
          <tr>
            <th colspan="2" class="text-center">DATOS DEL PROVEEDOR</th>
          </tr>
        </thead>
        <tbody>
            <tr>
                <th class="datos-proveedor-titulo">
                    RAZON SOCIAL:
                </th>

                <th class="datos-proveedor">
                    {{$documento->proveedor->descripcion}}
                </th>

            </tr>

            @if($documento->proveedor->ruc)

            <tr>
                <th class="datos-proveedor-titulo">
                    RUC:
                </th>

                <th class="datos-proveedor">
                    {{$documento->proveedor->ruc}}
                </th>

            </tr>

            @else
            <tr>
                <th class="datos-proveedor-titulo">
                    DNI:
                </th>

                <th class="datos-proveedor">
                    {{$documento->proveedor->dni}}
                </th>

            </tr>
            @endif

            <tr>
                <th class="datos-proveedor-titulo">
                    DIRECCION:
                </th>

                <th class="datos-proveedor">
                    {{$documento->proveedor->direccion}}
                </th>

            </tr>
            <tr>
                <th class="datos-proveedor-titulo">
                    CONTACTO:
                </th>

                <th class="datos-proveedor">
                @if($documento->proveedor->contacto)
                {{$documento->proveedor->contacto}}
                @else
                -
                @endif
                </th>

            </tr>
            <tr>
                <th class="datos-proveedor-titulo">
                    TELEFONO:
                </th>

                <th class="datos-proveedor">
                    @if($documento->proveedor->telefono)
                    {{$documento->proveedor->telefono}}
                    @else
                    -
                    @endif
                </th>

            </tr>
            <tr>
                <th class="datos-proveedor-titulo">
                    CORREO:
                </th>

                <th class="datos-proveedor">
                    <a href="mailto:{{$documento->proveedor->correo}}">{{$documento->proveedor->correo}}
                    </a>
                </th>

            </tr>
        </tbody>

      </table>


      <table border="0" cellspacing="0" cellpadding="0" id="tabla-productos">
        <thead>
          <tr>
            <th class="no">CANT.</th>
            <th class="desc">PRESENTACION</th>
            <th class="unit">PRODUCTO</th>
            <th class="qty">COSTO FLETE</th>
            <th class="qty">PRECIO</th>
            <th class="total">TOTAL</th>
          </tr>
        </thead>
        <tbody>

        @foreach($detalles as $detalle)
          <tr>
            <td class="no">{{$detalle->cantidad}}</td>
            <td class="desc">
                --
            </td>
            <td class="unit">{{$detalle->producto->nombre}}</td>
            <td class="qty">{{$moneda.' '.$detalle->costo_flete}}</td>
            <td class="qty">{{$moneda.' '.$detalle->precio}}</td>
            <td class="total">{{$moneda.' '.$detalle->precio * $detalle->cantidad}}</td>
          </tr>
        @endforeach

        </tbody>
        <tfoot>
          <tr>
            <td colspan="4"></td>
            <td class="sub" colspan="1">SUBTOTAL</td>
            <td class="sub-monto">{{$moneda.'  '.$subtotal}}</td>
          </tr>
          <tr>
            <td colspan="4"></td>
            <td class="sub" colspan="1">IGV 
                @if($detalle->documento->igv)
                    {{$detalle->documento->igv}}%
                @else
                    18%
                @endif
                </td>
            <td class="sub-monto">{{$moneda.'  '.$igv}}</td>
          </tr>
          <tr>
            <td colspan="4"></td>
            <td class="sub" colspan="1">TOTAL</td>
            <td class="sub-monto">{{$moneda.'  '.$total}}</td>
          </tr>
        </tfoot>
      </table>

      <table border="0" cellspacing="0" cellpadding="0" id="tabla-transporte">
        <thead>
          <tr>
            <th colspan="2" class="text-center">DATOS DEL TRANSPORTISTA</th>
          </tr>
        </thead>
        <tbody>

            <tr>
                <th class="datos-transporte-titulo">
                    RUC:
                </th>

                <th class="datos-transporte">
                    {{$documento->proveedor->ruc_transporte}}
                </th>

            </tr>

            <tr>
                <th class="datos-transporte-titulo">
                    EMPRESA:
                </th>

                <th class="datos-transporte">
                    {{$documento->proveedor->transporte}}
                </th>

            </tr>

            <tr>
                <th class="datos-transporte-titulo">
                    DIRECCION:
                </th>

                <th class="datos-transporte">
                    {{$documento->proveedor->direccion_transporte}}
                </th>

            </tr>

        </tbody>

      </table>

      <table border="0" cellspacing="0" cellpadding="0" id="tabla-adicional">
        <tbody>

            <tr>
                <th class="datos-adicional-titulo">
                    TIPO DE DOCUMENTO:
                </th>

                <th class="datos-adicional">
                    {{$documento->tipo_compra}}
                </th>

            </tr>

            <tr>
                <th class="datos-adicional-titulo">
                    CONDICION DE DOCUMENTO:
                </th>

                <th class="datos-adicional">
                    {{$documento->modo_compra}}
                </th>

            </tr>

            <tr>
                <th class="datos-adicional-titulo">
                    OBSERVACION:
                </th>

                <th class="datos-adicional">
                    {{$documento->observacion}}
                </th>

            </tr>

        </tbody>

      </table>

    </main>
    <footer>
      SISCOM SAC
    </footer>
  </body>
</html>