<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use App\Mantenimiento\Empresa\Empresa;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Pedidos\OrdenProduccion;
use App\Pedidos\OrdenProduccionDetalle;
use Exception;

class OrdenProduccionController extends Controller
{
    public function index(){
        return view('pedidos.ordenes.index');
    }

    public function getTable(Request $request){
        
        $ordenes_produccion =   DB::select('select CONCAT("OP","-",op.id) as op_id_name,
                                op.id, op.user_id, op.user_nombre,op.fecha_propuesta_atencion,op.observacion,
                                op.created_at as fecha_registro,op.estado,op.tipo
                                from ordenes_produccion as op 
                                where op.estado != "ANULADO"');

        
        return DataTables::of($ordenes_produccion)->toJson();
    }

    public function getDetalle($orden_produccion_id){
        try {
            $orden_produccion_detalle   =   DB::select('select * 
                                            from ordenes_produccion_detalles as opd
                                            where opd.orden_produccion_id = ?',[$orden_produccion_id]);
                            
            return response()->json(['success'=>true,'orden_produccion_detalle'=>$orden_produccion_detalle]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function pdf($orden_produccion_id){
        try {
            $orden_produccion_detalle       =   DB::select('select * from ordenes_produccion_detalles as opd
                                            where opd.orden_produccion_id = ?',[$orden_produccion_id]);

            $lstProgramacionProduccion  =   $this->formatearOrdenProduccionDetalle($orden_produccion_detalle);

            $tallasBD                   =   DB::select('select t.id,t.descripcion from tallas as t
                                            where t.estado ="ACTIVO"');

            $empresa                    =   Empresa::first();
            $usuario_impresion_nombre   =   Auth::user()->usuario;
            $fecha_actual               =   Carbon::now();

            
            $pdf = PDF::loadview('pedidos.ordenes.pdf.prog_produccion', [
                'lstProgramacionProduccion'     =>  $lstProgramacionProduccion,
                'tallasBD'                      =>  $tallasBD,
                'empresa'                       =>  $empresa,
                'usuario_impresion_nombre'      =>  $usuario_impresion_nombre,
                'fecha_actual'                  =>  $fecha_actual
            ])->setPaper('a4', 'landscape')->setWarnings(false);
            
            return $pdf->stream('PROGRAMACIÓN_PRODUCCIÓN.pdf');

        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function formatearOrdenProduccionDetalle($orden_produccion_detalle){
        $productos = [];
    
        foreach ($orden_produccion_detalle as $detalle) {
            // Verificar si el producto ya existe en el array $productos
            if (!isset($productos[$detalle->producto_id])) {
                $productos[$detalle->producto_id] = (object)[
                    "modelo" => (object)[
                        "id" => $detalle->modelo_id,
                        "nombre" => $detalle->modelo_nombre
                    ],
                    "id" => $detalle->producto_id,
                    "nombre" => $detalle->producto_nombre,
                    "colores" => []
                ];
            }
    
            // Referencia al producto actual
            $producto = $productos[$detalle->producto_id];
    
            // Verificar si el color ya existe en el array $colores del producto
            if (!isset($producto->colores[$detalle->color_id])) {
                $producto->colores[$detalle->color_id] = (object)[
                    "id" => $detalle->color_id,
                    "nombre" => $detalle->color_nombre,
                    "tallas" => []
                ];
            }
    
            // Agregar la talla al color correspondiente
            $producto->colores[$detalle->color_id]->tallas[] = (object)[
                "id" => $detalle->talla_id,
                "nombre" => $detalle->talla_nombre,
                "cantidad_pendiente" => $detalle->cantidad
            ];
    
            // Actualizar el producto en el array
            $productos[$detalle->producto_id] = $producto;
        }
    
        // Convertir los índices del array a un formato plano (opcional)
        $productos = array_values(array_map(function($producto) {
            $producto->colores = array_values($producto->colores);
            return $producto;
        }, $productos));
    
        return $productos;
    }

    public function create(){
        $tallas     =   DB::select('select t.id,t.descripcion from tallas as t where t.estado = "ACTIVO"');
        $modelos    =   DB::select('select m.id,m.descripcion from modelos as m where m.estado = "ACTIVO"');
        return view('pedidos.ordenes.create',compact('tallas','modelos'));
    }

    public function getProductosByModelo($modelo_id){
        try {
            $productos  =   DB::select('select p.id,p.nombre 
                            from productos as p
                            where p.modelo_id = ? and p.estado = "ACTIVO"',[$modelo_id]);

            return response()->json(['success' => true,'productos'=>$productos]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getColoresTallas($producto_id){
       
        try {
           
            $colores =  DB::select('SELECT 
                                    p.id AS producto_id,
                                    p.nombre AS producto_nombre,
                                    c.id AS color_id,
                                    c.descripcion AS color_nombre
                                FROM 
                                    producto_colores AS pc 
                                    inner join productos as p on p.id = pc.producto_id
                                    inner join colores as c on c.id = pc.color_id
                                WHERE 
                                    pc.producto_id = ? 
                                    AND p.estado = "ACTIVO" and c.estado = "ACTIVO" ',[$producto_id]);

            $stocks =   DB::select('select  pct.producto_id,pct.color_id,pct.talla_id,
                        pct.stock,pct.stock_logico, t.descripcion as talla_nombre
                        from producto_color_tallas as pct
                        inner join productos as p on p.id = pct.producto_id
                        inner join colores as c on c.id = pct.color_id 
                        inner join tallas as t on t.id = pct.talla_id
                        where p.estado = "ACTIVO" and c.estado = "ACTIVO" and t.estado = "ACTIVO"
                        and p.id = ?',[$producto_id]);

            $tallas =   DB::select('select t.id,t.descripcion from tallas as t where t.estado="ACTIVO"');   

            $producto_color_tallas  =   null;
            if(count($colores) > 0){
                $producto_color_tallas  =   $this->formatearColoresTallas($colores,$stocks,$tallas);
            }

            return response()->json(['success' => true,'producto_color_tallas'=>$producto_color_tallas]);
        } catch (\Throwable $th) {
    
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function formatearColoresTallas($colores, $stocks, $tallas)
    {
        $producto = [];

        if (count($colores) > 0) {
            $producto['id'] = $colores[0]->producto_id;
            $producto['nombre'] = $colores[0]->producto_nombre;
        } else {
            $producto['id'] = null;
            $producto['nombre'] = null;
        }

    
        $lstColores = [];

        //======== RECORRIENDO COLORES =======
        foreach ($colores as $color) {
            $item_color = [];
            $item_color['id'] = $color->color_id;
            $item_color['nombre'] = $color->color_nombre;

            //======== OBTENIENDO TALLAS DEL COLOR =======
            $lstTallas = [];

            foreach ($tallas as $talla) {
                $item_talla = [];
                $item_talla['id'] = $talla->id;
                $item_talla['nombre'] = $talla->descripcion;

                // Filtrar stocks para color y talla actuales
                $stock_filtrado = array_filter($stocks, function ($stock) use ($producto, $color, $talla) {
                    return $stock->producto_id == $producto['id'] &&
                        $stock->color_id == $color->color_id &&
                        $stock->talla_id == $talla->id;
                });

                // Asignar stock y stock lógico si existe, o establecer en 0
                if (!empty($stock_filtrado)) {
                    $first_stock = reset($stock_filtrado); // Obtiene el primer elemento del array filtrado
                    $item_talla['stock'] = $first_stock->stock;
                    $item_talla['stock_logico'] = $first_stock->stock_logico;
                } else {
                    $item_talla['stock'] = 0;
                    $item_talla['stock_logico'] = 0;
                }

                $lstTallas[] = $item_talla;
            }

            $item_color['tallas'] = $lstTallas;
            $lstColores[] = $item_color;
        }

        $producto['colores'] = $lstColores;

        return $producto;
    }

    public function store(Request $request){
        DB::beginTransaction();
        try {
            $lstProgramacionProduccion  =   json_decode($request->get('lstProgramacionProduccion'));
            
              //===== CREAMOS LA ORDEN DE PRODUCCIÓN CABEZERA ========
              $orden_produccion                             =   new OrdenProduccion();
              $orden_produccion->user_id                    =   Auth::user()->id;
              $orden_produccion->user_nombre                =   Auth::user()->usuario;
              $orden_produccion->fecha_propuesta_atencion   =   $request->get('fecha_propuesta_atencion');
              $orden_produccion->observacion                =   $request->get('observacion');
              $orden_produccion->tipo                       =   "STOCK";
              $orden_produccion->save();
  
              //======= GUARDANDO DETALLES DE LA ORDEN DE PRODUCCIÓN =====
              foreach ($lstProgramacionProduccion as $producto) {
                  foreach ($producto->tallas as $talla ) {
                      
                    $orden_produccion_detalle                       =   new OrdenProduccionDetalle();
                    $orden_produccion_detalle->orden_produccion_id  =   $orden_produccion->id;

                    //====== OBTENIENDO MODELO ID =======
                    $modelo =   DB::select('select m.id,m.descripcion
                                from modelos as m 
                                inner join productos as p on p.modelo_id = m.id
                                where m.estado = "ACTIVO" and p.id = ?',[$producto->producto_id]);

                    if(count($modelo) === 0){
                        throw new Exception("No se encontró el modelo del producto ".$producto->producto_nombre);
                    }

                    $orden_produccion_detalle->modelo_id                =   $modelo[0]->id;
                    $orden_produccion_detalle->producto_id              =   $producto->producto_id;
                    $orden_produccion_detalle->color_id                 =   $producto->color_id;
                    $orden_produccion_detalle->talla_id                 =   $talla->talla_id;
                    $orden_produccion_detalle->modelo_nombre            =   $modelo[0]->descripcion;
                    $orden_produccion_detalle->producto_nombre          =   $producto->producto_nombre;
                    $orden_produccion_detalle->color_nombre             =   $producto->color_nombre;
                    $orden_produccion_detalle->talla_nombre             =   $talla->talla_nombre;
                    $orden_produccion_detalle->cantidad                 =   $talla->cantidad;
                    $orden_produccion_detalle->save();                    
                      
                  } 
              }
  
              DB::commit();
              return response()->json(['success'=>true,'message'=>'ORDEN DE PRODUCCIÓN N° '.$orden_produccion->id.' GENERADA CON ÉXITO']);
  
            
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
    

}
