<?php

namespace App\Http\Controllers\Api\Almacenes\Categoria;

use App\Almacenes\Categoria;
use App\Almacenes\Producto;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Throwable;

class ProductoController extends Controller
{

    public function getAll(){
        try {

            $productos =   Producto::where('estado','ACTIVO')->where('tipo','PRODUCTO')->where('mostrar_en_web',true)->get();
            return response()->json(['success'=>true,'message'=>'PRODUCTOS OBTENIDOS','data'=>$productos]);

        } catch (Throwable $th) {
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'code'=>$th->getCode()]);
        }
    }

}
