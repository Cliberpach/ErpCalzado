<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use App\Pedidos\OrdenProduccion;
use App\Pedidos\OrdenPedidoDetalle;
use App\Pedidos\OrdenProduccionDetalle;
use Illuminate\Http\Request;
use App\Ventas\PedidoDetalle;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use App\Mantenimiento\Empresa\Empresa;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Pedido\Detalles\PedidoDetalleExport;

class DetalleController extends Controller
{
    public function index(){
        $clientes   =   DB::select('select c.id,c.tipo_documento,c.documento,c.nombre 
                        from clientes as c where c.estado = "ACTIVO"');

        $modelos    =   DB::select('select m.id,m.descripcion from modelos as m where m.estado = "ACTIVO"');


        $tallas     =   DB::select('select t.id,t.descripcion from tallas as t where t.estado = "ACTIVO"');

        return view('pedidos.detalles.index',compact('clientes','modelos','tallas'));
    }

    public function getTable(Request $request){
        $pedido_detalle_estado  = $request->input('pedido_detalle_estado');
        $cliente_id             = $request->input('cliente_id');
        $modelo_id              = $request->input('modelo_id');
        $producto_id            = $request->input('producto_id');


        $pedidos_detalles = PedidoDetalle::from('pedidos_detalles as pd')
                            ->select(
                                'p.id as pedido_id',
                                'p.created_at as pedido_fecha',
                                'p.cliente_id',
                                'p.cliente_nombre',
                                'p.user_nombre as vendedor_nombre',
                                'prod.modelo_id',
                                'pd.producto_id',
                                'pd.color_id',
                                'pd.talla_id',
                                'm.descripcion as modelo_nombre',
                                'pd.producto_nombre',
                                'pd.color_nombre',
                                'pd.talla_nombre',
                                'pd.cantidad',
                                'pd.precio_unitario_nuevo',
                                'pd.importe_nuevo',
                                'pd.cantidad_atendida',
                                'pd.cantidad_pendiente',
                                'pd.cantidad_enviada',
                                'pd.cantidad_devuelta',
                                \DB::raw('concat("PE-", p.id) as pedido_name_id')
                            )
                            ->join('pedidos as p', 'pd.pedido_id', '=', 'p.id')
                            ->join('productos as prod', 'prod.id', '=', 'pd.producto_id')
                            ->join('modelos as m', 'm.id', '=', 'prod.modelo_id');
                            

        // Aplicar el filtro de estado si se proporciona
        if (!empty($pedido_detalle_estado)) {
            if($pedido_detalle_estado === "PENDIENTE"){
                $pedidos_detalles->where('pd.cantidad_pendiente', '>', 0)
                ->where('p.estado','!=','FINALIZADO');
            }
            if($pedido_detalle_estado === "ATENDIDO"){
                $pedidos_detalles->where('pd.cantidad_pendiente', '=', 0);
            }
        }

        if (!empty($cliente_id)) {
            $pedidos_detalles->where('p.cliente_id', $cliente_id);
        }

        if (!empty($modelo_id)) {
            $pedidos_detalles->where('prod.modelo_id', $modelo_id);
        }

        if (!empty($producto_id)) {
            $pedidos_detalles->where('pd.producto_id', $producto_id);
        }

        // Ordenar y obtener los resultados
        $pedidos_detalles = $pedidos_detalles->orderBy('p.id', 'desc')->get();

        // Retornar los resultados como JSON para DataTable
        return DataTables::of($pedidos_detalles)->toJson();
    }

    public function getDetallesAtenciones($pedido_id,$producto_id,$color_id,$talla_id){
        try {
            $documentos_antenciones =   DB::select('select cd.id as documento_id,cd.serie,cd.correlativo,cd.cliente,
                                        cd.created_at,cdd.producto_id,cdd.color_id,cdd.talla_id,cdd.cantidad,
                                        u.usuario 
                                        from  cotizacion_documento as cd 
                                        inner join cotizacion_documento_detalles as cdd on cdd.documento_id = cd.id
                                        inner join users as u on u.id = cd.user_id
                                        where cd.pedido_id = ? and 
                                        cdd.producto_id = ? and
                                        cdd.color_id = ?  and
                                        cdd.talla_id = ? and 
                                        cd.tipo_doc_venta_pedido = "ATENCION"',[$pedido_id,$producto_id,$color_id,$talla_id]);
            
            //dd($documentos_antenciones);

            return response()->json(['success'=>true,'documentos_atenciones'=>$documentos_antenciones]);
            
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getDetallesDespachos($pedido_id,$producto_id,$color_id,$talla_id){
        try {
            $despachos  =   DB::select('select cd.id as documento_id,cd.serie,cd.correlativo,cd.cliente,
                                        cd.created_at as fecha_venta,cdd.producto_id,cdd.color_id,
                                        cdd.talla_id,cdd.cantidad,u.usuario,ev.user_despachador_nombre,
                                        ev.fecha_envio as fecha_despacho,ev.estado as estado_despacho
                                        from  cotizacion_documento as cd 
                                        inner join cotizacion_documento_detalles as cdd on cdd.documento_id = cd.id
                                        inner join users as u on u.id = cd.user_id
                                        inner join envios_ventas as ev on ev.documento_id = cd.id
                                        where cd.pedido_id = ? and 
                                        cdd.producto_id = ? and
                                        cdd.color_id = ?  and
                                        cdd.talla_id = ? ',[$pedido_id,$producto_id,$color_id,$talla_id]);
            

            return response()->json(['success'=>true,'despachos'=>$despachos]);
            
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getDetallesDevoluciones($pedido_id,$producto_id,$color_id,$talla_id){
        try {
            $notas_devoluciones  =   DB::select('select ne.numDocfectado as documento_afectado,ne.serie,ne.correlativo,
                                        cdd.producto_id,cdd.color_id,cdd.talla_id,
                                        u.usuario,ne.created_at as fecha, ned.cantidad as cantidad_devuelta,
                                        cd.pedido_id
                                        from  cotizacion_documento as cd 
                                        inner join cotizacion_documento_detalles as cdd on cdd.documento_id = cd.id 
                                        inner join nota_electronica as ne on ne.documento_id = cdd.documento_id
                                        inner join nota_electronica_detalle as ned on ned.nota_id = ne.id
                                        inner join users as u on u.id = ne.user_id
                                        where cd.pedido_id = ? and 
                                        cdd.producto_id = ? and
                                        cdd.color_id = ?  and
                                        cdd.talla_id = ? and
                                        ned.producto_id = ? and 
                                        ned.color_id = ? and 
                                        ned.talla_id = ?',[$pedido_id,$producto_id,$color_id,$talla_id,
                                        $producto_id,$color_id,$talla_id]);

        
            return response()->json(['success'=>true,'devoluciones'=>$notas_devoluciones]);
            
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
            }

            if(count($despacho_doc) > 1){
                dd($despacho_doc);
            }
        }
        dd('CANTIDADES ENVIADAS LLENAS');
    }

    //======== PDF PROGRAMACIÓN PRODUCCIÓN =======
    public function pdfProgramacionProduccion(Request $request){
        try {
            $lstProgramacionProduccion  =   json_decode($request->get('lstProgramaProduccion'));
            $tallasBD                   =   DB::select('select t.id,t.descripcion from tallas as t
                                            where t.estado ="ACTIVO"');

            $empresa                    =   Empresa::first();

            $usuario_impresion_nombre   =   Auth::user()->usuario;
            $fecha_actual               =   Carbon::now();

            
            $pdf = PDF::loadview('pedidos.detalles.pdf.prog_produccion', [
                'lstProgramacionProduccion'     =>  $lstProgramacionProduccion,
                'tallasBD'                      =>  $tallasBD,
                'empresa'                       =>  $empresa,
                'usuario_impresion_nombre'      =>  $usuario_impresion_nombre,
                'fecha_actual'                =>  $fecha_actual
            ])->setPaper('a4', 'landscape')->setWarnings(false);
            
            return $pdf->stream('PROGRAMACIÓN_PRODUCCIÓN.pdf');

        } catch (\Throwable $th) {
            dd($th->getMessage());
        }

    }

    public function generarOrdenProduccion(Request $request){
        DB::beginTransaction();
 
        try {
            $lstProgramacionProduccion  =   json_decode($request->get('lstProgramacionProduccion'));
            
            //===== CREAMOS LA ORDEN DE PEDIDO CABEZERA ========
            $orden_produccion                           =   new OrdenProduccion();
            $orden_produccion->user_id                  =   Auth::user()->id;
            $orden_produccion->user_nombre              =   Auth::user()->usuario;
            $orden_produccion->fecha_propuesta_atencion =   $request->get('fecha_propuesta_atencion');
            $orden_produccion->observacion              =   $request->get('observacion');
            $orden_produccion->tipo                     =   "PEDIDO";
            $orden_produccion->save();

            //======= GUARDANDO DETALLES DE LA ORDEN DE PEDIDO =====
            foreach ($lstProgramacionProduccion as $producto) {
                foreach ($producto->colores as $color ) {
                    foreach ($color->tallas as $talla) {
                        $orden_produccion_detalle                       =   new OrdenProduccionDetalle();
                        $orden_produccion_detalle->orden_produccion_id  =   $orden_produccion->id;
                        $orden_produccion_detalle->modelo_id            =   $producto->modelo->id;
                        $orden_produccion_detalle->producto_id          =   $producto->id;
                        $orden_produccion_detalle->color_id             =   $color->id;
                        $orden_produccion_detalle->talla_id             =   $talla->id;
                        $orden_produccion_detalle->modelo_nombre        =   $producto->modelo->nombre;
                        $orden_produccion_detalle->producto_nombre      =   $producto->nombre;
                        $orden_produccion_detalle->color_nombre         =   $color->nombre;
                        $orden_produccion_detalle->talla_nombre         =   $talla->nombre;
                        $orden_produccion_detalle->cantidad             =   $talla->cantidad_pendiente;
                        $orden_produccion_detalle->save();                    
                    }  
                } 
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ORDEN DE PRODUCCIÓN N° '.$orden_produccion->id.' GENERADA CON ÉXITO']);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getExcel($pedido_detalle_estado = null, $cliente_id = null, $modelo_id = null, $producto_id = null)
    {
        return Excel::download(new PedidoDetalleExport($pedido_detalle_estado, $cliente_id, $modelo_id, $producto_id), 'pedido_detalles.xlsx');
    }

    public function getPdf($pedido_detalle_estado = null, $cliente_id = null, $modelo_id = null, $producto_id = null)
    {
        $empresa                    =   Empresa::first();
        $usuario_impresion_nombre   =   Auth::user()->usuario;
        $fecha_actual               =   Carbon::now();

        // Obtener los detalles de los pedidos según los filtros proporcionados
        $pedidos_detalles = PedidoDetalle::from('pedidos_detalles as pd')
                            ->select(
                                \DB::raw('concat("PE-", p.id) as pedido_id'),
                                'p.created_at as pedido_fecha',
                                'p.cliente_nombre',
                                'p.user_nombre as vendedor_nombre',
                                'pd.producto_nombre',
                                'pd.color_nombre',
                                'pd.talla_nombre',
                                'pd.cantidad',
                                'pd.precio_unitario_nuevo',
                                'pd.importe_nuevo',
                                'pd.cantidad_atendida',
                                'pd.cantidad_pendiente',
                                'pd.cantidad_enviada',
                                \DB::raw('"-" as cantidad_fabricacion'),
                                \DB::raw('"-" as cantidad_cambio'),
                                \DB::raw('"-" as cantidad_devolucion')
                            )
                            ->join('pedidos as p', 'pd.pedido_id', '=', 'p.id')
                            ->join('productos as prod', 'prod.id', '=', 'pd.producto_id')
                            ->join('modelos as m', 'm.id', '=', 'prod.modelo_id')
                            ->where('p.estado','!=','FINALIZADO');

        // Aplicar los filtros si se proporcionan
        if ($pedido_detalle_estado !== '-') {
            if($pedido_detalle_estado === "PENDIENTE"){
                $pedidos_detalles->where('pd.cantidad_pendiente', '>', 0);
            }
            if($pedido_detalle_estado === "ATENDIDO"){
                $pedidos_detalles->where('pd.cantidad_pendiente', '=', 0);
            }
        }

        if ($cliente_id !== '-') {
            $pedidos_detalles->where('p.cliente_id', $cliente_id);
        }

        if ($modelo_id !== '-') {
            $pedidos_detalles->where('prod.modelo_id', $modelo_id);
        }

        if ($producto_id !== '-') {
            $pedidos_detalles->where('pd.producto_id', $producto_id);
        }

        // Obtener los resultados
        $pedidos_detalles = $pedidos_detalles->orderBy('p.id', 'desc')->get();

        // Generar el PDF
        $pdf = Pdf::loadView('pedidos.detalles.pdf.prog_produccion', compact('pedidos_detalles', 'empresa', 'usuario_impresion_nombre', 'fecha_actual'))
              ->setPaper('a4', 'landscape'); 

        // Descargar el PDF
        return $pdf->download('pedidos_detalles.pdf');
    }
}
