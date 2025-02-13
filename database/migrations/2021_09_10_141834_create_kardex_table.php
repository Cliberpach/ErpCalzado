<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKardexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kardex', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sede_id');
            $table->foreign('sede_id')->references('id')->on('empresa_sedes');

            $table->unsignedInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            $table->unsignedInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->unsignedInteger('color_id');
            $table->foreign('color_id')->references('id')->on('colores');

            $table->unsignedInteger('talla_id');
            $table->foreign('talla_id')->references('id')->on('tallas');

            $table->string('almacen_nombre',160);
            $table->string('producto_nombre',160);
            $table->string('color_nombre',160);
            $table->string('talla_nombre',160);
            
            $table->unsignedDecimal('cantidad', 15,2);
            $table->unsignedDecimal('precio', 15,2)->nullable();
            $table->unsignedDecimal('importe', 15,2)->nullable();

            $table->string('accion',160)->nullable();
            $table->unsignedDecimal('stock', 15,2); 
            $table->string('numero_doc',160)->nullable();

            $table->unsignedInteger('documento_id')->nullable();

            $table->unsignedInteger('registrador_id');
            $table->foreign('registrador_id')->references('id')->on('users');
            $table->string('registrador_nombre',200);

            $table->date('fecha')->nullable();
            $table->string('descripcion',200)->nullable();
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
        Schema::dropIfExists('kardex');
    }
}
