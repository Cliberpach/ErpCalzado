<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ventas\EnvioVenta;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use App\Mantenimiento\Empresa\Empresa;
use Illuminate\Support\Facades\Auth;
use Throwable;

class DespachoController extends Controller
{
    public function index(){
        $this->authorize('haveaccess','despachos.index');

        $cliente_varios       =   DB::select('select c.id,c.nombre from clientes as c where c.id = 1');

        return view('ventas.despachos.index',compact('cliente_varios'));
    }



    public function getTable(Request $request)
    {
        $fecha_inicio   =   $request->get('fecha_inicio');
        $fecha_fin      =   $request->get('fecha_fin');
        $estado         =   $request->get('estado');
        $cliente_id     =   $request->get('cliente_id');


        $query  =   DB::table('envios_ventas as ev')
                    ->select(
                        'eso.nombre as sede_origen_nombre',
                        'es.nombre as sede_despachadora_nombre',
                        'ev.id',
                        'ev.documento_nro',
                        'ev.cliente_nombre',
                        'ev.cliente_celular',
                        'ev.user_vendedor_nombre',
                        'ev.almacen_nombre',
                        'ev.user_despachador_nombre',
                        'ev.fecha_envio_propuesta',
                        DB::raw("IFNULL(ev.fecha_envio, '-') AS fecha_envio"),
                        DB::raw("DATE_FORMAT(ev.created_at, '%Y-%m-%d %H:%i:%s') AS fecha_registro"),
                        'ev.tipo_envio',
                        'ev.empresa_envio_nombre',
                        'ev.sede_envio_nombre',
                        DB::raw("CONCAT(ev.departamento, ' - ', ev.provincia, ' - ', ev.distrito) AS ubigeo"),
                        'ev.tipo_pago_envio',
                        'ev.destinatario_nombre',
                        DB::raw("CONCAT(ev.destinatario_tipo_doc, ': ', ev.destinatario_nro_doc) AS destinatario_nro_doc"),
                        'ev.monto_envio',
                        'ev.entrega_domicilio',
                        'ev.direccion_entrega',
                        'ev.estado',
                        'ev.documento_id',
                        'ev.obs_despacho',
                        'ev.modo'
                    )
                    ->join('empresa_sedes as es','es.id','ev.sede_despachadora_id')
                    ->join('empresa_sedes as eso','eso.id','ev.sede_id')
                    ->orderByDesc('id');

        if ($fecha_inicio) {
            $query->whereDate('ev.created_at', '>=', $fecha_inicio);
        }
        if ($fecha_fin) {
            $query->whereDate('ev.created_at', '<=', $fecha_fin);
        }
        if ($estado) {
            $query->where('ev.estado', '=', $estado);
        }
        if ($cliente_id) {
            $query->where('ev.cliente_id', '=', $cliente_id);
        }

        //========= FILTRO POR ROLES ======
        $roles = DB::table('role_user as rl')
                ->join('roles as r', 'r.id', '=', 'rl.role_id')
                ->where('rl.user_id', Auth::user()->id)
                ->pluck('r.name')
                ->toArray();

        //======== ADMIN PUEDE VER TODOS LOS DESPACHOS DE SU SEDE =====
        if (in_array('ADMIN', $roles)) {

            $query->where('ev.sede_despachadora_id', Auth::user()->sede_id);

        } else {

            //====== USUARIOS PUEDEN VER SUS PROPIOS DESPACHOS ======
            $query->where('ev.sede_despachadora_id', Auth::user()->sede_id);

        }

        return DataTables::of($query)->make(true);
    }


