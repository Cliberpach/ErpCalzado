<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase 1.2 de docs/PLANIFICATIONS/2026-07-17-flujo-envio-domicilio.md
 * (ecommerceMerris): mismos códigos de ubigeo INEI que ya usan
 * `departamentos`/`provincias`/`distritos` acá — verificado que
 * ecommerceMerris usa el mismo estándar, no hace falta traducir nada.
 */
class AddUbigeoIdsToReservasWebTable extends Migration
{
    public function up()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->char('department_id', 2)->nullable()->after('cliente_direccion');
            $table->char('province_id', 4)->nullable()->after('department_id');
            $table->char('district_id', 6)->nullable()->after('province_id');
        });
    }

    public function down()
    {
        Schema::table('reservas_web', function (Blueprint $table) {
            $table->dropColumn(['department_id', 'province_id', 'district_id']);
        });
    }
}
