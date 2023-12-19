<?php

namespace App\Imports\Producto;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductoMultiImport implements withMultipleSheets
{
    /**
    * @param Collection $collection
    */
    public function sheets(): array
    {
        return [
            0 => new ProductoImport()
        ];
    }
}
