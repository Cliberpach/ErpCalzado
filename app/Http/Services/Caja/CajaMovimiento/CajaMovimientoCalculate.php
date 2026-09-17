<?php

namespace App\Http\Services\Caja\CajaMovimiento;

use App\Pos\MovimientoCaja;
use Illuminate\Support\Collection;

class CajaMovimientoCalculate
{
    private Collection $tiposPago;

    public function __construct(Collection $tiposPago)
    {
        $this->tiposPago = $tiposPago;
    }

    // ─── VENTAS CONTADO ───────────────────────────────────────────────────────
    // Incluye documentos ACTIVO y ANULADO para visibilidad total.
    // Solo los ACTIVO cuentan en totales.
    // Excluye docs con convert_de_id (son resultados de conversión, no ventas originales).

    public function seccionVentasContado(MovimientoCaja $movimiento, array $docsConvertidosMap): array
    {
        $filas          = [];
        $totalesPorTipo = [];
        $totalGeneral   = 0.0;
        $totalNotas     = 0.0;

        foreach ($movimiento->detalleMovimientoVentas as $item) {
            $doc = $item->documento;

            if ($doc->condicion_id != 1) continue;
            if (!is_null($doc->convert_de_id)) continue; // es resultado de conversión, omitir

            $anulado      = $doc->estado === 'ANULADO';
            $notaInfo     = $this->extraerInfoNotas($doc);
            $efectivaNeto = $anulado ? 0.0 : max(0.0, floatval($doc->total_pagar) - $notaInfo['total']);

            // Destino de conversión (si fue convertido a otro documento)
            $convertidoA = null;
            if ($doc->convert_en_id && isset($docsConvertidosMap[$doc->convert_en_id])) {
                $dc = $docsConvertidosMap[$doc->convert_en_id];
                $convertidoA = $dc['serie'] . '-' . $dc['correlativo'] . ' (' . $dc['tipo'] . ')';
            } elseif ($doc->convert_en_serie) {
                $convertidoA = $doc->convert_en_serie . ' (ver doc)';
            }

            $filas[] = [
                'numero'       => $doc->serie . '-' . $doc->correlativo,
                'cliente'      => $doc->clienteEntidad->nombre ?? $doc->cliente ?? '-',
                'cobrar'       => $item->cobrar,
                'total'        => floatval($doc->total_pagar),
                'anulado'      => $anulado,
                'tieneNota'    => !empty($notaInfo['notas']),
                'notaInfo'     => $notaInfo,      // ['total', 'tipo', 'notas'[]]
                'efectivaNeto' => $efectivaNeto,
                'convertidoA'  => $convertidoA,
                'pagosLineas'  => $anulado ? [] : $this->buildPagosLineas(
                    $doc->pago_1_tipo_pago_nombre, $doc->pago_1_monto,
                    $doc->pago_2_tipo_pago_nombre, $doc->pago_2_monto
                ),
            ];

            if (!$anulado && $item->cobrar === 'SI') {
                if ($doc->pago_1_tipo_pago_nombre && floatval($doc->pago_1_monto) > 0)
                    $totalesPorTipo[$doc->pago_1_tipo_pago_nombre] = ($totalesPorTipo[$doc->pago_1_tipo_pago_nombre] ?? 0) + floatval($doc->pago_1_monto);
                if ($doc->pago_2_tipo_pago_nombre && floatval($doc->pago_2_monto) > 0)
                    $totalesPorTipo[$doc->pago_2_tipo_pago_nombre] = ($totalesPorTipo[$doc->pago_2_tipo_pago_nombre] ?? 0) + floatval($doc->pago_2_monto);
                $totalGeneral += floatval($doc->total_pagar);
                $totalNotas   += $notaInfo['total'];
            }
        }

        return [
            'filas'          => $filas,
            'totalesPorTipo' => $this->aplanarTotales($totalesPorTipo),
            'totalGeneral'   => $totalGeneral,   // ventas neto (ya descontando notas)
            'totalNotas'     => $totalNotas,
        ];
    }

    // ─── COBRANZAS ────────────────────────────────────────────────────────────

