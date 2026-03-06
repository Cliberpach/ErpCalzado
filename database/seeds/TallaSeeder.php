<?php

use App\Almacenes\Talla;
use Illuminate\Database\Seeder;

class TallaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $talla                  =   new Talla();
        $talla->descripcion     =   'TALLA';
        $talla->tipo            =   'FICTICIO';
        $talla->estado          =   'ANULADO';
        $talla->save();
    }
}
