<?php

namespace App\Http\Services\Almacen\Colores;

use App\Almacenes\Producto;
use App\Models\Almacenes\Color\Color;
use Illuminate\Support\Collection;

class ColorManager
{
    private ColorService $s_color;

    public function __construct(){
        $this->s_color   =   new ColorService();
    }

    public function store(array $datos):Color{
        return $this->s_color->store($datos);
    }

    public function getColor(int $id):Color{
        return $this->s_color->getColor($id);
    }

    public function update (int $id,array $datos):Color{
        return $this->s_color->update($id,$datos);
    }

    public function destroy(int $id):Color{
        return $this->s_color->destroy($id);
    }

}
