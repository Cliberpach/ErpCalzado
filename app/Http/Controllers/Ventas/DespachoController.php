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

class DespachoController extends Controller
{
    public function index(){
        $this->authorize('haveaccess','despachos.index');

        $clientes       =   DB::select('select c.id,c.nombre from clientes as c');
        
        return view('ventas.despachos.index',compact('clientes'));
    }

    public function getTable(Request $request)
    {
        $fecha_inicio   = $request->get('fecha_inicio');
        $fecha_fin      = $request->get('fecha_fin');
    
        $envios_ventas = EnvioVenta::orderBy('id', 'desc');
    
        if ($fecha_inicio) {
            $envios_ventas = $envios_ventas->whereDate('created_at', '>=', $fecha_inicio);
        } 
        if ($fecha_fin) {
            $envios_ventas = $envios_ventas->whereDate('created_at', '<=', $fecha_fin);
        }
    
        $envios_ventas = $envios_ventas->get();
        
        $coleccion = collect([]);
        foreach($envios_ventas as $envio_venta) {
            $coleccion->push([
                'id'                        =>  $envio_venta->id,
                'documento_nro'             =>  $envio_venta->documento_nro,
                'cliente_nombre'            =>  $envio_venta->cliente_nombre,
                'cliente_celular'           =>  $envio_venta->cliente_celular,
                'user_vendedor_nombre'      =>  $envio_venta->user_vendedor_nombre,
                'user_despachador_nombre'   =>  $envio_venta->user_despachador_nombre,
                'fecha_envio_propuesta' =>  $envio_venta->fecha_envio_propuesta,
                'fecha_envio'           =>  $envio_venta->fecha_envio?$envio_venta->fecha_envio:"-",
                'fecha_registro'        =>  Carbon::parse($envio_venta->created_at)->format('Y-m-d H:i:s'),
                'tipo_envio'            =>  $envio_venta->tipo_envio,
                'empresa_envio_nombre'  =>  $envio_venta->empresa_envio_nombre,
                'sede_envio_nombre'     =>  $envio_venta->sede_envio_nombre,
                'ubigeo'                =>  $envio_venta->departamento.' - '.$envio_venta->provincia.' - '.$envio_venta->distrito,
                'tipo_pago_envio'       =>  $envio_venta->tipo_pago_envio,
                'destinatario_nombre'   =>  $envio_venta->destinatario_nombre,
                'destinatario_nro_doc'  =>  $envio_venta->destinatario_tipo_doc.': '.$envio_venta->destinatario_nro_doc,
                'monto_envio'           =>  $envio_venta->monto_envio,
                'entrega_domicilio'     =>  $envio_venta->entrega_domicilio,
                'direccion_entrega'     =>  $envio_venta->direccion_entrega,
                'estado'                =>  $envio_venta->estado,
                'documento_id'          =>  $envio_venta->documento_id,
                'obs_despacho'          =>  $envio_venta->obs_despacho
                ]);
        }
        return DataTables::of($coleccion)->toJson();
    }


    public function showDetalles($documento_id){
        try {
            $detalles_doc_venta     =   DB::select('select * from cotizacion_documento_detalles as cdd
                                        where cdd.documento_id=?',[$documento_id]);
            
            return response()->json(['success'=>true,'detalles_doc_venta'=>$detalles_doc_venta]);
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
            ->update(['estado' => 'EMBALADO']);

            DB::commit();

            return response()->json(['success'=>true,'message'=>"ENVÍO EMBALADO"]);

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

            //======= REVIZAR SI EL DOCUMENTO ESTÁ LIGADO A UN PEDIDO ATENCIÓN =======
            $pedido_atencion    =   DB::select('select pa.pedido_id 
                                    from pedidos_atenciones as pa 
                                    where pa.documento_id = ?',[$request->get('documento_id')]);

            //========== EN CASO EL DOCUMENTO SEA PRODUCTO DE UNA ATENCIÓN DE PEDIDO ========
            if(count($pedido_atencion) === 1){

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
        } catch (\Throwable $th) {
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
