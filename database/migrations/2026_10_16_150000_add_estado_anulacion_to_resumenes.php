<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade a `resumenes` las columnas del borrado lógico.
 *
 * La pantalla no tenía forma de deshacer un resumen: si se grababa mal, o si
 * el envío fallaba, las boletas quedaban marcadas y no había manera de volver
 * a resumirlas. Se elimina de forma lógica y sólo si nunca llegó a SUNAT
 * (send_sunat = 0); un resumen aceptado no se toca, porque sus boletas ya
 * están declaradas y volver a enviarlas sería declararlas dos veces.
 *
 * No se borra la fila para que quede rastro de quién deshizo qué y cuándo, y
 * para que el correlativo quede consumido en vez de reutilizarse.
 *
 * Sólo añade columnas: no usa ->change(), que necesita doctrine/dbal y no está
 * instalado en este proyecto.
 *
 * Compatible con PHP 7.4 y 8.3.
 */
class AddEstadoAnulacionToResumenes extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('resumenes')) {
            return;
        }

        Schema::table('resumenes', function (Blueprint $table) {
            if (!Schema::hasColumn('resumenes', 'estado')) {
                $table->enum('estado', array('ACTIVO', 'ANULADO'))
                      ->default('ACTIVO')
                      ->after('sede_id');
            }
            if (!Schema::hasColumn('resumenes', 'anulado_por_id')) {
                $table->unsignedBigInteger('anulado_por_id')->nullable()->after('registrador_nombre');
            }
            if (!Schema::hasColumn('resumenes', 'anulado_por_nombre')) {
                $table->string('anulado_por_nombre', 191)->nullable()->after('anulado_por_id');
            }
            if (!Schema::hasColumn('resumenes', 'anulado_at')) {
                $table->timestamp('anulado_at')->nullable()->after('anulado_por_nombre');
            }
            if (!Schema::hasColumn('resumenes', 'motivo_anulacion')) {
                $table->string('motivo_anulacion', 191)->nullable()->after('anulado_at');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('resumenes')) {
            return;
        }

        Schema::table('resumenes', function (Blueprint $table) {
            foreach (array('motivo_anulacion', 'anulado_at', 'anulado_por_nombre', 'anulado_por_id', 'estado') as $columna) {
                if (Schema::hasColumn('resumenes', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
}
