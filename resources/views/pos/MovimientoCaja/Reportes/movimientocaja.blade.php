<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte Caja Movimiento</title>
    <link rel="icon" href="{{ base_path() . '/img/siscom.ico' }}" />

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: black;
        }

        .cabecera {
            width: 100%;
            position: relative;
            height: 100px;
            max-height: 150px;
        }

        .logo {
            width: 30%;
            position: absolute;
            left: 0%;
        }

        .logo .logo-img {
            position: relative;
            width: 95%;
            margin-right: 5%;
            height: 90px;
        }

        .img-fluid {
            width: 100%;
            height: 100%;
        }

        .empresa {
            width: 60%;
            position: absolute;
            left: 30%;
        }

        .empresa .empresa-info {
            position: relative;
            width: 100%;
        }

        .nombre-empresa {
            font-size: 16px;
        }

        .ruc-empresa {
            font-size: 15px;
        }

        .direccion-empresa {
            font-size: 12px;
        }

        .text-info-empresa {
            font-size: 12px;
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

        .numero-documento {
            margin: 1px;
            padding-top: 20px;
            padding-bottom: 20px;
            border: 2px solid #52BE80;
            font-size: 14px;
        }

        .nombre-documento {
            margin-top: 5px;
            margin-bottom: 5px;
            margin-left: 0px;
            margin-right: 0px;
            width: 100%;
            background-color: #7DCEA0;
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

        .informacion {
            width: 100%;
            position: relative;
            border: 2px solid #52BE80;
        }

        .tbl-informacion {
            width: 100%;
            font-size: 12px;
        }

        .cuerpo {
            width: 100%;
            position: relative;
            border: 1px solid #52BE80;
            margin-top: 10px;
        }

        .tbl-detalles {
            width: 100%;
            font-size: 12px;
        }

        .tbl-detalles thead {
            border-top: 2px solid #52BE80;
            border-left: 2px solid #52BE80;
            border-right: 2px solid #52BE80;
            background-color: #E8F8F5;
        }

        .tbl-detalles tbody {
            border-top: 2px solid #52BE80;
            border-bottom: 2px solid #52BE80;
            border-left: 2px solid #52BE80;
            border-right: 2px solid #52BE80;
        }

        .info-total-qr {
            position: relative;
            width: 100%;
        }

        .tbl-total {
            width: 100%;
            border: 2px solid #229954;
        }

        .qr-img {
            margin-top: 15px;
        }

        .text-cuerpo {
            font-size: 12px
        }

        .tbl-qr {
            width: 100%;
        }

        /*---------------------------------------------*/

        .m-0 {
            margin: 0;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .p-0 {
            padding: 0;
        }

        .cont-check {
            position: relative;
        }

        .checkmark {
            display: inline-block;
            width: 22px;
            height: 22px;
            -ms-transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .checkmark_stem {
            position: absolute;
            width: 3px;
            height: 12px;
            background-color: #229954;
            left: 11px;
            top: 6px;
        }

        .checkmark_kick {
            position: absolute;
            width: 3px;
            height: 3px;
            background-color: #229954;
            left: 8px;
            top: 15px;
        }

        .cont-remove {
            position: relative;
        }

        .remove {
            display: inline-block;
            width: 22px;
            height: 22px;
            -ms-transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .remove_stem {
            position: absolute;
            width: 3px;
            height: 12px;
            background-color: brown;
            left: 11px;
            top: 6px;
        }

        .remove_kick {
            position: absolute;
            width: 12px;
            height: 3px;
            background-color: brown;
            left: 7px;
            top: 10px;
        }

        .pagos-cell {
            font-size: 11px;
            line-height: 1.6;
            text-align: left;
            padding-left: 6px;
        }
    </style>
</head>

<body>
    <div class="cabecera">
        <div class="logo">
            <div class="logo-img">
                @if ($empresa->ruta_logo)
                    <img src="{{ base_path() . '/storage/app/' . $empresa->ruta_logo }}" class="img-fluid">
                @else
                    <img src="{{ public_path() . '/img/default.png' }}" class="img-fluid">
                @endif
            </div>
        </div>
        <div class="empresa">
            <div class="empresa-info">
                <p class="m-0 p-0 text-uppercase nombre-empresa">
                    {{ DB::table('empresas')->count() == 0 ? 'SISCOM ' : DB::table('empresas')->first()->razon_social }}
                </p>
                <p class="m-0 p-0 text-uppercase direccion-empresa">
                    {{ DB::table('empresas')->count() == 0 ? '- ' : DB::table('empresas')->first()->direccion_fiscal }}
                </p>

                <p class="m-0 p-0 text-info-empresa">Central telefónica:
                    {{ DB::table('empresas')->count() == 0 ? '-' : DB::table('empresas')->first()->telefono }}</p>
                <p class="m-0 p-0 text-info-empresa">Email:
                    {{ DB::table('empresas')->count() == 0 ? '-' : DB::table('empresas')->first()->correo }}</p>
            </div>
        </div>

    </div><br>
    <div class="informacion">
        <table class="tbl-informacion">
            <tbody style="padding-top: 5px; padding-bottom: 5px;">
                <tr>
                    <td style="padding-left: 5px;">CAJA</td>
                    <td>:</td>
                    <td>{{ $movimiento->caja->nombre }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 5px;">Colaborador</td>
                    <td>:</td>
                    <td>{{ $colaborador->nombre }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 5px;">Turno</td>
                    <td>:</td>
                    <td>{{ 'MAÑANA' }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 5px;">Monto Inicial</td>
                    <td>:</td>
                    <td>{{ $movimiento->monto_inicial }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 5px;">Fecha</td>
                    <td>:</td>
                    <td>{{ date_format($movimiento->created_at, 'Y/m/d') }}</td>
                </tr>
            </tbody>
        </table>
    </div><br>

    {{-- ============================================================ --}}
    {{-- VENTAS --}}
    {{-- ============================================================ --}}
    <span style="text-transform: uppercase;font-size:15px">VENTAS</span>
    <br>
    <div class="cuerpo">
        <table class="tbl-detalles text-uppercase" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th style="text-align: center; border-right: 2px solid #52BE80">NUMERO</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">CLIENTE</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">DEV</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">COBRAR</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">MONTO</th>
                    <th style="text-align: center;">PAGOS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movimiento->detalleMovimientoVentas as $ventas)
                    @if (
                        $ventas->documento->condicion_id == 1 &&
                            $ventas->documento->estado_pago == 'PAGADA' &&
                            ifNoConvertido($ventas->documento->id))
                        @php
                            $pagosVenta = [];
                            if (!empty($ventas->documento->pago_1_tipo_pago_nombre) && floatval($ventas->documento->pago_1_monto) > 0)
                                $pagosVenta[] = $ventas->documento->pago_1_tipo_pago_nombre . ': ' . number_format(floatval($ventas->documento->pago_1_monto), 2);
                            if (!empty($ventas->documento->pago_2_tipo_pago_nombre) && floatval($ventas->documento->pago_2_monto) > 0)
                                $pagosVenta[] = $ventas->documento->pago_2_tipo_pago_nombre . ': ' . number_format(floatval($ventas->documento->pago_2_monto), 2);
                        @endphp
                        <tr>
                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                {{ $ventas->documento->serie . '-' . $ventas->documento->correlativo }}</td>
                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                {{ $ventas->documento->clienteEntidad->nombre }}</td>
                            <td style="text-align: center; border-right: 2px solid #52BE80;">
                                @if (count($ventas->documento->notas) > 0)
                                    <div class="cont-check">
                                        <span class="checkmark">
                                            <div class="checkmark_stem"></div>
                                            <div class="checkmark_kick"></div>
                                        </span>
                                    </div>
                                @elseif($ventas->documento->estado === 'ANULADO')
                                    ANULADO
                                @endif
                            </td>
                            <td style="text-align: center; border-right: 2px solid #52BE80;">
                                {{ $ventas->cobrar }}
                            </td>
                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                {{ $ventas->documento->total_pagar }}
                            </td>
                            <td class="pagos-cell">
                                @forelse ($pagosVenta as $linea)
                                    {{ $linea }}<br>
                                @empty
                                    -
                                @endforelse
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="5"
                        style="text-align: center; border-right: 2px solid #52BE80; border-top: 2px solid #52BE80">TOTAL
                    </td>
                    <td style="border-top: 2px solid #52BE80; padding: 4px 6px; font-size: 11px; line-height: 1.6;">
                        @foreach (tipos_pago() as $tipo)
                            @php $tot = cuadreMovimientoCajaIngresosVentaResum($movimiento, $tipo->id); @endphp
                            @if ($tot > 0)
                                {{ $tipo->descripcion }}: {{ number_format($tot, 2) }}<br>
                            @endif
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
    </div><br>

    {{-- ============================================================ --}}
    {{-- COBRANZA CLIENTES --}}
    {{-- ============================================================ --}}
    <span style="text-transform: uppercase;font-size:15px">COBRANZA CLIENTES</span>
    <br>
    <div class="cuerpo">
        <table class="tbl-detalles text-uppercase" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th style="text-align: center; border-right: 2px solid #52BE80">NUMERO</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">CLIENTE</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">MONTO</th>
                    <th style="text-align: center;">PAGOS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movimiento->detalleCuentaCliente as $cuentaCliente)
                    @php
                        $pagoCobranzaLinea = '';
                        foreach (tipos_pago() as $tp) {
                            if ($tp->id == $cuentaCliente->tipo_pago_id) {
                                $m = ($tp->id == 1) ? $cuentaCliente->efectivo : $cuentaCliente->importe;
                                if (floatval($m) > 0)
                                    $pagoCobranzaLinea = $tp->descripcion . ': ' . number_format(floatval($m), 2);
                                break;
                            }
                        }
                    @endphp
                    <tr>
                        <td style="text-align: center; border-right: 2px solid #52BE80">
                            {{ $cuentaCliente->cuenta_cliente->documento->serie . '-' . $cuentaCliente->cuenta_cliente->documento->correlativo }}
                        </td>
                        <td style="text-align: center; border-right: 2px solid #52BE80">
                            {{ $cuentaCliente->cuenta_cliente->documento->clienteEntidad->nombre }}</td>
                        <td style="text-align: center; border-right: 2px solid #52BE80">
                            {{ $cuentaCliente->monto }}
                        </td>
                        <td class="pagos-cell">{{ $pagoCobranzaLinea ?: '-' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3"
                        style="text-align: center; border-right: 2px solid #52BE80; border-top: 2px solid #52BE80">TOTAL
                    </td>
                    <td style="border-top: 2px solid #52BE80; padding: 4px 6px; font-size: 11px; line-height: 1.6;">
                        @foreach (tipos_pago() as $tipo)
                            @php $tot = cuadreMovimientoCajaIngresosCobranzaResum($movimiento, $tipo->id); @endphp
                            @if ($tot > 0)
                                {{ $tipo->descripcion }}: {{ number_format($tot, 2) }}<br>
                            @endif
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>

    {{-- ============================================================ --}}
    {{-- EGRESOS POR CAJA --}}
    {{-- ============================================================ --}}
    <span style="text-transform: uppercase;font-size:15px">EGRESOS POR CAJA</span>
    <br>
    <div class="cuerpo">
        <table class="tbl-detalles text-uppercase" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th style="text-align: center; border-right: 2px solid #52BE80;">ID RECIBO</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">DESCRIPCION</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">MONTO</th>
                    <th style="text-align: center;">PAGOS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movimiento->detalleMoviemientoEgresos as $detalleEgreso)
                    @if ($detalleEgreso->egreso->estado == 'ACTIVO')
                        @php
                            $pagoEgresoLinea = '';
                            foreach (tipos_pago() as $tp) {
                                if ($tp->id == $detalleEgreso->egreso->tipo_pago_id) {
                                    $m = ($tp->id == 1) ? $detalleEgreso->egreso->efectivo : $detalleEgreso->egreso->importe;
                                    if (floatval($m) > 0)
                                        $pagoEgresoLinea = $tp->descripcion . ': ' . number_format(floatval($m), 2);
                                    break;
                                }
                            }
                        @endphp
                        <tr>
                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                {{ $detalleEgreso->egreso->documento }}</td>
                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                {{ $detalleEgreso->egreso->descripcion }}</td>
                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                {{ $detalleEgreso->egreso->monto }}</td>
                            <td class="pagos-cell">{{ $pagoEgresoLinea ?: '-' }}</td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="3"
                        style="text-align: center; border-right: 2px solid #52BE80; border-top: 2px solid #52BE80">TOTAL
                    </td>
                    <td style="border-top: 2px solid #52BE80; padding: 4px 6px; font-size: 11px; line-height: 1.6;">
                        @foreach (tipos_pago() as $tipo)
                            @php $tot = cuadreMovimientoCajaEgresosEgresoResum($movimiento, $tipo->id); @endphp
                            @if ($tot > 0)
                                {{ $tipo->descripcion }}: {{ number_format($tot, 2) }}<br>
                            @endif
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>

    {{-- ============================================================ --}}
    {{-- RECIBOS DE CAJA --}}
    {{-- ============================================================ --}}
    <span style="text-transform: uppercase;font-size:15px">RECIBOS DE CAJA</span>
    <div class="cuerpo">
        <table class="tbl-detalles text-uppercase" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th style="text-align: center; border-right: 2px solid #52BE80;">ID RECIBO</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">DESCRIPCION</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">MONTO</th>
                    <th style="text-align: center;">PAGOS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recibos as $recibo)
                    @if ($recibo->estado == 'ACTIVO')
                        <tr>
                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                {{ 'RC-' . $recibo->id }}</td>
                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                {{ $recibo->cliente_nombre . '-' . $recibo->estado_servicio }}</td>
                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                {{ $recibo->monto }}</td>
                            <td class="pagos-cell">
                                @if (!empty($recibo->metodo_pago) && floatval($recibo->monto) > 0)
                                    {{ strtoupper($recibo->metodo_pago) }}: {{ number_format(floatval($recibo->monto), 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="3"
                        style="text-align: center; border-right: 2px solid #52BE80; border-top: 2px solid #52BE80">TOTAL
                    </td>
                    <td style="border-top: 2px solid #52BE80; padding: 4px 6px; font-size: 11px; line-height: 1.6;">
                        @foreach (tipos_pago() as $tipo)
                            @php $tot = calcularTotalesRecibosCaja($movimiento, $tipo->descripcion); @endphp
                            @if ($tot > 0)
                                {{ $tipo->descripcion }}: {{ number_format($tot, 2) }}<br>
                            @endif
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>

    {{-- ============================================================ --}}
    {{-- PAGOS PROVEEDORES --}}
    {{-- ============================================================ --}}
    <span style="text-transform: uppercase;font-size:15px">PAGOS PROVEEDORES</span>
    <br>
    <div class="cuerpo">
        <table class="tbl-detalles text-uppercase" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th style="text-align: center; border-right: 2px solid #52BE80;">TIPO DOC</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">NUMERO</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">PROVEEDOR</th>
                    <th style="text-align: center; border-right: 2px solid #52BE80">MONTO</th>
                    <th style="text-align: center;">PAGOS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movimiento->detalleCuentaProveedor as $detalleProveedor)
                    @php
                        $pagoProvLinea = '';
                        foreach (tipos_pago() as $tp) {
                            if ($tp->id == $detalleProveedor->tipo_pago_id) {
                                $m = ($tp->id == 1) ? $detalleProveedor->efectivo : $detalleProveedor->importe;
                                if (floatval($m) > 0)
                                    $pagoProvLinea = $tp->descripcion . ': ' . number_format(floatval($m), 2);
                                break;
                            }
                        }
                    @endphp
                    <tr>
                        <td style="text-align: center; border-right: 2px solid #52BE80">
                            {{ $detalleProveedor->cuenta_proveedor->documento->tipo_compra }}</td>
                        <td style="text-align: center; border-right: 2px solid #52BE80">
                            {{ $detalleProveedor->cuenta_proveedor->documento->serie_tipo . ' - ' . $detalleProveedor->cuenta_proveedor->documento->numero_tipo }}
                        </td>
                        <td style="text-align: center; border-right: 2px solid #52BE80">
                            {{ $detalleProveedor->cuenta_proveedor->documento->proveedor->descripcion }}</td>
                        <td style="text-align: center; border-right: 2px solid #52BE80">
                            {{ $detalleProveedor->efectivo + $detalleProveedor->importe }}
                        </td>
                        <td class="pagos-cell">{{ $pagoProvLinea ?: '-' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4"
                        style="text-align: center; border-top: 2px solid #52BE80; border-right: 2px solid #52BE80">
                        TOTAL</td>
                    <td style="border-top: 2px solid #52BE80; padding: 4px 6px; font-size: 11px; line-height: 1.6;">
                        @foreach (tipos_pago() as $tipo)
                            @php $tot = cuadreMovimientoCajaEgresosPagoResum($movimiento, $tipo->id); @endphp
                            @if ($tot > 0)
                                {{ $tipo->descripcion }}: {{ number_format($tot, 2) }}<br>
                            @endif
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
    </div><br>

    <br>
    <div class="info-total-qr">
        <table class="tbl-qr" cellpadding="2" cellspacing="0">
            <tr>
                <td style="width: 100%;">
                    <table class="tbl-total text-uppercase" cellpadding="2" cellspacing="0">
                        <thead style="background-color: #52BE80; color: white;">
                            <tr>
                                <th style="text-align:center; padding: 5px;" colspan="2">DETALLES EFECTIVO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align:left; padding: 5px;">
                                    <p class="m-0 p-0">VENTAS</p>
                                </td>
                                <td style="text-align:right; padding: 5px;">
                                    <p class="p-0 m-0">
                                        {{ number_format(cuadreMovimientoCajaIngresosVentaResum($movimiento, 1), 2) }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:left; padding: 5px;">
                                    <p class="m-0 p-0">DEVOLUCIONES</p>
                                </td>
                                <td style="text-align:right; padding: 5px;">
                                    <p class="p-0 m-0">
                                        {{ number_format(cuadreMovimientoDevolucionesResum($movimiento, 1), 2) }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr>
                                </td>
                            </tr>
                            <tr style="background-color: #A2D9CE;">
                                <td style="text-align:left; padding: 5px;">
                                    <p class="m-0 p-0">VENTAS EFECTIVA</p>
                                </td>
                                <td style="text-align:right; padding: 5px;">
                                    <p class="p-0 m-0">
                                        {{ number_format(cuadreMovimientoCajaIngresosVentaResum($movimiento, 1) - cuadreMovimientoDevolucionesResum($movimiento, 1), 2) }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:left; padding: 5px;">
                                    <p class="m-0 p-0">COBRANZA</p>
                                </td>
                                <td style="text-align:right; padding: 5px;">
                                    <p class="p-0 m-0">
                                        {{ number_format(cuadreMovimientoCajaIngresosCobranzaResum($movimiento, 1), 2) }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:left; padding: 5px;">
                                    <p class="m-0 p-0">PAGOS</p>
                                </td>
                                <td style="text-align:right; padding: 5px;">
                                    <p class="p-0 m-0">
                                        {{ number_format(cuadreMovimientoCajaEgresosPagoResum($movimiento, 1), 2) }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:left; padding: 5px;">
                                    <p class="m-0 p-0">EGRESOS</p>
                                </td>
                                <td style="text-align:right; padding: 5px;">
                                    <p class="p-0 m-0">
                                        {{ number_format(cuadreMovimientoCajaEgresosEgresoResum($movimiento, 1) - cuadreMovimientoDevolucionesResum($movimiento, 1), 2) }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr>
                                </td>
                            </tr>
                            <tr style="background-color: #FCF3CF;">
                                <td style="text-align:left; padding: 5px;">
                                    <p class="m-0 p-0">EFECTIVO</p>
                                </td>
                                <td style="text-align:right; padding: 5px;">
                                    <p class="p-0 m-0">
                                        {{ number_format(cuadreMovimientoCajaIngresosVentaResum($movimiento, 1) + cuadreMovimientoCajaIngresosCobranzaResum($movimiento, 1) - cuadreMovimientoCajaEgresosEgresoResum($movimiento, 1) - cuadreMovimientoCajaEgresosPagoResum($movimiento, 1), 2) }}
                                    </p>
                                </td>
                            </tr>
                            <tr style="background-color: #FCF3CF;">
                                <td style="text-align:left; padding: 5px;">
                                    <p class="m-0 p-0">SALDO ANTERIOR</p>
                                </td>
                                <td style="text-align:right; padding: 5px;">
                                    <p class="p-0 m-0">{{ number_format($movimiento->monto_inicial, 2) }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr>
                                </td>
                            </tr>
                            <tr style="background-color: #FCF3CF;">
                                <td style="text-align:left; padding: 5px;">
                                    <p class="m-0 p-0">SALDO CAJA DEL DIA</p>
                                </td>
                                <td style="text-align:right; padding: 5px;">
                                    <p class="p-0 m-0">
                                        {{ number_format($movimiento->monto_inicial + cuadreMovimientoCajaIngresosVentaResum($movimiento, 1) + cuadreMovimientoCajaIngresosCobranzaResum($movimiento, 1) - cuadreMovimientoCajaEgresosEgresoResum($movimiento, 1) - cuadreMovimientoCajaEgresosPagoResum($movimiento, 1), 2) }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <table class="tbl-qr" cellpadding="2" cellspacing="0">
            <tr>
                <td style="width: 100%;">
                    <table class="tbl-total text-uppercase" cellpadding="2" cellspacing="0">
                        <thead style="background-color: #52BE80; color: white;">
                            <tr>
                                <th style="text-align:center; padding: 5px;" colspan="2">VENTAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (tipos_pago() as $tipo)
                                @if ($tipo->id > 1)
                                    <tr>
                                        <td style="text-align:left; padding: 5px;">
                                            <p class="m-0 p-0">{{ $tipo->descripcion }}</p>
                                        </td>
                                        <td style="text-align:right; padding: 5px;">
                                            <p class="p-0 m-0">
                                                {{ number_format(cuadreMovimientoCajaIngresosVentaResum($movimiento, $tipo->id), 2) }}
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:left; padding: 5px;">
                                            <p class="m-0 p-0">{{ $tipo->descripcion }} DEVOLUCIONES</p>
                                        </td>
                                        <td style="text-align:right; padding: 5px;">
                                            <p class="p-0 m-0">
                                                {{ number_format(cuadreMovimientoDevolucionesResum($movimiento, $tipo->id), 2) }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                <td colspan="2">
                                    <hr>
                                </td>
                            </tr>
                            <tr style="background-color: #A2D9CE;">
                                <td style="text-align:left; padding: 5px;">
                                    <p class="p-0 m-0">TOTAL VENTA ELECTRONICO</p>
                                </td>
                                <td style="text-align:right; padding: 5px;">
                                    <p class="p-0 m-0">
                                        {{ number_format(cuadreMovimientoCajaIngresosVentaElectronico($movimiento), 2) }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr>
                                </td>
                            </tr>
                            <tr style="background-color: #A2D9CE;">
                                <td style="text-align:left; padding: 5px;">
                                    <p class="p-0 m-0">TOTAL VENTA DEL DIA</p>
                                </td>
                                <td style="text-align:right; padding: 5px;">
                                    <p class="p-0 m-0">
                                        {{ number_format(cuadreMovimientoCajaIngresosVenta($movimiento) + cuadreMovimientoCajaIngresosRecibo($movimiento) - cuadreMovimientoDevoluciones($movimiento), 2) }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <span style="text-transform: uppercase;font-size:15px">Trabajadores de ventas presentes</span>
                    <div class="cuerpo">
                        <table class="tbl-detalles text-uppercase" cellpadding="8" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="text-align: center; border-right: 2px solid #52BE80;">Codigo</th>
                                    <th style="text-align: center; border-right: 2px solid #52BE80">Nombres</th>
                                    <th style="text-align: center; border-right: 2px solid #52BE80">Fecha Entrada</th>
                                    <th style="text-align: center; border-right: 2px solid #52BE80">Fecha Salida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($usuarios) == 0)
                                    <tr>
                                        <td colspan="4" style="text-align: center; border-right: 2px solid #52BE80">
                                            Sin Usuarios Ventas
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($usuarios as $u)
                                        <tr>
                                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                                {{ $u->id }}</td>
                                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                                {{ $u->usuario }}</td>
                                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                                {{ $u->fecha_entrada }}</td>
                                            <td style="text-align: center; border-right: 2px solid #52BE80">
                                                {{ $u->fecha_salida }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div><br>
                </td>
            </tr>
        </table>
        <br>
    </div>
</body>

</html>
