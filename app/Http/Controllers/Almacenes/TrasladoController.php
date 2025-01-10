<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Almacen;
use App\Almacenes\Modelo;
use App\Almacenes\Talla;
use App\Almacenes\Traslado;
use App\Almacenes\TrasladoDetalle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrasladoController extends Controller
{
    public function index(){
        return view('almacenes.traslados.index');
    }

    public function create(){

        $tallas     =   Talla::where('estado','ACTIVO')->get();
        $modelos    =   Modelo::where('estado','ACTIVO')->get();
        $sede_id    =   Auth::user()->sede->id;


        $almacen_principal_origen   =   Almacen::where('estado','ACTIVO') 
                                        ->where('tipo_almacen','PRINCIPAL')
                                        ->where('sede_id',$sede_id)
                                        ->get();

        $almacenes_principales_destino  =   Almacen::where('estado','ACTIVO')   
                                            ->where('tipo_almacen','PRINCIPAL')
                                            ->where('sede_id','<>',$sede_id)
                                            ->get();

        return view('almacenes.traslados.create',
        compact('tallas','modelos','sede_id','almacen_principal_origen','almacenes_principales_destino'));

    }

    public function getProductosAlmacen($modelo_id,$almacen_origen_id){
        
        $stocks =   DB::select('select p.id as producto_id, p.nombre as producto_nombre,
                    p.precio_venta_1,p.precio_venta_2,p.precio_venta_3,
                    pct.color_id,c.descripcion as color_name,
                    pct.talla_id,t.descripcion as talla_name,pct.stock,
                    pct.stock_logico
                    from producto_color_tallas as pct
                    inner join productos as p
                    on p.id = pct.producto_id
                    inner join colores as c
                    on c.id = pct.color_id
                    inner join tallas as t
                    on t.id = pct.talla_id
                    where p.modelo_id = ? 
                    AND pct.almacen_id = ?
                    AND c.estado="ACTIVO" 
                    AND t.estado="ACTIVO"
                    AND p.estado="ACTIVO" 
                    order by p.id,c.id,t.id',[$modelo_id,$almacen_origen_id]);

        $producto_colores = DB::select('select 
                            p.id as producto_id,p.nombre as producto_nombre,
                            c.id as color_id, c.descripcion as color_nombre,
                            p.precio_venta_1,p.precio_venta_2,p.precio_venta_3
                            from producto_colores as pc
                            inner join productos as p on p.id = pc.producto_id
                            inner join colores as c on c.id = pc.color_id
                            where 
                            p.modelo_id = ? 
                            AND pc.almacen_id = ?
                            AND c.estado="ACTIVO" 
                            AND p.estado="ACTIVO"
                            group by p.id,p.nombre,c.id,c.descripcion,
                            p.precio_venta_1,p.precio_venta_2,p.precio_venta_3
                            order by p.id,c.id',[$modelo_id,$almacen_origen_id]);

        return response()->json(["message" => "success" , "stocks" => $stocks 
            ,"producto_colores" => $producto_colores ]);

    }

    public function getStock($producto_id,$color_id,$talla_id,$almacen_id){

        try {

            $stock_logico   =   DB::select('
                                    SELECT pct.stock_logico 
                                    FROM producto_color_tallas as pct
                                    WHERE 
                                    pct.producto_id = ? 
                                    AND pct.color_id = ?
                                    AND pct.talla_id = ?
                                    AND pct.almacen_id = ?',
                                    [$producto_id, $color_id, $talla_id,$almacen_id]
                                );


            return response()->json(["message" => "success", "data" => $stock_logico]);
        } catch (\Exception $e) {
            return response()->json(["message" => "Error al obtener el stock", "error" => $e->getMessage()], 500);
        }                    
    }


/*
array:10 [
  "notadetalle_tabla" => array:1 [
    0 => null
  ]
  "_token"                      => "uzjjE8H1q7pQjSz63y59f4eMTVLD21aDecg3A9Ah"
  "fecha"                       => "2025-01-09"
  "almacen_origen"              => "1"
  "almacen_destino"             => "7"
  "observacion"                 => null
  "tabla_ns_productos_length"   => "10"
  "tabla_ns_detalle_length"     => "10"
  "sede_id"                     => "14"
  "detalle"                     => "[{"producto_id":"1","producto_nombre":"ABRIL","color_id":"6","color_nombre":"NUDE CUERO","talla_id":"1","talla_nombre":"35","cantidad":"1"},
                                    {"producto_id":"503","producto_nombre":"PRODUCTO TEST SEDE CENTRAL","color_id":"30","color_nombre":"DORADO ESPEJO","talla_id":"2","talla_nombre":"36","cantidad":"1"}]"
]
*/ 
    public function store(Request $request){
        
        DB::beginTransaction();
        try {   

            $sede_destino                   =   DB::select('select a.sede_id 
                                                from almacenes as a
                                                where a.id = ?',[$request->get('almacen_destino')]);
            
            $detalle    =   json_decode($request->get('detalle'));

            //======== GRABANDO MAESTRO DEL TRASLADO ========
            $traslado                       =   new Traslado();
            $traslado->almacen_origen_id    =   $request->get('almacen_origen');
            $traslado->almacen_destino_id   =   $request->get('almacen_destino');
            $traslado->observacion          =   $request->get('observacion');
            $traslado->sede_origen_id       =   $request->get('sede_id');
            $traslado->sede_destino_id      =   $sede_destino[0]->sede_id;
            $traslado->save();

            //======== GRABANDO DETALLE DEL TRASLADO =====
            foreach ($detalle as $item) {
                $traslado_detalle               =   new TrasladoDetalle();
                $traslado_detalle->traslado_id  =   $traslado->id;
                $traslado_detalle->producto_id  =   $item->producto_id;
                $traslado_detalle->color_id     =   $item->color_id;
                $traslado_detalle->talla_id     =   $item->talla_id;
                $traslado_detalle->almacen_id   =   $request->get('almacen_origen');
                $traslado_detalle->cantidad     =   $item->cantidad;
                $traslado_detalle->save();
            }
            
            
            return response()->json(['success'=>true,'message'=>'TRASLADO REGISTRADO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

}
