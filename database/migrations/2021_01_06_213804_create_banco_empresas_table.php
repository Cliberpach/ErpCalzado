<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBancoEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banco_empresas', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('empresa_id')->unsigned();
            $table->foreign('empresa_id')
                  ->references('id')->on('empresas')
                  ->onDelete('cascade');
            $table->longText('descripcion');
            $table->longText('tipo_moneda');
            $table->longText('num_cuenta');
            $table->longText('cci');
            $table->unsignedDecimal('itf', 15,2)->nullable();
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
        Schema::dropIfExists('banco_empresas');
    }
}
