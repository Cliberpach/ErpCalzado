<?php

namespace App\Http\Controllers\Api\Almacenes\Categoria;

use App\Almacenes\Categoria;
use App\Http\Controllers\Controller;
use Throwable;

class CategoriaController extends Controller
{
    public function getAll()
    {
        try {

            $categorias = Categoria::select(
                'id',
                'descripcion',
                'estado',
                'tipo',
                'img_nombre',
                'img_ruta'
            )
                ->where('estado', 'ACTIVO')
                ->where('tipo', 'CATEGORIA')
                ->where('mostrar_en_web', true)
                ->get()
                ->map(function ($categoria) {

                    return [
                        'id' => $categoria->id,
                        'descripcion' => $categoria->descripcion,
                        'estado' => $categoria->estado,
                        'tipo' => $categoria->tipo,
                        'img_nombre' => $categoria->img_nombre,
                        'img_ruta' => $categoria->img_ruta,

                        'img_url' => $categoria->img_ruta
                            ? asset('storage/' . $categoria->img_ruta)
                            : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'CATEGORÍAS OBTENIDAS',
                'data' => $categorias
            ], 200);
        } catch (Throwable $th) {


            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
}
