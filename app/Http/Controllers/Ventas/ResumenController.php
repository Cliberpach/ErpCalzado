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


require __DIR__ . '/../../../../vendor/autoload.php';

class ResumenController extends Controller
{
    public function index(){
        return view('ventas.resumenes.index');
    }

    public function getComprobantes($fechaComprobantes){
        $comprobantes   =   DB::select('select cd.id as documento_id, cd.serie as documento_serie,
                            cd.correlativo as documento_correlativo, td.parametro as documento_moneda,
                            cd.total_pagar as documento_total,cd.total_igv as documento_igv,
                            cd.total as documento_subtotal,cd.documento_cliente as documento_doc_cliente
                            from cotizacion_documento as cd
                            inner join tabladetalles as td on td.id=cd.moneda
                            where cd.fecha_documento=? and cd.serie="B001" 
                            and td.tabla_id=1',[$fechaComprobantes]);   

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
            $resumen                =   new Resumen();
            $resumen->serie         =   'R001';
            $resumen->correlativo   =   $correlativo;
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

            


            return response()->json([   'message'=> 'RESUMEN REGISTRADO COMO'.'R001',
                                        'fecha' =>  $fecha_comprobantes,
                                        'res'   =>  $res
                                    ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error'=>$e->getMessage()]);
        }
       
    }

    public function isActive(){
        $resumenActive  = DB::table('empresa_numeracion_facturaciones')->where('tipo_comprobante', 190)->exists();
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
        $correlativo   =   DB::select('select enf.numero_iniciar 
                                    from empresa_numeracion_facturaciones as enf
                                    where enf.tipo_comprobante=190')[0]->numero_iniciar;

        return $correlativo;
    }


    public function sendSunat($comprobantes,$fecha_comprobantes,$resumen){
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


        //==== ENVIANDO A SUNAT ======
        $see = $util->getSee(SunatEndpoints::FE_BETA);
        $res = $see->send($sum);

        //==== GUARDANDO XML ====
        $util->writeXml($sum, $see->getFactory()->getLastXml(),"RESUMEN");

        //==== VERIFICANDO SI SE ENVIÓ A SUNAT ====
        $envioSunat     =   $res->isSuccess();
        $message_envio  =   '';
        $ticket         =   null;

        if($envioSunat){
            $message_envio          =   "ENVIADO A SUNAT";
            $ticket                 =   $res->getTicket();
            $resumen->send_sunat    =   1;
            $resumen->ticket        =   $ticket;

            //===== CONSULTAR ESTADO DEL RESUMEN ENVIADO =======
            $this->consultarTicket($ticket,$see,$util,$sum,$resumen);
        }else{
            $message_envio          =   "OCURRIO UN ERROR, NO SE ENVIÓ A SUNAT";
            $resumen->send_sunat    =   0;
            //===== OBTENIENDO ERRORES =====
            $error                  =   'CODE: '.$res->getError()->getCode().' - '.'MESSAGE: '.$res->getError()->getMessage();
            $resumen->response_error=   $error;
        }

        $resumen->update();

        return 'Y DALE U';
    }

    public function consultarTicket($ticket,$see,$util,$sum,$resumen){
        $res_ticket     =   $see->getStatus($ticket);
        
        $code_estado    =   $res_ticket->getCode();
        
        //====== ENVIO CORRECTO Y CDR RECIBIDO ======
        if($code_estado == 0){
            //===== GUARDANDO CDR ======
            $cdr = $res_ticket->getCdrResponse();
            $util->writeCdr($sum, $res_ticket->getCdrZip(),"RESUMEN");

            //==== GUARDANDO DATOS DEL CDR ====
            $resumen->cdr_response_id           =   $res_ticket->getCdrResponse()->getId();
            $resumen->cdr_response_code         =   $res_ticket->getCdrResponse()->getCode();
            $resumen->cdr_response_description  =   $res_ticket->getCdrResponse()->getDescription();

            $resumen->update();
            
        }

    }
}
