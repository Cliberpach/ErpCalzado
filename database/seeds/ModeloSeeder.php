<?php

use App\Almacenes\Modelo;
use Illuminate\Database\Seeder;

class ModeloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modelo              =   new Modelo();
        $modelo->descripcion =   'MODELO';
        $modelo->tipo        =   'FICTICIO';
        $modelo->estado      =   'ANULADO';
        $modelo->save();
    }
}