    public function seccionCobranzas(MovimientoCaja $movimiento): array
    {
        $filas          = [];
        $totalesPorTipo = [];
        $totalGeneral   = 0.0;

        foreach ($movimiento->detalleCuentaCliente as $item) {
            $docVenta       = $item->cuenta_cliente->documento;
            $monto          = floatval($item->monto);
            $tipoPagoNombre = $this->nombreTipoPago($item->tipo_pago_id);

            $filas[] = [
                'numero'      => $docVenta->serie . '-' . $docVenta->correlativo,
                'cliente'     => $docVenta->clienteEntidad->nombre ?? $docVenta->cliente ?? '-',
                'monto'       => $monto,
                'pagosLineas' => ["{$tipoPagoNombre}: " . number_format($monto, 2)],
            ];

            $totalesPorTipo[$tipoPagoNombre] = ($totalesPorTipo[$tipoPagoNombre] ?? 0) + $monto;
            $totalGeneral += $monto;
        }

        return [
            'filas'          => $filas,
            'totalesPorTipo' => $this->aplanarTotales($totalesPorTipo),
            'totalGeneral'   => $totalGeneral,
        ];
    }

    // ─── EGRESOS POR CAJA ─────────────────────────────────────────────────────

    public function seccionEgresos(MovimientoCaja $movimiento): array
    {
        $filas          = [];
        $totalesPorTipo = [];
        $totalGeneral   = 0.0;

        foreach ($movimiento->detalleMoviemientoEgresos as $item) {
            $egreso = $item->egreso;
            if ($egreso->estado !== 'ACTIVO') continue;

            $monto          = floatval($egreso->monto);
            $tipoPagoNombre = $this->nombreTipoPago($egreso->tipo_pago_id);

            $filas[] = [
                'idEgreso'    => $egreso->documento,
                'descripcion' => $egreso->descripcion,
                'monto'       => $monto,
                'pagosLineas' => ["{$tipoPagoNombre}: " . number_format($monto, 2)],
            ];

            $totalesPorTipo[$tipoPagoNombre] = ($totalesPorTipo[$tipoPagoNombre] ?? 0) + $monto;
            $totalGeneral += $monto;
        }

        return [
            'filas'          => $filas,
            'totalesPorTipo' => $this->aplanarTotales($totalesPorTipo),
            'totalGeneral'   => $totalGeneral,
        ];
    }

    // ─── PAGOS PROVEEDORES ────────────────────────────────────────────────────

    public function seccionPagosProveedor(MovimientoCaja $movimiento): array
    {
        $filas          = [];
        $totalesPorTipo = [];
        $totalGeneral   = 0.0;

        foreach ($movimiento->detalleCuentaProveedor as $item) {
            $docCompra      = $item->cuenta_proveedor->documento;
            $monto          = floatval($item->monto);
            $tipoPagoNombre = $this->nombreTipoPago($item->tipo_pago_id);

            $filas[] = [
                'tipoDoc'     => $docCompra->tipo_compra ?? '-',
                'numero'      => ($docCompra->serie_tipo ?? '') . ' - ' . ($docCompra->numero_tipo ?? ''),
                'proveedor'   => $docCompra->proveedor->descripcion ?? '-',
                'monto'       => $monto,
                'pagosLineas' => ["{$tipoPagoNombre}: " . number_format($monto, 2)],
            ];

            $totalesPorTipo[$tipoPagoNombre] = ($totalesPorTipo[$tipoPagoNombre] ?? 0) + $monto;
            $totalGeneral += $monto;
        }

        return [
            'filas'          => $filas,
            'totalesPorTipo' => $this->aplanarTotales($totalesPorTipo),
            'totalGeneral'   => $totalGeneral,
        ];
    }

    // ─── CONVERSIONES ─────────────────────────────────────────────────────────

