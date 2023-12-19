<?php

namespace App\Exports\Cliente;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ClienteMultiExport implements WithMultipleSheets
{
    public function sheets(): array
    {
      return [
          "lista"=>new ClienteExport(),
          "listCombobox"=>new ClienteListaExport()
      ];

    }
}
