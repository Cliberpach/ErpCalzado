<?php

use App\Configuracion\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Configuracion::create([
            'slug' => 'CEC',
            'descripcion' => 'Cobrar en caja',
            'propiedad' => 'SI'
        ]);

        Configuracion::create([
            'slug'          => 'EARB',
            'descripcion'   => 'ENVÍO AUTOMÁTICO RESÚMENES BOLETAS',
            'propiedad'     => 'NO',
            'nro_dias'      =>  null
        ]);

        Configuracion::create([
            'slug'          => 'AG',
            'descripcion'   => 'AMBIENTE GREENTER',
            'propiedad'     => 'BETA',
            'nro_dias'      =>  null
        ]);

    }
}
