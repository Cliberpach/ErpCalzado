<?php

namespace App\Http\Services\Almacen\Tallas;

use App\Almacenes\Talla;

class TallaRepository
{
    public function getTallas()
    {
        $tallas =   Talla::where('estado', 'ACTIVO')
            ->orderByRaw("
                        CASE
                            WHEN descripcion = '35' THEN 1
                            WHEN descripcion = '36' THEN 2
                            WHEN descripcion = '37' THEN 3
                            WHEN descripcion = '38' THEN 4
                            WHEN descripcion = '39' THEN 5
                            WHEN descripcion = '40' THEN 6
                            WHEN descripcion = '41' THEN 7
                            ELSE 8
                        END
                    ")
            ->orderBy('descripcion')
            ->get();
        return $tallas;
    }
}