    public function seccionConversiones(MovimientoCaja $movimiento, array $docsConvertidosMap): array
    {
        $filas = [];

        foreach ($movimiento->detalleMovimientoVentas as $item) {
            $doc = $item->documento;

            // Documentos que FUERON convertidos a otro (son el origen)
            if ($doc->convert_en_id) {
                $destino = null;
                if (isset($docsConvertidosMap[$doc->convert_en_id])) {
                    $dc      = $docsConvertidosMap[$doc->convert_en_id];
                    $destino = $dc['serie'] . '-' . $dc['correlativo'] . ' (' . $dc['tipo'] . ')';
                } else {
                    $destino = $doc->convert_en_serie ? $doc->convert_en_serie . ' (ver doc)' : 'N/D';
                }

                $filas[] = [
                    'origen'  => $doc->serie . '-' . $doc->correlativo,
                    'destino' => $destino,
                    'cliente' => $doc->clienteEntidad->nombre ?? $doc->cliente ?? '-',
                    'monto'   => floatval($doc->total_pagar),
                ];
            }

            // Documentos que SON resultado de conversión (tienen convert_de_id)
            if ($doc->convert_de_id) {
                $filas[] = [
                    'origen'  => ($doc->convert_de_serie ?? 'Orig.') . ' (origen)',
                    'destino' => $doc->serie . '-' . $doc->correlativo,
                    'cliente' => $doc->clienteEntidad->nombre ?? $doc->cliente ?? '-',
                    'monto'   => floatval($doc->total_pagar),
                ];
            }
        }

        // Deduplicar por par origen-destino
        $vistos = [];
        $unique = [];
        foreach ($filas as $f) {
            $key = $f['origen'] . '|' . $f['destino'];
            if (!isset($vistos[$key])) {
                $vistos[$key] = true;
                $unique[] = $f;
            }
        }

        return $unique;
    }

    // ─── RESUMEN EFECTIVO ─────────────────────────────────────────────────────

    public function resumenEfectivo(MovimientoCaja $movimiento): array
    {
        $ventasBruto = 0.0;

        foreach ($movimiento->detalleMovimientoVentas as $item) {
            $doc = $item->documento;
            if (!$this->esVentaContadoValida($doc) || $item->cobrar !== 'SI') continue;

            if (intval($doc->pago_1_tipo_pago_id) === 1) $ventasBruto += floatval($doc->pago_1_monto);
            if (intval($doc->pago_2_tipo_pago_id) === 1) $ventasBruto += floatval($doc->pago_2_monto);
        }

        $cobranzaEfectivo = 0.0;
        foreach ($movimiento->detalleCuentaCliente as $item) {
            if ($item->tipo_pago_id == 1) $cobranzaEfectivo += floatval($item->efectivo);
        }

        $pagosProvEfectivo = 0.0;
        foreach ($movimiento->detalleCuentaProveedor as $item) {
            if ($item->tipo_pago_id == 1) $pagosProvEfectivo += floatval($item->efectivo);
        }

        $egresosEfectivo = 0.0;
        foreach ($movimiento->detalleMoviemientoEgresos as $item) {
            $eg = $item->egreso;
            if ($eg->estado === 'ACTIVO' && $eg->tipo_pago_id == 1) {
                $egresosEfectivo += floatval($eg->monto);
            }
        }

        // El flujo de caja real no descuenta notas de crédito aquí —
        // las NC son documentos tributarios; la salida real de dinero se registra
        // como un Egreso de Caja (que SÍ tiene tipo de pago). Si no hay egreso,
        // el cajero tiene que rendirlo. Mostramos las NC solo como información.
        $efectivoNeto  = $ventasBruto + $cobranzaEfectivo - $pagosProvEfectivo - $egresosEfectivo;
        $saldoAnterior = floatval($movimiento->monto_inicial);

        return [
            'ventasBruto'     => $ventasBruto,
            'cobranza'        => $cobranzaEfectivo,
            'pagosProveedor'  => $pagosProvEfectivo,
            'egresos'         => $egresosEfectivo,
            'efectivoNeto'    => $efectivoNeto,
            'saldoAnterior'   => $saldoAnterior,
            'saldoCajaDelDia' => $saldoAnterior + $efectivoNeto,
        ];
    }

    // ─── RESUMEN PAGOS ELECTRÓNICOS ───────────────────────────────────────────

