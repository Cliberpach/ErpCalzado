<?php

namespace App\Http\Services\Almacen\Colores;

use App\Models\Almacenes\Color\Color;

class ColorService
{
    public function store(array $data): Color
    {
        $instance              =   new Color();
        $instance->descripcion =    mb_convert_encoding($data['descripcion'], 'UTF-8', 'UTF-8');
        $instance->save();

        if (!empty($data['imagen'])) {
            $this->saveImg($data['imagen'], $instance);
        }
        return $instance;
    }

    public function getColor(int $id): Color
    {
        return Color::findOrFail($id);
    }

    public function update(int $id, array $datos): Color
    {
        $datos = collect($datos)->mapWithKeys(function ($value, $key) {
            if (str_ends_with($key, '_edit')) {
                $key = str_replace('_edit', '', $key);
            }
            return [$key => $value];
        })->toArray();


        $color                  =   Color::findOrFail($id);
        $color->descripcion     =   $datos['descripcion'];
        $color->codigo          =   $datos['codigo'];
        $color->update();
        return $color;
    }

    public function destroy(int $id): Color
    {
        $color = Color::findOrFail($id);
        $color->estado = 'ANULADO';
        $color->update();

        return $color;
    }

    public function saveImg($img, $instance)
    {
        if (!empty($img)) {
            $file = $img;

            $imgName = $instance->id . '_color.' . $file->getClientOriginalExtension();

            $path = $file->storeAs(
                'colores',
                $imgName,
                'public'
            );

            $instance->update([
                'img_ruta' => $path,
                'img_nombre' => $imgName
            ]);
        }
    }
}
