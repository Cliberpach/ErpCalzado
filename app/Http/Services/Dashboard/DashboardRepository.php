<?php

namespace App\Http\Services\Dashboard;

use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function salesYear(string $year, string $sede)
    {
        $ventas = DB::table('cotizacion_documento as cd')
            ->selectRaw('MONTH(cd.fecha_documento) as mes, ROUND(SUM(cd.total_pagar), 2) as total_mes')
            ->whereYear('fecha_documento', $year)
            ->where('cd.sede_id', $sede)
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return $ventas;
    }

    public function salesOrigin(string $year, string $month, string $sede, string $tipo)
    {
        $query = DB::table('cotizacion_documento as cd')
            ->selectRaw('
            cd.origen_venta_id,
            cd.origen_venta_nombre as name,
            ROUND(SUM(cd.total_pagar), 2) as y
        ')
            ->whereYear('cd.fecha_documento', $year)
            ->where('cd.sede_id', $sede)
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA');

        if ($tipo === 'month') {
            $query->whereMonth('cd.fecha_documento', $month);
        }

        $ventas = $query
            ->groupBy('cd.origen_venta_id', 'cd.origen_venta_nombre')
            ->orderByDesc('y')
            ->get()
            ->map(function ($item) {
                $item->y = (float) $item->y;
                return $item;
            });

        return $ventas;
    }

    public function topProducts(string $year, ?string $month, string $sede, ?int $color, ?int $talla)
    {
        $query = DB::table('cotizacion_documento_detalles as cdd')
            ->join('cotizacion_documento as cd', 'cd.id', '=', 'cdd.documento_id')
            ->selectRaw('
                cdd.nombre_producto,
                ROUND(SUM(cdd.cantidad), 2) as total_vendido
            ')
            ->whereYear('cd.fecha_documento', $year)
            ->where('cd.sede_id', $sede)
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->where('cdd.estado', 'ACTIVO')
            ->where('cdd.eliminado', '0')
            ->where('cdd.tipo', 'PRODUCTO');

        if (!empty($month)) {
            $query->whereMonth('cd.fecha_documento', $month);
        }
        if (!empty($color)) {
            $query->where('cdd.color_id', $color);
        }
        if (!empty($talla)) {
            $query->where('cdd.talla_id', $talla);
        }

        $productos = $query
            ->groupBy('cdd.nombre_producto')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        return $productos;
    }

    public function conversionRate(string $year, string $sede)
    {
        $data = DB::table('cotizaciones as c')
            ->leftJoin('cotizacion_documento as cd', function ($join) {
                $join->on('cd.cotizacion_venta', '=', 'c.id')
                    ->where('cd.estado', 'ACTIVO')
                    ->where('cd.estado_pago', 'PAGADA');
            })
            ->selectRaw('
                MONTH(c.fecha_documento) as mes,
                COUNT(DISTINCT c.id) as total_cotizaciones,
                COUNT(DISTINCT cd.id) as total_ventas,
                ROUND(
                    (COUNT(DISTINCT cd.id) / COUNT(DISTINCT c.id)) * 100, 2
                ) as conversion
            ')
            ->whereYear('c.fecha_documento', $year)
            ->where('c.sede_id', $sede)
            ->where('c.estado', '!=', 'ANULADO')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return $data;
    }

    public function dispatchs()
    {
        $dispatchs = DB::table('envios_ventas as ev')
            ->join('cotizacion_documento as cd', 'ev.documento_id', '=', 'cd.id')
            ->select(
                'ev.id',
                'ev.cliente_nombre',
                'ev.estado',
                'ev.created_at',
                'cd.serie',
                'cd.correlativo'
            )
            ->whereIn('ev.estado', ['PENDIENTE', 'EMBALADO'])
            ->orderBy('ev.created_at', 'desc')
            ->limit(3)
            ->get();

        return $dispatchs;
    }

    public function customers()
    {
        $customers = DB::table('cotizacion_documento')
            ->selectRaw('
                SUM(CASE WHEN num_cotizaciones = 1 THEN 1 ELSE 0 END) as clientes_nuevos,
                SUM(CASE WHEN num_cotizaciones > 1 THEN 1 ELSE 0 END) as clientes_recurrentes
            ')
            ->from(DB::raw('(SELECT cliente_id, COUNT(*) as num_cotizaciones
                     FROM cotizacion_documento
                     WHERE
                     estado = "ACTIVO"
                     AND estado_pago = "PAGADA"
                     GROUP BY cliente_id) as sub'))
            ->first();
        return $customers;
    }

    public function getDataCarousel(string $year, string $month, string $sede)
    {
        $notas = DB::table('nota_electronica as ne')
            ->join('nota_electronica_detalle as ned', 'ned.nota_id', '=', 'ne.id')
            ->join('productos as p2', 'p2.id', '=', 'ned.producto_id')
            ->select(
                'ne.documento_id',
                DB::raw('SUM((ned.mtoPrecioUnitario - COALESCE(p2.costo,0)) * COALESCE(ned.cantidad,0)) as total_notas')
            )
            ->groupBy('ne.documento_id');

        $utilidad = DB::table('cotizacion_documento as cd')
            ->leftJoin('cotizacion_documento_detalles as cdd', 'cdd.documento_id', '=', 'cd.id')
            ->leftJoin('productos as p', 'p.id', '=', 'cdd.producto_id')
            ->leftJoinSub($notas, 'n', 'n.documento_id', '=', 'cd.id')
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->whereYear('cd.fecha_documento', $year)
            ->whereMonth('cd.fecha_documento', $month)
            ->where('cd.sede_id', $sede)
            ->select(DB::raw('
                SUM(
                    (cdd.precio_unitario_nuevo - COALESCE(p.costo,0)) * COALESCE(cdd.cantidad,0)
                )
                - COALESCE(SUM(n.total_notas),0) as utilidad_total
            '))
            ->first();

        $notas2 = DB::table('nota_electronica as ne')
            ->select('ne.documento_id', DB::raw('SUM(COALESCE(ne.mtoImpVenta, 0)) as total_notas'))
            ->groupBy('ne.documento_id');

        $total_ventas = DB::table('cotizacion_documento as cd')
            ->leftJoinSub($notas2, 'n', 'n.documento_id', '=', 'cd.id')
            ->select(
                DB::raw('SUM(CASE WHEN cd.tipo_venta_id = 127 THEN COALESCE(cd.total_pagar,0) - COALESCE(n.total_notas,0) ELSE 0 END) as total_facturas'),
                DB::raw('SUM(CASE WHEN cd.tipo_venta_id = 128 THEN COALESCE(cd.total_pagar,0) - COALESCE(n.total_notas,0) ELSE 0 END) as total_boletas'),
                DB::raw('SUM(COALESCE(cd.total_pagar,0) - COALESCE(n.total_notas,0)) as total_ambos')
            )
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->whereYear('cd.fecha_documento', $year)
            ->whereMonth('cd.fecha_documento', $month)
            ->where('cd.sede_id', $sede)
            ->whereIn('cd.tipo_venta_id', [127, 128])
            ->first();


        $customer_actives = DB::table('cotizacion_documento as cd')
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->whereYear('cd.fecha_documento', $year)
            ->whereMonth('cd.fecha_documento', $month)
            ->where('cd.sede_id', $sede)
            ->distinct('cd.cliente_id')
            ->count('cd.cliente_id');

        //======== PROMEDIO VENTAS CLIENTE ========
        $notas3 = DB::table('nota_electronica as ne')
            ->select('ne.documento_id', DB::raw('SUM(ne.mtoImpVenta) as total_notas'))
            ->groupBy('ne.documento_id');

        $promedio_ventas_cliente = DB::table('cotizacion_documento as cd')
            ->leftJoinSub($notas3, 'n', 'n.documento_id', '=', 'cd.id')
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->whereYear('cd.fecha_documento', $year)
            ->whereMonth('cd.fecha_documento', $month)
            ->where('cd.sede_id', $sede)
            ->select(DB::raw('
                SUM(COALESCE(cd.total_pagar,0) - COALESCE(n.total_notas,0))
                / COUNT(DISTINCT cd.cliente_id) as promedio_venta_cliente
            '))
            ->first();

        //======= ENVIOS =========
        $envios = DB::table('cotizacion_documento as cd')
            ->select(
                DB::raw("SUM(CASE WHEN cd.estado_despacho = 'DESPACHADO' THEN 1 ELSE 0 END) as envios_realizados"),
                DB::raw("SUM(CASE WHEN cd.estado_despacho IN ('PENDIENTE') THEN 1 ELSE 0 END) as envios_pendientes"),
                DB::raw("SUM(CASE WHEN cd.estado_despacho = 'EMBALADO' THEN 1 ELSE 0 END) as envios_embalados"),
            )
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->whereYear('cd.fecha_documento', $year)
            ->whereMonth('cd.fecha_documento', $month)
            ->where('cd.sede_id', $sede)
            ->first();

        $ventas_facturas = DB::table('cotizacion_documento as cd')
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->where('cd.tipo_venta_id', 127)
            ->whereYear('cd.fecha_documento', $year)
            ->whereMonth('cd.fecha_documento', $month)
            ->where('cd.sede_id', $sede)
            ->count();

        $ventas_boletas = DB::table('cotizacion_documento as cd')
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->where('cd.tipo_venta_id', 128)
            ->whereYear('cd.fecha_documento', $year)
            ->whereMonth('cd.fecha_documento', $month)
            ->where('cd.sede_id', $sede)
            ->count();

        $ventas_hoy = DB::table('cotizacion_documento as cd')
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->whereDate('cd.fecha_documento', now()->toDateString())
            ->where('cd.sede_id', $sede)
            ->count();

        return (object)[
            'utilidad' => $utilidad->utilidad_total,
            'total_ventas' => $total_ventas,
            'customer_actives' => $customer_actives,
            'promedio_ventas_cliente'   =>  $promedio_ventas_cliente->promedio_venta_cliente,
            'envios'    =>  $envios,
            'ventas_facturas'   =>  $ventas_facturas,
            'ventas_boletas'    =>  $ventas_boletas,
            'ventas_hoy'        =>  $ventas_hoy
        ];
    }

    public function getParesYearMonth(string $tipo, string $year, ?string $month, string $sede)
    {
        $query = DB::table('cotizacion_documento_detalles as cdd')
            ->join('cotizacion_documento as cd', 'cd.id', '=', 'cdd.documento_id')
            ->whereYear('cd.fecha_documento', $year)
            ->where('cd.sede_id', $sede)
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->where('cdd.estado', 'ACTIVO')
            ->where('cdd.eliminado', '0')
            ->where('cdd.tipo', 'PRODUCTO');

        // 🔥 POR MES → días
        if ($tipo === 'month') {
            $query->selectRaw('
                DAY(cd.fecha_documento) as periodo,
                ROUND(SUM(cdd.cantidad), 2) as total_pares
            ')
                ->whereMonth('cd.fecha_documento', $month)
                ->groupByRaw('DAY(cd.fecha_documento)')
                ->orderByRaw('DAY(cd.fecha_documento)');
        }

        // 🔥 POR AÑO → meses
        else {
            $query->selectRaw('
                MONTH(cd.fecha_documento) as periodo,
                ROUND(SUM(cdd.cantidad), 2) as total_pares
            ')
                ->groupByRaw('MONTH(cd.fecha_documento)')
                ->orderByRaw('MONTH(cd.fecha_documento)');
        }

        $data = $query->get();

        $categories = [];
        $values = [];

        foreach ($data as $row) {
            if ($tipo === 'year') {
                // 🔥 Meses bonitos
                $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                $categories[] = $meses[$row->periodo - 1];
            } else {
                // 🔥 Días con formato 01, 02...
                $categories[] = str_pad($row->periodo, 2, '0', STR_PAD_LEFT);
            }

            $values[] = (float) $row->total_pares;
        }

        return [
            'categories' => $categories,
            'values' => $values
        ];
    }

    public function getSalesColor(string $year, ?string $month, string $sede)
    {
        $query = DB::table('cotizacion_documento_detalles as cdd')
            ->join('cotizacion_documento as cd', 'cd.id', '=', 'cdd.documento_id')
            ->whereYear('cd.fecha_documento', $year)
            ->where('cd.sede_id', $sede)
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->where('cdd.estado', 'ACTIVO')
            ->where('cdd.eliminado', '0')
            ->where('cdd.tipo', 'PRODUCTO');

        if ($month) {
            $query->whereMonth('cd.fecha_documento', $month);
        }

        $data = $query->selectRaw('
            cdd.nombre_color as color,
            ROUND(SUM(cdd.cantidad), 2) as total_pares
        ')
            ->groupBy('cdd.nombre_color')
            ->orderByDesc('total_pares')
            ->limit(10)
            ->get();

        $categories = [];
        $values = [];

        foreach ($data as $row) {
            $categories[] = $row->color ?? 'Sin Color';
            $values[] = (float) $row->total_pares;
        }

        return [
            'categories' => $categories,
            'values' => $values
        ];
    }

    public function getSalesSizes(string $year, ?string $month, string $sede)
    {
        $query = DB::table('cotizacion_documento_detalles as cdd')
            ->join('cotizacion_documento as cd', 'cd.id', '=', 'cdd.documento_id')
            ->whereYear('cd.fecha_documento', $year)
            ->where('cd.sede_id', $sede)
            ->where('cd.estado', 'ACTIVO')
            ->where('cd.estado_pago', 'PAGADA')
            ->where('cdd.estado', 'ACTIVO')
            ->where('cdd.eliminado', '0')
            ->where('cdd.tipo', 'PRODUCTO');

        if ($month) {
            $query->whereMonth('cd.fecha_documento', $month);
        }

        $data = $query->selectRaw('
            cdd.nombre_talla as talla,
            ROUND(SUM(cdd.cantidad), 2) as total_pares
        ')
            ->groupBy('cdd.nombre_talla')
            ->orderByDesc('total_pares')
            ->limit(10)
            ->get();

        $categories = [];
        $values = [];

        foreach ($data as $row) {
            $categories[] = $row->talla ?? 'Sin Talla';
            $values[] = (float) $row->total_pares;
        }

        return [
            'categories' => $categories,
            'values' => $values
        ];
    }

    public function getDeliveryTime(string $year, ?string $month, string $sede)
    {
        $query = DB::table('envios_ventas as ev')
            ->join('paquetes_embalados_detalle as ped', 'ped.envio_venta_id', '=', 'ev.id')
            ->join('repartos_detalle as rd', 'rd.paquete_embalado_id', '=', 'ped.paquete_embalado_id')
            ->where('ev.estado', 'DESPACHADO')
            ->whereYear('ev.created_at', $year)
            ->where('ev.sede_id', $sede);

        if ($month) {
            $query->whereMonth('ev.created_at', $month);
        }

        $result = $query->selectRaw('
            ROUND(AVG(
                TIMESTAMPDIFF(HOUR, ev.created_at, rd.created_at) / 24
            ), 2) as promedio_dias
        ')->first();

        return [
            'promedio' => (float) ($result->promedio_dias ?? 0)
        ];
    }
}
