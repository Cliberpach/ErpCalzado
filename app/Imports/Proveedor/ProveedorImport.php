<?php

namespace App\Imports\Proveedor;

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
use App\Compras\Articulo;
use App\Compras\Proveedor;
use App\Mantenimiento\Tabla\Detalle;
use Illuminate\Support\Facades\Log;

class ProveedorImport implements ToCollection, WithHeadingRow, WithValidation
{
    use Importable;
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if ($row['nombre'] != null && Proveedor::where('descripcion', $row['nombre'])->count() == 0) {
                $proveedor = new Proveedor();
                $proveedor->descripcion = $row['nombre'];
                $proveedor->ruc = $row['ruc'];
                $proveedor->tipo_persona = $row['tipopersona'];
                $proveedor->tipo_persona = $row['tipopersona'];
                $proveedor->direccion = $row['direccion'];
                $proveedor->tipo_documento= 'RUC';
                $proveedor->zona = $row['zona'];
                $proveedor->correo = $row['correo'];
                $proveedor->telefono = $row['telefono'];
                $proveedor->celular = $row['celular'];
                $proveedor->contacto = $row['nombrecontacto'];
                $proveedor->celular_contacto = $row['correocontacto'];
                $proveedor->telefono_contacto = $row['telefonocontacto'];
                $proveedor->correo_contacto = $row['celularcontacto'];
                $proveedor->save();
            }
        }
    }
    public function rules(): array
    {
        return [];
    }
}
