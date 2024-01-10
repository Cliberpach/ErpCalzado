<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StorageProcedureSpUpdatenumeraciondocumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        DROP PROCEDURE IF EXISTS sp_updateserializacion;
        CREATE PROCEDURE `sp_updateserializacion`(
            IN `documento_id` INT,
            IN `idserializacion` INT
        )
        BEGIN
           DECLARE _serie  varchar(10);
            DECLARE _correlativo INT; 
            SET _serie = (SELECT ef.serie FROM empresa_numeracion_facturaciones AS ef WHERE ef.id=idserializacion);
            SET _correlativo = (SELECT ef.numero_fin + 1 FROM empresa_numeracion_facturaciones AS ef WHERE ef.id=idserializacion);
            SELECT _serie;
            
            #actualiza el correlativo del documento
            UPDATE cotizacion_documento SET serie=_serie,correlativo=_correlativo WHERE id = documento_id;
            
            #update numero_fin empresa_numeracion_facturaciones
            UPDATE empresa_numeracion_facturaciones SET numero_fin = numero_fin + 1 WHERE id = idserializacion; 
        END";
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedure = "DROP PROCEDURE IF EXISTS sp_updateserializacion";
        DB::unprepared($procedure);
    }
}
