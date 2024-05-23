<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Ventas\Resumen;
use App\Ventas\DetalleResumen;

use App\Greenter\Utils\Util;
use Greenter\Model\Response\SummaryResult;
use Greenter\Model\Sale\Document;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Summary\SummaryPerception;
use Greenter\Ws\Services\SunatEndpoints;
use DateTime;
use Illuminate\Support\Facades\Response; 
use Yajra\DataTables\Facades\DataTables;


require __DIR__ . '/../../../../vendor/autoload.php';

class ResumenController extends Controller
{
    public function index(){
        //$resumenes = Resumen::orderByDesc('id')->get();

        return view('ventas.resumenes.index');
    }

    public function getResumenes(Request $request){
        $resumenes  =   DB::select('select * from resumenes as r');

        return DataTables::of($resumenes)->toJson();
    }

    public function getComprobantes($fechaComprobantes){
        //======= SELECCIONAMOS LOS COMPROBANTES DE LA FECHA RESPECTIVA ======
        //===== QUE NO SE ENCUENTREN REGISTRADOS EN NINGÚN DETALLE DE RESUMEN ========
        $comprobantes   =   DB::select('select cd.id as documento_id, cd.serie as documento_serie,
                            cd.correlativo as documento_correlativo, td.parametro as documento_moneda,
                            cd.total_pagar as documento_total,cd.total_igv as documento_igv,
                            cd.total as documento_subtotal,cd.documento_cliente as documento_doc_cliente
                            from cotizacion_documento as cd
                            inner join tabladetalles as td on td.id=cd.moneda
                            where cd.fecha_documento=? and cd.serie="B001" and sunat="0" 
                            and td.tabla_id=1
                            and cd.id NOT IN (
                                SELECT
                                    rd.documento_id
                                FROM
                                    resumenes_detalles AS rd
                            )',[$fechaComprobantes]);   

        return response()->json(['success'=>$comprobantes , 'fecha' => $fechaComprobantes]);
    }

    public function store(Request $request){
        try {
            //===== INICIAR TRANSACCIÓN =====
            DB::beginTransaction();

            //====== RECEPCIONANDO COMPROBANTES Y FECHA ======
            $comprobantes       =   json_decode($request->get('comprobantes'));
            $fecha_comprobantes =   json_decode($request->get('fecha_comprobantes'));

            //===== BUSCANDO CORRELATIVO DEL COMPROBANTE =====
            $correlativo    =   $this->getCorrelativo();

            //==== GUARDANDO RESUMEN EN LA BD ====
            $resumen                    =   new Resumen();
            $resumen->serie             =   'R001';
            $resumen->correlativo       =   $correlativo;
            $resumen->fecha_comprobantes  =   $fecha_comprobantes;
            $resumen->save();


            //===== GRABANDO DETALLE DEL RESUMEN ====
            foreach ($comprobantes as $comprobante) {
                $detalle_resumen                        =   new DetalleResumen();
                $detalle_resumen->resumen_id            =   $resumen->id;
                $detalle_resumen->documento_id          =   $comprobante->documento_id;
                $detalle_resumen->documento_serie       =   $comprobante->documento_serie;
                $detalle_resumen->documento_correlativo =   $comprobante->documento_correlativo;
                $detalle_resumen->documento_subtotal    =   $comprobante->documento_subtotal;
                $detalle_resumen->documento_igv         =   $comprobante->documento_igv;
                $detalle_resumen->documento_total       =   $comprobante->documento_total;
                $detalle_resumen->documento_doc_cliente =   $comprobante->documento_doc_cliente;
                $detalle_resumen->save();
            }
            
            DB::commit();

            //===== ENVIANDO A SUNAT ======
            $res    =   $this->sendSunat($comprobantes,$fecha_comprobantes,$resumen);

            $nuevo_resumen      =   DB::select('select * from resumenes as r
                                    where r.id=?',[$resumen->id])[0];
            return response()
            ->json(
            [   'res_store'    => ['type'=> 'success','message'=>'RESUMEN REGISTRADO','nuevo_resumen'=>$nuevo_resumen],
                'fecha'         =>  $fecha_comprobantes,
                'res_send'      =>  $res
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()
            ->json([
                'res_store'=> ['type'=>'error','message'=>'ERROR AL REGISTRAR EL RESUMEN','exception'=>$e->getMessage()]
            ]);
        }
    }

    public function isActive(){
        //===== 186 EN PRODUCCION - 190 EN LOCALHOST =====
        $resumenActive  = DB::table('empresa_numeracion_facturaciones')->where('serie', 'R001')->exists();
        return response()->json(['resumenActive'=>$resumenActive]);
    }

    public function getCorrelativo(){
        //===== OBTENIENDO EL ÚLTIMO RESUMEN DE LA TABLA RESÚMENES =======
        $ultimoResumen    =  DB::table('resumenes')->latest()->first();

        //======== EN CASO YA EXISTAN RESÚMENES GENERADOS =====
        if($ultimoResumen){
            $correlativo    =   $ultimoResumen->correlativo+1;
            return $correlativo;
        }

        //===== BUSCAMOS EL REGISTRO DEL COMPROBANTE RESÚMENES =======
        //===== 186 EN PRODUCCION - 190 EN LOCALHOST =====
        $correlativo   =   DB::select('select enf.numero_iniciar 
                                    from empresa_numeracion_facturaciones as enf
                                    where enf.tipo_comprobante=186')[0]->numero_iniciar;

        return $correlativo;
    }


    public function sendSunat($comprobantes,$fecha_comprobantes,$resumen){
        $respuesta  =   ['type'=>'','message'=>'','exception'=>''];

        try {
            $util = Util::getInstance();

            //====== CONSTRUYENDO DETALLES =====
            $detalles_send  =   [];
            foreach ($comprobantes as $comprobante) {
                //====== ESTADOS ======
                //====== 1:NUEVO ==== "2:MODIFICAR" ======= 3:ANULAR =====
                //====== PARA 2,3 DEBE ESTAR PREVIAMENTE INFORMADO EL COMPROBANTE ======
                $detalle = new SummaryDetail();
                $detalle->setTipoDoc('03')
                    ->setSerieNro($comprobante->documento_serie.'-'.$comprobante->documento_correlativo)
                    ->setEstado('1')
                    ->setClienteTipo('1')
                    ->setClienteNro($comprobante->documento_doc_cliente)
                    ->setTotal($comprobante->documento_total)
                    ->setMtoOperGravadas($comprobante->documento_subtotal)
                    ->setMtoOperInafectas(0)
                    ->setMtoOperExoneradas(0)
                    ->setMtoOperExportacion(0)
                    ->setMtoOtrosCargos(0)
                    ->setMtoIGV($comprobante->documento_igv);
                
                $detalles_send[]    =   $detalle;
            }
    
            //====== CONSTRUYENDO RESUMEN ====
            $sum = new Summary();
            // FECHA GENERACIÓN MENOR QUE FECHA RESUMEN
            $sum->setFecGeneracion(new DateTime($fecha_comprobantes))
                ->setFecResumen(new DateTime($fecha_comprobantes))
                ->setCorrelativo($resumen->correlativo)
                ->setCompany($util->shared->getCompany())
                ->setDetails($detalles_send);
    
            //====== GUARDANDO NAME DEL SUMMARY =======
            $resumen->summary_name  =   $sum->getName();
            $resumen->update();
    
    
            //==== ENVIANDO A SUNAT ======
            //===== MODO BETA ======
            //$see = $util->getSee(SunatEndpoints::FE_BETA);
    
            //===== MODO PRODUCCION =====
            $see = $util->getSee(SunatEndpoints::FE_PRODUCCION);
    
            $res = $see->send($sum);
    
            //==== GUARDANDO XML ====
            $util->writeXml($sum, $see->getFactory()->getLastXml(),"RESUMEN",null);
            $resumen->ruta_xml  =    __DIR__.'/../../../Greenter/files/resumenes_xml/'.$sum->getName().'.xml';
    
    
            //==== VERIFICANDO SI SE ENVIÓ A SUNAT ====
            $envioSunat     =   $res->isSuccess();
            $message_envio  =   '';
            $ticket         =   null;
    
            if($envioSunat){
    
                $message_envio          =   "ENVIADO A SUNAT";
                $ticket                 =   $res->getTicket();
                $resumen->send_sunat    =   1;
                $resumen->ticket        =   $ticket;
                $resumen->regularize    =   0;
    
                //======= ACTUALIZANDO DOCUMENTOS DE VENTA COMO ENVIADOS A SUNAT =========
                $detalles   =   DB::select('select * from resumenes_detalles as rd
                                where rd.resumen_id=?',[$resumen->id]);
    
                foreach ($detalles as $detalle) {
                    DB::table('cotizacion_documento')->where('id',$detalle->documento_id)
                    ->update(['sunat' => '1']);
                }
        
                $respuesta['type']      =   'success';
                $respuesta['message']   =   'ENVÍO CORRECTO A SUNAT';
                $respuesta['exception'] =   ''; 
                $respuesta['estado']    =   "ENVIADO";  
                
            }else{
                $message_envio          =   "OCURRIO UN ERROR EN EL ENVÍO A SUNAT";
                $resumen->send_sunat    =   0;
                $resumen->regularize    =   1;
                
                $exception  =   '';
                //===== OBTENIENDO ERRORES =====
                if($res->getError()){
                    $error                  =   'CODE: '.$res->getError()->getCode().' - '.'MESSAGE: '.$res->getError()->getMessage();
                    $resumen->response_error=   $error;
                    $exception              =   $error;
                }
                   
                $respuesta['type']      =   'error';
                $respuesta['message']   =   'OCURRIO UN ERROR EN EL ENVÍO A SUNAT';  
                $respuesta['exception'] =   $exception; 
                $respuesta['estado']    =   "ERROR EN EL ENVÍO"; 

            }

            $resumen->update();
            
            return $respuesta;
        } catch (\Throwable $e) {
            $resumen->response_error    =   $e->getMessage();
            $resumen->update();

            return ['type'  =>'error',
            'message'       =>'ERROR AL ENVIAR A SUNAT',
            'exception'     =>$e->getMessage(),
            'estado'        =>"ERROR EN EL ENVÍO"];
        }   
    }


    public function consultarTicket(Request $request){
        try {
            $message        =   "";
            $resumen_id     =   json_decode($request->get('resumen_id'));

            $resumen        =   Resumen::findOrFail($resumen_id);
            $ticket         =   $resumen->ticket;
            $summary_name   =   $resumen->summary_name;

            $util = Util::getInstance();

            //===== INICIAR ENDPOINTS SUNAT ====
            //$see = $util->getSee(SunatEndpoints::FE_BETA);
            $see = $util->getSee(SunatEndpoints::FE_PRODUCCION);

            $res_ticket     =   $see->getStatus($ticket);

            $code_estado    =   $res_ticket->getCode();
            $cdr            =   $res_ticket->getCdrResponse();
           
    
            //====== ENVIO CORRECTO Y CDR RECIBIDO ======
            if($code_estado == 0){
                //===== GUARDANDO CDR ======
                $util->writeCdr(null, $res_ticket->getCdrZip(), "RESUMEN",$summary_name);
                $resumen->ruta_cdr  =   __DIR__.'/../../../Greenter/files/resumenes_cdr/'.$summary_name.'.zip';   
    
                //==== GUARDANDO DATOS DEL CDR ====
                if($res_ticket->getCdrResponse()){
                    $resumen->cdr_response_id           =   $res_ticket->getCdrResponse()->getId();
                    $resumen->cdr_response_code         =   $res_ticket->getCdrResponse()->getCode();
                    $resumen->cdr_response_description  =   $res_ticket->getCdrResponse()->getDescription();
                }
               
                //====== GUARDANDO ESTADO DEL TICKET ====
                $resumen->code_estado               =   $code_estado;  
                $message    =   "ENVÍO CORRECTO Y CDR RECIBIDO";
            }
    
            //===== ENVÍO CON ERRORES Y CDR RECIBIDO =====
            if($code_estado == 99 && $cdr){
                //===== GUARDANDO CDR ======
                $util->writeCdr(null, $res_ticket->getCdrZip(),"RESUMEN",$summary_name);
                $resumen->ruta_cdr  =   __DIR__.'/../../../Greenter/files/resumenes_cdr/'.$summary_name.'.zip';   
    
                //==== GUARDANDO DATOS DEL CDR ====
                $resumen->cdr_response_id           =   $res_ticket->getCdrResponse()->getId();
                $resumen->cdr_response_code         =   $res_ticket->getCdrResponse()->getCode();
                $resumen->cdr_response_description  =   $res_ticket->getCdrResponse()->getDescription();
    
                //====== GUARDANDO ESTADO DEL TICKET ====
                $resumen->code_estado               =   $code_estado;
    
                //======= SEÑALAR COMO RESUMEN CON ERRORES =======
                $resumen->regularize    =   1;
    
                //===== GUARDANDO ERRORES =======
                if($res_ticket->getError()){
                    $error                  =   'CODE: '.$res_ticket->getError()->getCode().' - '.'MESSAGE: '.$res_ticket->getError()->getMessage();
                    $resumen->response_error=   $error;
                }
                $message    =   "ENVÍO CON ERRORES Y CDR RECIBIDO";
            }
    
            //===== ENVÍO CON ERRORES Y SIN CDR =====
            if($code_estado == 99 && !$cdr){
                //======= SEÑALAR COMO RESUMEN CON ERRORES =======
                $resumen->regularize    =   1;
    
                //====== GUARDANDO ESTADO DEL TICKET ====
                $resumen->code_estado               =   $code_estado;
    
                 //===== GUARDANDO ERRORES =======
                 if($res_ticket->getError()){
                    $error                  =   'CODE: '.$res_ticket->getError()->getCode().' - '.'MESSAGE: '.$res_ticket->getError()->getMessage();
                    $resumen->response_error=   $error;
                }
                $message    =   "ENVÍO CON ERRORES,SIN CDR";
            }
    
            //======= EN PROCESO =======
            if($code_estado == 98){
                //====== GUARDANDO ESTADO DEL TICKET ====
                $resumen->code_estado               =   $code_estado;
                $message    =   "ENVÍO EN PROCESO";
            }

            $resumen->update();  

            //======= ARCHIVO YA PRESENTADO ANTERIORMENTE ========
            if($resumen->cdr_response_code == 2223 ){
                //======== MARCAR COMO ENVIADO =======
                $resumen->regularize    =   0;
                $resumen->send_sunat    =   1;
                $resumen->code_estado   =   0;
                $resumen->update();
            }

            $respuesta  =   ['type'  =>'success',
            'message'           =>$message,
            'code_estado'       =>$code_estado,
            'resumen'           =>$resumen];   
            
            return response()->json([ 'res'=>$respuesta  ]);

        } catch (\Throwable $e) {
            $respuesta  = ['type'=>'error','message'=>'Error al consultar el ticket','exception'=>$e->getMessage()]; 
            return response()->json([ 'res'=>$respuesta  ]);
        }
    }

    public function getXml($resumen_id){
        $resumen    =   Resumen::find($resumen_id);
        $nombreArchivo = basename($resumen->ruta_xml);

        $headers = [
            'Content-Type' => 'text/xml',
        ];
    
        return Response::download($resumen->ruta_xml, $nombreArchivo, $headers);
    }

    public function getCdr($resumen_id){
        $resumen        =   Resumen::find($resumen_id);
        $nombreArchivo  =   basename($resumen->ruta_cdr);

        $headers = [
            'Content-Type' => 'text/xml',
        ];
    
        return Response::download($resumen->ruta_cdr, $nombreArchivo, $headers);
    }

    public function reenviarSunat(Request $request){
        $resumen_id     =   json_decode($request->get('resumen_id'));

        $resumen        =   Resumen::findOrFail($resumen_id);
        $ticket         =   $resumen->ticket;
        $summary_name   =   $resumen->summary_name;
        $comprobantes   =   DB::select('select * from resumenes_detalles as rd 
                            where rd.resumen_id=?',[$resumen->id]);

        $resumen->update();
        $res    =   $this->sendSunat($comprobantes,$resumen->fecha_comprobantes,$resumen);

        return response()
        ->json(
        [   
            'res_send'      =>  $res,
            'resumen'       =>  $resumen
        ]);
    }


    public function getDetallesResumen($resumen_id){
        try {
            $resumen_detalle    =   DB::select('select rd.resumen_id, rd.documento_serie,rd.documento_correlativo,
                                    rd.documento_total,rd.documento_igv,rd.documento_subtotal,cd.cliente,
                                    rd.documento_doc_cliente,cd.created_at as fecha
                                    from resumenes_detalles as rd
                                    inner join cotizacion_documento as cd on cd.id=rd.documento_id 
                                    where rd.resumen_id=?',[$resumen_id]);

            return response()->json(['success'=>true,'resumen_detalle'=>$resumen_detalle]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>'ERROR AL OBTENER EL DETALLE DEL RESÚMEN',
            'exception'=>$th->getMessage()]);
        }
    }

}
