<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Ventas\Electronico\ComprobanteController;
use Illuminate\Support\Facades\DB;
use Throwable;

class SendBoletasEspeciales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boletas-especiales:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Boletas pagadas con anticipos';

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
            Log::channel('boletas_especiales')->info('========== COMMAND SEND boletas_especiales ==========');

            $boletas_especiales =   DB::select('SELECT
                                        cd.*
                                        from cotizacion_documento as cd
                                        where
                                        cd.tipo_venta_id = "128"
                                        AND cd.sunat = "0"
                                        AND cd.cdr_response_code is null
                                        AND cd.anticipo_consumido_id is not null
                                        AND cd.created_at >= DATE_SUB(NOW(), INTERVAL 4 DAY)
                                    ');

            $send_controller    =   new ComprobanteController();

            foreach ($boletas_especiales as $boleta) {
                Log::channel('boletas_especiales')->info('=======> ENVIANDO ' . $boleta->serie . '-' . $boleta->correlativo);

                $res    =   $send_controller->sunat($boleta->id);

                Log::channel('boletas_especiales')->info('=======> RESPUESTA ' . $boleta->serie . '-' . $boleta->correlativo);
                Log::channel('boletas_especiales')->info(json_encode($res));
            }
        } catch (Throwable $th) {
            Log::channel('boletas_especiales')->info('=======> ERROR EN EL ENVÍO');
            Log::channel('boletas_especiales')->info(json_encode($th));
        }
        return 0;
    }
}
