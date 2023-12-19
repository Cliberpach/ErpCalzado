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
    }
}
