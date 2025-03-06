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
use Exception;
use Illuminate\Support\Facades\Auth;
use Throwable;

require __DIR__ . '/../../../../vendor/autoload.php';

class ResumenController extends Controller
{
    public function index(){

        $sede_id    =   Auth::user()->sede_id;
        return view('ventas.resumenes.index',compact('sede_id'));
    }

    public function getResumenes(Request $request){

        $resumenes  =   DB::table('resumenes as r')
                        ->select('r.*');

           //========= FILTRO POR ROLES ======
        $roles = DB::table('role_user as rl')
         ->join('roles as r', 'r.id', '=', 'rl.role_id')
         ->where('rl.user_id', Auth::user()->id)
         ->pluck('r.name')
         ->toArray(); 

        //======== MOSTRAR SOLO RESÚMENES DE SU SEDE =====
        $resumenes->where('r.sede_id', Auth::user()->sede_id);


        return DataTables::of($resumenes)->toJson();
    }


    public function getComprobantes($fechaComprobantes,$sede_id){


        $enf        =   DB::select('select 
                        enf.* 
                        from empresa_numeracion_facturaciones as enf
                        inner join tabladetalles as td on td.id = enf.tipo_comprobante
                        where 
                        enf.sede_id = ?
                        AND td.parametro = "B"
                        AND td.tabla_id = 21
                        AND enf.estado = "ACTIVO"',
                        [$sede_id])[0];
        
        //======= SELECCIONAMOS LOS COMPROBANTES DE LA FECHA RESPECTIVA ======
        //===== QUE NO SE ENCUENTREN REGISTRADOS EN NINGÚN DETALLE DE RESUMEN ========
        $comprobantes   =   DB::select('select 
                            cd.id as documento_id, 
                            cd.serie as documento_serie,
                            cd.correlativo as documento_correlativo, 
                            td.parametro as documento_moneda,
                            cd.total_pagar as documento_total,
                            cd.total_igv as documento_igv,
                            cd.total as documento_subtotal,
                            cd.documento_cliente as documento_doc_cliente
                            from cotizacion_documento as cd
                            inner join tabladetalles as td on td.id = cd.moneda
                            where 
                            cd.fecha_documento = ? 
                            and cd.serie = ? 
                            and sunat = "0" 
                            and td.tabla_id = 1
                            and cd.id NOT IN (
                                SELECT
                                    rd.documento_id
                                FROM
                                    resumenes_detalles AS rd
                            )',[$fechaComprobantes,$enf->serie]);   

        return response()->json(['success'=>$comprobantes ,
        'fecha' => $fechaComprobantes]);

    }

/*
array:3 [
  "comprobantes"        => "[{"documento_id":8143,"documento_serie":"B001","documento_correlativo":4988,"documento_moneda":"PEN","documento_total":"70.00","documento_igv":"10.68","documento_subtotal":"59.32","documento_doc_cliente":71535599},{"documento_id":8144,"documento_serie":"B001","documento_correlativo":4989,"documento_moneda":"PEN","documento_total":"55.00","documento_igv":"8.39","documento_subtotal":"46.61","documento_doc_cliente":3373564},{"documento_id":8145,"documento_serie":"B001","documento_correlativo":4990,"documento_moneda":"PEN","documento_total":"178.00","documento_igv":"27.15","documento_subtotal":"150.85","documento_doc_cliente":41508474},{"documento_id":8149,"documento_serie":"B001","documento_correlativo":4991,"documento_moneda":"PEN","documento_total":"71.00","documento_igv":"10.83","documento_subtotal":"60.17","documento_doc_cliente":72794677},{"documento_id":8157,"documento_serie":"B001","documento_correlativo":4993,"documento_moneda":"PEN","documento_total":"175.00","documento_igv":"26.69","documento_subtotal":"148.31","documento_doc_cliente":43819263}]"
  "fecha_comprobantes"  => "2025-02-28"
  "sede_id"             => 1
]
*/ 
    public function store(Request $request){
        try {

            //===== INICIAR TRANSACCIÓN =====
            DB::beginTransaction();

            //======= VERIFICANDO SI RESUMENES ESTAN ACTIVOS ======

            $resumenIsActive =    ResumenController::isActive($request->get('sede_id'))->getData();
           

            if(!$resumenIsActive->success){
               return $resumenIsActive;
            }

            //====== RECEPCIONANDO COMPROBANTES Y FECHA ======
            $comprobantes       =   json_decode($request->get('comprobantes'));
            $fecha_comprobantes =   $request->get('fecha_comprobantes');

            //===== BUSCANDO CORRELATIVO DEL COMPROBANTE =====
            $datos_correlativo    =   $this->getCorrelativo($request->get('sede_id'));


            //==== GUARDANDO RESUMEN EN LA BD ====
            $resumen                        =   new Resumen();
            $resumen->serie                 =   $datos_correlativo->serie;
            $resumen->correlativo           =   $datos_correlativo->correlativo;
            $resumen->fecha_comprobantes    =   $fecha_comprobantes;
            $resumen->sede_id               =   $request->get('sede_id');
            if(!$request->has("command")){
                $resumen->registrador_id        =   Auth::user()->colaborador_id;
                $resumen->registrador_nombre    =   Auth::user()->usuario;
            }else{
                $resumen->registrador_id        =   1;
                $resumen->registrador_nombre    =   "ENVÍO AUTOMÁTICO";
            }
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

            //========= ACTUALIZANDO A INICIADO =======
            DB::table('empresa_numeracion_facturaciones')
            ->where('serie', $resumenIsActive->serie) 
            ->where('sede_id', Auth::user()->sede_id) 
            ->where('emision_iniciada', '0') 
            ->update([
                'emision_iniciada'  =>  '1',  
                'updated_at'        =>  now()
            ]);
            
            DB::commit();

           
        } catch (Throwable $th) {
            DB::rollback();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }

        //===== ENVIANDO A SUNAT ======
        $request_send   =   new Request();
        $request_send->merge([
            'resumen_id' => $resumen->id
        ]);

        $res            =   $this->sendSunat($request_send);

        $res_decode     =   $res->getData();

        if(!$res_decode->success){
            return response()->json([
            'success'   =>  true,
            'message'   =>'RESÚMEN DE BOLETAS REGISTRADO, PERO NO PUDO ENVIARSE A SUNAT',
            'error'     =>  $res_decode->message,  
            'id'        =>  $resumen->id]);
        }

        return $res;

    }

    public static function isActive($sede_id){

        try {

        
            //===== 186 EN PRODUCCION - 190 EN LOCALHOST =====
            $resumenActive  =   DB::table('empresa_numeracion_facturaciones as enf')
                                ->select('enf.serie')
                                ->join('tabladetalles as td', 'td.id', '=', 'enf.tipo_comprobante')
                                ->where('td.parametro', "R")
                                ->where('td.tabla_id', 21)
                                ->where('enf.sede_id',$sede_id)
                                ->where('enf.estado','ACTIVO')
                                ->get();
    
            if(count($resumenActive) === 0){
                throw new Exception("RESÚMENES NO ESTÁ ACTIVO EN LA SEDE!!!");
            }

            return response()->json([
            'success'   =>  true,
            'message'   =>  "RESÚMENES ACTIVO EN LA SEDE COMO: ".$resumenActive[0]->serie,
            'serie'     =>  $resumenActive[0]->serie]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
       
       
    }

    public function getCorrelativo($sede_id){


        //===== OBTENIENDO EL ÚLTIMO RESUMEN DE LA SEDE EN LA TABLA RESÚMENES =======
        $ultimoResumen  =   DB::table('resumenes')
                            ->where('sede_id',$sede_id)
                            ->latest()
                            ->first();

        //======== EN CASO YA EXISTAN RESÚMENES GENERADOS =====
        if($ultimoResumen){
            $correlativo    =   $ultimoResumen->correlativo + 1;
            return (object)['serie'=>$ultimoResumen->serie,'correlativo'=>$correlativo];
        }

        //===== BUSCAMOS EL REGISTRO DEL COMPROBANTE RESÚMENES =======
        //===== 186 EN PRODUCCION - 190 EN LOCALHOST =====
        $enf    =    DB::select('select 
                            enf.numero_iniciar,
                            enf.serie 
                            from empresa_numeracion_facturaciones as enf
                            inner join tabladetalles as td on td.id = enf.tipo_comprobante
                            where 
                            td.parametro = "R" 
                            AND td.tabla_id = 21 
                            AND enf.estado = "ACTIVO"
                            AND enf.sede_id = ?',
                            [$sede_id])[0];

        return (object)['serie'=>$enf->serie,'correlativo'=>$enf->numero_iniciar];
    }


/*
array:1 [
  "resumen_id" => 6
]
*/

//========= SUMMARY RESULT =====
/*
Greenter\Model\Response\SummaryResult {#3127 // app\Http\Controllers\Market\Ventas\ResumenController.php:306
  #success: true
  #error: null
  #ticket: "1740176422672"
}
*/ 
    public function sendSunat(Request $request){

        try {
          
            if(!$request->get('resumen_id')){
                throw new Exception("NO EXISTE EL RESUMEN ID EN LA PETICIÓN ENVIAR A SUNAT!!!");
            }
    
            //====== COMPROBANDO SI EXISTE EL RESUMEN ======
            $resumen   =   Resumen::find($request->get('resumen_id'));   
            if(!$resumen){
                throw new Exception("EL RESUMEN NO EXISTE EN LA BD!!!");
            }
    
            //======= COMPROBANDO QUE NO SE HAYA ENVIADO ANTES =======
            if($resumen->send_sunat == '1'){
                throw new Exception("ESTE RESUMEN YA FUE ENVIADO A SUNAT!!!");
            }

            //===== OBTENIENDO COMPROBANTES ======
            $comprobantes   =   DB::select('select 
                                rd.*
                                from resumenes_detalles as rd
                                where rd.resumen_id = ?',
                                [$request->get('resumen_id')]);

            if(count($comprobantes) === 0){
                throw new Exception("EL DETALLE DEL RESUMEN ESTÁ VACÍO!!!");
            }

    
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
            $sum->setFecGeneracion(new DateTime($resumen->fecha_comprobantes))
                    ->setFecResumen(new DateTime($resumen->fecha_comprobantes))
                    ->setCorrelativo($resumen->correlativo)
                    ->setCompany($util->shared->getCompany($resumen->sede_id))
                    ->setDetails($detalles_send);
    
            //====== GUARDANDO NAME DEL SUMMARY =======
            $resumen->summary_name  =   $sum->getName();
            $resumen->update();
    
            $see    =   $this->controlConfiguracionGreenter($util);
           
         
            $res = $see->send($sum);
    
            //==== GUARDANDO XML ====
            $util->writeXml($sum, $see->getFactory()->getLastXml(),"RESUMEN",null);
            //$resumen->ruta_xml    =    __DIR__.'/../../../Greenter/files/resumenes_xml/'.$sum->getName().'.xml';
            $resumen->ruta_xml      =   'storage/greenter/resumenes/xml/'.$sum->getName().'.xml';
    
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
                $detalles   =   DB::select('select 
                                rd.* 
                                from resumenes_detalles as rd
                                where rd.resumen_id = ?',[$resumen->id]);
    
                foreach ($detalles as $detalle) {
                    DB::table('cotizacion_documento')
                    ->where('id',$detalle->documento_id)
                    ->update([
                        'resumen_id'    =>  $resumen->id,
                        'sunat'         =>  '1'
                    ]);
                }
        
            
                
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
                   
                throw new Exception($exception);


            }

            $resumen->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ENVIADO A SUNAT CON ÉXITO!!!',
            'id'=>$request->get('resumen_id')]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success'   =>  false,
                'message'   =>  $th->getMessage(),
                'id'        =>  $request->get('resumen_id'),
                'line'      =>  $th->getLine(),
                'th'        =>  $th->getFile()]);
        }   
    }


