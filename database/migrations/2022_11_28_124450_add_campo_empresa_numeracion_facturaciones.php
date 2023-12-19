<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoEmpresaNumeracionFacturaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresa_numeracion_facturaciones', function (Blueprint $table) {
            $table->integer("numero_fin")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('empresa_numeracion_facturaciones', function (Blueprint $table) {
            $table->dropColumn("numero_fin");
        });
    }
}
