<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta_cliente', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cotizacion_documento_id');
            $table->foreign('cotizacion_documento_id')->references('id')->on('cotizacion_documento')->onDelete('cascade');
            $table->string('numero_doc')->nullable();
            $table->date('fecha_doc')->nullable();
            $table->unsignedDecimal('monto', 15,2);
            $table->text('acta')->nullable();
            $table->unsignedDecimal('saldo')->nullable()->default(0.00);            
            $table->enum('estado',['PENDIENTE','PAGADO','ANULADO'])->default('PENDIENTE');
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
        Schema::dropIfExists('cuenta_cliente');
    }
}
