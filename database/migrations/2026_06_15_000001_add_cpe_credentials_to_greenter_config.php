<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCpeCredentialsToGreenterConfig extends Migration
{
    public function up()
    {
        Schema::table('greenter_config', function (Blueprint $table) {
            $table->string('cpe_client_id')->nullable()->after('clave_api_guia_remision');
            $table->string('cpe_client_secret')->nullable()->after('cpe_client_id');
        });
    }

    public function down()
    {
        Schema::table('greenter_config', function (Blueprint $table) {
            $table->dropColumn(['cpe_client_id', 'cpe_client_secret']);
        });
    }
}
