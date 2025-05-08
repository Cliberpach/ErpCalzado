<?php

namespace App\Console\Commands;
use Illuminate\Http\Request;
use App\Http\Controllers\Consultas\Ventas\AlertaController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendNotasCreditoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notas_credito:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar notas de crédito a sunat';

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
        try {
            Log::channel('notas_credito')->info('========== COMMAND SEND NOTAS CRÉDITO ==========');

            $notas  =   DB::select('select 
                        nc.* 
                        from nota_electronica as nc
                        where 
                        nc.tipDocAfectado <> "04"
                        AND nc.sunat = "0"
                        AND nc.cdr_response_code is null');
    
            $send_controller    =   new AlertaController();
    
            foreach ($notas as $nota) {
                Log::channel('notas_credito')->info('=======> ENVIANDO '.$nota->serie.'-'.$nota->correlativo);

                $request    =   new Request();
                $request->merge(['id'=>$nota->id]);
                $res    =   $send_controller->sunat_notas($request);
                Log::channel('notas_credito')->info('=======> RESPUESTA '.$nota->serie.'-'.$nota->correlativo);
                Log::channel('notas_credito')->info(json_encode($res));
            }

        } catch (\Throwable $th) {
            Log::channel('notas_credito')->info('=======> ERROR EN EL ENVÍO');
            Log::channel('notas_credito')->info(json_encode($th));
        }
      

        return 0;
    }
}