    public function showDetalles($documento_id){
        try {

            $documento              =   DB::select('
                                        SELECT
                                            cd.serie,
                                            cd.correlativo,
                                            cd.almacen_nombre as almacen_despacho,
                                            es.nombre as sede_despacho
                                        FROM cotizacion_documento as cd
                                        JOIN almacenes as a on a.id = cd.almacen_id
                                        JOIN empresa_sedes as es on es.id = a.sede_id
                                            WHERE cd.id = ?
                                        ',[$documento_id])[0];

            $detalles_doc_venta     =   DB::select('select
                                        cdd.nombre_producto,
                                        cdd.nombre_color,
                                        cdd.nombre_talla,
                                        cdd.nombre_modelo,
                                        cdd.cantidad,
                                        IFNULL(cdd.cantidad_cambiada, 0) AS cantidad_cambiada,
                                        cdd.cantidad_sin_cambio
                                        from cotizacion_documento_detalles as cdd
                                        where cdd.documento_id=?',[$documento_id]);

            return response()->json(['success'=>true,'detalles_doc_venta'=>$detalles_doc_venta,'documento'=>$documento]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>"ERROR EN EL SERVIDOR",'exception'=>$th->getMessage()]);
        }
    }

    public function pdfBultos($documento_id, $despacho_id, $nro_bultos)
    {
        set_time_limit(300);
        $empresa = Empresa::first();

        $despacho = DB::select('SELECT ev.distrito, ev.destinatario_nombre, ev.documento_nro,ev.cliente_nombre,
                        ev.destinatario_tipo_doc,ev.destinatario_nro_doc, ev.cliente_celular, ev.entrega_domicilio,
                        ev.direccion_entrega,ev.created_at,ev.empresa_envio_nombre,ev.tipo_pago_envio,ev.obs_rotulo
                        FROM envios_ventas AS ev
                        WHERE ev.id=? AND ev.documento_id=?', [$despacho_id, $documento_id]);

        $pdf = PDF::loadview('ventas.despachos.pdf-bultos.pdf2', [
            'empresa'       =>  $empresa,
            'nro_bultos'    =>  $nro_bultos,
            'despacho'      =>  $despacho[0]
        ])->setPaper('a4')
        ->setPaper('a4', 'landscape')
        ->setWarnings(false);

        return $pdf->stream($despacho[0]->distrito.'-'.$despacho[0]->cliente_nombre.'-'.$despacho[0]->created_at.'.pdf');
    }




    public function setEmbalaje(Request $request){

        try {
            DB::beginTransaction();
            //======= ACTUALIZANDO DESPACHO ========
            DB::table('envios_ventas')
            ->where('id', $request->get('despacho_id'))
            ->where('documento_id', $request->get('documento_id'))
            ->update(['estado' => 'RESERVADO']);

            DB::commit();

            return response()->json(['success'=>true,'message'=>"ENVÍO RESERVADO"]);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['success'=>false,'message'=>"ERROR EN EL SERVIDOR",
            'exception'=>$th->getMessage()]);
        }

    }


    public function setDespacho(Request $request){

        try {
            DB::beginTransaction();
            $fecha_actual = Carbon::now()->format('Ymd');

            //======= ACTUALIZANDO DESPACHO ========
            DB::table('envios_ventas')
            ->where('id', $request->get('despacho_id'))
            ->where('documento_id', $request->get('documento_id'))
            ->update(['estado'          => 'DESPACHADO',
            'fecha_envio'               =>  $fecha_actual,
            'user_despachador_id'       =>  Auth::user()->id,
            'user_despachador_nombre'   =>  Auth::user()->usuario]);

            //======= REVIZAR SI EL DOCUMENTO ESTÁ LIGADO A UN PEDIDO =======
            $pedido_atencion    =   DB::select('select cd.pedido_id
                                    from cotizacion_documento as cd
                                    where cd.id = ?',[$request->get('documento_id')]);

            //========== EN CASO EL DOCUMENTO SEA PRODUCTO DE UNA ATENCIÓN DE PEDIDO ========
            if(count($pedido_atencion) === 1){
                if($pedido_atencion[0]->pedido_id){
                     //======= OBTENER DETALLE DEL DOCUMENTO ======
                    $doc_detalles   =   DB::select('select * from cotizacion_documento_detalles as cdd
                    where cdd.documento_id = ?',[$request->get('documento_id')]);

                    //===== RECORRER EL DETALLE DEL DOCUMENTO DE VENTA =====
                    foreach($doc_detalles as $item){

                    //===== ACTUALIZAR CANTIDADES ENVIADAS DEL PEDIDO ASOCIADO AL DOC VENTA ======
                    DB::update('UPDATE pedidos_detalles
                    SET cantidad_enviada = cantidad_enviada + ?
                    WHERE pedido_id = ? and producto_id = ? and color_id = ? and talla_id = ?',
                    [$item->cantidad,
                    $pedido_atencion[0]->pedido_id,
                    $item->producto_id,
                    $item->color_id,
                    $item->talla_id]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>"ENVÍO DESPACHADO"]);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['success'=>false,'message'=>"ERROR EN EL SERVIDOR",
            'exception'=>$th->getMessage()]);
        }

    }


    public function getDespacho($documento_id){
        try {
            $despacho   =   EnvioVenta::where('documento_id',$documento_id)->get();
            return response()->json(['success'=>true,'despacho'=>$despacho]);
        } catch (Throwable $th) {
            return response()->json(['success'=>false,'message'=>"ERROR EN EL SERVIDOR",'exception'=>$th->getMessage()]);
        }
    }

    public function updateDespacho(Request $request){
        try {
            DB::beginTransaction();
            //======== OBTENER EL DOCUMENTO ID =======
            $jsonData       = $request->getContent();

            $data_envio     = json_decode($jsonData);

            $documento_id                       =   $data_envio->documento_id;

            //====== ACTUALIZAR DESPACHO ========
            $envio_venta                                =   EnvioVenta::where('documento_id', $documento_id)->first();
            $envio_venta->departamento              =   $data_envio->departamento->nombre;
            $envio_venta->provincia                 =   $data_envio->provincia->text;
            $envio_venta->distrito                  =   $data_envio->distrito->text;
            $envio_venta->empresa_envio_id          =   $data_envio->empresa_envio->id;
            $envio_venta->empresa_envio_nombre      =   $data_envio->empresa_envio->empresa;
            $envio_venta->sede_envio_id             =   $data_envio->sede_envio->id;
            $envio_venta->sede_envio_nombre         =   $data_envio->sede_envio->direccion;
            $envio_venta->tipo_envio                =   $data_envio->tipo_envio->descripcion;
            $envio_venta->destinatario_tipo_doc     =   $data_envio->destinatario->tipo_documento;
            $envio_venta->destinatario_nro_doc      =   $data_envio->destinatario->nro_documento;
            $envio_venta->destinatario_nombre       =   $data_envio->destinatario->nombres;
            $envio_venta->tipo_pago_envio           =   $data_envio->tipo_pago_envio->descripcion;
            $envio_venta->entrega_domicilio         =   $data_envio->entrega_domicilio?"SI":"NO";
            $envio_venta->direccion_entrega         =   $data_envio->direccion_entrega;
            $envio_venta->fecha_envio_propuesta     =   $data_envio->fecha_envio_propuesta;
            $envio_venta->origen_venta              =   $data_envio->origen_venta->descripcion;
            $envio_venta->obs_rotulo                =   $data_envio->obs_rotulo;
            $envio_venta->obs_despacho              =   $data_envio->obs_despacho;
            $envio_venta->update();

            DB::commit();

            return response()->json(['success'=>true,'formEnvio'=> $data_envio ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['success'=>false,'exception'=>$th->getMessage()]);

        }
    }
}
