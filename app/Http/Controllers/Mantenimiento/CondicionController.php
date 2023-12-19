<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Tabla\Detalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CondicionController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'condicion.index');
        return view('mantenimiento.condiciones.index');
    }

    public function getRepository()
    {
        return datatables()->query(
            DB::table('condicions')
                ->select(
                    'condicions.*',
                    DB::raw('DATE_FORMAT(created_at, "%d/%m/%Y") as creado'),
                    DB::raw('DATE_FORMAT(updated_at, "%d/%m/%Y") as actualizado')
                )->where('condicions.estado', 'ACTIVO')->orderBy('id', 'DESC')
        )->toJson();
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $rules = [
            'tabladetalle_id_guardar' => 'required',
            'dias_guardar' => 'required',
        ];

        $message = [
            'tabladetalle_id_guardar.required' => 'El campo Descripción es obligatorio.',
            'dias_guardar.required' => 'El campo Ubicación es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        $tabladetalle = Detalle::find($request->get('tabladetalle_id_guardar'));

        $cad_a = substr($tabladetalle->simbolo, 0, 1);
        $cad_b = substr($tabladetalle->simbolo, 1, strlen($tabladetalle->simbolo));
        $slug = strtoupper($cad_a) . $cad_b;

        $condicion = new Condicion();
        $condicion->descripcion = $tabladetalle->descripcion;
        $condicion->slug = $slug;
        $condicion->tabladetalle_id = $request->get('tabladetalle_id_guardar');
        $condicion->dias = $request->get('dias_guardar');
        $condicion->save();


        //Registro de actividad
        $descripcion = "SE AGREGÓ LA CONDICION CON EL NOMBRE: " . $condicion->descripcion;
        $gestion = "CONDICION";
        crearRegistro($condicion, $descripcion, $gestion);

        Session::flash('success', 'Condición creada.');
        return redirect()->route('mantenimiento.condiciones.index')->with('guardar', 'success');
    }

    public function update(Request $request)
    {

        $data = $request->all();

        $rules = [
            'tabla_id' => 'required',
            'tabladetalle_id' => 'required',
            'dias' => 'required',
        ];

        $message = [
            'id.required' => 'El campo Descripción es obligatorio.',
            'dias.required' => 'El campo dias es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        $tabladetalle = Detalle::find($request->get('tabladetalle_id'));

        $cad_a = substr($tabladetalle->simbolo, 0, 1);
        $cad_b = substr($tabladetalle->simbolo, 1, strlen($tabladetalle->simbolo));
        $slug = strtoupper($cad_a) . $cad_b;

        $condicion = Condicion::findOrFail($request->get('tabla_id'));
        $condicion->descripcion = $tabladetalle->descripcion;
        $condicion->slug = $slug;
        $condicion->tabladetalle_id = $request->get('tabladetalle_id');
        $condicion->dias = $request->get('dias');
        $condicion->update();

        //Registro de actividad
        $descripcion = "SE MODIFICÓ LA CONDICION CON EL NOMBRE: " . $condicion->descripcion;
        $gestion = "CONDICIÓN";
        modificarRegistro($condicion, $descripcion, $gestion);

        Session::flash('success', 'Condición modificado.');
        return redirect()->route('mantenimiento.condiciones.index')->with('modificar', 'success');
    }

    public function destroy($id)
    {

        $condicion = Condicion::findOrFail($id);
        $condicion->estado = 'ANULADO';
        $condicion->update();

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL CONDICION CON EL NOMBRE: " . $condicion->descripcion;
        $gestion = "CONDICION";
        eliminarRegistro($condicion, $descripcion, $gestion);


        Session::flash('success', 'Condicion eliminada.');
        return redirect()->route('mantenimiento.condiciones.index')->with('eliminar', 'success');
    }

    public function exist(Request $request)
    {
        $condicion = null;

        if ($request->tabladetalle_id != null && $request->id != null && $request->dias != null) { // edit
            $condicion = Condicion::where([
                ['tabladetalle_id', $request->tabladetalle_id],
                ['dias', $request->dias],
                ['id', '<>', $request->id]
            ])->where('estado', '!=', 'ANULADO}')->first();
        } else { // create
            $condicion = Condicion::where('tabladetalle_id', $request->tabladetalle_id)->where('dias', (int)$request->dias)->where('estado', '!=', 'ANULADO')->first();
        }

        $result = ['existe' => $condicion ? true : false, 'data' => $request->all()];

        return response()->json($result);
    }
}