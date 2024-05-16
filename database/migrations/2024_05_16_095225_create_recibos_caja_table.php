<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecibosCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibos_caja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');
            $table->foreignId('caja_id')->references('id')->on('caja')->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('cliente_id'); 
            $table->decimal('monto', 10, 2);
            $table->string('metodo_pago', 100);
            $table->enum('estado',['ACTIVO','ANULADO'])->default('ACTIVO');
            $table->enum('estado_servicio',['LIBRE','CANJE'])->default('LIBRE');


            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cliente_id')->references('id')->on('clientes');

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
        Schema::dropIfExists('recibos_caja');
    }
}
