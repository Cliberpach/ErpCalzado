<?php

namespace App\Http\Controllers\Api\Almacenes\Categoria;

use App\Almacenes\Categoria;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Throwable;

class CategoriaController extends Controller
{

    public function getAll()
    {
        try {

            $categorias = Categoria::where('estado', 'ACTIVO')
                ->where('tipo', 'CATEGORIA')
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
            ]);
        } catch (Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'code' => $th->getCode()
            ]);
        }
    }
}
