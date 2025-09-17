<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFnCalcularSaldo extends Migration
{
     /**
     * Run the migrations.
     */
    public function up(): void
    {

        DB::unprepared('
            DROP FUNCTION IF EXISTS fn_calcular_saldo;
            CREATE FUNCTION `fn_calcular_saldo`(`p_cuenta_bancaria_id` INT, `p_fecha` DATE)
            RETURNS decimal(15,6)
            DETERMINISTIC
            BEGIN
                DECLARE p_saldo DECIMAL(15,6) DEFAULT 0;

                SELECT COALESCE(SUM(cantidad_entrada) - SUM(cantidad_salida), 0)
                INTO p_saldo
                FROM (

                    SELECT
                        SUM(CASE WHEN kc.tipo_operacion = "INGRESO" THEN kc.monto ELSE 0 END) AS cantidad_entrada,
                        SUM(CASE WHEN kc.tipo_operacion = "EGRESO" THEN kc.monto ELSE 0 END) AS cantidad_salida
                    FROM kardex_cuentas AS kc
                    WHERE
                        (kc.cuenta_bancaria_id = p_cuenta_bancaria_id)
                        AND kc.estado = "ACTIVO"
                        AND DATE(kc.fecha_registro) < p_fecha

                ) AS k;

                RETURN p_saldo;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fn_calcular_saldo');
    }
};
