<?php

namespace App\Imports\Proveedor;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DatosProveedorImport implements ToCollection, WithHeadingRow
{
    public $data = array();
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            array_push($this->data, array(
                'nombre' => $row['nombre'],
                'ruc' => $row['ruc'],
                'tipopersona' => $row['tipopersona'],
                'direccion' => $row['direccion'],
                'zona' => $row['zona'],
                'correo' => $row['correo'],
                'telefono' => $row['telefono'],
                'celular' => $row['celular'],
                'nombrecontacto' => $row['nombrecontacto'],
                'correocontacto' => $row['correocontacto'],
                'telefonocontacto' => $row['telefonocontacto'],
                'celularcontacto' => $row['celularcontacto'],
            ));
        }
    }
    public function get_data()
    {
        return $this->data;
    }
}
