<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->Increments('id');

            $table->unsignedInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            
            $table->unsignedInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            
            $table->foreignId('condicion_id')->nullable()->constrained()->onDelete('SET NULL');

            $table->unsignedInteger('vendedor_id')->nullable();

            $table->date('fecha_documento');
            $table->date('fecha_atencion')->nullable();

            $table->unsignedDecimal('sub_total', 15, 2);
            $table->unsignedDecimal('monto_embalaje', 15, 2)->nullable();
            $table->unsignedDecimal('monto_envio', 15, 2)->nullable();
            $table->unsignedDecimal('total', 15, 2);
            $table->unsignedDecimal('total_igv', 15, 2);
            $table->unsignedDecimal('total_pagar', 15, 2);
            $table->unsignedDecimal('porcentaje_descuento', 15, 2)->nullable();
            $table->unsignedDecimal('monto_descuento', 15, 2)->nullable();

            $table->string('igv_check',2)->nullable();
            $table->char('igv',3)->nullable();

            $table->string('moneda');

            //$table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('estado',['VIGENTE','ATENDIDA', 'ANULADO', 'VENCIDA'])->default('VIGENTE');
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
        Schema::dropIfExists('cotizaciones');
    }
}
