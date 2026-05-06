<?php

namespace App\Http\Services\Almacen\Colores;

use App\Models\Almacenes\Color\Color;
use Illuminate\Support\Facades\Storage;

class ColorService
{
    public function store(array $data): array
    {
        $descripcion = mb_strtoupper(trim($data['descripcion']), 'UTF-8');

        $color = Color::where('descripcion', $descripcion)->first();

        if ($color) {

            if ($color->estado === 'ANULADO') {
                // 🔁 REACTIVAR
                $color->estado = 'ACTIVO';
                $color->codigo = $data['codigo'] ?? null;
                $color->save();

                if (!empty($data['imagen'])) {
                    $this->saveImg($data['imagen'], $color);
                }

                return [
                    'color' => $color,
                    'message' => 'El color ya existía y fue reactivado correctamente.'
                ];
            }
        }

        // 🆕 CREAR
        $instance = new Color();
        $instance->descripcion = $descripcion;
        $instance->codigo = $data['codigo'] ?? null;
        $instance->estado = 'ACTIVO';
        $instance->save();

        if (!empty($data['imagen'])) {
            $this->saveImg($data['imagen'], $instance);
        }

        return [
            'color' => $instance,
            'message' => 'Color creado correctamente.'
        ];
    }

    public function getColor(int $id): Color
    {
        return Color::findOrFail($id);
    }

    public function update(int $id, array $datos): array
    {
        $datos = collect($datos)->mapWithKeys(function ($value, $key) {
            if (str_ends_with($key, '_edit')) {
                $key = str_replace('_edit', '', $key);
            }
            return [$key => $value];
        })->toArray();

        $descripcion = strtoupper(trim($datos['descripcion']));
        $color = Color::findOrFail($id);

        $otroColor = Color::where('descripcion', $descripcion)
            ->where('id', '!=', $id)
            ->first();

        if ($otroColor) {

            if ($otroColor->estado === 'ANULADO') {
                $otroColor->estado = 'ACTIVO';
                $otroColor->codigo = $datos['codigo'] ?? $otroColor->codigo;
                $otroColor->save();

                $color->estado = 'ANULADO';
                $color->save();

                return [
                    'color' => $otroColor,
                    'message' => 'Se reactivó un color existente con el mismo nombre.'
                ];
            }
        }

        // ✅ UPDATE NORMAL
        $color->descripcion = $descripcion;
        $color->codigo = $datos['codigo'] ?? null;
        $color->save();

        if (!empty($datos['imagen'])) {

            if ($color->img_ruta && Storage::disk('public')->exists($color->img_ruta)) {
                Storage::disk('public')->delete($color->img_ruta);
            }

            $this->saveImg($datos['imagen'], $color);
        } else {

            if ($color->img_ruta && Storage::disk('public')->exists($color->img_ruta)) {
                Storage::disk('public')->delete($color->img_ruta);
            }

            $color->update([
                'img_ruta'   => null,
                'img_nombre' => null
            ]);
        }

        return [
            'color' => $color,
            'message' => 'Color actualizado correctamente.'
        ];
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
