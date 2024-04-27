<?php

namespace App\Http\Controllers\Consultas\Kardex;
use Carbon\Carbon;

use App\Almacenes\Kardex;
use App\Almacenes\Producto;
use App\Almacenes\Color;
use App\Almacenes\Talla;
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
        $productos  =   DB::select('select pct.producto_id,pct.color_id,pct.talla_id,
                        p.nombre as producto_nombre,c.descripcion as color_nombre,t.descripcion as talla_nombre
                        from producto_color_tallas as pct
                        inner join productos as p on p.id=pct.producto_id
                        inner join colores as c on c.id=pct.color_id
                        inner join tallas as t on t.id=pct.talla_id');
        
        return view('consultas.kardex.producto', compact('kardex7668','productos'));
    }

    public function getTable(Request $request)
    {
        $consulta = Kardex::where('estado','!=','ANULADO');
        if($request->fecha_desde && $request->fecha_hasta)
        {
            $consulta->whereBetween('fecha', [$request->fecha_desde, $request->fecha_hasta]);
        }

        if($request->producto_id)
        {
            $ids = explode("_", $request->producto_id);

            $producto_id    = $ids[0];
            $color_id       = $ids[1];
            $talla_id       = $ids[2];
            $consulta->where('producto_id',$producto_id)
            ->where('color_id',$color_id)
            ->where('talla_id',$talla_id);
        }

        $documentos = $consulta->orderBy('id', 'desc')->get();

        $coleccion = collect();
        foreach($documentos as $documento){
                $producto   =   Producto::find($documento->producto_id);   
                $color      =   Color::find($documento->color_id);   
                $talla      =   Talla::find($documento->talla_id);   

                $compras        =   '-';
                $ingresos       =   '-';
                $ventas         =   '-';
                $devoluciones   =   '-';
                $salidas        =   '-';
        
                if($documento->origen == 'VENTA'){
                    $ventas     =   $documento->numero_doc;
                }
                if($documento->origen == 'Salida'){
                    $salidas    =   $documento->numero_doc;
                }
                if($documento->origen == 'COMPRA'){
                    $compras    =   $documento->numero_doc;
                }
                if($documento->origen == 'INGRESO'){
                    $ingresos    =   $documento->numero_doc;
                }

                $coleccion->push([
                    'numero_doc'    =>  $documento->numero_doc,
                    'fecha'         =>  Carbon::parse($documento->created_at)->format('Y-m-d H:i:s'),
                    'producto'      =>  $producto?$producto->nombre:'',
                    'color'         =>  $color?$color->descripcion:'',
                    'talla'         =>  $talla?$talla->descripcion:'',
                    'origen'        =>  $documento->origen,
                    'cantidad'      =>  $documento->cantidad,
                    'stock'         =>  $documento->stock,
                    'compras'       =>  $compras,
                    'ingresos'      =>  $ingresos,
                    'ventas'        =>  $ventas,
                    'devoluciones'  =>  $devoluciones,
                    'salidas'       =>  $salidas,
                    'accion'        =>  $documento->origen.' '.$documento->accion,
                    'descripcion' =>  $documento->descripcion
                ]);
        }

        return response()->json([
            'success' => true,
            'kardex' => $coleccion,
        ]);
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
