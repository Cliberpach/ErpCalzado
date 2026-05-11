<?php

namespace App\Http\Services\Almacen\Productos;

use App\Almacenes\CodigoBarra;
use App\Almacenes\Producto;
use App\Almacenes\ProductoColor;
use App\Almacenes\ProductoColorTalla;
use App\Almacenes\Talla;
use App\Http\Services\Almacen\Tallas\TallaService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductoService
{
    private ProductoMapper $s_mapper;
    private ProductoRepository $s_repository;
    private ProductoDto $s_dto;
    private ProductValidation $s_validation;

    public function __construct()
    {
        $this->s_repository =   new ProductoRepository();
        $this->s_mapper     =   new ProductoMapper();
        $this->s_dto        =   new ProductoDto();
        $this->s_validation =   new ProductValidation();
    }

    public function store(array $data): Producto
    {
        $data   =   $this->s_validation->validationStore($data);

        $dto    =   $this->s_dto->dtoStore($data);
        $instance   =   $this->s_repository->store($dto);

        $dto_features   =   $this->s_dto->dtoProductFeatures($data['features'], $instance->id);
        $this->s_repository->storeMasiveFeatures($dto_features);

        //======= GUARDAMOS LOS COLORES ASIGNADOS AL PRODUCTO ========
        $coloresAsignados = json_decode($data['coloresJSON']);
        foreach ($coloresAsignados as $color_id) {
            $almacen_id                     =   $data['almacen'];

            $producto_color                 =   new ProductoColor();
            $producto_color->almacen_id     =   $almacen_id;
            $producto_color->producto_id    =   $instance->id;
            $producto_color->color_id       =   $color_id;
            $producto_color->save();
        }

        $instance->codigo = 1000 + $instance->id;
        $instance->update();

        //========= GUARDAR IMAGENES =========
        $this->s_repository->saveImages($data, $instance);

        return $instance;
    }

    public function update(array $data, int $id): Producto
    {
        $data   =   $this->s_validation->validationStore($data);
        $dto_store  =   $this->s_dto->dtoStore($data);

        $instance   =   $this->s_repository->update($dto_store, $id);

        $this->s_repository->destroyFeatures($id);
        $dto_features   =   $this->s_dto->dtoProductFeatures($data['features'], $instance->id);
        $this->s_repository->storeMasiveFeatures($dto_features);

        //=========== EDITAMOS LOS COLORES DEL PRODUCTO ==========
        $coloresNuevos = json_decode($data['coloresJSON']); //['A','C']     ['A','R','C']  ['A','B']

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
                $data['almacen']
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
                ->where('almacen_id', $data['almacen'])
                ->delete();
            //===== ELIMINANDO TALLAS DEL COLOR DEL ALMACÉN =====
            DB::table('producto_color_tallas')
                ->where('producto_id', $id)
                ->where('color_id', $value)
                ->where('almacen_id', $data['almacen'])
                ->delete();
        }

        //======== CASO II: COLORES DE LA LISTA NUEVA NO ESTÁN EN LA LISTA ANTERIOR ======
        //===== DEBEN REGISTRARSE =====
        $colores_diferentes_2 = array_diff($ids_colores_nuevos, $ids_colores_anteriores);
        foreach ($colores_diferentes_2 as $key => $value) {
            //==== REGISTRANDO COLORES ======
            $this->s_repository->insertarProductoColor($data['almacen'], $id, $value);
        }

        //======== ACTUALIZAR IMÁGENES =========
        $this->s_repository->actualizarImagenes($data, $instance);

        return $instance;
    }

    public function generarAdhesivos(array $data): Collection
    {
        $almacen_id     =   $data['almacen_id'];
        $producto_id    =   $data['producto_id'];
        $color_id       =   $data['color_id'];
        $tallas_bd      =   Talla::where('estado', 'ACTIVO')->get();

        foreach ($tallas_bd as $talla) {

            $existeTalla    =   ProductoColorTalla::where('almacen_id', $almacen_id)
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
            ->join('modelos as m', 'm.id', 'p.modelo_id')
            ->join('categorias as ca', 'ca.id', 'p.categoria_id')
            ->join('codigos_barra as cb', function ($join) {
                $join->on('cb.producto_id', '=', 'pct.producto_id')
                    ->on('cb.color_id', '=', 'pct.color_id')
                    ->on('cb.talla_id', '=', 'pct.talla_id');
            })
            ->where('pct.almacen_id', $almacen_id)
            ->where('pct.producto_id', $producto_id)
            ->where('pct.color_id', $color_id)
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

    public function destroy(int $id): Producto
    {
        $producto = Producto::findOrFail($id);
        $producto->estado = 'ANULADO';
        $producto->update();

        //========== ANULAMOS PRODUCTO COLORES Y PRODUCTO COLOR TALLAS =========
        DB::table('producto_colores')
            ->where('producto_id', $id)
            ->update([
                "estado"        =>  'ANULADO',
                "updated_at"    =>  Carbon::now()
            ]);

        DB::table('producto_color_tallas')
            ->where('producto_id', $id)
            ->update([
                "estado"        =>  'ANULADO',
                "updated_at"    =>  Carbon::now()
            ]);

        return $producto;
    }

    public function getProductoColores(int $almacen_id, int $producto_id)
    {
        return $this->s_repository->getProductoColores($almacen_id, $producto_id);
    }

    public function getProductoStocks(int $almacen_id, int $producto_id)
    {
        return $this->s_repository->getProductoStocks($almacen_id, $producto_id);
    }

    public function getPreciosVenta(int $producto_id)
    {
        return $this->s_repository->getPreciosVenta($producto_id);
    }

    public function getColoresTalla(int $almacen_id, int $producto_id)
    {
        $s_talla        =   new TallaService();

        $precios_venta  =   $this->getPreciosVenta($producto_id);
        $colores        =   $this->getProductoColores($almacen_id, $producto_id);
        $stocks         =   $this->getProductoStocks($almacen_id, $producto_id);
        $tallas         =   $s_talla->getTallas();

        $producto_color_tallas  =   null;
        if (count($colores) > 0) {
            $producto_color_tallas  =   $this->s_mapper->formatearColoresTallas($colores, $stocks, $tallas);
        }

        return (object)[
            'producto_color_tallas' =>  $producto_color_tallas,
            'precios_venta'        =>  $precios_venta
        ];
    }
}
