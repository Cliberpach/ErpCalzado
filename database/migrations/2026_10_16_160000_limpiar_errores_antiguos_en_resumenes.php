<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Limpia los mensajes de error que quedaron pegados a resúmenes ya resueltos.
 *
 * `consultarTicket()` escribe `status_result_error_code` y
 * `status_result_error_message` cada vez que SUNAT contesta con error —el 98
 * «el procesamiento aún no ha terminado» entre ellos— y nunca los borraba. Si
 * una consulta posterior traía la respuesta definitiva, el resumen se quedaba
 * con las dos verdades a la vez: el CDR aceptado y, debajo, el mensaje viejo.
 * El listado los pintaba los dos, el segundo en rojo.
 *
 * `response_error` es del envío: si el resumen tiene CDR, el envío funcionó y
 * ese mensaje tampoco aplica. Y `regularize` se ponía a 1 junto al error sin
 * que nadie lo bajara.
 *
 * El código ya no deja que vuelva a pasar, pero estas filas no pasarán nunca
 * por él: están en estado definitivo y el barrido `resumenes:consultar`
 * excluye el 0 y el 99 justamente por eso.
 *
 * Sólo toca los que tienen estado DEFINITIVO. Los que siguen sin resolver
 * conservan su mensaje, porque ahí sí corresponde.
 *
 * Idempotente: volver a ejecutarla no encuentra nada que cambiar.
 *
 * Compatible con PHP 7.4 y 8.3.
 */
class LimpiarErroresAntiguosEnResumenes extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('resumenes')) {
            return;
        }

        foreach (array('status_result_error_code', 'status_result_error_message', 'response_error', 'regularize') as $columna) {
            if (!Schema::hasColumn('resumenes', $columna)) {
                return;
            }
        }

        DB::statement('
            UPDATE resumenes
            SET status_result_error_code    = NULL,
                status_result_error_message = NULL,
                response_error              = NULL,
                regularize                  = 0
            WHERE CAST(IFNULL(code_estado, "-1") AS SIGNED) IN (0, 99)
              AND (status_result_error_code    IS NOT NULL
                OR status_result_error_message IS NOT NULL
                OR response_error              IS NOT NULL
                OR regularize                  = 1)
        ');
    }

    /**
     * Sin vuelta atrás: los mensajes borrados no se pueden reconstruir, y
     * tampoco aportaban nada —eran de un intento anterior ya superado—. El
     * respaldo nocturno es la red de seguridad si hiciera falta recuperarlos.
     */
    public function down()
    {
        // Intencionadamente vacío.
    }
}
