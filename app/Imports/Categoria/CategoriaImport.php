<?php

namespace App\Imports\Categoria;

use App\Almacenes\Categoria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class CategoriaImport implements ToCollection,WithHeadingRow
{
    use Importable;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        Log::info($collection);
        foreach ($collection as  $row) {
            if($row['rubro']!=null && Categoria::where('descripcion',$row['rubro'])->where('estado','ACTIVO')->count()==0) {
                $categoria=new Categoria();
                $categoria->descripcion=$row['rubro'];
                $categoria->save();
            }
        }
    }
}
