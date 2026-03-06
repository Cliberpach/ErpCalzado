<?php

use App\Almacenes\Marca;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $marca                  =   new Marca();
        $marca->marca           =   'MARCA';
        $marca->tipo            =   'FICTICIO';
        $marca->estado          =   'ANULADO';
        $marca->save();
    }
}
