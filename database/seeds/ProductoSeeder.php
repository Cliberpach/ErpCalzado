<?php

use Illuminate\Database\Seeder;
use App\Almacenes\Producto;
use App\Almacenes\Color;
use App\Almacenes\Talla;
use App\Almacenes\Modelo;
use App\Almacenes\Categoria;
use App\Almacenes\Marca;
use App\Almacenes\Familia;
use App\Almacenes\SubFamilia;
use App\Almacenes\LoteProducto;
use Carbon\Carbon;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //========= COLORES ===============
        // $color1= new Color();
        // $color1->descripcion = "AZUL";
        // $color1->save();

        // $color2= new Color();
        // $color2->descripcion = "NEGRO";
        // $color2->save();

        $color3= new Color();
        $color3->descripcion = "BLANCO";
        $color3->save();

        // $color4= new Color();
        // $color4->descripcion = "CELESTE";
        // $color4->save();

        // $color5= new Color();
        // $color5->descripcion = "ANARANJADO";
        // $color5->save();

        // $color6= new Color();
        // $color6->descripcion = "PLATEADO";
        // $color6->save();

        //=========== TALLAS =============
        $talla1 = new Talla();
        $talla1->descripcion = "34";
        $talla1->save();

        // $talla2 = new Talla();
        // $talla2->descripcion = "35";
        // $talla2->save();

        // $talla3 = new Talla();
        // $talla3->descripcion = "37";
        // $talla3->save();

        // $talla4 = new Talla();
        // $talla4->descripcion = "39";
        // $talla4->save();

        // $talla5 = new Talla();
        // $talla5->descripcion = "41";
        // $talla5->save();

        //====== MARCAS ========
        // $marca1 = new Marca();
        // $marca1->marca = "ADIDAS";
        // $marca1->save();

        // $marca2 = new Marca();
        // $marca2->marca = "NIKE";
        // $marca2->save();

        // $marca3 = new Marca();
        // $marca3->marca = "PUMA";
        // $marca3->save();

        //======= CATEGORIAS ========
        // $categoria1 =  new Categoria();
        // $categoria1->descripcion = "BOTAS";
        // $categoria1->save();

        // $categoria2 =  new Categoria();
        // $categoria2->descripcion = "ZAPATILLAS";
        // $categoria2->save();

        // $categoria3 =  new Categoria();
        // $categoria3->descripcion = "SANDALIAS";
        // $categoria3->save();

        // $categoria4 =  new Categoria();
        // $categoria4->descripcion = "TACONES";
        // $categoria4->save();

        //========= MODELO ============
        // $modelo1 = new Modelo();
        // $modelo1->descripcion = "DELUXE";
        // $modelo1->save();

        // $modelo2 = new Modelo();
        // $modelo2->descripcion = "FINO";
        // $modelo2->save();

        // $modelo3 = new Modelo();
        // $modelo3->descripcion = "ULTRA";
        // $modelo3->save();

        //========== PRODUCTO ===========
        // $producto = new Producto();
        // $producto->categoria_id = $categoria1->id;
        // $producto->marca_id = $marca1->id;
        // $producto->modelo_id = $modelo1;
        // $producto->almacen_id = 1;
        

    }
}
