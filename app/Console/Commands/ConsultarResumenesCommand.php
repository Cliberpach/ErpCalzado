<?php

namespace App\Console\Commands;

use App\Http\Controllers\Ventas\ResumenController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Consulta en SUNAT el estado de los resúmenes que se enviaron y se quedaron
 * sin respuesta.
 *
 * Por qué hace falta: el comando de envío (resumenes:send) consulta el ticket
 * en la MISMA ejecución, segundos después de enviar, que es justo cuando SUNAT
 * responde 98 (en proceso). Con 98 no se reintenta nada, y no existía ningún
 * proceso que volviera a mirar. En el sistema de referencia eso dejó 92
 * resúmenes sin resolver: 8 parados en 98 desde 2025 y 82 que nunca llegaron a
 * consultarse, el más antiguo de julio de 2024.
 *
 * La ventana por defecto son 7 días. Un ticket que sigue en 98 pasada una
 * semana no cambia por insistir: necesita que lo mire una persona. Para el
 * atraso acumulado está --dias, que se usa a mano y una vez, no desde el cron.
 *
 * Compatible con PHP 7.4 y 8.3.
 */
class ConsultarResumenesCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'resumenes:consultar
                            {--dias=7 : Días hacia atrás que se miran (subirlo a mano para el atraso histórico)}
                            {--limite=50 : Máximo de resúmenes por ejecución}';

    /**
     * @var string
     */
    protected $description = 'Consulta en SUNAT el estado de los resúmenes enviados que quedaron sin respuesta';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $dias   = (int) $this->option('dias');
        $limite = (int) $this->option('limite');

        if ($dias <= 0)   { $dias   = 7; }
        if ($limite <= 0) { $limite = 50; }

        Log::channel('resumenes')->info('========== COMMAND CONSULTAR RESUMENES ==========');
        Log::channel('resumenes')->info('VENTANA: ' . $dias . ' DÍA(S)   LÍMITE: ' . $limite);

        $pendientes = $this->resumenesSinResolver($dias, $limite);

        if (count($pendientes) === 0) {
            $this->info('No hay resúmenes sin resolver en los últimos ' . $dias . ' día(s).');
            Log::channel('resumenes')->info('NO HAY RESÚMENES SIN RESOLVER');

            return 0;
        }

        $this->info('Resúmenes a consultar: ' . count($pendientes));
        Log::channel('resumenes')->info('RESÚMENES A CONSULTAR: ' . count($pendientes));

        $controlador = $this->controlador();

        $consultados = 0;
        $aceptados   = 0;
        $enProceso   = 0;
        $rechazados  = 0;
        $sinCambio   = 0;
        $fallidos    = 0;
        $duplicados  = 0;

        // Tickets que aparecen en mas de un resumen. SUNAT contesta sobre el
        // ticket, no sobre el resumen, asi que consultar uno de estos escribiria
        // en el la respuesta del otro: su CDR, su codigo y su estado. Y al quedar
        // en 0 saldria del alcance de los barridos siguientes, resuelto en falso.
        $ticketsRepetidos = $this->ticketsRepetidos();

        foreach ($pendientes as $pendiente) {

            $etiqueta = $pendiente->serie . '-' . $pendiente->correlativo . ' (id ' . $pendiente->id . ')';

            if (isset($ticketsRepetidos[$pendiente->ticket])) {
                $duplicados++;

                $aviso = $etiqueta . ' COMPARTE EL TICKET ' . $pendiente->ticket . ' CON '
                       . ($ticketsRepetidos[$pendiente->ticket] - 1) . ' RESUMEN(ES) MAS. NO SE CONSULTA: '
                       . 'SUNAT RESPONDERIA POR EL OTRO. REVISAR A MANO.';

                $this->warn('  ' . $aviso);
                Log::channel('resumenes')->warning('resumenes:consultar ' . $aviso);

                continue;
            }

            // try/catch POR RESUMEN: uno malo no puede tumbar el barrido entero,
            // que es lo que le pasa hoy a resumenes:send, sin try/catch ninguno.
            try {
                $consultados++;

                $respuesta = $controlador->consultarTicket(new Request(array('resumen_id' => $pendiente->id)));
                $datos     = $respuesta->getData();

                // consultarTicket atrapa sus propios errores y devuelve success=false
                // en vez de lanzar. Sin mirar ese campo, un fallo se contaria como
                // "sin cambio" y el barrido diria que todo fue bien.
                if (isset($datos->success) && !$datos->success) {
                    $fallidos++;

                    $this->error('  ' . $etiqueta . ' -> FALLO LA CONSULTA');
                    Log::channel('resumenes')->error('resumenes:consultar ' . $etiqueta . ': '
                        . (isset($datos->message) ? $datos->message : 'sin mensaje'));

                    continue;
                }


                // El resultado se lee de la base y no del mensaje devuelto: es el
                // dato que de verdad quedó grabado.
                $estado = DB::table('resumenes')->where('id', $pendiente->id)->value('code_estado');

                if ($estado === null || (string) $estado === '') {
                    $sinCambio++;
                    $resultado = 'SIN CAMBIO';
                } elseif ((int) $estado === 0) {
                    $aceptados++;
                    $resultado = 'ACEPTADO';
                } elseif ((int) $estado === 98) {
                    $enProceso++;
                    $resultado = 'EN PROCESO';
                } elseif ((int) $estado === 99) {
                    $rechazados++;
                    $resultado = 'RECHAZADO';
                } else {
                    $sinCambio++;
                    $resultado = 'CÓDIGO ' . $estado;
                }

                $this->line('  ' . $etiqueta . ' -> ' . $resultado);
                Log::channel('resumenes')->info('  ' . $etiqueta . ' -> ' . $resultado);

            } catch (Throwable $th) {
                $fallidos++;

                $this->error('  ' . $etiqueta . ' -> FALLÓ LA CONSULTA');
                Log::channel('resumenes')->error('resumenes:consultar ' . $etiqueta . ': '
                    . $th->getMessage() . ' [' . $th->getFile() . ':' . $th->getLine() . ']');

                continue;
            }
        }

        $resumenFinal = 'CONSULTADOS: ' . $consultados
                      . ' | ACEPTADOS: ' . $aceptados
                      . ' | EN PROCESO: ' . $enProceso
                      . ' | RECHAZADOS: ' . $rechazados
                      . ' | SIN CAMBIO: ' . $sinCambio
                      . ' | FALLIDOS: ' . $fallidos
                      . ' | TICKET DUPLICADO: ' . $duplicados;

        $this->info($resumenFinal);
        Log::channel('resumenes')->info($resumenFinal);

        if ($duplicados > 0) {
            $aviso = 'HAY ' . $duplicados . ' RESUMEN(ES) CON UN TICKET QUE NO ES SOLO SUYO. '
                   . 'NO SE CONSULTARON Y SEGUIRAN SIN RESOLVER HASTA QUE SE REVISEN A MANO.';

            $this->warn($aviso);
            Log::channel('resumenes')->warning($aviso);
        }

        if ($rechazados > 0) {
            // Un rechazo no se arregla solo: las boletas siguen marcadas como
            // enviadas y hoy no hay forma de devolverlas a pendientes desde la
            // interfaz. Que quede dicho para quien lea el log por la mañana.
            $aviso = 'HAY ' . $rechazados . ' RESUMEN(ES) RECHAZADO(S) POR SUNAT. REVISAR A MANO: '
                   . 'SUS BOLETAS SIGUEN MARCADAS COMO ENVIADAS.';

            $this->warn($aviso);
            Log::channel('resumenes')->warning($aviso);
        }

        return 0;
    }

    /**
     * Los tickets que estan en mas de un resumen, como {ticket => cuantos}.
     *
     * Hoy no hay ninguno, y lo normal es que nunca lo haya: el ticket lo da
     * SUNAT en cada envio. Pero si llegara a pasar, consultar a ciegas seria
     * peor que no consultar, porque dejaria el resumen en codigo 0 con datos
     * ajenos y ya no volveria a mirarse.
     *
     * @return array
     */
    private function ticketsRepetidos()
    {
        $filas = DB::select('SELECT r.ticket, COUNT(*) AS n
                    FROM resumenes AS r
                    WHERE r.ticket IS NOT NULL AND r.ticket <> ""
                    GROUP BY r.ticket
                    HAVING COUNT(*) > 1');

        $repetidos = [];
        foreach ($filas as $fila) {
            $repetidos[$fila->ticket] = (int) $fila->n;
        }

        return $repetidos;
    }

    /**
     * El controlador que hace la consulta.
     *
     * Aislado en un método para poder sustituirlo en pruebas por uno que no
     * hable con SUNAT. En producción devuelve el de siempre.
     *
     * @return \App\Http\Controllers\Ventas\ResumenController
     */
    protected function controlador()
    {
        return new ResumenController();
    }

    /**
     * Los resúmenes que se enviaron y aún no tienen respuesta de SUNAT.
     *
     * Se excluyen el 0 (aceptado) y el 99 (rechazado): ya son respuestas
     * definitivas y reconsultarlas sólo serviría para castigar el servicio.
     *
     * La fecha de corte se calcula en PHP y no con DATE_SUB(NOW(), ...): los
     * created_at los escribe la aplicación en hora de Lima, y si el MySQL del
     * servidor va en UTC un NOW() de SQL desplazaría la ventana cinco horas.
     *
     * @return array
     */
    private function resumenesSinResolver($dias, $limite)
    {
        $desde = Carbon::now()->subDays($dias)->format('Y-m-d H:i:s');

        // La columna 'estado' la añade la migración del borrado lógico. Si el
        // despliegue todavía no la tiene, el barrido no debe reventar por eso.
        $filtroEstado = Schema::hasColumn('resumenes', 'estado')
            ? ' AND r.estado = "ACTIVO" '
            : ' ';

        return DB::select('SELECT
                    r.id,
                    r.serie,
                    r.correlativo,
                    r.ticket,
                    r.code_estado,
                    r.created_at
                    FROM resumenes AS r
                    WHERE r.send_sunat = 1
                    AND r.ticket IS NOT NULL
                    AND r.ticket <> ""
                    AND (r.code_estado IS NULL OR CAST(r.code_estado AS UNSIGNED) NOT IN (0, 99))'
                    . $filtroEstado .
                    'AND r.created_at >= ?
                    ORDER BY r.created_at
                    LIMIT ' . (int) $limite,
                    array($desde));
    }
}
