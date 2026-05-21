<?php

namespace App\Http\Services\Almacen\Productos;

use App\Almacenes\Producto;
use App\Almacenes\ProductoColor;
use App\Models\Almacenes\Producto\ProductFeature;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ProductoRepository
{

    public function store(array $dto): Producto
    {
        return Producto::create($dto);
    }

    public function update(array $dto, int $id): Producto
    {
        $product                   =   Producto::findOrFail($id);
        $product->update($dto);
        return $product;
    }

    public function insertarProductoColor(int $almacen_id, int $producto_id, int $color_id)
    {
        $producto_color                 =   new ProductoColor();
        $producto_color->almacen_id     =   $almacen_id;
        $producto_color->producto_id    =   $producto_id;
        $producto_color->color_id       =   $color_id;
        $producto_color->save();
    }

    public function saveImages(array $datos, Producto $producto)
    {
        $destinationPath = public_path('storage/productos/img');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        for ($i = 1; $i <= 5; $i++) {
            $inputName = "imagen{$i}";

            if (isset($datos[$inputName]) && $datos[$inputName] instanceof UploadedFile) {
                $file = $datos[$inputName];

                $filename = "img{$i}_producto{$producto->id}_" . '.' . $file->getClientOriginalExtension();

                $file->move($destinationPath, $filename);

                $producto->{"img{$i}_ruta"} = "storage/productos/img/" . $filename;
                $producto->{"img{$i}_nombre"} = $filename;
            }
        }
        $producto->update();
    }

    public function actualizarImagenes(array $datos, Producto $producto)
    {
        $destinationPath = public_path('storage/productos/img');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        for ($i = 1; $i <= 5; $i++) {
            $inputName   = "imagen{$i}";       // archivo subido
            $rutaCampo   = "img{$i}_ruta";     // columna en la BD
            $nombreCampo = "img{$i}_nombre";   // columna en la BD

            // Caso 1: eliminar
            if (!empty($datos["remove_{$inputName}"])) {
                if ($producto->$rutaCampo && File::exists(public_path($producto->$rutaCampo))) {
                    File::delete(public_path($producto->$rutaCampo));
                }
                $producto->$rutaCampo = null;
                $producto->$nombreCampo = null;
            }

            // Caso 2: nueva imagen
            elseif (isset($datos[$inputName]) && $datos[$inputName] instanceof \Illuminate\Http\UploadedFile) {
                $file = $datos[$inputName];

                if ($producto->$rutaCampo && File::exists(public_path($producto->$rutaCampo))) {
                    File::delete(public_path($producto->$rutaCampo));
                }

                $filename = "img{$i}_producto{$producto->id}_" . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);

                $producto->$rutaCampo = "storage/productos/img/" . $filename;
                $producto->$nombreCampo = $filename;
            }

            // limpiar inputs que no son columnas
            unset($datos[$inputName], $datos["remove_{$inputName}"]);
        }

        // Guardamos todo
        $producto->update();

        return $producto;
    }

    public function getProductoColores(int $almacen_id, int $producto_id)
    {
        $colores   =   DB::select(
            'SELECT
            pc.almacen_id,
            p.id as producto_id,
            p.nombre as producto_nombre,
            c.id as color_id,
            c.descripcion as color_nombre
            from producto_colores as pc
            inner join productos as p on p.id = pc.producto_id
            inner join colores as c on c.id = pc.color_id
            where
            p.id = ?
            AND pc.almacen_id = ?
            AND c.estado = "ACTIVO"
            AND p.estado = "ACTIVO"
            group by pc.almacen_id,p.id,p.nombre,c.id,c.descripcion
            order by p.id,c.id',
            [$producto_id, $almacen_id]
        );

        return $colores;
    }

    public function getProductoStocks(int $almacen_id, int $producto_id)
    {
        $stocks = DB::table('producto_color_tallas as pct')
            ->join('productos as p',   'p.id',  '=', 'pct.producto_id')
            ->join('colores as c',     'c.id',  '=', 'pct.color_id')
            ->join('tallas as t',      't.id',  '=', 'pct.talla_id')
            ->join('marcas as ma',     'ma.id', '=', 'p.marca_id')
            ->join('categorias as ca', 'ca.id', '=', 'p.categoria_id')
            ->join('modelos as mo',    'mo.id', '=', 'p.modelo_id')
            ->where('p.estado', 'ACTIVO')
            ->where('c.estado', 'ACTIVO')
            ->where('t.estado', 'ACTIVO')
            ->where('pct.almacen_id', $almacen_id)
            ->where('pct.producto_id', $producto_id)
            ->select(
                'p.id as producto_id',
                'p.nombre as producto_nombre',
                'c.id as color_id',
                't.id as talla_id',
                'pct.stock_logico',
                'pct.stock'
            );

        return $stocks->get();
    }

    public function getVariantsConStock(int $almacen_id, int $producto_id)
    {
        $stocks = DB::table('producto_color_tallas as pct')
            ->join('productos as p',   'p.id',  '=', 'pct.producto_id')
            ->join('colores as c',     'c.id',  '=', 'pct.color_id')
            ->join('tallas as t',      't.id',  '=', 'pct.talla_id')
            ->join('marcas as ma',     'ma.id', '=', 'p.marca_id')
            ->join('categorias as ca', 'ca.id', '=', 'p.categoria_id')
            ->join('modelos as mo',    'mo.id', '=', 'p.modelo_id')
            ->where('p.estado', 'ACTIVO')
            ->where('c.estado', 'ACTIVO')
            ->where('t.estado', 'ACTIVO')
            ->where('pct.almacen_id', $almacen_id)
            ->where('pct.producto_id', $producto_id)
            ->where('pct.stock_logico', '>', 0)
            ->select('p.id as producto_id', 'p.nombre as producto_nombre', 'c.id as color_id', 't.id as talla_id', 'pct.stock_logico');

        return $stocks->get();
    }

    public function getPreciosVenta(int $producto_id)
    {
        $columns = Schema::getColumnListing('productos');

        $precioColumns = collect($columns)
            ->filter(function ($col) {
                return str_starts_with($col, 'precio_venta');
            })
            ->toArray();

        $selectColumns = array_merge([], $precioColumns);

        $precios_venta = Producto::from('productos as p')
            ->where('p.id', $producto_id)
            ->where('p.estado', 'ACTIVO')
            ->select($selectColumns)
            ->first();

        $sale_prices    =   [];
        $tipos_clientes =   [
            'UNIDAD',
            'SURTIDO',
            'EMPRENDEDOR',
            'SERIADO'
        ];
        $index  =   0;
        foreach ($precioColumns as $key => $name_column) {
            $sale_price =   $precios_venta[$name_column];
            if (!$sale_price) continue;
            $sale_price =   (object)[
                'key'           =>  $index,
                'sale_price'    =>  $precios_venta[$name_column],
                'name_price'    =>  $tipos_clientes[$index],
                'name_column'   =>  $name_column,
                'text'          =>  $precios_venta[$name_column] . ' - ' . $tipos_clientes[$index]
            ];
            $sale_prices[]  =   $sale_price;
            $index++;
        }

        return $sale_prices;

        return array_filter($precios_venta->toArray(), function ($precio) {
            return !is_null($precio);
        });
    }

    public function storeMasiveFeatures(array $dto)
    {
        ProductFeature::insert($dto);
    }

    public function destroyFeatures(int $id)
    {
        ProductFeature::where('product_id', $id)->delete();
    }
}
