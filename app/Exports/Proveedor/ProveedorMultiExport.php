<?php

namespace App\Exports\Proveedor;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProveedorMultiExport implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function sheets(): array
    {
        return [
            "producto"=>new ProveedorExport(),
            "detalle"=>new ProveedorListaExport()
        ];
    }
}