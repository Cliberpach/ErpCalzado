<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBancosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bancos', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('proveedor_id')->unsigned();
            $table->foreign('proveedor_id')
                  ->references('id')->on('proveedores')
                  ->onDelete('cascade');
            $table->string('descripcion');
            $table->string('tipo_moneda');
            $table->string('num_cuenta');
            $table->string('cci');
            $table->unsignedDecimal('itf', 15,2);
            $table->enum('estado',['ACTIVO','ANULADO'])->default('ACTIVO');
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
        Schema::dropIfExists('bancos');
    }
}