    public function resumenElectronico(MovimientoCaja $movimiento): array
    {
        $porTipo          = [];
        $totalElectronico = 0.0;
        $totalVentaDia    = 0.0;

        foreach ($this->tiposPago as $tipo) {
            if ($tipo->id == 1) continue;

            $ventas = 0.0;

            foreach ($movimiento->detalleMovimientoVentas as $item) {
                $doc = $item->documento;
                if (!$this->esVentaContadoValida($doc) || $item->cobrar !== 'SI') continue;

                if (intval($doc->pago_1_tipo_pago_id) === $tipo->id) $ventas += floatval($doc->pago_1_monto);
                if (intval($doc->pago_2_tipo_pago_id) === $tipo->id) $ventas += floatval($doc->pago_2_monto);
            }

            if ($ventas > 0) {
                $porTipo[] = [
                    'nombre' => $tipo->descripcion,
                    'ventas' => $ventas,
                ];
                $totalElectronico += $ventas;
            }
        }

        // Total venta del día = sum de ventas ACTIVO brutas (efectivo + electrónico)
        foreach ($movimiento->detalleMovimientoVentas as $item) {
            $doc = $item->documento;
            if ($this->esVentaContadoValida($doc) && $item->cobrar === 'SI') {
                $totalVentaDia += floatval($doc->total_pagar);
            }
        }

        return [
            'porTipo'               => $porTipo,
            'totalVentaElectronica' => $totalElectronico,
            'totalVentaDia'         => $totalVentaDia,
        ];
    }

    // ─── RESUMEN POR MÉTODO DE PAGO ───────────────────────────────────────────

    public function resumenPorMetodoPago(MovimientoCaja $movimiento): array
    {
        $ingresos = [];
        $egresos  = [];

        foreach ($movimiento->detalleMovimientoVentas as $item) {
            $doc = $item->documento;
            if (!$this->esVentaContadoValida($doc) || $item->cobrar !== 'SI') continue;

            // Ingresos brutos por tipo — las devoluciones van como egresos si el cajero
            // las registró; no las descontamos aquí para no inventar el método de pago.
            if ($doc->pago_1_tipo_pago_nombre && floatval($doc->pago_1_monto) > 0)
                $ingresos[$doc->pago_1_tipo_pago_nombre] = ($ingresos[$doc->pago_1_tipo_pago_nombre] ?? 0) + floatval($doc->pago_1_monto);
            if ($doc->pago_2_tipo_pago_nombre && floatval($doc->pago_2_monto) > 0)
                $ingresos[$doc->pago_2_tipo_pago_nombre] = ($ingresos[$doc->pago_2_tipo_pago_nombre] ?? 0) + floatval($doc->pago_2_monto);
        }

        foreach ($movimiento->detalleCuentaCliente as $item) {
            $nombre = $this->nombreTipoPago($item->tipo_pago_id);
            $ingresos[$nombre] = ($ingresos[$nombre] ?? 0) + floatval($item->monto);
        }

        $pagosProveedor = [];
        foreach ($movimiento->detalleCuentaProveedor as $item) {
            $nombre = $this->nombreTipoPago($item->tipo_pago_id);
            $pagosProveedor[$nombre] = ($pagosProveedor[$nombre] ?? 0) + floatval($item->monto);
        }

        foreach ($movimiento->detalleMoviemientoEgresos as $item) {
            $eg = $item->egreso;
            if ($eg->estado === 'ACTIVO') {
                $nombre = $this->nombreTipoPago($eg->tipo_pago_id);
                $egresos[$nombre] = ($egresos[$nombre] ?? 0) + floatval($eg->monto);
            }
        }

        $allNombres = array_unique(array_merge(
            array_keys($ingresos),
            array_keys($egresos),
            array_keys($pagosProveedor)
        ));
        $resultado = [];
        foreach ($allNombres as $nombre) {
            $ing  = $ingresos[$nombre]       ?? 0;
            $egr  = $egresos[$nombre]        ?? 0;
            $prov = $pagosProveedor[$nombre] ?? 0;
            $resultado[] = [
                'nombre'          => $nombre,
                'ingresos'        => $ing,
                'egresosCaja'     => $egr,
                'pagosProveedor'  => $prov,
                'neto'            => $ing - $egr - $prov,
            ];
        }

        return $resultado;
    }

    // ─── PRIVATE ──────────────────────────────────────────────────────────────

    private function esVentaContadoValida($doc): bool
    {
        return $doc->condicion_id == 1
            && $doc->estado_pago  === 'PAGADA'
            && $doc->estado       === 'ACTIVO'
            && is_null($doc->convert_de_id);
    }

