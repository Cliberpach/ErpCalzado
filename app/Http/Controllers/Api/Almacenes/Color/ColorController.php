<?php

namespace App\Http\Controllers\Api\Almacenes\Color;

use App\Http\Controllers\Controller;
use App\Models\Almacenes\Color\Color;
use Throwable;

class ColorController extends Controller
{

    public function getAll(){
        try {

            $colores =   Color::where('estado','ACTIVO')->where('tipo','COLOR')->get();
            return response()->json(['success'=>true,'message'=>'COLORES OBTENIDOS','data'=>$colores]);

        } catch (Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'code'=>$th->getCode()]);
        }
    }

}
