<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte Caja Movimiento</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #1a1a1a; font-size: 11px; }

        /* ── CABECERA ── */
        .cabecera { width: 100%; position: relative; height: 96px; }
        .logo     { width: 28%; position: absolute; left: 0; }
        .logo img { width: 92%; height: 88px; }
        .empresa  { width: 60%; position: absolute; left: 30%; }
        .empresa p { margin: 2px 0; }
        .nombre-empresa { font-size: 14px; font-weight: bold; }

        /* ── INFO MOVIMIENTO ── */
        .tbl-info { width: 100%; border-collapse: collapse; font-size: 11px;
                    border: 2px solid #52BE80; margin-top: 4px; }
        .tbl-info td { padding: 3px 6px; }
        .tbl-info .lbl { font-weight: bold; width: 12%; }

        /* ── SECCIÓN TÍTULO ── */
        .seccion-titulo { text-transform: uppercase; font-size: 12px; font-weight: bold;
                          margin-top: 10px; margin-bottom: 1px; color: #1a5c35;
                          border-left: 3px solid #52BE80; padding-left: 5px; }

        /* ── TABLA DETALLE ── */
        .tbl-data { width: 100%; border-collapse: collapse; font-size: 10px; }
        .tbl-data thead tr { background-color: #D5F5E3; }
        .tbl-data th { padding: 4px 5px; text-align: center;
                       border: 1px solid #52BE80; text-transform: uppercase; }
        .tbl-data td { padding: 3px 5px; border: 1px solid #A9DFBF; vertical-align: top; }
        .tbl-data .fila-total td { border-top: 2px solid #52BE80;
                                   background-color: #EAFAF1; font-weight: bold; }
        .tbl-data .fila-anulado td { color: #999; }

        .pagos-cell { text-align: left; line-height: 1.5; }
        .tc { text-align: center; }
        .tr { text-align: right; }

        /* Nota de crédito badge */
        .nota-badge { font-size: 9px; color: #c0392b; font-weight: bold; }
        .nota-detalle { font-size: 9px; color: #922b21; }
        .conv-badge  { font-size: 9px; color: #1a5276; }
        .check-verde { color: #229954; font-weight: bold; }

        /* ── RESÚMENES ── */
        .tbl-resumen { width: 100%; border-collapse: collapse; font-size: 11px;
                       border: 2px solid #229954; }
        .tbl-resumen thead tr { background-color: #52BE80; color: white; }
        .tbl-resumen th { padding: 5px 7px; text-align: center; }
        .tbl-resumen td { padding: 4px 7px; border-bottom: 1px solid #A9DFBF; }
        .tbl-resumen .fila-subtotal td { background-color: #A2D9CE; font-weight: bold; }
        .tbl-resumen .fila-total    td { background-color: #FCF3CF; font-weight: bold; }
        .tbl-resumen .fila-negativo td { color: #922b21; }
        .tbl-resumen .tr { text-align: right; }

        /* ── TOTAL VENTA DÍA (bloque final) ── */
        .bloque-total-dia { width: 100%; border-collapse: collapse; margin-top: 10px;
                            border: 2px solid #229954; }
        .bloque-total-dia td { padding: 6px 10px; background-color: #A2D9CE;
                               font-size: 13px; font-weight: bold; text-transform: uppercase; }

        .m-0 { margin: 0; }
        .text-uppercase { text-transform: uppercase; }
    </style>
</head>
<body>

{{-- ══════════════════════════════════════════════════════════ CABECERA ══ --}}
<div class="cabecera">
    <div class="logo">
        @if ($empresa->ruta_logo)
            <img src="{{ base_path() . '/storage/app/' . $empresa->ruta_logo }}" alt="logo">
        @else
            <img src="{{ public_path() . '/img/default.png' }}" alt="logo">
        @endif
    </div>
    <div class="empresa">
        <p class="nombre-empresa text-uppercase">{{ $empresa->razon_social }}</p>
        <p class="text-uppercase">{{ $empresa->direccion_fiscal }}</p>
        <p>Tel: {{ $empresa->telefono }} &nbsp;|&nbsp; {{ $empresa->correo }}</p>
    </div>
</div>
<br>

{{-- ══════════════════════════════════════════════════ INFO MOVIMIENTO ══ --}}
<table class="tbl-info">
    <tbody>
        <tr>
            <td class="lbl">CAJA</td><td>:</td>
            <td>{{ $cabecera['cajaNombre'] }}</td>
            <td class="lbl">FECHA</td><td>:</td>
            <td>{{ $cabecera['fecha'] }}</td>
        </tr>
        <tr>
            <td class="lbl">COLABORADOR</td><td>:</td>
            <td>{{ $cabecera['colaborador'] }}</td>
            <td class="lbl">MONTO INICIAL</td><td>:</td>
            <td>S/ {{ number_format($cabecera['montoInicial'], 2) }}</td>
        </tr>
        <tr>
            <td class="lbl">EMISIÓN</td><td>:</td>
            <td colspan="3">{{ $cabecera['fechaReporte'] }}</td>
        </tr>
    </tbody>
</table>

{{-- ══════════════════════════════════════════════════ VENTAS CONTADO ══ --}}
<p class="seccion-titulo">Ventas Contado</p>
<table class="tbl-data">
    <thead>
        <tr>
            <th style="width:11%">Número</th>
            <th style="width:25%">Cliente</th>
            <th style="width:7%">Cobrar</th>
            <th style="width:9%">Total</th>
            <th style="width:20%">Nota / Estado</th>
            <th style="width:14%">Conv. a</th>
            <th>Pagos</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($ventasContado['filas'] as $fila)
            <tr @if($fila['anulado']) class="fila-anulado" @endif>
                <td class="tc">{{ $fila['numero'] }}</td>
                <td>{{ $fila['cliente'] }}</td>
                <td class="tc">{{ $fila['cobrar'] }}</td>
                <td class="tr">
                    {{ number_format($fila['total'], 2) }}
                    @if ($fila['anulado'])
                        <br><span class="nota-badge">ANULADO</span>
                    @endif
                </td>

                {{-- Nota de crédito info --}}
                <td>
                    @if ($fila['anulado'])
                        &mdash;
                    @elseif (!empty($fila['notaInfo']['notas']))
                        <span class="nota-badge">
                            NC {{ $fila['notaInfo']['tipo'] }}
                            ({{ number_format($fila['notaInfo']['total'], 2) }})
                        </span>
                        @foreach ($fila['notaInfo']['notas'] as $n)
                            <br><span class="nota-detalle">{{ $n['motivo'] }}: {{ number_format($n['monto'], 2) }}</span>
                        @endforeach
                        @if ($fila['notaInfo']['tipo'] === 'PARCIAL')
                            <br><span class="nota-detalle">Neto: {{ number_format($fila['efectivaNeto'], 2) }}</span>
                        @endif
                    @else
                        &mdash;
                    @endif
                </td>

                {{-- Conversión --}}
                <td class="tc">
                    @if ($fila['convertidoA'])
                        <span class="conv-badge">→ {{ $fila['convertidoA'] }}</span>
                    @else
                        &mdash;
                    @endif
                </td>

                <td class="pagos-cell">
                    @foreach ($fila['pagosLineas'] as $linea)
                        {{ $linea }}<br>
                    @endforeach
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="tc">Sin ventas contado</td></tr>
        @endforelse

        <tr class="fila-total">
            <td colspan="3" class="tc">TOTAL</td>
            <td class="tr">{{ number_format($ventasContado['totalGeneral'], 2) }}</td>
            <td class="nota-detalle" style="font-size:10px;">
                @if ($ventasContado['totalNotas'] > 0)
                    NC desc: {{ number_format($ventasContado['totalNotas'], 2) }}
                @endif
            </td>
            <td></td>
            <td class="pagos-cell">
                @foreach ($ventasContado['totalesPorTipo'] as $t)
                    {{ $t['nombre'] }}: {{ number_format($t['monto'], 2) }}<br>
                @endforeach
            </td>
        </tr>
    </tbody>
</table>

{{-- ══════════════════════════════════════════════════════ COBRANZAS ══ --}}
<p class="seccion-titulo">Cobranza Clientes</p>
<table class="tbl-data">
    <thead>
        <tr>
            <th style="width:15%">Número</th>
            <th style="width:45%">Cliente</th>
            <th style="width:15%">Monto</th>
            <th>Pagos</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($cobranzas['filas'] as $fila)
            <tr>
                <td class="tc">{{ $fila['numero'] }}</td>
                <td>{{ $fila['cliente'] }}</td>
                <td class="tr">{{ number_format($fila['monto'], 2) }}</td>
                <td class="pagos-cell">
                    @foreach ($fila['pagosLineas'] as $linea){{ $linea }}<br>@endforeach
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="tc">Sin cobranzas</td></tr>
        @endforelse
        <tr class="fila-total">
            <td colspan="2" class="tc">TOTAL</td>
            <td class="tr">{{ number_format($cobranzas['totalGeneral'], 2) }}</td>
            <td class="pagos-cell">
                @foreach ($cobranzas['totalesPorTipo'] as $t){{ $t['nombre'] }}: {{ number_format($t['monto'], 2) }}<br>@endforeach
            </td>
        </tr>
    </tbody>
</table>

{{-- ══════════════════════════════════════════════════ EGRESOS POR CAJA ══ --}}
<p class="seccion-titulo">Egresos por Caja</p>
<table class="tbl-data">
    <thead>
        <tr>
            <th style="width:14%">ID Egreso</th>
            <th style="width:46%">Descripción</th>
            <th style="width:15%">Monto</th>
            <th>Pagos</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($egresos['filas'] as $fila)
            <tr>
                <td class="tc">{{ $fila['idEgreso'] }}</td>
                <td>{{ $fila['descripcion'] }}</td>
                <td class="tr">{{ number_format($fila['monto'], 2) }}</td>
                <td class="pagos-cell">
                    @foreach ($fila['pagosLineas'] as $linea){{ $linea }}<br>@endforeach
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="tc">Sin egresos</td></tr>
        @endforelse
        <tr class="fila-total">
            <td colspan="2" class="tc">TOTAL</td>
            <td class="tr">{{ number_format($egresos['totalGeneral'], 2) }}</td>
            <td class="pagos-cell">
                @foreach ($egresos['totalesPorTipo'] as $t){{ $t['nombre'] }}: {{ number_format($t['monto'], 2) }}<br>@endforeach
            </td>
        </tr>
    </tbody>
</table>

{{-- ══════════════════════════════════════════════════ PAGOS PROVEEDORES ══ --}}
<p class="seccion-titulo">Pagos Proveedores</p>
<table class="tbl-data">
    <thead>
        <tr>
            <th style="width:10%">Tipo Doc</th>
            <th style="width:18%">Número</th>
            <th style="width:32%">Proveedor</th>
            <th style="width:12%">Monto</th>
            <th>Pagos</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pagosProveedor['filas'] as $fila)
            <tr>
                <td class="tc">{{ $fila['tipoDoc'] }}</td>
                <td class="tc">{{ $fila['numero'] }}</td>
                <td>{{ $fila['proveedor'] }}</td>
                <td class="tr">{{ number_format($fila['monto'], 2) }}</td>
                <td class="pagos-cell">
                    @foreach ($fila['pagosLineas'] as $linea){{ $linea }}<br>@endforeach
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="tc">Sin pagos a proveedores</td></tr>
        @endforelse
        <tr class="fila-total">
            <td colspan="3" class="tc">TOTAL</td>
            <td class="tr">{{ number_format($pagosProveedor['totalGeneral'], 2) }}</td>
            <td class="pagos-cell">
                @foreach ($pagosProveedor['totalesPorTipo'] as $t){{ $t['nombre'] }}: {{ number_format($t['monto'], 2) }}<br>@endforeach
            </td>
        </tr>
    </tbody>
</table>

{{-- ═══════════════════════════════════════════════════════ CONVERSIONES ══ --}}
@if (!empty($conversiones))
<p class="seccion-titulo">Conversiones de Documentos</p>
<table class="tbl-data">
    <thead>
        <tr>
            <th style="width:25%">Documento Origen</th>
            <th style="width:25%">Convertido a</th>
            <th style="width:35%">Cliente</th>
            <th style="width:15%">Monto</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($conversiones as $conv)
            <tr>
                <td class="tc">{{ $conv['origen'] }}</td>
                <td class="tc"><span class="conv-badge">{{ $conv['destino'] }}</span></td>
                <td>{{ $conv['cliente'] }}</td>
                <td class="tr">{{ number_format($conv['monto'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ══════════════════════════════════════ RESÚMENES EFECTIVO + ELECTRÓNICO ══ --}}
<br>
<table style="width:100%; border-collapse: collapse;">
    <tr>
        {{-- ── Efectivo ── --}}
        <td style="width:48%; vertical-align: top; padding-right: 6px;">
            <table class="tbl-resumen">
                <thead><tr><th colspan="2">RESUMEN EFECTIVO</th></tr></thead>
                <tbody>
                    <tr style="background:#EBF5FB;">
                        <td style="color:#1a5276;">Total venta del día <small>(todos los métodos)</small></td>
                        <td class="tr" style="color:#1a5276; font-weight:bold;">{{ number_format($resumenElect['totalVentaDia'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Ventas efectivo (bruto)</td>
                        <td class="tr">{{ number_format($resumenEfectivo['ventasBruto'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>+ Cobranza efectivo</td>
                        <td class="tr">{{ number_format($resumenEfectivo['cobranza'], 2) }}</td>
                    </tr>
                    <tr class="fila-negativo">
                        <td>- Pagos proveedor efectivo</td>
                        <td class="tr">{{ number_format($resumenEfectivo['pagosProveedor'], 2) }}</td>
                    </tr>
                    <tr class="fila-negativo">
                        <td>- Egresos caja efectivo</td>
                        <td class="tr">{{ number_format($resumenEfectivo['egresos'], 2) }}</td>
                    </tr>
                    <tr class="fila-subtotal">
                        <td>Efectivo neto del día</td>
                        <td class="tr">{{ number_format($resumenEfectivo['efectivoNeto'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>+ Saldo anterior</td>
                        <td class="tr">{{ number_format($resumenEfectivo['saldoAnterior'], 2) }}</td>
                    </tr>
                    <tr class="fila-total">
                        <td>SALDO CAJA DEL DÍA</td>
                        <td class="tr">{{ number_format($resumenEfectivo['saldoCajaDelDia'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </td>

        {{-- ── Electrónico ── --}}
        <td style="width:48%; vertical-align: top; padding-left: 6px;">
            <table class="tbl-resumen">
                <thead><tr><th colspan="2">PAGOS ELECTRÓNICOS</th></tr></thead>
                <tbody>
                    @forelse ($resumenElect['porTipo'] as $tipo)
                        <tr>
                            <td>{{ $tipo['nombre'] }}</td>
                            <td class="tr">{{ number_format($tipo['ventas'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="tc">Sin pagos electrónicos</td></tr>
                    @endforelse
                    <tr class="fila-total">
                        <td>TOTAL VENTAS ELECTRÓNICAS</td>
                        <td class="tr">{{ number_format($resumenElect['totalVentaElectronica'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════ INGRESOS / EGRESOS POR MÉTODO ══ --}}
<br>
<table class="tbl-resumen">
    <thead>
        <tr>
            <th style="width:28%">MÉTODO DE PAGO</th>
            <th style="width:18%">INGRESOS<br><small style="font-size:8px; font-weight:normal;">(ventas + cobr.)</small></th>
            <th style="width:18%">EGRESOS CAJA</th>
            <th style="width:18%">PAG. PROV.</th>
            <th style="width:18%">NETO</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($resumenMetodos as $fila)
            <tr>
                <td>{{ $fila['nombre'] }}</td>
                <td class="tr">{{ number_format($fila['ingresos'], 2) }}</td>
                <td class="tr">{{ number_format($fila['egresosCaja'], 2) }}</td>
                <td class="tr">{{ number_format($fila['pagosProveedor'], 2) }}</td>
                <td class="tr">{{ number_format($fila['neto'], 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="tc">Sin movimientos</td></tr>
        @endforelse
    </tbody>
</table>

{{-- ══════════════════════════════ TOTAL VENTA + SALDO CONSOLIDADO ══ --}}
@php
    $_totIng   = array_sum(array_column($resumenMetodos, 'ingresos'));
    $_totEgr   = array_sum(array_column($resumenMetodos, 'egresosCaja'))
               + array_sum(array_column($resumenMetodos, 'pagosProveedor'));
    $_saldoCons = $resumenEfectivo['saldoAnterior'] + $_totIng - $_totEgr;
@endphp
<br>
<table class="bloque-total-dia">
    <tr>
        <td style="width:70%; background:#A2D9CE; font-size:12px;">
            TOTAL VENTA DEL DÍA
            <small style="font-size:9px; font-weight:normal;">(ventas contado, todos los métodos)</small>
        </td>
        <td style="text-align:right; width:30%; background:#A2D9CE; font-size:12px;">
            S/ {{ number_format($resumenElect['totalVentaDia'], 2) }}
        </td>
    </tr>
    <tr>
        <td style="width:70%; background:#1a5c35; color:white; font-size:13px;">
            SALDO CONSOLIDADO DEL TURNO
            <small style="font-size:9px; font-weight:normal; opacity:0.8;">
                (inicial + ventas + cobranzas - egresos caja - pag. proveedor)
            </small>
        </td>
        <td style="text-align:right; width:30%; background:#1a5c35; color:white; font-size:13px;">
            S/ {{ number_format($_saldoCons, 2) }}
        </td>
    </tr>
</table>

</body>
</html>
