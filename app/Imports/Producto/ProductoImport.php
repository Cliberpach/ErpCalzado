<?php

namespace App\Imports\Producto;

use App\Almacenes\Almacen;
use App\Almacenes\Categoria;
use App\Almacenes\Marca;
use App\Almacenes\Producto;
use App\Almacenes\TipoCliente;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use App\Mantenimiento\Tabla\Detalle;
use Illuminate\Support\Facades\Log;

class ProductoImport implements ToCollection,WithHeadingRow,WithValidation
{
    use Importable;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row){
            if($row['nombre']!=null && Producto::where('nombre',$row['nombre'])->where('estado','ACTIVO')->count()==0){
                $producto = new Producto();
                $producto->nombre = $row['nombre'];
                $producto->marca_id =Marca::where('marca',$row['marcas'])->first()->id;
                $producto->almacen_id = Almacen::where('descripcion',$row['almacenes'])->first()->id;
                $producto->categoria_id = Categoria::where('descripcion',$row['categorias'])->first()->id;
                $medida=explode('-',$row['unidadmedida']);
                $m = Detalle::where('simbolo',$medida[0])->where('descripcion',$medida[1])->first();
                $m_aux = Detalle::where('simbolo','NIU')->where('descripcion','UNIDAD (BIENES)')->first();
                $producto->medida = $m ? $m->id : $m_aux->id;
                $producto->peso_producto = $row['peso'];
                $producto->stock_minimo = $row['stockminimo'];
                $producto->precio_venta_minimo = 0;
                $producto->precio_venta_maximo = 0;
                $producto->codigo_barra = $row['codigobarra'];
                $producto->igv = $row['igv']=='SI'? 1 : 0;
                $producto->save();

                $producto->codigo = 1000 + $producto->id;
                $producto->update();



                TipoCliente::create([
                    'producto_id' => $producto->id,
                    'cliente' => '121',
                    'monto' => $row['porcentajenormal'],
                    'porcentaje' => $row['porcentajenormal'],
                    'moneda' => 1,
                ]);

                TipoCliente::create([
                    'producto_id' => $producto->id,
                    'cliente' =>'122',
                    'monto' => $row['porcentajedistribuidor'],
                    'porcentaje' => $row['porcentajedistribuidor'],
                    'moneda' => 1,
                ]);
            }
        }

    }
    public function rules(): array
    {
        return [
        ];
    }
}
