<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Almacen;
use App\Almacenes\Conductor;
use App\Almacenes\DetalleNotaSalidad;
use App\Almacenes\Kardex;
use App\Almacenes\Modelo;
use App\Almacenes\MovimientoNota;
use App\Almacenes\NotaIngreso;
use App\Almacenes\NotaSalidad;
use App\Almacenes\Producto;
use Yajra\DataTables\Facades\DataTables;
use App\Almacenes\ProductoColor;
use App\Almacenes\ProductoColorTalla;
use App\Almacenes\Talla;
use App\Almacenes\Traslado;
use App\Almacenes\TrasladoDetalle;
use App\Almacenes\Vehiculo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilidadesController;
use App\Http\Controllers\Ventas\GuiaController;
use App\Http\Requests\Ventas\Guias\GuiaStoreRequest;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Sedes\Sede;
use App\User;
use App\Ventas\Cliente;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrasladoController extends Controller
{
    public function index(){
        return view('almacenes.traslados.index');
    }

    public function getTraslados(Request $request){

        $sede_id    =   Auth::user()->sede_id;

        $traslados  =   DB::table('traslados as t')
                        ->join('almacenes as ao', 'ao.id', '=', 't.almacen_origen_id')
                        ->join('almacenes as ad', 'ad.id', '=', 't.almacen_destino_id')
                        ->join('empresa_sedes as eso', 'eso.id', '=', 't.sede_origen_id')
                        ->join('empresa_sedes as esd', 'esd.id', '=', 't.sede_destino_id')
                        ->leftJoin('guias_remision as gr','gr.id','t.guia_id')
                        ->select(
                            DB::raw('CONCAT(gr.serie,"-", gr.correlativo) as guia'),
                            DB::raw('CONCAT("TR-", t.id) as simbolo'),
                            't.id',
                            'ao.descripcion as almacen_origen_nombre',
                            'ad.descripcion as almacen_destino_nombre',
                            't.observacion',
                            'eso.direccion as sede_origen_direccion',
                            'esd.direccion as sede_destino_direccion',
                            't.created_at as fecha_registro',
                            't.fecha_traslado',
                            't.registrador_nombre',
                            't.estado',
                            't.guia_id'
                        )
                        ->where('t.sede_origen_id',$sede_id)
                        ->get();

        return DataTables::of($traslados)->make(true);

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
        compact('tallas','modelos','sede_id',
        'almacen_principal_origen','almacenes_principales_destino'));

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
array:12 [
  "notadetalle_tabla" => array:1 [
    0 => null
  ]
  "_token"                      => "1spzO1oq9XyJSxSRjR65I8WhUCKGIZQQCGCnGzr6"
  "registrador_nombre"          => "ADMINISTRADOR"
  "fecha_registro"              => "2025-01-11"
  "fecha_traslado"              => "2025-01-11"
  "almacen_origen"              => "1"
  "almacen_destino"             => "7"
  "observacion"                 => null
  "tabla_ns_productos_length"   => "10"
  "tabla_ns_detalle_length"     => "10"
  "sede_id"                     => "14"
  "detalle"                     => "[{"producto_id":"503","producto_nombre":"PRODUCTO TEST SEDE CENTRAL","color_id":"30","color_nombre":"DORADO ESPEJO","talla_id":"2","talla_nombre":"36","cantidad":"1"},{"producto_id":"503","producto_nombre":"PRODUCTO TEST SEDE CENTRAL","color_id":"86","color_nombre":"PLATEADO BRILLO","talla_id":"2","talla_nombre":"36","cantidad":"2"}]"
]
*/ 
    public function store(Request $request){
     
        DB::beginTransaction();
        try {   

            $detalle        =   json_decode($request->get('detalle'));

            $sede_destino   =   DB::select('select 
                                a.sede_id 
                                from almacenes as a
                                where 
                                a.id = ?',[$request->get('almacen_destino')]);
            

            //======== GRABANDO MAESTRO DEL TRASLADO ========
            $traslado                       =   new Traslado();
            $traslado->almacen_origen_id    =   $request->get('almacen_origen');
            $traslado->almacen_destino_id   =   $request->get('almacen_destino');
            $traslado->observacion          =   $request->get('observacion');
            $traslado->sede_origen_id       =   $request->get('sede_id');
            $traslado->sede_destino_id      =   $sede_destino[0]->sede_id;
            $traslado->fecha_traslado       =   $request->get('fecha_traslado');
            $traslado->registrador_id       =   Auth::user()->id;
            $traslado->registrador_nombre   =   Auth::user()->usuario;
            $traslado->estado               =   'PENDIENTE';
            $traslado->save();

            $almacen_origen_bd              =   Almacen::find($request->get('almacen_origen'));
            $almacen_destino_bd             =   Almacen::find($request->get('almacen_destino'));

            //======= NOTA SALIDA ALMACÉN ORIGEN =======
            /*$nota_salida                    =   new NotaSalidad();
            $nota_salida->numero            =   '-';
            $nota_salida->fecha             =   Carbon::now()->format('Y-m-d');
            $nota_salida->origen            =   $almacen_origen_bd->descripcion;
            $nota_salida->destino           =   $almacen_destino_bd->descripcion;
            $nota_salida->observacion       =   'TRASLADO';
            $nota_salida->usuario           =   Auth::user()->usuario;
            $nota_salida->almacen_origen_id =   $request->get('almacen_origen');
            $nota_salida->almacen_destino_id=   $request->get('almacen_destino');
            $nota_salida->sede_id           =   $request->get('sede_id');
            $nota_salida->save();

            //======= NOTA INGRESO ALMACÉN DESTINO =========
            $nota_ingreso                       =   new NotaIngreso();
            $nota_ingreso->numero               =   '-';
            $nota_ingreso->fecha                =   Carbon::now()->format('Y-m-d');
            $nota_ingreso->origen               =   'TRASLADO';
            $nota_ingreso->almacen_destino_id   =   $request->get('almacen_destino');
            $nota_ingreso->usuario              =   Auth::user()->usuario;
            $nota_ingreso->observacion          =   'TRASLADO';
            $nota_ingreso->sede_id              =   $sede_destino[0]->sede_id;
            $nota_ingreso->save();  */

            //======== GRABANDO DETALLE DEL TRASLADO =====
            foreach ($detalle as $item) {

                //====== VALIDANDO EXISTENCIA DEL ITEM ======
                $item_bd_origen     =   UtilidadesController::validarItem($item,$request->get('almacen_origen'));

                //====== VALIDANDO STOCK DEL ITEM =======
                $stock              =   UtilidadesController::getStockItem($item_bd_origen);
                if (!is_numeric($item->cantidad)) {
                    throw new Exception("LA CANTIDAD DE LOS ITEMS DEBE SER NUMÉRICA!!!");
                }
                if((int)$stock < (int)$item->cantidad){
                    throw new Exception("LA CANTIDAD DE LOS ITEMS DEBE SER MENOR O IGUAL AL STOCK!!!");
                }

                //======= DETALLE TRASLADO =======
                $traslado_detalle                   =   new TrasladoDetalle();
                $traslado_detalle->traslado_id      =   $traslado->id;
                $traslado_detalle->producto_id      =   $item_bd_origen->producto_id;
                $traslado_detalle->color_id         =   $item_bd_origen->color_id;
                $traslado_detalle->talla_id         =   $item_bd_origen->talla_id;
                $traslado_detalle->almacen_id       =   $request->get('almacen_origen');
                $traslado_detalle->almacen_nombre   =   $item_bd_origen->almacen_nombre;
                $traslado_detalle->producto_nombre  =   $item_bd_origen->producto_nombre;
                $traslado_detalle->color_nombre     =   $item_bd_origen->color_nombre;
                $traslado_detalle->talla_nombre     =   $item_bd_origen->talla_nombre;
                $traslado_detalle->cantidad         =   $item->cantidad;
                $traslado->estado                   =   'PENDIENTE';
                $traslado_detalle->save();

                //======== DETALLE NOTA SALIDA =====
               /* DB::insert('INSERT INTO detalle_nota_salidad (nota_salidad_id, producto_id, cantidad, created_at, updated_at, color_id, talla_id) VALUES (?, ?, ?, ?, ?, ?, ?)', [
                    $nota_salida->id, 
                    $item->producto_id, 
                    $item->cantidad, 
                    Carbon::now(), 
                    Carbon::now(), 
                    $item->color_id, 
                    $item->talla_id, 
                ]);

                //======= DETALLE NOTA DE INGRESO ===========
                DB::insert('INSERT INTO detalle_nota_ingreso (nota_ingreso_id, producto_id, color_id, talla_id, cantidad, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)', [
                    $nota_ingreso->id, 
                    $item->producto_id, 
                    $item->color_id, 
                    $item->talla_id,
                    $item->cantidad, 
                    Carbon::now(), 
                    Carbon::now()
                ]);*/

            //======== EN ALMACÉN ORIGEN (BAJA EL STOCK) =========
                //=======> RESTAR STOCK ======
                UtilidadesController::restarStockItem($item_bd_origen,$item->cantidad);

                //======> OBTENIENDO STOCK =======
                $stock          =   UtilidadesController::getStockItem($item_bd_origen);

                //=======> GUARDAR EN KARDEX ======
                $kardex                     =   new Kardex();
                $kardex->sede_id            =   $request->get('sede_id');
                $kardex->almacen_id         =   $request->get('almacen_origen');
                $kardex->producto_id        =   $item_bd_origen->producto_id;
                $kardex->color_id           =   $item_bd_origen->color_id;
                $kardex->talla_id           =   $item_bd_origen->talla_id;
                $kardex->almacen_nombre     =   $almacen_origen_bd->descripcion;
                $kardex->producto_nombre    =   $item_bd_origen->producto_nombre;
                $kardex->color_nombre       =   $item_bd_origen->color_nombre;
                $kardex->talla_nombre       =   $item_bd_origen->talla_nombre;
                $kardex->cantidad           =   $item->cantidad;
                $kardex->precio             =   null;
                $kardex->importe            =   null;
                $kardex->accion             =   'TRASLADO SALIDA';
                $kardex->stock              =   $stock;
                $kardex->numero_doc         =   'TR-'.$traslado->id;
                $kardex->documento_id       =   $traslado->id;
                $kardex->registrador_id     =   Auth::user()->id;
                $kardex->registrador_nombre =   Auth::user()->usuario;
                $kardex->fecha              =   Carbon::today()->toDateString();
                $kardex->descripcion        =   mb_strtoupper("TRASLADO SALIDA", 'UTF-8');
                $kardex->save();

            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'TRASLADO REGISTRADO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
    }

    public function sumarStockDestino($item,$almacen_destino_id){

        //====== COMPROBANDO SI EXISTE EL PRODUCTO COLOR EN EL DESTINO =====
        $existeTalla    =   ProductoColorTalla::where('producto_id', $item->producto_id)
                            ->where('color_id', $item->color_id)
                            ->where('talla_id', $item->talla_id)
                            ->where('almacen_id',$almacen_destino_id)
                            ->exists();

        //====== EN CASO NO EXISTA LA TALLA - NO EXISTE EL COLOR ========
        if(!$existeTalla){

            $producto_color                 =   new ProductoColor();
            $producto_color->producto_id    =   $item->producto_id;
            $producto_color->color_id       =   $item->color_id;
            $producto_color->almacen_id     =   $almacen_destino_id;  
            $producto_color->save();  
            
            $pct_destino                =   new ProductoColorTalla();
            $pct_destino->producto_id   =   $item->producto_id;
            $pct_destino->color_id      =   $item->color_id;
            $pct_destino->talla_id      =   $item->talla_id;
            $pct_destino->almacen_id    =   $almacen_destino_id;
            $pct_destino->stock         =   $item->cantidad;
            $pct_destino->stock_logico  =   $item->cantidad;
            $pct_destino->save();

        }else{

            //======= INCREMENTAMOS STOCK ======
            ProductoColorTalla::where('producto_id', $item->producto_id)
            ->where('color_id', $item->color_id)
            ->where('talla_id', $item->talla_id)
            ->where('almacen_id', $almacen_destino_id)
            ->update([
                 'stock'         =>  DB::raw("stock + $item->cantidad"),
                 'stock_logico'  =>  DB::raw("stock_logico + $item->cantidad"),
                 'estado'        =>  '1',  
            ]);

        }

    }

    public function getKardexData($traslado,$item_bd,$request,$cantidad,$stock){
        $kardex_datos   =   (object)[
                            'producto_id'                   =>  $item_bd->producto_id,
                            'color_id'                      =>  $item_bd->color_id,
                            'talla_id'                      =>  $item_bd->talla_id,
                            'origen'                        =>  'TRASLADO',
                            'numero_doc'                    =>  'TRASLADO-'.$traslado->id,  
                            'fecha'                         =>  Carbon::now()->format('Y-m-d'),
                            'cantidad'                      =>  $cantidad,
                            'descripcion'                   =>  Auth::user()->usuario,
                            'precio'                        =>  null,
                            'importe'                       =>  null,
                            'stock'                         =>  $stock,
                            'documento_id'                  =>  $traslado->id,
                            'sede_id'                       =>  $request->get('sede_id'),
                            'almacen_id'                    =>  $item_bd->almacen_id,
                            'usuario_registrador_id'        =>  Auth::user()->id,
                            'usuario_registrador_nombre'    =>  Auth::user()->usuario,
                            'producto_nombre'               =>  $item_bd->producto_nombre,
                            'color_nombre'                  =>  $item_bd->color_nombre,
                            'talla_nombre'                  =>  $item_bd->talla_nombre,
                            'almacen_nombre'                =>  $item_bd->almacen_nombre 
                            ];
        return $kardex_datos;
    }

    public function show($traslado_id){
        $detalle            =   TrasladoDetalle::where('traslado_id',$traslado_id)->get();
        $traslado           =   Traslado::find($traslado_id);
        $tallas             =   Talla::where('estado','ACTIVO')->get();
        $almacen_origen     =   Almacen::find($traslado->almacen_origen_id);
        $almacen_destino    =   Almacen::find($traslado->almacen_destino_id);

        return view('almacenes.traslados.show',
        compact('detalle','traslado','tallas','almacen_origen','almacen_destino'));
    }

    
    public function generarGuiaCreate($traslado_id){

        $traslado           =   Traslado::find($traslado_id);   
        $almacen_traslado   =   Almacen::find($traslado->almacen_origen_id);
        $traslado_detalle   =   $this->formatearArrayDetalle(TrasladoDetalle::where('traslado_id',$traslado_id)->get());

        $sede_id            =   Auth::user()->sede_id;
        $sede_origen        =   Sede::find($traslado->sede_origen_id);
        $sede_destino       =   Sede::find($traslado->sede_destino_id);

        $almacenes          =   Almacen::where('estado','ACTIVO')
                                ->where('tipo_almacen','PRINCIPAL')
                                ->where('sede_id',$sede_id)
                                ->get();


        $registrador        =   User::find(Auth::user()->id);
        $modelos            =   Modelo::where('estado','ACTIVO')->get();
        $tallas             =   Talla::where('estado','ACTIVO')->get();
        $empresas           =   Empresa::where('estado','ACTIVO')->get();
        $conductores        =   Conductor::where('estado','ACTIVO')->get();
        $vehiculos          =   Vehiculo::where('estado','ACTIVO')->get();
      
        $clientes           =   Cliente::where('estado', 'ACTIVO')->get();
        $tipos_documento    =   DB::select('select 
                                td.* 
                                from tabladetalles as td 
                                where td.tabla_id = 3');

        $sedes              =   Sede::where('estado','ACTIVO')
                                ->where('id','<>',$sede_id)
                                ->get();

        $motivos_traslado   =   DB::select('select 
                                td.*
                                from tabladetalles as td
                                where 
                                td.tabla_id = 34
                                AND td.simbolo IN ("01","04")');
                  

        return view('almacenes.traslados.guia.create',[

            'sede_origen'       =>  $sede_origen,
            'sede_destino'      =>  $sede_destino,
            'motivos_traslado'  =>  $motivos_traslado,
            'sede_id'           =>  $sede_id,
            'almacenes'         =>  $almacenes,
            'registrador'       =>  $registrador,
            'modelos'           =>  $modelos,
            'tallas'            =>  $tallas,
            'empresas'          =>  $empresas,
            'conductores'       =>  $conductores,
            'sedes'             =>  $sedes,
            'vehiculos'         =>  $vehiculos,
            'clientes'          =>  $clientes,
            'tipos_documento'   =>  $tipos_documento,
            'traslado'          =>  $traslado,
            'almacen_traslado'  =>  $almacen_traslado,
            'traslado_detalle'  =>  $traslado_detalle
        ]);
    }

/*
array:17 [
  "traslado" => "TR-3"
  "registrador" => "ADMINISTRADOR"
  "fecha_emision" => "2025-02-27"
  "modalidad_traslado" => "02"
  "fecha_traslado" => "2025-02-27"
  "peso" => "0.1"
  "unidad" => "KGM"
  "vehiculo" => "1"
  "conductor" => "4"
  "sede_origen" => "SEDE CENTRAL"
  "sede_destino" => "SEDE CHACHAPOYAS"
  "cliente" => "1"
  "sede_id" => "1"
  "registrador_id" => "1"
  "traslado_id" => "3"
  "almacen"         => "1"
  "motivo_traslado" => "04"
]
*/ 
    public function generarGuiaStore(GuiaStoreRequest $request){

        $lstGuia    =   $this->formatearArrayDetalle(TrasladoDetalle::where('traslado_id',$request->get('traslado_id'))->get());
        $traslado   =   Traslado::find($request->get('traslado_id'));

        $request->merge([
            'lstGuia'       =>  json_encode($lstGuia),  
            'sede_genera_guia'  =>  $request->get('sede_id'),
            'sede_usa_guia'     =>  $request->get('sede_id'),
            'sede_origen'   =>  $traslado->sede_origen_id,
            'sede_destino'  =>  $traslado->sede_destino_id,
            'traslado'      =>  $traslado->id
        ]);
        
        $guia_controller        =   new GuiaController();
        $res                    =   $guia_controller->store($request);
        $jsonResponse           =   $res->getData(); 

        if(!$jsonResponse->success){
            return $res;
        }
        
        DB::beginTransaction();
        try {

            $traslado->guia_id  =   $jsonResponse->id;
            $traslado->update();   

            DB::commit();

            return $res;
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
       
    }

    public function formatearArrayDetalle($detalles){
        $detalleFormateado=[];
        $productosProcesados = [];
        foreach ($detalles as $detalle) {
            $cod   =   $detalle->producto_id.'-'.$detalle->color_id;
            if (!in_array($cod, $productosProcesados)) {
                $producto=[];
                //======== obteniendo todas las detalle talla de ese producto_color =================
                $producto_color_tallas = $detalles->filter(function ($detalleFiltro) use ($detalle) {
                    return $detalleFiltro->producto_id == $detalle->producto_id && $detalleFiltro->color_id == $detalle->color_id;
                });
                
                $producto['producto_id']        = $detalle->producto_id;
                $producto['color_id']           = $detalle->color_id;
                $producto['producto_nombre']    = $detalle->producto_nombre;
                $producto['color_nombre']       = $detalle->color_nombre;
                

                $tallas=[];
                $subtotal=0.0;
                $cantidadTotal=0;
                foreach ($producto_color_tallas as $producto_color_talla) {
                    $talla=[];
                    $talla['talla_id']      =   $producto_color_talla->talla_id;
                    $talla['cantidad']      =   $producto_color_talla->cantidad;
                    $talla['talla_nombre']  =   $producto_color_talla->talla_nombre;
                    $cantidadTotal          +=  $talla['cantidad'];
                   array_push($tallas,$talla);
                }
                
                $producto['tallas']=$tallas;
                $producto['subtotal']=$subtotal;
                $producto['cantidad_total']=$cantidadTotal;
                array_push($detalleFormateado,$producto);
                $productosProcesados[] = $detalle->producto_id.'-'.$detalle->color_id;
            }
        }
        return $detalleFormateado;
    }
   

}
