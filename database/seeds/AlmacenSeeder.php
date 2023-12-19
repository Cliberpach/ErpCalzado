<?php

use App\Almacenes\Almacen;
use Illuminate\Database\Seeder;

class AlmacenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $almacen = new Almacen();
        $almacen->descripcion="CENTRAL";
        $almacen->ubicacion="TIENDA";
        $almacen->save();

    }
}
