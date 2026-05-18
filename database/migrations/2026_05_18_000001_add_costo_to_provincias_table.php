<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostoToProvinciasTable extends Migration
{
    public function up()
    {
        Schema::table('provincias', function (Blueprint $table) {
            $table->decimal('costo', 8, 2)->nullable()->default(null)->after('nombre');
        });
    }

    public function down()
    {
        Schema::table('provincias', function (Blueprint $table) {
            $table->dropColumn('costo');
        });
    }
}
