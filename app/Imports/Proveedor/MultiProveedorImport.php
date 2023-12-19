<?php

namespace App\Imports\Proveedor;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiProveedorImport implements withMultipleSheets
{
    /**
    * @param Collection $collection
    */
    public function sheets(): array
    {
        return [
            0 =>new ProveedorImport()
        ];
    }
}

