<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoAlmacenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_almacenes', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('almacen_inicio_id')->unsigned()->nullable();
            $table->unsignedInteger('almacen_final_id')->unsigned();
            $table->unsignedDecimal('cantidad', 15,2);
            $table->string('nota');
            $table->mediumText('observacion');
            $table->unsignedInteger('usuario_id')->unsigned(); 
            $table->enum('movimiento',['SALIDA','INGRESO']);

            $table->unsignedInteger('producto_id')->unsigned();
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            
            $table->unsignedInteger('lote_id')->unsigned()->nullable();
            $table->foreign('lote_id')->references('id')->on('lote_productos')->onDelete('cascade');

            $table->unsignedInteger('compra_documento_id')->unsigned();
            $table->foreign('compra_documento_id')
                  ->references('id')->on('compra_documentos')
                  ->onDelete('cascade');
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
        Schema::dropIfExists('movimiento_almacenes');
    }
}