    /**
     * Extrae información de notas de crédito (tipo_nota='0') del documento.
     * Retorna: ['total' => float, 'tipo' => 'TOTAL'|'PARCIAL'|null, 'notas' => []]
     */
    private function extraerInfoNotas($doc): array
    {
        $notas      = [];
        $totalNota  = 0.0;

        foreach ($doc->notas as $nota) {
            if ($nota->tipo_nota === '0' && $nota->estado === 'ACTIVO') {
                $monto      = floatval($nota->mtoImpVenta);
                $totalNota += $monto;
                $notas[]    = [
                    'monto'  => $monto,
                    'motivo' => $nota->desMotivo ?? '-',
                ];
            }
        }

        $totalPagar = floatval($doc->total_pagar ?: 0);
        $tipoNota   = null;
        if ($totalNota > 0) {
            $tipoNota = ($totalNota >= $totalPagar - 0.01) ? 'TOTAL' : 'PARCIAL';
        }

        return [
            'total' => $totalNota,
            'tipo'  => $tipoNota,
            'notas' => $notas,
        ];
    }

    private function buildPagosLineas(?string $n1, $m1, ?string $n2, $m2): array
    {
        $lineas = [];
        if ($n1 && floatval($m1) > 0) $lineas[] = $n1 . ': ' . number_format(floatval($m1), 2);
        if ($n2 && floatval($m2) > 0) $lineas[] = $n2 . ': ' . number_format(floatval($m2), 2);
        return $lineas;
    }

    private function aplanarTotales(array $totales): array
    {
        $resultado = [];
        foreach ($totales as $nombre => $monto) {
            if ($monto > 0.001) $resultado[] = ['nombre' => $nombre, 'monto' => round($monto, 2)];
        }
        return $resultado;
    }

    private function nombreTipoPago(int $id): string
    {
        $tipo = $this->tiposPago->firstWhere('id', $id);
        return $tipo ? $tipo->descripcion : "TIPO#{$id}";
    }

    // ─── PRODUCTOS VENDIDOS ───────────────────────────────────────────────────

