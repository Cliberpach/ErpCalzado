<?php

namespace App\Http\Controllers\Consultas\Kardex;

use App\Almacenes\Kardex;
use App\Almacenes\Producto;
use App\Exports\KardexProductoExport;
use App\Http\Controllers\Controller;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProductoController extends Controller
{
    public function index()
    {
        $kardex7668 = 'ocultate';
        return view('consultas.kardex.producto', compact('kardex7668'));
    }

    public function getTable(Request $request)
    {

        $fecini = $request->d_start;
        $fecfin = $request->d_end;
        $input = $request->input;
        $stock = (int) $request->stock;
        
        $kardex = DB::table('productos')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->join('tabladetalles', 'tabladetalles.id', '=', 'productos.medida')
            ->select(
                'productos.nombre',
                'categorias.descripcion as categoria',
                'marcas.marca',
                'tabladetalles.descripcion as medida',
                DB::raw(
                    "(ifnull((SELECT sum(ddc.cantidad) from compra_documento_detalles ddc INNER JOIN compra_documentos dc ON ddc.documento_id = dc.id WHERE dc.fecha_emision < '{$fecini}' AND ddc.producto_id = productos.id AND dc.estado != 'ANULADO'),0) + ifnull((SELECT sum(dni.cantidad) from detalle_nota_ingreso dni INNER JOIN nota_ingreso ni ON dni.nota_ingreso_id = ni.id WHERE ni.fecha < '{$fecini}' AND dni.producto_id = productos.id AND ni.estado != 'ANULADO'),0) -ifnull((SELECT SUM(ddv.cantidad) FROM cotizacion_documento_detalles ddv INNER JOIN cotizacion_documento dv ON ddv.documento_id = dv.id INNER JOIN lote_productos lp ON ddv.lote_id = lp.id WHERE dv.fecha_documento < '{$fecini}' AND dv.estado != 'ANULADO' AND lp.producto_id = productos.id AND ddv.eliminado = '0'),0) + ifnull((SELECT SUM(ddv.cantidad) FROM cotizacion_documento_detalles ddv INNER JOIN cotizacion_documento dv ON ddv.documento_id = dv.id INNER JOIN lote_productos lp ON ddv.lote_id = lp.id WHERE dv.fecha_documento < '{$fecini}' AND dv.estado != 'ANULADO' AND lp.producto_id = productos.id AND dv.tipo_venta != '129' AND dv.convertir != '' AND ddv.eliminado = '0'),0) - ifnull((SELECT SUM(dns.cantidad) FROM detalle_nota_salidad dns INNER JOIN nota_salidad ns ON dns.nota_salidad_id = ns.id WHERE ns.fecha < '{$fecini}' AND ns.estado != 'ANULADO' AND dns.producto_id = productos.id),0) + ifnull((SELECT SUM(ned.cantidad) FROM nota_electronica_detalle ned INNER JOIN nota_electronica ne ON ned.nota_id = ne.id INNER JOIN cotizacion_documento_detalles cdd ON cdd.id = ned.detalle_id INNER JOIN lote_productos lpn ON lpn.id = cdd.lote_id WHERE ne.fechaEmision < '{$fecini}' AND ne.estado != 'ANULADO' AND lpn.producto_id = productos.id),0)) as STOCKINI"
                ),
                DB::raw(
                    "ifnull((SELECT SUM(cdd.cantidad) from compra_documento_detalles cdd INNER JOIN compra_documentos cd ON cdd.documento_id = cd.id WHERE cd.fecha_emision >= '{$fecini}' AND cd.fecha_emision <= '{$fecfin}' AND cd.estado != 'ANULADO' AND cdd.producto_id = productos.id),0) AS COMPRAS"
                ),
                DB::raw(
                    "ifnull((SELECT sum(dni.cantidad) from detalle_nota_ingreso dni INNER JOIN nota_ingreso ni ON dni.nota_ingreso_id = ni.id WHERE ni.fecha >= '{$fecini}' AND ni.fecha <= '{$fecfin}' AND dni.producto_id = productos.id AND ni.estado != 'ANULADO'),0) AS INGRESOS"
                ),
                DB::raw(
                    "ifnull((SELECT SUM(ned.cantidad) FROM nota_electronica_detalle ned INNER JOIN nota_electronica ne ON ned.nota_id = ne.id INNER JOIN cotizacion_documento_detalles cdd ON cdd.id = ned.detalle_id INNER JOIN lote_productos lpn ON lpn.id = cdd.lote_id WHERE ne.fechaEmision >= '{$fecini}' AND ne.fechaEmision <= '{$fecfin}' AND  ne.estado != 'ANULADO' AND lpn.producto_id = productos.id),0) as DEVOLUCIONES"
                ),
                DB::raw(
                    "(ifnull((SELECT SUM(vdd.cantidad) from cotizacion_documento_detalles vdd INNER JOIN cotizacion_documento vd ON vdd.documento_id = vd.id INNER JOIN lote_productos lp ON vdd.lote_id = lp.id WHERE vd.fecha_documento >= '{$fecini}' and vd.fecha_documento <= '{$fecfin}' AND vd.estado != 'ANULADO' AND lp.producto_id = productos.id AND vdd.eliminado = '0'),0) - ifnull((SELECT SUM(vdd.cantidad) from cotizacion_documento_detalles vdd INNER JOIN cotizacion_documento vd ON vdd.documento_id = vd.id INNER JOIN lote_productos lp ON vdd.lote_id = lp.id WHERE vd.fecha_documento >= '{$fecini}' and vd.fecha_documento <= '{$fecfin}' AND vd.estado != 'ANULADO' AND lp.producto_id = productos.id AND vd.tipo_venta != '129' AND vd.convertir != '' AND vdd.eliminado = '0'),0)) AS VENTAS"
                ),
                DB::raw(
                    "ifnull((SELECT SUM(dns.cantidad) FROM detalle_nota_salidad dns INNER JOIN nota_salidad ns ON dns.nota_salidad_id = ns.id WHERE ns.fecha >= '{$fecini}' AND ns.fecha <= '{$fecfin}' AND ns.estado != 'ANULADO' AND dns.producto_id = productos.id),0) AS SALIDAS"
                ),
                DB::raw(
                    "((ifnull((SELECT sum(ddc.cantidad) from compra_documento_detalles ddc INNER JOIN compra_documentos dc ON ddc.documento_id = dc.id WHERE dc.fecha_emision < '{$fecini}' AND ddc.producto_id = productos.id AND dc.estado != 'ANULADO'),0) + ifnull((SELECT sum(dni.cantidad) from detalle_nota_ingreso dni INNER JOIN nota_ingreso ni ON dni.nota_ingreso_id = ni.id WHERE ni.fecha < '{$fecini}' AND dni.producto_id = productos.id AND ni.estado != 'ANULADO'),0) - ifnull((SELECT SUM(ddv.cantidad) FROM cotizacion_documento_detalles ddv INNER JOIN cotizacion_documento dv ON ddv.documento_id = dv.id INNER JOIN lote_productos lp ON ddv.lote_id = lp.id WHERE dv.fecha_documento < '{$fecini}' AND dv.estado != 'ANULADO' AND lp.producto_id = productos.id AND ddv.eliminado = '0'),0) + ifnull((SELECT SUM(ddv.cantidad) FROM cotizacion_documento_detalles ddv INNER JOIN cotizacion_documento dv ON ddv.documento_id = dv.id INNER JOIN lote_productos lp ON ddv.lote_id = lp.id WHERE dv.fecha_documento < '{$fecini}' AND dv.estado != 'ANULADO' AND lp.producto_id = productos.id AND dv.tipo_venta != '129' AND dv.convertir != '' AND ddv.eliminado = '0'),0) - ifnull((SELECT SUM(dns.cantidad) FROM detalle_nota_salidad dns INNER JOIN nota_salidad ns ON dns.nota_salidad_id = ns.id WHERE ns.fecha < '{$fecini}' AND ns.estado != 'ANULADO' AND dns.producto_id = productos.id),0) + ifnull((SELECT SUM(ned.cantidad) FROM nota_electronica_detalle ned INNER JOIN nota_electronica ne ON ned.nota_id = ne.id INNER JOIN cotizacion_documento_detalles cdd ON cdd.id = ned.detalle_id INNER JOIN lote_productos lpn ON lpn.id = cdd.lote_id WHERE ne.fechaEmision < '{$fecini}' AND ne.estado != 'ANULADO' AND lpn.producto_id = productos.id),0)) - (ifnull((SELECT SUM(vdd.cantidad) from cotizacion_documento_detalles vdd INNER JOIN cotizacion_documento vd ON vdd.documento_id = vd.id INNER JOIN lote_productos lp ON vdd.lote_id = lp.id WHERE vd.fecha_documento >= '{$fecini}' and vd.fecha_documento <= '{$fecfin}' AND vd.estado != 'ANULADO' AND lp.producto_id = productos.id AND vdd.eliminado = '0'),0) - ifnull((SELECT SUM(vdd.cantidad) from cotizacion_documento_detalles vdd INNER JOIN cotizacion_documento vd ON vdd.documento_id = vd.id INNER JOIN lote_productos lp ON vdd.lote_id = lp.id WHERE vd.fecha_documento >= '{$fecini}' and vd.fecha_documento <= '{$fecfin}' AND vd.estado != 'ANULADO' AND lp.producto_id = productos.id AND vd.tipo_venta != '129' AND vd.convertir != '' AND vdd.eliminado = '0'),0) + ifnull((SELECT SUM(dns.cantidad) FROM detalle_nota_salidad dns INNER JOIN nota_salidad ns ON dns.nota_salidad_id = ns.id WHERE ns.fecha >= '{$fecini}' AND ns.fecha <= '{$fecfin}' AND ns.estado != 'ANULADO' AND dns.producto_id = productos.id),0)) + ifnull((SELECT SUM(cdd.cantidad) from compra_documento_detalles cdd INNER JOIN compra_documentos cd ON cdd.documento_id = cd.id WHERE cd.fecha_emision >= '{$fecini}' AND cd.fecha_emision <= '{$fecfin}' AND cd.estado != 'ANULADO' AND cdd.producto_id = productos.id),0) + ifnull((SELECT sum(dni.cantidad) from detalle_nota_ingreso dni INNER JOIN nota_ingreso ni ON dni.nota_ingreso_id = ni.id WHERE ni.fecha >= '{$fecini}' AND ni.fecha <= '{$fecfin}' AND dni.producto_id = productos.id AND ni.estado != 'ANULADO'),0) + ifnull((SELECT SUM(ned.cantidad) FROM nota_electronica_detalle ned INNER JOIN nota_electronica ne ON ned.nota_id = ne.id INNER JOIN cotizacion_documento_detalles cdd ON cdd.id = ned.detalle_id INNER JOIN lote_productos lpn ON lpn.id = cdd.lote_id WHERE ne.fechaEmision >= '{$fecini}' AND ne.fechaEmision <= '{$fecfin}' AND  ne.estado != 'ANULADO' AND lpn.producto_id = productos.id),0)) as STOCK"
                ),
                DB::raw("'{$fecini}' as fecini"),
                DB::raw("'{$fecfin}'  as fecfin"),
            )->where("productos.estado", "<>", 'ANULADO');

        if ($input) {
            $kardex = $kardex->where('productos.nombre', 'like', '%' . $input . '%');
        }
        if ($stock > 0) {
            $kardex = $kardex->where('productos.stock', '>', 0);
        }
        if ($stock == 0) {
            $kardex = $kardex->where('productos.stock', 0);
        }

        $kardex = $kardex
            ->orderBy("productos.id", "ASC")
            ->paginate(10);

        return response()->json($kardex);
    }

    public function DonwloadExcel(Request $request)
    {
        ini_set("max_execution_time", 60000);
        ini_set('memory_limit', '1024M');
        $fecini = $request->d_start;
        $fecfin = $request->d_end;
        $input = $request->input;
        $stock = (int) $request->stock;
        $kardex = Producto::with([
            'categoria',
            'marca',
            'tabladetalle',
            'compraDetalles' => function ($query) {
                return $query->select('id', "producto_id", 'documento_id', 'cantidad');
            },
            'compraDetalles.documento' => function ($query) {
                return $query->select("id", 'fecha_emision', 'estado')
                    ->where("estado", "<>", 'ANULADO');
            },
            "DetalleNI" => function ($query) {
                return $query->select("id", "producto_id", 'nota_ingreso_id', 'cantidad');
            },
            "DetalleNI.nota_ingreso" => function ($query) {
                return $query->select("id", "fecha", "estado")
                    ->where("estado", '<>', 'ANULADO');
            },
            'DetalleVenta' => function ($query) {
                return $query->select("id", "codigo_producto", "documento_id", "cantidad", 'eliminado')
                    ->where("eliminado", "=", '0');
            },
            "DetalleVenta.documento" => function ($query) {
                return $query->select("id", "fecha_documento", "estado")
                    ->where("estado", "<>", "ANULADO");
            },
            "DetalleNS" => function ($query) {
                return $query->select("id", "nota_salidad_id", "producto_id", "cantidad");
            },
            "DetalleNS.nota_salidad" => function ($query) {
                return $query->select("id", "fecha", "estado")
                    ->where("estado", "<>", "ANULADO");
            },
            "DetalleNE" => function ($query) {
                return $query->select("id", "documento_id", "codigo_producto", 'cantidad');
            },
            "DetalleNE.detalles" => function ($query) {
                return $query->select("id", 'detalle_id', 'cantidad', 'nota_id');
            },
            "DetalleNE.detalles.nota_dev" => function ($query) {
                return $query->select("id", "fechaEmision", "estado")
                    ->where("estado", "<>", 'ANULADO');
            },
        ])
            ->where("productos.estado", "<>", 'ANULADO')
            ->orderBy("productos.id", "ASC");
        if ($input) {
            $kardex = $kardex->where('productos.nombre', 'like', '%' . $input . '%');
        }

        if ($stock > 0) {
            $kardex = $kardex->where('productos.stock', '>', 0);
        }
        if ($stock == 0) {
            $kardex = $kardex->where('productos.stock', 0);
        }
        $collection = collect();

        foreach ($kardex->get() as $item) {

            $obj = new Collection();
            $obj->nombre = $item->nombre;
            $obj->codigo = $item->codigo;
            $obj->categoria = $item->categoria->descripcion;
            $obj->marca = $item->marca->marca;
            $obj->medida = $item->tabladetalle->descripcion;
            $obj->STOCKINI = $this->STOCKINI($item, $fecini, $fecfin);
            $obj->COMPRAS = $this->compras($item, $fecini, $fecfin);
            $obj->INGRESOS = $this->ingresos($item, $fecini, $fecfin);
            $obj->DEVOLUCIONES = $this->devoluciones($item, $fecini, $fecfin);
            $obj->VENTAS = $this->ventas($item, $fecini, $fecfin);
            $obj->SALIDAS = $this->salidas($item, $fecini, $fecfin);
            $obj->STOCK = $this->stock($item, $fecini, $fecfin);
            $collection->push($obj);
        }
        
        ob_get_contents();
        ob_end_clean();
        ob_start();
        return Excel::download(new KardexProductoExport($collection, $request->all()), "Kardex-producto_" . $fecini . "_" . $fecfin . ".xlsx");
    }
    private function totales($total)
    {
        return $total < 0 ? 0 : $total;
    }
    private function stock($item, $fecini, $fecfin)
    {
        $stock1 = $item->compraDetalles->where("documento.fecha_emision", "<", $fecini)->sum("cantidad");
        $stock2 = $item->DetalleNI->where("nota_ingreso.fecha", "<", $fecini)->sum("cantidad");
        $stock3 = $item->DetalleVenta->where("documento.fecha_documento", "<", $fecini)->sum("cantidad");
        $stock4 = $item->DetalleVenta->where("documento.fecha_documento", "<", $fecini)
            ->where("documento.tipo_venta", "<>", '129')
            ->where("documento.convertir", "<>", '')
            ->sum("cantidad");

        $stock5 = $item->DetalleNS->where("nota_salidad.fecha", "<", $fecini)
            ->sum("cantidad");
        $stock6 = 0;
        foreach ($item->DetalleNE as $detalle) {
            if (count($detalle->detalles) > 0) {
                $stock6 = $detalle->detalles->where("nota_dev.fechaEmision", "<", $fecini)->sum("cantidad");
            }
        }

        $stock7 = $item->DetalleVenta->where("documento.fecha_documento", ">=", $fecini)
            ->where("documento.fecha_documento", "<=", $fecfin)
            ->sum("cantidad");
        $stock8 = $item->DetalleVenta->where("documento.fecha_documento", ">=", $fecini)
            ->where("documento.fecha_documento", "<=", $fecfin)
            ->where("documento.tipo_venta", "<>", '129')
            ->where("documento.convertir", "<>", '')
            ->sum("cantidad");

        $stock9 = $item->DetalleNS->where("nota_salidad.fecha", ">=", $fecini)
            ->where("nota_salidad.fecha", "<=", $fecfin)
            ->sum("cantidad");

        $stock10 = $item->compraDetalles
            ->where("documento.fecha_emision", ">=", $fecini)
            ->where("documento.fecha_emision", "<=", $fecfin)
            ->sum("cantidad");

        $stock11 = $item->DetalleNI
            ->where("nota_ingreso.fecha", ">=", $fecini)
            ->where("nota_ingreso.fecha", "<=", $fecfin)
            ->sum("cantidad");
        $stock12 = 0;

        $dataDevo = $item->DetalleNE->filter(function ($nde) use ($fecini, $fecfin) {
            return count($nde->detalles->where("nota_dev.fechaEmision", ">=", $fecini)->where("nota_dev.fechaEmision", "<=", $fecfin)) > 0;
        })->values();

        foreach ($dataDevo as $x) {
            $stock12 += $x->detalles->sum("cantidad");
        }

        return $this->totales((($stock1 + $stock2 - $stock3 + $stock4 - $stock5 + $stock6) - ($stock7 - $stock8 + $stock9) + ($stock10 + $stock11 + $stock12)));
    }
    private function STOCKINI($item, $fecini, $fecfin)
    {
        $ddc = $item->compraDetalles->where("documento.fecha_emision", "<", $fecini)->sum("cantidad");
        $dni = $item->DetalleNI->where("nota_ingreso.fecha", "<", $fecini)->sum("cantidad");
        $ddv = $item->DetalleVenta->where("documento.fecha_documento", "<", $fecini)->sum("cantidad");
        $ddv1 = $item->DetalleVenta->where("documento.fecha_documento", "<", $fecini)
            ->where("documento.convertir", '<>', '')
            ->where("documento.tipo_venta", '<>', '129')
            ->sum("cantidad");
        $dns = $item->DetalleNS->where("nota_salidad.fecha", "<", $fecini)->sum("cantidad");
        $ned = 0;
        /** ned */
        foreach ($item->DetalleNE as $detalle) {
            if (count($detalle->detalles) > 0) {
                $ned = $detalle->detalles->where("nota_dev.fechaEmision", "<", $fecini)->sum("cantidad");
            }
        }
        return $this->totales(($ddc + $dni - $ddv + $ddv1 - $dns + $ned));
    }
    private function devoluciones($item, $fecini, $fecfin)
    {
        $devoluciones = 0;
        $dataDevo = $item->DetalleNE->filter(function ($nde) use ($fecini, $fecfin) {
            return count($nde->detalles->where("nota_dev.fechaEmision", ">=", $fecini)->where("nota_dev.fechaEmision", "<=", $fecfin)) > 0;
        })->values();

        foreach ($dataDevo as $x) {
            $devoluciones += $x->detalles->sum("cantidad");
        }
        return $this->totales($devoluciones);

    }

    private function ventas($item, $fecini, $fecfin)
    {
        /** ventas */
        $venta1 = $item->DetalleVenta->where("documento.fecha_documento", ">=", $fecini)
            ->where("documento.fecha_documento", "<=", $fecfin)->sum("cantidad");
        $venta2 = $item->DetalleVenta->where("documento.fecha_documento", ">=", $fecini)
            ->where("documento.fecha_documento", "<=", $fecfin)
            ->where("documento.tipo_venta", "<>", '129')
            ->where("documento.convertir", "<>", '')
            ->sum("cantidad");
        $ventas = $venta1 - $venta2;
        return $this->totales($ventas);
    }
    private function salidas($item, $fecini, $fecfin)
    {
        return $this->totales($item->DetalleNS->where("nota_salidad.fecha", ">=", $fecini)
                ->where("nota_salidad.fecha", "<=", $fecfin)
                ->sum("cantidad"));
    }
    private function compras($item, $fecini, $fecfin)
    {
        return $this->totales($item->compraDetalles
                ->where("documento.fecha_emision", ">=", $fecini)
                ->where("documento.fecha_emision", "<=", $fecfin)
                ->sum("cantidad"));
    }
    private function ingresos($item, $fecini, $fecfin)
    {
        return $this->totales($item->DetalleNI
                ->where("nota_ingreso.fecha", ">=", $fecini)
                ->where("nota_ingreso.fecha", "<=", $fecfin)
                ->sum("cantidad"));
    }
    public function index_top()
    {
        return view('consultas.kardex.producto_top');
    }

    public function getTableTop(Request $request)
    {
        $top = $request->top;

        $documentos = Documento::where('estado', '!=', 'ANULADO');
        if ($request->fecha_desde && $request->fecha_hasta) {
            $documentos = $documentos->whereBetween('fecha_documento', [$request->fecha_desde, $request->fecha_hasta]);
        }

        $documentos = $documentos->orderBy('id', 'desc')->get();

        $coleccion_aux = collect();
        $coleccion = collect();
        foreach ($documentos as $documento) {
            $detalles = Detalle::where('estado', 'ACTIVO')->where('documento_id', $documento->id)->get();
            foreach ($detalles as $detalle) {
                $coleccion_aux->push([
                    'codigo' => $detalle->lote->producto->codigo,
                    'cantidad' => $detalle->cantidad,
                    'producto_id' => $detalle->lote->producto_id,
                    'producto' => $detalle->lote->producto->nombre,
                    'costo' => $detalle->lote->detalle_compra ? $detalle->lote->detalle_compra->precio : 0.00,
                    'precio' => $detalle->precio_nuevo,
                    'importe' => number_format($detalle->precio_nuevo * $detalle->cantidad, 2),
                ]);
            }
        }

        $productos = Producto::where('estado', 'ACTIVO')->get();

        foreach ($productos as $producto) {
            $suma_vendidos = $coleccion_aux->where('producto_id', $producto->id)->sum('cantidad') ? $coleccion_aux->where('producto_id', $producto->id)->sum('cantidad') : 0;
            $suma_importe = $coleccion_aux->where('producto_id', $producto->id)->sum('importe') ? $coleccion_aux->where('producto_id', $producto->id)->sum('importe') : 0;
            $coleccion->push([
                'codigo' => $producto->codigo,
                'producto' => $producto->nombre,
                'cantidad' => $suma_vendidos,
                'importe' => $suma_importe,
            ]);
        }

        $coll = $coleccion->sortByDesc('cantidad')->take($top);

        $arr = array();
        foreach ($coll as $coll_) {
            $arr_aux = array(
                'codigo' => $coll_['codigo'],
                'producto' => $coll_['producto'],
                'cantidad' => $coll_['cantidad'],
                'importe' => $coll_['importe'],
            );
            array_push($arr, $arr_aux);
        }

        return response()->json([
            'success' => true,
            'kardex' => $arr,
            'top' => count($coll->all()),
        ]);
    }
}
