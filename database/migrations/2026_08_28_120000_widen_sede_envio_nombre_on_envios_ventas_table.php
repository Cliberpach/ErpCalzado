<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Amplía envios_ventas.sede_envio_nombre de VARCHAR(255) a VARCHAR(300).
 *
 * MOTIVO
 * El valor de esta columna se copia tal cual desde empresa_envio_sedes.direccion,
 * que en el esquema real es VARCHAR(300). El destino era VARCHAR(255): 45
 * caracteres menos que el origen. Cualquier sede con dirección de 256..300
 * caracteres rompía el INSERT de despachos con:
 *     SQLSTATE[22001] Data too long for column 'sede_envio_nombre'
 *
 * NO se trunca en PHP a propósito: este campo se imprime en el comprobante
 * (resources/views/ventas/documentos/impresion/comprobante_ticket.blade.php)
 * y es la dirección de agencia que usa el transportista.
 *
 * SE ESCRIBE CONTRA EL ESQUEMA REAL, NO CONTRA EL DECLARADO
 * Las migraciones de 2024_05_02 declararon estas columnas con $table->string()
 * y Schema::defaultStringLength(191), es decir VARCHAR(191). En producción se
 * aplicaron ALTER manuales fuera de control de versiones y hoy 15 de las 18
 * columnas de texto de envios_ventas difieren de su migración
 * (sede_envio_nombre 191->255, direccion_entrega 191->150, origen_venta 191->50...).
 * Por eso aquí se usa SQL explícito en lugar de $table->string(...)->change():
 *   1. doctrine/dbal no está instalado, requisito de ->change() en Laravel 7.
 *   2. envios_ventas tiene columnas enum (estado, modo) que dbal no sabe leer.
 *   3. ->change() reconstruiría la definición a partir del Blueprint, no de la
 *      tabla real, y arrastraría la deriva en lugar de respetarla.
 *
 * El CHARACTER SET y el COLLATE se declaran de forma explícita porque la
 * columna es utf8mb4_unicode_ci mientras que la tabla tiene por defecto
 * utf8mb4_0900_ai_ci: omitirlos cambiaría el cotejamiento en silencio.
 *
 * La columna no participa en ningún índice, así que MySQL 8 resuelve el cambio
 * en modo INPLACE, sin reconstruir la tabla.
 */
class WidenSedeEnvioNombreOnEnviosVentasTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('envios_ventas')) {
            return;
        }

        DB::statement("
            ALTER TABLE `envios_ventas`
            MODIFY `sede_envio_nombre`
                VARCHAR(300)
                CHARACTER SET utf8mb4
                COLLATE utf8mb4_unicode_ci
                NULL DEFAULT NULL
        ");
    }

    public function down()
    {
        if (!Schema::hasTable('envios_ventas')) {
            return;
        }

        // Revertir sólo es seguro si ningún valor supera los 255 caracteres.
        $excedidos = DB::table('envios_ventas')
            ->whereRaw('CHAR_LENGTH(sede_envio_nombre) > 255')
            ->count();

        if ($excedidos > 0) {
            throw new \RuntimeException(
                "No se puede revertir: {$excedidos} fila(s) de envios_ventas tienen " .
                "sede_envio_nombre con más de 255 caracteres y se truncarían. " .
                "Revise esas filas antes de volver a VARCHAR(255)."
            );
        }

        DB::statement("
            ALTER TABLE `envios_ventas`
            MODIFY `sede_envio_nombre`
                VARCHAR(255)
                CHARACTER SET utf8mb4
                COLLATE utf8mb4_unicode_ci
                NULL DEFAULT NULL
        ");
    }
}
