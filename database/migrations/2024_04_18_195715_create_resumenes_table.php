<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResumenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resumenes', function (Blueprint $table) {
            $table->id();
            $table->string('serie', 10)->nullable();
            $table->bigInteger('correlativo')->nullable();
            $table->boolean('regularize')->default(false);
            $table->boolean('send_sunat')->default(false);
            $table->string('ticket', 50)->nullable();
            $table->longText('response_error')->nullable();
            $table->string('cdr_response_id', 100)->nullable();
            $table->string('cdr_response_code', 10)->nullable();
            $table->longText('cdr_response_description')->nullable();
            $table->string('code_estado', 10)->nullable();
            $table->date('fecha_comprobantes')->nullable();
            $table->longText('ruta_xml')->nullable();
            $table->longText('ruta_cdr')->nullable();
            $table->string('summary_name', 100)->nullable();
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
        Schema::dropIfExists('resumenes');
    }
}
