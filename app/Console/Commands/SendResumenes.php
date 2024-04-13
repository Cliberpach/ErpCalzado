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

        $fecha_comprobantes   =   $this->getFechaComprobantes();


        //===== OBTENIENDO TODAS LAS BOLETAS NO ENVIADAS DE HACE DOS DÍAS ======
        $boletas = DB::select(' select cd.id as documento_id, cd.serie as documento_serie,
                        cd.correlativo as documento_correlativo, td.parametro as documento_moneda,
                        cd.total_pagar as documento_total,cd.total_igv as documento_igv,
                        cd.total as documento_subtotal,cd.documento_cliente as documento_doc_cliente,
                        cd.fecha_documento
                        FROM cotizacion_documento AS cd
                        inner join tabladetalles as td on td.id=cd.moneda
                        WHERE cd.fecha_documento = DATE_SUB(CURDATE(), INTERVAL 3 DAY)
                        and sunat="0" and tipo_venta="128"  
                        and cd.id NOT IN (
                            SELECT
                                rd.documento_id
                            FROM
                                resumenes_detalles AS rd
                        )');


        if(count($boletas)>0){
             //======= PREPARANDO REQUEST ======
            $request = new Request([
                'comprobantes'          => json_encode($boletas), 
                'fecha_comprobantes'    => json_encode($fecha_comprobantes), 
            ]);

            $resumenController  =   new ResumenController();
            $respuesta  =   $resumenController->store($request);
        }
       Log::info($respuesta);
        
        // return 0;
    }

    public function getFechaComprobantes(){
        $fechaActual = Carbon::now();
        $fechaRestada = $fechaActual->subDays(3);

        $fechaFormatoAnioMesDia = $fechaRestada->format('Y-m-d');

        return $fechaFormatoAnioMesDia;
    }
}