    /**
     * Arma el resumen por categoría, la conciliación contra el TOTAL de VENTAS
     * CONTADO y el detalle pivotado categoría -> modelo -> color x talla.
     *
     * Recibe las filas planas de CajaMovimientoRepository::getDetalleProductosCaja()
     * y no vuelve a tocar la base: el pivote y los subtotales se hacen en PHP.
     *
     * @param  array<int, mixed>   $filas        filas planas (categoria, modelo, color, talla, pares, monto)
     * @param  array<string,mixed> $conciliacion salida de getConciliacionCaja()
     * @return array<string, mixed>
     */
    public function seccionProductos(array $filas, array $conciliacion): array
    {
        $categorias = array();   // categoria => ['pares','monto']
        $grupos     = array();   // categoria => ['tallas'=>[], 'filas'=>[], subtotales]
        $tallasTodas = array();
        $totalPorTalla = array();
        $totalPares = 0.0;
        $totalMonto = 0.0;

        foreach ($filas as $f) {
            $cat    = $f->categoria;
            $modelo = $f->modelo;
            $color  = $f->color;
            $talla  = $f->talla;
            $pares  = floatval($f->pares);
            $monto  = floatval($f->monto);

            if (!isset($categorias[$cat])) {
                $categorias[$cat] = array('categoria' => $cat, 'pares' => 0.0, 'monto' => 0.0);
            }
            $categorias[$cat]['pares'] += $pares;
            $categorias[$cat]['monto'] += $monto;

            if (!isset($grupos[$cat])) {
                $grupos[$cat] = array(
                    'categoria'      => $cat,
                    'tallas'         => array(),
                    'porTalla'       => array(),
                    'filas'          => array(),
                    'subtotalPares'  => 0.0,
                    'subtotalMonto'  => 0.0,
                );
            }
            $grupos[$cat]['tallas'][$talla] = true;
            $grupos[$cat]['subtotalPares'] += $pares;
            $grupos[$cat]['subtotalMonto'] += $monto;

            $subTalla = isset($grupos[$cat]['porTalla'][$talla]) ? $grupos[$cat]['porTalla'][$talla] : 0.0;
            $grupos[$cat]['porTalla'][$talla] = $subTalla + $pares;

            $totTalla = isset($totalPorTalla[$talla]) ? $totalPorTalla[$talla] : 0.0;
            $totalPorTalla[$talla] = $totTalla + $pares;

            $clave = $modelo . '||' . $color;
            if (!isset($grupos[$cat]['filas'][$clave])) {
                $grupos[$cat]['filas'][$clave] = array(
                    'modelo'   => $modelo,
                    'color'    => $color,
                    'porTalla' => array(),
                    'pares'    => 0.0,
                    'monto'    => 0.0,
                );
            }
            $actual = isset($grupos[$cat]['filas'][$clave]['porTalla'][$talla])
                ? $grupos[$cat]['filas'][$clave]['porTalla'][$talla] : 0.0;
            $grupos[$cat]['filas'][$clave]['porTalla'][$talla] = $actual + $pares;
            $grupos[$cat]['filas'][$clave]['pares'] += $pares;
            $grupos[$cat]['filas'][$clave]['monto'] += $monto;

            $tallasTodas[$talla] = true;
            $totalPares += $pares;
            $totalMonto += $monto;
        }

        // Resumen por categoría, ordenado por pares descendente.
        $resumen = array_values($categorias);
        usort($resumen, function ($a, $b) {
            if ($a['pares'] == $b['pares']) {
                return strcmp($a['categoria'], $b['categoria']);
            }
            return ($a['pares'] < $b['pares']) ? 1 : -1;
        });

        // Ordena las tallas de cada grupo y reindexa sus filas.
        foreach ($grupos as $cat => $g) {
            $grupos[$cat]['tallas'] = $this->ordenarTallas(array_keys($g['tallas']));
            $filasOrdenadas = array_values($g['filas']);
            usort($filasOrdenadas, function ($a, $b) {
                $c = strcmp($a['modelo'], $b['modelo']);
                return $c !== 0 ? $c : strcmp($a['color'], $b['color']);
            });
            $grupos[$cat]['filas'] = $filasOrdenadas;
        }

        // Los grupos salen en el mismo orden que el resumen (por pares desc).
        $gruposOrdenados = array();
        foreach ($resumen as $r) {
            if (isset($grupos[$r['categoria']])) {
                $gruposOrdenados[] = $grupos[$r['categoria']];
            }
        }

        // ── Conciliación ──
        $productos = $totalMonto;
        $envio     = isset($conciliacion['envio']) ? floatval($conciliacion['envio']) : 0.0;
        $embalaje  = isset($conciliacion['embalaje']) ? floatval($conciliacion['embalaje']) : 0.0;
        $sinDet    = isset($conciliacion['montoSinDetalle']) ? floatval($conciliacion['montoSinDetalle']) : 0.0;
        $docsSinDet = isset($conciliacion['docsSinDetalle']) ? intval($conciliacion['docsSinDetalle']) : 0;
        $totalCont = isset($conciliacion['totalContado']) ? floatval($conciliacion['totalContado']) : 0.0;

        $calculado  = $productos + $envio + $embalaje + $sinDet;
        $diferencia = round($totalCont - $calculado, 2);

        return array(
            'resumen'        => $resumen,
            'grupos'         => $gruposOrdenados,
            'tallasGlobales' => $this->ordenarTallas(array_keys($tallasTodas)),
            'totalPorTalla'  => $totalPorTalla,
            'totalPares'     => $totalPares,
            'totalMonto'     => $totalMonto,
            'conciliacion'   => array(
                'productos'      => $productos,
                'envio'          => $envio,
                'embalaje'       => $embalaje,
                'montoSinDetalle' => $sinDet,
                'docsSinDetalle' => $docsSinDet,
                'calculado'      => $calculado,
                'totalContado'   => $totalCont,
                'diferencia'     => $diferencia,
                // Tolerancia de ±0.05 pedida: por debajo se considera cuadrado.
                'cuadra'         => abs($diferencia) <= 0.05,
            ),
        );
    }

    /**
     * Ordena tallas: primero las numéricas de menor a mayor, después las no
     * numéricas alfabéticamente (S, M, L, XL, T12, S/T...).
     *
     * @param  array<int, string> $tallas
     * @return array<int, string>
     */
    private function ordenarTallas(array $tallas): array
    {
        $numericas    = array();
        $noNumericas  = array();

        foreach ($tallas as $t) {
            if (is_numeric($t)) {
                $numericas[] = $t;
            } else {
                $noNumericas[] = $t;
            }
        }

        usort($numericas, function ($a, $b) {
            $fa = floatval($a);
            $fb = floatval($b);
            if ($fa == $fb) { return 0; }
            return ($fa < $fb) ? -1 : 1;
        });
        sort($noNumericas, SORT_NATURAL | SORT_FLAG_CASE);

        return array_merge($numericas, $noNumericas);
    }
}
