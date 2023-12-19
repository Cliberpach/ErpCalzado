<?php

namespace App\Imports\Producto;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
class ProductoSheet implements  WithMultipleSheets
{
    public $objeto;
    public function sheets(): array
    {
        $this->objeto = new DatosProductoImport();
        return
            [
                0 => $this->objeto
            ];
    }
    public function get_data()
    {
        return $this->objeto->get_data();
    }
}
