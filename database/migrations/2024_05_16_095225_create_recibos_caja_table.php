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
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->unsignedInteger('cliente_id')->nullable()->index();
            $table->decimal('monto', 10, 2)->nullable();
            $table->string('metodo_pago', 100)->nullable();
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');
            $table->enum('estado_servicio', ['LIBRE', 'USANDO', 'CANJEADO'])->default('LIBRE');

            $table->decimal('saldo', 10, 2)->nullable();
            $table->unsignedBigInteger('movimiento_id')->nullable()->index();
            $table->longText('img_pago')->nullable();
            $table->longText('img_pago_2')->nullable();
            $table->longText('observacion')->nullable();
            $table->timestamps();


            $table->foreign('movimiento_id')->references('id')->on('movimiento_caja')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recibos_caja', function (Blueprint $table) {
            $table->dropForeign(['movimiento_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['cliente_id']);
        });
        Schema::dropIfExists('recibos_caja');
    }
}
