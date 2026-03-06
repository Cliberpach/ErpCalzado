<?php

use App\Almacenes\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categoria              =   new Categoria();
        $categoria->descripcion =   'CATEGORIA';
        $categoria->tipo        =   'FICTICIO';
        $categoria->estado      =   'ANULADO';
        $categoria->save();
    }
}
