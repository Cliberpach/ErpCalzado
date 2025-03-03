<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>{{ $documento->serie.'-'.$documento->correlativo }}</title>
        <link rel="icon" href="{{ base_path() . '/img/siscom.ico' }}" />
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
            .tbl-detalles tfoot{
                font-size: 9.5px;
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

            .tbl-info-credito {
                width: 100%;
                font-size: 6px;
                border: 1px solid black;
            }

            .tbl-info-retencion {
                width: 100%;
                font-size: 6px;
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
        <div class="cabecera">
            <div class="logo">
                @if($empresa->ruta_logo)
                <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}" class="img-fluid">
                @else
                <img src="{{ public_path() . '/img/default.png' }}" class="img-fluid">
                @endif
            </div>
            <div class="empresa">
                {{-- <p class="m-0 p-0 text-uppercase nombre-empresa">{{ DB::table('empresas')->count() == 0 ? 'SISCOM ' : DB::table('empresas')->first()->razon_social }}</p>
                <p class="m-0 p-0 text-uppercase ruc-empresa">RUC {{ DB::table('empresas')->count() == 0 ? '- ' : DB::table('empresas')->first()->ruc }}</p>
                <p class="m-0 p-0 text-uppercase ruc-empresa"> {{ $empresa->direccion_fiscal }}</p>
                <p class="m-0 p-0 text-uppercase ruc-empresa">-------------------------</p> --}}
                <p class="m-0 p-0 text-uppercase direccion-empresa">{{ $sede->nombre }}</p>
                <p class="m-0 p-0 text-uppercase direccion-empresa">{{ $sede->direccion }}</p>
                <p class="m-0 p-0 text-info-empresa">Teléfono: {{ $sede->telefono }}</p>
            </div><br>
            <div class="comprobante">
                <div class="numero-documento">
                    <p class="m-0 p-0 text-uppercase">{{ $documento->nombreDocumento() }}</p>
                    <p class="m-0 p-0 text-uppercase">{{$documento->serie.'-'.$documento->correlativo}}</p>
                </div>
            </div>
        </div><br>
        <div class="informacion">
            <table class="tbl-informacion">
                <tr>
                    <td>F. EMISIÓN</td>
                    <td>:</td>
                    <td>{{ getFechaFormato( $documento->fecha_documento ,'d/m/Y')}} {{ date_format($documento->created_at, 'H:i') }}</td>
                </tr>
                <tr>
                    <td>F. VENC.</td>
                    <td>:</td>
                    <td>{{ getFechaFormato( $documento->fecha_vencimiento ,'d/m/Y')}}</td>
                </tr>
                <tr>
                    <td>CLIENTE</td>
                    <td>:</td>
                    <td>{{ $documento->clienteEntidad->nombre }}</td>
                </tr>
                <tr>
                    <td class="text-uppercase">{{ $documento->tipo_documento_cliente}}</td>
                    <td>:</td>
                    <td>{{ $documento->clienteEntidad->documento }}</td>
                </tr>
                <tr>
                    <td>DIRECCIÓN</td>
                    <td>:</td>
                    <td>{{ $documento->clienteEntidad->direccion }}</td>
                </tr>
                <tr>
                    <td>TELÉFONO</td>
                    <td>:</td>
                    <td class="text-uppercase">{{ $documento->clienteEntidad->telefono_movil}}</td>
                </tr>
                @if ($documento->observacion)
                <tr>
                    <td>PLACA</td>
                    <td>:</td>
                    <td class="text-uppercase">{{ $documento->observacion }}</td>
                </tr>
                @endif
                <tr>
                    <td>ATENDIDO POR</td>
                    <td>:</td>
                    <td class="text-uppercase">{{$documento->user->usuario }}</td>
                    {{-- <td class="text-uppercase">{{ $documento->user->user->persona ? $documento->user->user->persona->getApellidosYNombres() : $documento->user->usuario }}</td> --}}
                </tr>
            </table>
        </div><br>
        <div class="cuerpo">
            <table class="tbl-detalles text-uppercase" cellpadding="2" cellspacing="0">
                <thead>
                    <tr >
                        <th style="text-align: left; width: 10%;">CANT</th>
                        <th style="text-align: left; width: 15%;">UM</th>
                        <th style="text-align: left; width: 55%;">DESCRIPCION</th>
                        <th style="text-align: left; width: 10%;">P.UNIT.</th>
                        <th style="text-align: right; width: 10%;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detalles as $item)
                        @if($documento->tipo_venta_id == 129)
                            @if ($item->cantidad - $item->detalles->sum('cantidad') > 0)
                                <tr>
                                    <td style="text-align: left">{{ number_format($item->cantidad - $item->detalles->sum('cantidad'), 2) }}</td>
                                    <td style="text-align: left">{{ $item->unidad }}</td>
                                    <td style="text-align: left">{{ $item->nombre_producto.'-'.$item->nombre_modelo.'-'.$item->nombre_color.'-'.$item->nombre_talla }}</td>
                                    <td style="text-align: left">{{ number_format($item->precio_unitario,2) }}</td>
                                    <td style="text-align: right">{{ number_format(($item->cantidad - $item->detalles->sum('cantidad')) * $item->precio_unitario, 2) }}</td>
                                </tr>
                            @endif
                        @else
                            <tr>
                                <td style="text-align: left">{{ number_format($item->cantidad, 2) }}</td>
                                <td style="text-align: left">{{ $item->unidad }}</td>
                                <td style="text-align: left">{{ $item->nombre_producto.'-'.$item->nombre_modelo.'-'.$item->nombre_color.'-'.$item->nombre_talla }}</td>
                                <td style="text-align: left">{{ number_format($item->precio_unitario, 2) }}</td>
                                <td style="text-align: right">{{ number_format($item->importe, 2) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    @if ($documento->monto_embalaje!=0)
                        <tr>
                            <td style="text-align: left">{{ number_format(1, 2) }}</td>
                            <td style="text-align: left">NIU</td>
                            <td style="text-align: left">EMBALAJE</td>
                            <td style="text-align: left">{{ number_format($documento->monto_embalaje, 2) }}</td>
                            <td style="text-align: right">{{ number_format($documento->monto_embalaje, 2) }}</td>
                        </tr>
                    @endif
                    @if ($documento->monto_envio!=0)
                        <tr>
                            <td style="text-align: left">{{ number_format(1, 2) }}</td>
                            <td style="text-align: left">NIU</td>
                            <td style="text-align: left">ENVÍO</td>
                            <td style="text-align: left">{{ number_format($documento->monto_envio, 2) }}</td>
                            <td style="text-align: right">{{ number_format($documento->monto_envio, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    @if($documento->tipo_venta_id != 129)
                        @if ($documento->monto_descuento!=0)
                            <tr>
                                <th colspan="4" style="text-align:right">Descuento: S/.</th>
                                <th style="text-align:right">{{ number_format($documento->monto_descuento, 2) }}</th>
                            </tr>
                        @endif
                        <tr>
                            <th colspan="4" style="text-align:right">Sub Total: S/.</th>
                            <th style="text-align:right">{{ number_format($documento->total, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="4" style="text-align:right">IGV: S/.</th>
                            <th style="text-align:right">{{ number_format($documento->total_igv, 2) }}</th>
                        </tr>
                        @if (!empty($documento->retencion))
                            <tr>
                                <th colspan="4" style="text-align:right">Total: S/.</th>
                                <th style="text-align:right">{{ number_format($documento->total + $documento->retencion->impRetenido, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="4" style="text-align:right">Imp. Retenido: S/.</th>
                                <th style="text-align:right">{{ number_format($documento->retencion->impRetenido, 2) }}</th>
                            </tr>  
                        @endif
                        <tr>
                            <th colspan="4" style="text-align:right">Total a pagar: S/.</th>
                            <th style="text-align:right">{{ number_format($documento->total_pagar, 2) }}</th>
                        </tr>
                    @else
                        @if ($documento->monto_descuento!=0)
                            <tr>
                                <th colspan="4" style="text-align:right">Descuento: S/.</th>
                                <th style="text-align:right">{{ number_format($documento->monto_descuento, 2) }}</th>
                            </tr>
                        @endif
                        <tr>
                            <th colspan="4" style="text-align:right">Total a pagar: S/.</th>
                            <th style="text-align:right">{{ number_format($documento->total_pagar - $documento->notas->sum('mtoImpVenta'), 2) }}</th>
                        </tr>
                   @endif
                </tfoot>
            </table>
            <br>
            <p class="p-0 m-0 text-uppercase text-cuerpo">{{$documento->legenda}}</b></p>
            <br>
            @if ($mostrar_cuentas === "SI")
                <table class="tbl-qr">
                    <tr>
                        <td>
                            @foreach($empresa->bancos as $banco)
                                <p class="m-0 p-0 text-cuerpo"><b class="text-uppercase">{{ $banco->descripcion}}</b> {{ $banco->tipo_moneda}} <b>N°: </b> {{ $banco->num_cuenta}} <b>CCI:</b> {{ $banco->cci}}</p>
                            @endforeach
                        </td>
                    </tr>
                </table>
            @endif
            @if (strtoupper($documento->condicion->descripcion) == 'CREDITO' || strtoupper($documento->condicion->descripcion) == 'CRÉDITO')
                <br>
                <div style="border: 1px solid black; padding: 2px">
                    <table class="tbl-info-credito" style="margin-bottom: 2px;">
                        <tr>
                            <th colspan="3" style="text-align: left">Informacion del crédito</th>
                        </tr>
                        <tr>
                            <td style="text-align: left">Monto neto pendiente de pago</td>
                            <td>:</td>
                            <td>S/. {{ number_format($documento->total_pagar - $documento->notas->sum('mtoImpVenta'), 2) }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left">Total de cuotas</td>
                            <td>:</td>
                            <td>1</td>
                        </tr>
                    </table>
                    <table class="tbl-info-credito" style="margin-top: 2px;">
                        <tr>
                            <th style="text-align: center">N° Cuota</th>
                            <th style="text-align: center">Fec. Venc.</th>
                            <th style="text-align: center">Monto</th>
                        </tr>
                        <tr>
                            <td style="text-align: center">1</td>
                            <td style="text-align: center">{{ $documento->fecha_vencimiento }}</td>
                            <td style="text-align: center">{{ number_format($documento->total_pagar - $documento->notas->sum('mtoImpVenta'), 2) }}</td>
                        </tr>
                    </table>
                </div>
            @endif
            @if (!empty($documento->retencion))
                <div style="border: 1px solid black; padding: 2px; margin-top: 5px;">
                    <table class="tbl-info-retencion">
                        <tr>
                            <th style="text-align: left;">Información de la retención</th>
                        </tr>
                        <tr>
                            <td>
                                Base imponible de la Retención: &nbsp;&nbsp; S/. {{ number_format($documento->total + $documento->retencion->impRetenido, 2) }}
                            </td>
                            <td>
                                Porcentaje de retención: &nbsp;&nbsp; {{ $documento->clienteEntidad->tasa_retencion }}%
                            </td>
                            <td>
                                Monto de la Retención: &nbsp;&nbsp; S/. {{ number_format($documento->retencion->impRetenido, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            @endif
        </div><br>
        <div class="qr">
            @if($documento->ruta_qr)
                <img src="{{ base_path() . '/storage/app/'.$documento->ruta_qr }}">
            @endif
            @if($documento->hash)
            <p class="m-0 p-0">Código Hash: {{ $documento->hash }}</p>
            @endif
        </div>
        <footer>
            <b>Para consultar el comprobante ingresar a <a target="_blank" href="{{ (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
                "https" : "http") . "://" . $_SERVER['HTTP_HOST']."/buscar"}}"><em>{{ (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
                "https" : "http") . "://" . $_SERVER['HTTP_HOST']."/buscar"}}</em></a></b>
        </footer>
    </body>

</html>
