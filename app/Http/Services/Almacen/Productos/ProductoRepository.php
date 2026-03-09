<?php

namespace App\Http\Services\Almacen\Productos;

use App\Almacenes\Producto;
use App\Almacenes\ProductoColor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ProductoRepository
{

    public function insertarProducto(array $datos): Producto
    {
        $producto                   =   new Producto();
        $producto->nombre           =   mb_strtoupper($datos['nombre'], 'UTF-8');
        $producto->marca_id         =   $datos['marca'];
        $producto->categoria_id     =   $datos['categoria'];
        $producto->modelo_id        =   $datos['modelo'];
        $producto->medida           =   105;
        $producto->precio_venta_1   =   $datos['precio1'];
        $producto->precio_venta_2   =   $datos['precio2'];
        $producto->precio_venta_3   =   $datos['precio3'];
        $producto->costo            =   $datos['costo'] ?? 0;
        $producto->mostrar_en_web   =   $datos['mostrar_en_web'];
        $producto->descripcion      =   $datos['descripcion'] ?? null;
        $producto->save();

        return $producto;
    }

    public function actualizarProducto(array $datos, int $id): Producto
    {
        $producto                   =   Producto::findOrFail($id);
        $producto->nombre           =   $datos['nombre'];
        $producto->marca_id         =   $datos['marca'];
        $producto->categoria_id     =   $datos['categoria'];
        $producto->modelo_id        =   $datos['modelo'];
        $producto->precio_venta_1   =   $datos['precio1'];
        $producto->precio_venta_2   =   $datos['precio2'];
        $producto->precio_venta_3   =   $datos['precio3'];
        $producto->precio_venta_4   =   $datos['precio4'];
        $producto->costo            =   $datos['costo'];
        $producto->mostrar_en_web   =   $datos['mostrar_en_web'] ?? false;
        $producto->descripcion      =   $datos['descripcion'] ?? null;
        $producto->update();
        return $producto;
    }

    public function insertarProductoColor(int $almacen_id, int $producto_id, int $color_id)
    {
        $producto_color                 =   new ProductoColor();
        $producto_color->almacen_id     =   $almacen_id;
        $producto_color->producto_id    =   $producto_id;
        $producto_color->color_id       =   $color_id;
        $producto_color->save();
    }

    public function guardarImagenes(array $datos, Producto $producto)
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
        $stocks =  DB::select(
            'SELECT
            p.id AS producto_id,
            p.nombre AS producto_nombre,
            pct.color_id,
            c.descripcion AS color_nombre,
            pct.talla_id,
            t.descripcion AS talla_nombre,
            pct.stock,
            pct.stock_logico,
            pct.almacen_id
            FROM producto_color_tallas AS pct
            INNER JOIN productos AS p ON p.id = pct.producto_id
            INNER JOIN colores AS c ON c.id = pct.color_id
            INNER JOIN tallas AS t ON t.id = pct.talla_id
            WHERE p.id = ?
            AND c.estado="ACTIVO"
            AND t.estado="ACTIVO"
            AND p.estado="ACTIVO"
            AND pct.almacen_id = ?
            ORDER BY p.id,c.id,t.id',
            [$producto_id, $almacen_id]
        );
        return $stocks;
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

        return $precios_venta;
    }
}