    public function controlConfiguracionGreenter($util){
        //==== OBTENIENDO CONFIGURACIÓN DE GREENTER ======
        $greenter_config    =   DB::select('select 
                                gc.ruta_certificado,
                                gc.id_api_guia_remision,
                                gc.modo,
                                gc.clave_api_guia_remision,
                                e.ruc,e.razon_social,
                                e.direccion_fiscal,
                                e.ubigeo,
                                e.direccion_llegada,
                                gc.sol_user,gc.sol_pass
                                from greenter_config as gc
                                inner join empresas as e on e.id = gc.empresa_id
                                inner join configuracion as c on c.propiedad = gc.modo
                                where gc.empresa_id=1 and c.slug="AG"');


        if(count($greenter_config) === 0){
            throw new Exception('NO SE ENCONTRÓ NINGUNA CONFIGURACIÓN PARA GREENTER');
        }

        if(!$greenter_config[0]->sol_user){
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_USER');
        }
        if(!$greenter_config[0]->sol_pass){
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_PASS');
        }
        if ($greenter_config[0]->modo !== "BETA" && $greenter_config[0]->modo !== "PRODUCCION") {
            throw new Exception('NO SE HA CONFIGURADO EL AMBIENTE BETA O PRODUCCIÓN PARA GREENTER');
        }

        $see    =   null;
        if($greenter_config[0]->modo === "BETA"){
            //===== MODO BETA ======
            $see = $util->getSee(SunatEndpoints::FE_BETA,$greenter_config[0]);
        }

        if($greenter_config[0]->modo === "PRODUCCION"){
            //===== MODO PRODUCCION ======
            $see = $util->getSee(SunatEndpoints::FE_PRODUCCION,$greenter_config[0]);
        }

        if(!$see){
            throw new Exception('ERROR EN LA CONFIGURACIÓN DE GREENTER, SEE ES NULO');
        }

        return $see;
    }


/*
array:1 [
  "resumen_id" => "340"
]
*/ 
    public function consultarTicket(Request $request){
        try {
            DB::beginTransaction();

            $message        =   "";

            $resumen_id     =   $request->get('resumen_id');

            $resumen        =   Resumen::findOrFail($resumen_id);
            $ticket         =   $resumen->ticket;
            $summary_name   =   $resumen->summary_name;

            $util = Util::getInstance();

            //===== INICIAR ENDPOINTS SUNAT ====
            //$see = $util->getSee(SunatEndpoints::FE_BETA);
            //$see = $util->getSee(SunatEndpoints::FE_PRODUCCION);

            $see            =   $this->controlConfiguracionGreenter($util);

            $res_ticket     =   $see->getStatus($ticket);

            $code_estado    =   $res_ticket->getCode();
            $cdr            =   $res_ticket->getCdrResponse();
           
    
            //====== ENVIO CORRECTO Y CDR RECIBIDO ======
            if($code_estado == 0){
                //===== GUARDANDO CDR ======
                $util->writeCdr(null, $res_ticket->getCdrZip(), "RESUMEN",$summary_name);
                //$resumen->ruta_cdr  =   __DIR__.'/../../../Greenter/files/resumenes_cdr/'.$summary_name.'.zip';   
                $resumen->ruta_cdr      =   'storage/greenter/resumenes/cdr/'.$summary_name.'.zip';

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
                //$resumen->ruta_cdr  =   __DIR__.'/../../../Greenter/files/resumenes_cdr/'.$summary_name.'.zip';  
                $resumen->ruta_cdr      =   'storage/greenter/resumenes/cdr/'.$summary_name.'.zip';
 
    
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

            DB::commit();
            return response()->json(['success'=>true,'message'=>$message]);

        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
    }

    public function getXml($resumen_id){
        $resumen        =   Resumen::find($resumen_id);
        $nombreArchivo  =   basename($resumen->ruta_xml);
        

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
