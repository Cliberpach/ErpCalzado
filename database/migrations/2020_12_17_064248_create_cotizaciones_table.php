<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->Increments('id');

            $table->unsignedInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            $table->unsignedInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas');

            $table->unsignedBigInteger('sede_id');
            $table->foreign('sede_id')->references('id')->on('empresa_sedes');

            $table->unsignedInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');

            $table->unsignedBigInteger('condicion_id');
            $table->foreign('condicion_id')->references('id')->on('condicions');

            $table->unsignedInteger('registrador_id');
            $table->foreign('registrador_id')->references('id')->on('users');

            $table->string('almacen_nombre',160);
            $table->string('registrador_nombre',160);


            $table->date('fecha_documento');
            $table->date('fecha_atencion')->nullable();

            $table->unsignedDecimal('sub_total', 15, 2);
            $table->unsignedDecimal('monto_embalaje', 15, 2)->nullable();
            $table->unsignedDecimal('monto_envio', 15, 2)->nullable();
            $table->unsignedDecimal('total', 15, 2);
            $table->unsignedDecimal('total_igv', 15, 2);
            $table->unsignedDecimal('total_pagar', 15, 2);
            $table->unsignedDecimal('porcentaje_descuento', 15, 2)->nullable();
            $table->unsignedDecimal('monto_descuento', 15, 2)->nullable();

            $table->string('igv_check',2)->nullable();
            $table->char('igv',3)->nullable();

            $table->string('moneda');

            $table->unsignedInteger('venta_id')->nullable();
            $table->string('venta_serie',20)->nullable();
            $table->string('venta_correlativo',20)->nullable();

            $table->enum('estado',['VIGENTE','ATENDIDA', 'ANULADO', 'VENCIDA'])->default('VIGENTE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotizaciones');
    }
}
