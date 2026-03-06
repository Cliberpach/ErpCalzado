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
        $almacen->descripcion = "ALMACEN";
        $almacen->ubicacion = "ALMACEN";
        $almacen->sede_id = 1;
        $almacen->tipo          =   'FICTICIO';
        $almacen->estado        =   'ANULADO';
        $almacen->save();

        $almacen = new Almacen();
        $almacen->descripcion = "CENTRAL";
        $almacen->ubicacion = "TIENDA";
        $almacen->sede_id = 1;
        $almacen->tipo_almacen  =   'PRINCIPAL';
        $almacen->save();

        $almacen = new Almacen();
        $almacen->descripcion = "MERMAS";
        $almacen->ubicacion = "TIENDA";
        $almacen->sede_id = 1;
        $almacen->tipo_almacen  =   'SECUNDARIO';
        $almacen->save();
    }
}
