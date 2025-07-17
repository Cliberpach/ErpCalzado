<?php

namespace App\Console\Commands;

use App\Http\Controllers\Ventas\Electronico\ComprobanteController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendFacturas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facturas:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar facturas a sunat';

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
            Log::channel('facturas')->info('========== COMMAND SEND FACTURAS ==========');

            $facturas  =   DB::select('SELECT
                                cd.*
                                from cotizacion_documento as cd
                                where
                                cd.tipo_venta_id = "127"
                                AND cd.sunat = "0"
                                AND cd.cdr_response_code is null
                                AND cd.created_at >= DATE_SUB(NOW(), INTERVAL 4 DAY)
                            ');

            $send_controller    =   new ComprobanteController();

            foreach ($facturas as $factura) {
                Log::channel('facturas')->info('=======> ENVIANDO ' . $factura->serie . '-' . $factura->correlativo);

                $res    =   $send_controller->sunat($factura->id);

                Log::channel('facturas')->info('=======> RESPUESTA ' . $factura->serie . '-' . $factura->correlativo);
                Log::channel('facturas')->info(json_encode($res));
            }
        } catch (Throwable $th) {
            Log::channel('facturas')->info('=======> ERROR EN EL ENVÍO');
            Log::channel('facturas')->info(json_encode($th));
        }

        return 0;
    }
}
