<?php

namespace App\Imports\Producto;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DatosProductoImport implements ToCollection, WithHeadingRow
{
    public $data = array();
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            array_push($this->data, array(
                'unidadmedida' => $row['unidadmedida'],
                'peso' => $row['peso'],
                'nombre' => $row['nombre'],
                'categorias' => $row['categorias'],
                'marcas' => $row['marcas'],
                'almacenes' => $row['almacenes'],
                'stockminimo' => $row['stockminimo'],
                'precioventaminimo' => 0,
                'precioventamaximo' => 0,
                'codigobarra' => $row['codigobarra'],
                'igv' => $row['igv'],
                'porcentajenormal' => $row['porcentajenormal'],
                'porcentajedistribuidor' => $row['porcentajedistribuidor'],
            ));
        }
    }
    public function get_data()
    {
        return $this->data;
    }
}
