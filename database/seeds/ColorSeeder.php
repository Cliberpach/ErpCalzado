<?php

use App\Models\Almacenes\Color\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $color              =   new Color();
        $color->descripcion =   'COLOR';
        $color->tipo        =   'FICTICIO';
        $color->estado      =   'ANULADO';
        $color->save();
    }
}
