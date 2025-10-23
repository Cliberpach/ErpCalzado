<?php

namespace App\Http\Services\Almacen\Productos;

use App\Almacenes\Producto;
use App\Almacenes\ProductoColor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

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
        $producto->costo            =   $datos['costo'];
        $producto->mostrar_en_web   =   $datos['mostrar_en_web']??false;
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
}
