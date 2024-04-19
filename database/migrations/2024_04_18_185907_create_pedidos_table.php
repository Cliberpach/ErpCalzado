<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cliente_id');
            $table->string('cliente_nombre');
            $table->unsignedInteger('empresa_id');
            $table->string('empresa_nombre');


            $table->unsignedDecimal('sub_total', 15, 2);
            $table->unsignedDecimal('total', 15, 2);
            $table->unsignedDecimal('total_igv', 15, 2);
            $table->unsignedDecimal('total_pagar', 15, 2);
            $table->unsignedDecimal('monto_embalaje',15,2)->nullable();
            $table->unsignedDecimal('monto_envio',15,2)->nullable();

            $table->unsignedDecimal('porcentaje_descuento', 15, 2)->nullable();
            $table->unsignedDecimal('monto_descuento', 15, 2)->nullable();

            $table->date('fecha_registro');
            $table->enum('estado', ['PENDIENTE', 'ATENDIDO'])->default('PENDIENTE');
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('empresa_id')->references('id')->on('empresas');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
}
