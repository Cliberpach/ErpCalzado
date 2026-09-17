<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $tituloDocumento }}</title>
    <style>
        @page { margin: 22px 20px; }

        body { font-family: Arial, Helvetica, sans-serif; color: #1a1a1a; font-size: 11px; }

        /* ── CABECERA (misma del reporte de caja) ── */
        .cabecera { width: 100%; position: relative; height: 96px; }
        .logo     { width: 20%; position: absolute; left: 0; }
        .logo img { width: 92%; height: 88px; }
        .empresa  { width: 65%; position: absolute; left: 22%; }
        .empresa p { margin: 2px 0; }
        .nombre-empresa { font-size: 14px; font-weight: bold; }

        .tbl-info { width: 100%; border-collapse: collapse; font-size: 11px;
                    border: 2px solid #52BE80; margin-top: 4px; }
        .tbl-info td { padding: 3px 6px; }
        .tbl-info .lbl { font-weight: bold; width: 12%; }

        .seccion-titulo { text-transform: uppercase; font-size: 12px; font-weight: bold;
                          margin-top: 10px; margin-bottom: 1px; color: #1a5c35;
                          border-left: 3px solid #52BE80; padding-left: 5px; }

        /* ── TABLA DE PRODUCTOS ── */
        .tbl-prod { width: 100%; border-collapse: collapse; font-size: 9px; }
        .tbl-prod thead tr { background-color: #D5F5E3; }
        .tbl-prod th { padding: 3px 3px; text-align: center;
                       border: 1px solid #52BE80; text-transform: uppercase; }
        .tbl-prod td { padding: 2px 3px; border: 1px solid #A9DFBF; }
        .tbl-prod .fila-subtotal td { background-color: #EAFAF1; font-weight: bold; }
        .tbl-prod .fila-total td { border-top: 2px solid #52BE80;
                                   background-color: #FCF3CF; font-weight: bold; }

        /* ── RESUMEN POR CATEGORÍA ── */
        .tbl-resumen { width: 100%; border-collapse: collapse; font-size: 10px; }
        .tbl-resumen thead tr { background-color: #D5F5E3; }
        .tbl-resumen th { padding: 4px 5px; text-align: center;
                          border: 1px solid #52BE80; text-transform: uppercase; }
        .tbl-resumen td { padding: 3px 5px; border: 1px solid #A9DFBF; }
        .tbl-resumen .fila-total td { border-top: 2px solid #52BE80;
                                      background-color: #EAFAF1; font-weight: bold; }

        /* El resumen y el cuadre son cortos: no deben partirse entre hojas. */
        .no-partir { page-break-inside: avoid; }

        /* Si una tabla del detalle cruza de página, el thead se repite y
           ninguna fila queda cortada por la mitad. */
        .tbl-prod thead { display: table-header-group; }
        .tbl-prod tr    { page-break-inside: avoid; }

        .tc { text-align: center; }
        .tr { text-align: right; }
        .text-uppercase { text-transform: uppercase; }
        .sin-datos { margin-top: 12px; font-style: italic; }
        .pie-nota { margin-top: 8px; font-size: 9px; color: #555; }
    </style>
</head>
<body>

{{-- ══════════════════════════════════════════════════════════ CABECERA ══ --}}
<div class="cabecera">
    <div class="logo">
        @if ($empresa->ruta_logo)
            <img src="{{ storage_path('app/public/' . preg_replace('#^public/#', '', $empresa->ruta_logo)) }}" alt="logo">
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
            <td class="lbl">EMISIÓN</td><td>:</td>
            <td>{{ $cabecera['fechaReporte'] }}</td>
        </tr>
    </tbody>
</table>

@php
    // Formatea pares: entero cuando no hay decimales (la cantidad es decimal en BD).
    $_par = function ($v) { return number_format($v, fmod($v, 1) == 0.0 ? 0 : 2); };

    $_con = $productos['conciliacion'];

    $_tallasGlobales = $productos['tallasGlobales'];

    // Con muchas tallas la tabla única deja columnas ilegibles: por encima de este
    // límite se parte por categoría y cada una lleva sólo sus propias tallas.
    $_partirPorCategoria = count($_tallasGlobales) > 18;
@endphp

{{-- ═════════════════════════════════════════════ RESUMEN POR CATEGORÍA ══ --}}
<div class="no-partir">
    <p class="seccion-titulo">Resumen por categoría</p>
    <table class="tbl-resumen">
        <thead>
            <tr>
                <th style="width:60%">Categoría</th>
                <th style="width:15%">Pares</th>
                <th style="width:25%">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productos['resumen'] as $_cat)
                <tr>
                    <td>{{ $_cat['categoria'] }}</td>
                    <td class="tc">{{ $_par($_cat['pares']) }}</td>
                    <td class="tr">{{ number_format($_cat['monto'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="tc">Sin productos vendidos al contado en esta caja</td>
                </tr>
            @endforelse
            <tr class="fila-total">
                <td>TOTAL</td>
                <td class="tc">{{ $_par($productos['totalPares']) }}</td>
                <td class="tr">S/ {{ number_format($productos['totalMonto'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="tbl-resumen" style="margin-top:6px">
        <tbody>
            <tr>
                <td style="width:75%">Total productos (suma por categoría)</td>
                <td class="tr" style="width:25%">{{ number_format($_con['productos'], 2) }}</td>
            </tr>
            <tr>
                <td>+ Envío</td>
                <td class="tr">{{ number_format($_con['envio'], 2) }}</td>
            </tr>
            <tr>
                <td>+ Embalaje</td>
                <td class="tr">{{ number_format($_con['embalaje'], 2) }}</td>
            </tr>
            <tr>
                <td>
                    + Ventas sin detalle de productos
                    <small style="font-size:8px">({{ $_con['docsSinDetalle'] }}
                        {{ $_con['docsSinDetalle'] == 1 ? 'documento' : 'documentos' }})</small>
                </td>
                <td class="tr">{{ number_format($_con['montoSinDetalle'], 2) }}</td>
            </tr>
            <tr class="fila-total">
                <td>= Total ventas contado</td>
                <td class="tr">S/ {{ number_format($_con['calculado'], 2) }}</td>
            </tr>
            @unless ($_con['cuadra'])
                <tr>
                    <td style="color:#c0392b; font-weight:bold">
                        Diferencia no conciliada
                        <small style="font-size:8px; font-weight:normal">
                            (el total de VENTAS CONTADO es S/ {{ number_format($_con['totalContado'], 2) }})
                        </small>
                    </td>
                    <td class="tr" style="color:#c0392b; font-weight:bold">
                        {{ number_format($_con['diferencia'], 2) }}
                    </td>
                </tr>
            @endunless
        </tbody>
    </table>
</div>

{{-- ═══════════════════════════════════════════════ DETALLE DE PRODUCTOS ══ --}}
@if (empty($productos['grupos']))

    <p class="seccion-titulo">Detalle de productos</p>
    <p class="sin-datos">No hay productos vendidos al contado en esta caja.</p>

@elseif ($_partirPorCategoria)

    <p class="seccion-titulo">Detalle de productos</p>
    @foreach ($productos['grupos'] as $_g)
        <p class="seccion-titulo" style="font-size:11px">{{ $_g['categoria'] }}</p>
        <table class="tbl-prod">
            <thead>
                <tr>
                    <th style="width:22%">Modelo</th>
                    <th style="width:18%">Color</th>
                    @foreach ($_g['tallas'] as $_t)
                        <th>{{ $_t }}</th>
                    @endforeach
                    <th style="width:7%">Total pares</th>
                    <th style="width:10%">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($_g['filas'] as $_f)
                    <tr>
                        <td>{{ $_f['modelo'] }}</td>
                        <td>{{ $_f['color'] }}</td>
                        @foreach ($_g['tallas'] as $_t)
                            <td class="tc">
                                {{ isset($_f['porTalla'][$_t]) ? $_par($_f['porTalla'][$_t]) : '' }}
                            </td>
                        @endforeach
                        <td class="tc">{{ $_par($_f['pares']) }}</td>
                        <td class="tr">{{ number_format($_f['monto'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="fila-subtotal">
                    <td colspan="2">SUBTOTAL {{ $_g['categoria'] }}</td>
                    @foreach ($_g['tallas'] as $_t)
                        <td class="tc">
                            {{ isset($_g['porTalla'][$_t]) ? $_par($_g['porTalla'][$_t]) : '' }}
                        </td>
                    @endforeach
                    <td class="tc">{{ $_par($_g['subtotalPares']) }}</td>
                    <td class="tr">{{ number_format($_g['subtotalMonto'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <table class="tbl-prod" style="margin-top:10px">
        <tbody>
            <tr class="fila-total">
                <td style="width:70%">TOTAL GENERAL</td>
                <td class="tc" style="width:15%">{{ $_par($productos['totalPares']) }} pares</td>
                <td class="tr" style="width:15%">S/ {{ number_format($productos['totalMonto'], 2) }}</td>
            </tr>
        </tbody>
    </table>

@else

    <p class="seccion-titulo">Detalle de productos</p>
    <table class="tbl-prod">
        <thead>
            <tr>
                <th style="width:16%">Categoría</th>
                <th style="width:16%">Modelo</th>
                <th style="width:14%">Color</th>
                @foreach ($_tallasGlobales as $_t)
                    <th>{{ $_t }}</th>
                @endforeach
                <th style="width:7%">Total pares</th>
                <th style="width:9%">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos['grupos'] as $_g)
                @foreach ($_g['filas'] as $_i => $_f)
                    <tr>
                        <td>{{ $_i === 0 ? $_g['categoria'] : '' }}</td>
                        <td>{{ $_f['modelo'] }}</td>
                        <td>{{ $_f['color'] }}</td>
                        @foreach ($_tallasGlobales as $_t)
                            <td class="tc">
                                {{ isset($_f['porTalla'][$_t]) ? $_par($_f['porTalla'][$_t]) : '' }}
                            </td>
                        @endforeach
                        <td class="tc">{{ $_par($_f['pares']) }}</td>
                        <td class="tr">{{ number_format($_f['monto'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="fila-subtotal">
                    <td colspan="3">SUBTOTAL {{ $_g['categoria'] }}</td>
                    @foreach ($_tallasGlobales as $_t)
                        <td class="tc">
                            {{ isset($_g['porTalla'][$_t]) ? $_par($_g['porTalla'][$_t]) : '' }}
                        </td>
                    @endforeach
                    <td class="tc">{{ $_par($_g['subtotalPares']) }}</td>
                    <td class="tr">{{ number_format($_g['subtotalMonto'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="fila-total">
                <td colspan="3">TOTAL GENERAL</td>
                @foreach ($_tallasGlobales as $_t)
                    <td class="tc">
                        {{ isset($productos['totalPorTalla'][$_t]) ? $_par($productos['totalPorTalla'][$_t]) : '' }}
                    </td>
                @endforeach
                <td class="tc">{{ $_par($productos['totalPares']) }}</td>
                <td class="tr">S/ {{ number_format($productos['totalMonto'], 2) }}</td>
            </tr>
        </tbody>
    </table>

@endif

<p class="pie-nota">
    Sólo ventas al contado de esta caja, sin anuladas y sin las marcadas como no cobrables.
    El monto no incluye envío ni embalaje, que se cobran en la cabecera del documento.
</p>

</body>
</html>
