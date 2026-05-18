<?php

namespace App\Http\Controllers\Mantenimiento\MetodoEntrega;

use App\Http\Controllers\Controller;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Provincia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TarifarioEnvioController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'metodo_entrega.index');
        $departamentos = Departamento::orderBy('nombre')->get(['id', 'nombre']);
        return view('mantenimiento.metodos_entrega.tarifarios.index', compact('departamentos'));
    }

    public function getTable(Request $request)
    {
        $query = DB::table('provincias as p')
            ->join('departamentos as d', 'd.id', '=', 'p.departamento_id')
            ->select('p.id', 'p.nombre', 'p.costo', 'd.nombre as departamento', 'd.id as departamento_id');

        if ($request->filled('departamento_id')) {
            $query->where('p.departamento_id', $request->departamento_id);
        }

        return DataTables::of($query)->toJson();
    }

    public function updateCosto(Request $request)
    {
        $this->authorize('haveaccess', 'metodo_entrega.index');

        $request->validate([
            'provincia_id' => 'required',
            'costo'        => 'required|numeric|min:0',
        ]);

        try {
            Provincia::where('id', $request->provincia_id)
                ->update(['costo' => $request->costo]);

            return response()->json(['success' => true, 'message' => 'Costo actualizado correctamente']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'ERROR EN EL SERVIDOR', 'exception' => $th->getMessage()]);
        }
    }
}
