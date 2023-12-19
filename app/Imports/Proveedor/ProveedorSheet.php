<?php

namespace App\Imports\Proveedor;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
class ProveedorSheet implements  WithMultipleSheets
{
    public $objeto;
    public function sheets(): array
    {
        $this->objeto = new DatosProveedorImport();
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
