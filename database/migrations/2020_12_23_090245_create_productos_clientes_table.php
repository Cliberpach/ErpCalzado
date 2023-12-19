<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos_clientes', function (Blueprint $table) {

            $table->Increments('id');
            $table->string('cliente');
            $table->unsignedDecimal('monto', 15, 2)->default(0);
            $table->unsignedDecimal('porcentaje', 15, 2)->default(0);
            $table->unsignedInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');

            $table->string('moneda');

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
        Schema::dropIfExists('productos_clientes');
    }
}
