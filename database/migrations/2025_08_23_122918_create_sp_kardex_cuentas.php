<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSpKardexCuentas extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_kardex_cuentas;
            CREATE PROCEDURE sp_kardex_cuentas(
                IN p_cuenta_id INT,
                IN p_fecha_inicio DATE,
                IN p_fecha_fin DATE
            )
            BEGIN

                DECLARE stock_actual DECIMAL(15,6) DEFAULT 0;
                DECLARE stock_anterior DECIMAL(15,6) DEFAULT 0;
                DECLARE cuenta_actual INT DEFAULT NULL;

                SET @stock_actual := 0, @stock_anterior := 0, @cuenta_actual := NULL;

                SELECT
                    k.id,
                    k.cuenta_bancaria_id,
                    k.fecha_registro,
                    k.metodo_pago_nombre,
                    k.tipo_documento,
                    k.documento,
                    k.banco_abreviatura,
                    k.nro_cuenta,
                    k.registrador_nombre,
                    @stock_anterior := IF(@cuenta_actual = k.cuenta_bancaria_id, @stock_actual, fn_calcular_saldo( k.cuenta_bancaria_id, p_fecha_inicio) ) AS stock_previo,
                    k.cantidad_entrada AS entrada,
                    k.cantidad_salida AS salida,
                    @stock_actual := @stock_anterior + k.cantidad_entrada - k.cantidad_salida AS stock_posterior,
                    @cuenta_actual := k.cuenta_bancaria_id AS cuenta_actual
                FROM (

                    SELECT
                        kc.id,
                        kc.cuenta_bancaria_id,
                        kc.created_at AS fecha_registro,
                        kc.metodo_pago_nombre,
                        kc.tipo_documento,
                        kc.documento,
                        kc.banco_abreviatura,
                        kc.nro_cuenta,
                        kc.registrador_nombre,
                        IF(kc.tipo_operacion = 'INGRESO',kc.monto,0) as cantidad_entrada,
                        IF(kc.tipo_operacion = 'EGRESO',kc.monto,0) as cantidad_salida
                    FROM kardex_cuentas AS kc
                    WHERE
                    kc.cuenta_bancaria_id = p_cuenta_id
                    AND kc.estado = 'ACTIVO'
                    AND (
                        (p_fecha_inicio IS NOT NULL AND p_fecha_fin IS NOT NULL AND DATE(kc.fecha_registro) BETWEEN p_fecha_inicio AND 						p_fecha_fin)
                            OR
                        (p_fecha_inicio IS NULL OR p_fecha_fin IS NULL)
                    )

                ) k
                ORDER BY k.cuenta_bancaria_id, k.fecha_registro ASC;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_kardex_cuentas");
    }
};
