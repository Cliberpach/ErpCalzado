<?php

namespace App\Imports\Modelo;

use App\Almacenes\Modelo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class ModeloImport implements ToCollection,WithHeadingRow
{
    use Importable;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        Log::info($collection);
        foreach ($collection as  $row) {
            if($row['modelo']!=null && Modelo::where('descripcion',$row['modelo'])->where('estado','ACTIVO')->count()==0) {
                $modelo=new Modelo();
                $modelo->descripcion=$row['modelo'];
                $modelo->save();
            }
        }
    }
}
