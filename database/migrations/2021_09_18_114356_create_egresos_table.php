<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEgresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('egreso', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tipodocumento_id');
            $table->unsignedInteger('cuenta_id')->nullable(); //176
            $table->string('documento')->nullable();
            $table->text('descripcion');
            $table->unsignedDecimal('importe',15,2)->default(0);
            $table->unsignedDecimal('efectivo',15,2)->default(0);
            $table->unsignedDecimal('monto',15,2)->default(0);
            $table->unsignedInteger('tipo_pago_id');
            $table->foreign('tipo_pago_id')->references('id')->on('tipos_pago')->onDelete('cascade');

            $table->string("usuario")->nullable();
            $table->integer("user_id")->nullable();
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
        Schema::dropIfExists('egreso');
    }
}
