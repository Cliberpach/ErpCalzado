<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use  App\Http\Controllers\Ventas\ResumenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;
use App\Configuracion\Configuracion;
use App\Mantenimiento\Sedes\Sede;

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
        //====== COMPROBANDO CONFIGURACIÓN ======
        Log::channel('resumenes')->info('========== COMMAND SEND RESUMENES ==========');
        Log::channel('resumenes')->info('VERIFICANDO CONFIGURACIÓN');

        $config = Configuracion::where('slug', 'EARB')->first();

        if($config->propiedad == "SI"){

            Log::channel('resumenes')->info('ENVÍO RESÚMENES AUTOMÁTICO ACTIVADO');
            $dias_menos =   $config->nro_dias;

            Log::channel('resumenes')->info('OBTENIENDO FECHA '.$dias_menos. ' DÍAS ANTERIORES');

            //====== OBTENER LA FECHA DE N DÍAS ANTES ====
            $fecha_comprobantes   =   $this->getFechaComprobantes($dias_menos);
            Log::channel('resumenes')->info($fecha_comprobantes);
    
            Log::channel('resumenes')->info('OBTENIENDO BOLETAS DE ESA FECHA');

            //======= ENVIAR RESÚMENES POR SEDE =======
            Log::channel('resumenes')->info('RECORRIENDO SEDES');
            $empresa_sedes  =   Sede::where('estado','ACTIVO')->get();

            foreach ($empresa_sedes as $sede) {

                Log::channel('resumenes')->info('->SEDE: '.$sede->nombre);
              
                //===== OBTENER LAS BOLETAS DE N DÍAS ANTES ======
                $resumenController  =   new ResumenController();
                $boletas            =   $resumenController->getComprobantes($fecha_comprobantes,$sede->id);

                Log::channel('resumenes')->info('EXTRAYENDO LISTADO DE BOLETAS');
                //===== EXTRAYENDO EL LISTADO DE BOLETAS =======
                $jsonData       =   $boletas->getData();
                $data           =   $jsonData;
                $listadoBoletas =   $data->success;

                Log::channel('resumenes')->info('VERIFICANDO SI EXISTEN BOLETAS NO ENVIADAS EN ESA FECHA');

                //====== EN CASO EXISTAN BOLETAS NO ENVIADAS DE HACE N DÍAS =======
                if(count($listadoBoletas) > 0){

                    //======= PREPARANDO REQUEST ======
                    $request = new Request([
                        'comprobantes'          =>  json_encode($listadoBoletas), 
                        'fecha_comprobantes'    =>  $fecha_comprobantes, 
                        'sede_id'               =>  $sede->id
                    ]);
        
                    //======= GRABAR Y ENVIAR A SUNAT =====
                    Log::channel('resumenes')->info('GRABANDO Y ENVIANDO RESUMEN');
                    $respuesta_store_send  =   $resumenController->store($request);

                    //===== RESPUESTA====
                    $jsonData           =   $respuesta_store_send->getData();
                    $data_store         =   $jsonData;
        
                    Log::channel('resumenes')->info('RES_STORE');
                    Log::channel('resumenes')->info($data_store);
                 
        
                    //====== EN CASO DE GRABADO CORRECTO ======
                    if($data_store->success){

                         Log::channel('resumenes')->info('GRABADO CORRECTO DEL RESUMEN');

                        //====== EN CASO DE ENVÍO CORRECTO ======
                       
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
    
                }else{
                    Log::info('NO HAY BOLETAS NO ENVIADAS EN ESA FECHA');
                }
    

            }

         
    
            
     
       
        }else{
            Log::info('ENVÍO RESÚMENES AUTOMÁTICO DESACTIVADO');
        }

        // return 0;
    }

    public function getFechaComprobantes($dias_menos){
        $fechaActual            = Carbon::now();
        $fechaRestada           = $fechaActual->subDays($dias_menos);

        $fechaFormatoAnioMesDia = $fechaRestada->format('Y-m-d');

        return $fechaFormatoAnioMesDia;
    }
}
