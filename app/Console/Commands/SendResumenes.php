<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use  App\Http\Controllers\Ventas\ResumenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;


class SendResumenes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resumenes:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creando un resumen de boletas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        Log::info('OBTENIENDO FECHA 4 DÍAS ANTERIORES');
        //====== OBTENER LA FECHA DE 4 DÍAS ANTES ====
        $fecha_comprobantes   =   $this->getFechaComprobantes();
        Log::info($fecha_comprobantes);

        Log::info('OBTENIENDO BOLETAS DE ESA FECHA');
        //===== OBTENER LAS BOLETAS DE 4 DÍAS ANTES ======
        $resumenController  =   new ResumenController();
        $boletas            =   $resumenController->getComprobantes($fecha_comprobantes);

        Log::info('EXTRAYENDO LISTADO DE BOLETAS');
        //===== EXTRAYENDO EL LISTADO DE BOLETAS =======
        $jsonData   = $boletas->getContent();
        $data       = json_decode($jsonData, true);
        $listadoBoletas =   $data['success'];
        
        Log::info('GRABAR Y ENVIAR A SUNAT EN CASO EXISTAN BOLETAS');
        //====== EN CASO EXISTAN BOLETAS NO ENVIADAS DE HACE 4 DÍAS =======
        if(count($listadoBoletas)>0){
           //======= PREPARANDO REQUEST ======
           $request = new Request([
            'comprobantes'          => json_encode($listadoBoletas), 
            'fecha_comprobantes'    => json_encode($fecha_comprobantes), 
            ]);

            //======= GRABAR Y ENVIAR A SUNAT =====
            $respuesta_store_send  =   $resumenController->store($request);
            //===== RESPUESTA====
            $jsonData   =   $respuesta_store_send->getContent();
            $data       =   json_decode($jsonData, true);
            $res_store  =   $data['res_store'];
            $res_send   =   $data['res_send'];

            Log::info('RES_STORE');
            Log::info($res_store);
            Log::info('RES_SEND');
            Log::info($res_send);

            //====== EN CASO DE GRABADO CORRECTO ======
            if($res_store['type'] == "success"){
                Log::info('GRABADO CORRECTO DEL RESUMEN');
                //====== EN CASO DE ENVÍO CORRECTO ======
                if($res_send['type'] == "success"){
                    Log::info('ENVÍO CORRECTO A SUNAT');
                    //======== CONSULTAR TICKET ====
                    Log::info('CONSULTANDO TICKET');
                    $resumen_id =   $res_store['nuevo_resumen']['id'];
                    $request = new Request([
                        'resumen_id'          => json_encode($resumen_id), 
                        ]);
                    $respuesta_consulta_ticket  =   $resumenController->consultarTicket($request);
                    Log::info('RES_CONSULTA');
                    $jsonData       =   $respuesta_consulta_ticket->getContent();
                    $data           =   json_decode($jsonData, true);
                  
                    Log::info($data);
                    if($data['res']['type']    ==  "success"){
                        Log::info("CONSULTA CORRECTA");
                    }
                }
            }
            
        }
       


        

        // return 0;
    }

    public function getFechaComprobantes(){
        $fechaActual = Carbon::now();
        $fechaRestada = $fechaActual->subDays(4);

        $fechaFormatoAnioMesDia = $fechaRestada->format('Y-m-d');

        return $fechaFormatoAnioMesDia;
    }
}
