<?php

namespace App\Http\Controllers\Mantenimiento\Tabla;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Tabla\General;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class GeneralController extends Controller
{
    public function index()
    {
        return view('mantenimiento.tablas.general.index');
    }

    public function getTable(){
        $tablas = General::all();
        $coleccion = collect([]);
        foreach($tablas as $tabla){
            $coleccion->push([
                'id' => $tabla->id,
                'descripcion' => $tabla->descripcion,
                'sigla' => $tabla->sigla,
                'fecha_actualizacion' =>  Carbon::parse($tabla->updated_at)->format( 'd/m/Y - H:i:s'),
                'creado' =>  Carbon::parse($tabla->created_at)->format( 'd/m/Y - H:i:s'),
            ]);
        }
        return DataTables::of($coleccion)->toJson();
    }

    public function update(Request $request){
        
        $data = $request->all();

        $rules = [
            'descripcion' => 'required',
            'sigla' => 'required'
        ];
        
        $message = [
            'descripcion.required' => 'El campo Descripción es obligatorio.',
            'sigla.required' => 'El campo Sigla es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        $tabla = General::findOrFail($request->get('tabla_id'));
        $tabla->descripcion = $request->get('descripcion');
        $tabla->sigla = $request->get('sigla');
        $tabla->update();

        Session::flash('success','Tabla General modificado.');
        return redirect()->route('mantenimiento.tabla.general.index')->with('modificar', 'success');
    }
}
