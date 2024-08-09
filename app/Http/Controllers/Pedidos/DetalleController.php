<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ventas\PedidoDetalle;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class DetalleController extends Controller
{
    public function index(){
        return view('pedidos.detalles.index');
    }

    public function getTable(Request $request){
        $fecha_inicio   =   $request->get('fecha_inicio');
        $fecha_fin      =   $request->get('fecha_fin');

        $pedidos_detalles   =   PedidoDetalle::from('pedidos_detalles as pd')
                                ->select(
                                    'p.id as pedido_id','pd.producto_id','pd.color_id','pd.talla_id',
                                    'pd.producto_nombre','pd.color_nombre','pd.talla_nombre','pd.cantidad','pd.precio_unitario_nuevo',
                                    'pd.importe_nuevo','pd.cantidad_atendida','pd.cantidad_enviada'
                                )
                                ->join('pedidos as p', 'pd.pedido_id', '=', 'p.id')
                                ->orderBy('p.id','desc')
                                ->get();

        return DataTables::of($pedidos_detalles)->toJson();
    }

    public function getDetallesAtenciones($pedido_id,$producto_id,$color_id,$talla_id){
        try {
            $documentos_antenciones =   DB::select('select pa.documento_id,cd.serie,cd.correlativo,cd.cliente,
                                        cd.created_at,cdd.producto_id,cdd.color_id,cdd.talla_id,cdd.cantidad,
                                        u.usuario 
                                        from pedidos_atenciones as pa 
                                        inner join cotizacion_documento as cd on cd.id = pa.documento_id
                                        inner join cotizacion_documento_detalles as cdd on cdd.documento_id = cd.id
                                        inner join users as u on u.id = cd.user_id
                                        where pa.pedido_id = ? and 
                                        cdd.producto_id = ? and
                                        cdd.color_id = ?  and
                                        cdd.talla_id = ?',[$pedido_id,$producto_id,$color_id,$talla_id]);
            
            //dd($documentos_antenciones);

            return response()->json(['success'=>true,'documentos_atenciones'=>$documentos_antenciones]);
            
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getDetallesDespachos($pedido_id,$producto_id,$color_id,$talla_id){
        try {
            $despachos  =   DB::select('select pa.documento_id,cd.serie,cd.correlativo,cd.cliente,
                                        cd.created_at as fecha_venta,cdd.producto_id,cdd.color_id,
                                        cdd.talla_id,cdd.cantidad,u.usuario,ev.user_despachador_nombre,
                                        ev.fecha_envio as fecha_despacho,ev.estado as estado_despacho
                                        from pedidos_atenciones as pa 
                                        inner join cotizacion_documento as cd on cd.id = pa.documento_id
                                        inner join cotizacion_documento_detalles as cdd on cdd.documento_id = cd.id
                                        inner join users as u on u.id = cd.user_id
                                        inner join envios_ventas as ev on ev.documento_id = cd.id
                                        where pa.pedido_id = ? and 
                                        cdd.producto_id = ? and
                                        cdd.color_id = ?  and
                                        cdd.talla_id = ? ',[$pedido_id,$producto_id,$color_id,$talla_id]);
            

            return response()->json(['success'=>true,'despachos'=>$despachos]);
            
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function llenarCantEnviada(){
        $pedidos_atenciones =   DB::select('select * from pedidos_atenciones as pa');

        //======= RECORRER TODAS LAS ATENCIONES DE PEDIDOS ======
        foreach ($pedidos_atenciones as $pedido_atencion) {
            //====== REVIZAR SI EL DOC DE ATENCIÓN TIENE DESPACHO ======
            $despacho_doc   =   DB::select('select * from envios_ventas as ev
                                where ev.documento_id = ?',[$pedido_atencion->documento_id]);

            if(count($despacho_doc) === 1){
                $estado_despacho    =   $despacho_doc[0]->estado;
                if($estado_despacho === "DESPACHADO"){
                    //====== SI ESTÁ DESPACHADO =======
                    //======== OBTENER DETALLE DE ESE DOC ATENCIÓN =====
                    $detalles_doc   =   DB::select('select * from cotizacion_documento_detalles as cdd
                                        where cdd.documento_id = ?',[$pedido_atencion->documento_id]);

                    foreach ($detalles_doc as $item_detalle) {

                        DB::update('UPDATE pedidos_detalles 
                        SET cantidad_enviada = cantidad_enviada + ? 
                        WHERE pedido_id = ? and producto_id = ? and color_id = ? and talla_id = ?',
                        [$item_detalle->cantidad, 
                        $pedido_atencion->pedido_id,
                        $item_detalle->producto_id,
                        $item_detalle->color_id,
                        $item_detalle->talla_id]);

                    }
                     
                }
            }else{
                dd('error al buscar despachos');
            }
        }
        dd('CANTIDADES ENVIADAS LLENAS');
    }
}
