<?php

namespace App\Http\Services\Almacen\Productos;

use App\Almacenes\CodigoBarra;
use App\Almacenes\Producto;
use App\Almacenes\ProductoColor;
use App\Almacenes\ProductoColorTalla;
use App\Almacenes\Talla;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductoService
{
    private ProductoRepository $s_repository;

      public function __construct()
    {
        $this->s_repository =   new ProductoRepository();
    }

    public function registrar(array $datos): Producto
    {
        //======= GUARDANDO PRODUCTO =======
        $producto                   =   new Producto();
        $producto->nombre           =   $datos['nombre'];
        $producto->marca_id         =   $datos['marca'];
        $producto->categoria_id     =   $datos['categoria'];
        $producto->modelo_id        =   $datos['modelo'];
        $producto->medida           =   105;
        $producto->precio_venta_1   =   $datos['precio1'];
        $producto->precio_venta_2   =   $datos['precio2'];
        $producto->precio_venta_3   =   $datos['precio3'];
        $producto->costo            =   $datos['costo'] ?? 0;
        $producto->save();

        //======= GUARDAMOS LOS COLORES ASIGNADOS AL PRODUCTO ========
        $coloresAsignados = json_decode($datos['coloresJSON']);

        foreach ($coloresAsignados as $color_id) {
            $almacen_id                     =   $datos['almacen'];

            $producto_color                 =   new ProductoColor();
            $producto_color->almacen_id     =   $almacen_id;
            $producto_color->producto_id    =   $producto->id;
            $producto_color->color_id       =   $color_id;
            $producto_color->save();
        }

        $producto->codigo = 1000 + $producto->id;
        $producto->update();

        //========= GUARDAR IMAGENES =========
        $this->s_repository->guardarImagenes($datos, $producto);

        return $producto;
    }

    public function actualizar(array $datos, int $id): Producto
    {
        $producto   =   $this->s_repository->actualizarProducto($datos, $id);

        //=========== EDITAMOS LOS COLORES DEL PRODUCTO ==========
        $coloresNuevos = json_decode($datos['coloresJSON']); //['A','C']     ['A','R','C']  ['A','B']

        //===== OBTENIENDO COLORES ANTERIORES DEL PRODUCTO ALMACÉN ===== //['A','R','C']     ['A','C']   ['A','B']
        $colores_anteriores =   DB::select(
            'SELECT
                                        pc.producto_id AS producto_id,
                                        pc.color_id AS color_id,
                                        pc.almacen_id
                                    FROM producto_colores AS pc
                                    WHERE
                                    pc.producto_id = ?
                                    AND pc.almacen_id = ?',
            [
                $id,
                $datos['almacen']
            ]
        );

        $collection_colores_anteriores  =   collect($colores_anteriores);
        $collection_colores_nuevos      =   collect($coloresNuevos);

        $ids_colores_anteriores = $collection_colores_anteriores->pluck('color_id')->toArray();
        $ids_colores_nuevos     = $collection_colores_nuevos->toArray();

        //===== CASO I: COLORES DE LA LISTA ANTERIOR NO ESTÁN EN LA LISTA NUEVA =====
        //===== DEBEN DE ELIMINARSE =====
        $colores_diferentes_1 = array_diff($ids_colores_anteriores, $ids_colores_nuevos);
        foreach ($colores_diferentes_1 as $key => $value) {
            //==== ELIMINANDO COLORES DEL ALMACÉN ======
            DB::table('producto_colores')
                ->where('producto_id', $id)
                ->where('color_id', $value)
                ->where('almacen_id', $datos['almacen'])
                ->delete();
            //===== ELIMINANDO TALLAS DEL COLOR DEL ALMACÉN =====
            DB::table('producto_color_tallas')
                ->where('producto_id', $id)
                ->where('color_id', $value)
                ->where('almacen_id', $datos['almacen'])
                ->delete();
        }

        //======== CASO II: COLORES DE LA LISTA NUEVA NO ESTÁN EN LA LISTA ANTERIOR ======
        //===== DEBEN REGISTRARSE =====
        $colores_diferentes_2 = array_diff($ids_colores_nuevos, $ids_colores_anteriores);
        foreach ($colores_diferentes_2 as $key => $value) {
            //==== REGISTRANDO COLORES ======
            $this->s_repository->insertarProductoColor($datos['almacen'], $id, $value);
        }

        //======== ACTUALIZAR IMÁGENES =========
        $this->s_repository->actualizarImagenes($datos,$producto);

        return $producto;
    }

    
    public function generarAdhesivos(array $data):Collection
    {
        $almacen_id     =   $data['almacen_id'];
        $producto_id    =   $data['producto_id'];
        $color_id       =   $data['color_id'];
        $tallas_bd      =   Talla::where('estado', 'ACTIVO')->get();

        foreach ($tallas_bd as $talla) {

            $existeTalla    =   ProductoColorTalla::where('almacen_id',$almacen_id)
                                ->where('producto_id', $producto_id)
                                ->where('color_id', $color_id)
                                ->where('talla_id', $talla->id)
                                ->exists();

            if (!$existeTalla) {
                $nueva_talla                =   new ProductoColorTalla();
                $nueva_talla->almacen_id    =   $almacen_id;
                $nueva_talla->producto_id   =   $producto_id;
                $nueva_talla->color_id      =   $color_id;
                $nueva_talla->talla_id      =   $talla->id;
                $nueva_talla->stock         =   0;
                $nueva_talla->stock_logico  =   0;
                $nueva_talla->save();
            }

            //======== CREAR CÓDIGO BARRAS ========
            $this->generarCodigoBarras($producto_id, $color_id, $talla->id);

        }

        //======== GENERAR PDF CON ETIQUETAS ==========
        $items  =   DB::table('producto_color_tallas as pct')
                    ->join('productos as p', 'p.id', '=', 'pct.producto_id')
                    ->join('colores as c', 'c.id', '=', 'pct.color_id')
                    ->join('tallas as t', 't.id', '=', 'pct.talla_id')
                    ->join('modelos as m','m.id','p.modelo_id')
                    ->join('categorias as ca','ca.id','p.categoria_id')
                    ->join('codigos_barra as cb', function ($join) {
                        $join->on('cb.producto_id', '=', 'pct.producto_id')
                            ->on('cb.color_id', '=', 'pct.color_id')
                            ->on('cb.talla_id', '=', 'pct.talla_id');
                    })
                    ->where('pct.almacen_id',$almacen_id)
                    ->where('pct.producto_id',$producto_id)
                    ->where('pct.color_id',$color_id)
                    ->select(
                        'm.id as modelo_id',
                        'p.id as producto_id',
                        'c.id as color_id',
                        't.id as talla_id',
                        'ca.descripcion as categoria_nombre',
                        'm.descripcion as modelo_nombre',
                        'p.nombre as producto_nombre',
                        'c.descripcion as color_nombre',
                        't.descripcion as talla_nombre',
                        'cb.codigo_barras',
                        'cb.ruta_cod_barras'
                    )
                    ->get();

        return $items;
    }


    public function generarCodigoBarras(int $producto_id, int $color_id, int $talla_id)
    {

        //======= BUSCAR SI YA TIENE UN CÓDIGO GENERADO =======
        $codigo_barra   =   CodigoBarra::where('producto_id', $producto_id)
            ->where('color_id', $color_id)
            ->where('talla_id', $talla_id)
            ->first();

        //======== SI EL PRODUCTO COLOR TALLA NO TIENE CÓDIGO DE BARRA ========
        if (!$codigo_barra) {


            //========= GENERAR IDENTIFICADOR ÚNICO PARA EL COD BARRAS ========
            $key            =   generarCodigo(8);

            //======== GENERAR IMG DEL COD BARRAS ========
            $generatorPNG   =   new \Picqer\Barcode\BarcodeGeneratorPNG();
            $code           =   $generatorPNG->getBarcode($key, $generatorPNG::TYPE_CODE_128);

            $name           =   $key . '.png';

            if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'productos'))) {
                mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'productos'));
            }

            $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'productos' . DIRECTORY_SEPARATOR . $name);

            file_put_contents($pathToFile, $code);

            //======== GUARDAR KEY Y RUTA IMG ========
            $codigoBarra                    = new CodigoBarra();
            $codigoBarra->producto_id       = $producto_id;
            $codigoBarra->color_id          = $color_id;
            $codigoBarra->talla_id          = $talla_id;
            $codigoBarra->codigo_barras     = $key;
            $codigoBarra->ruta_cod_barras   = 'public/productos/' . $name;
            $codigoBarra->save();
        }
    }
}
