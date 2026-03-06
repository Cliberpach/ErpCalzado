<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ventas\TipoCliente\TipoClienteStoreRequest;
use App\Http\Requests\Ventas\TipoCliente\TipoClienteUpdateRequest;
use App\Models\Ventas\TipoCliente\TipoCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class TipoClienteController extends Controller
{
    public function index()
    {
        return view('ventas.tipos_clientes.index');
    }

    public function getAll(Request $request)
    {
        $items =   TipoCliente::select(
            'id',
            'nombre'
        )
            ->where('estado', 'ACTIVO');

        return DataTables::of($items)->make(true);
    }


    /*
array:2 [
  "_token" => "Y4SrtC3C8UYpGfCGoUPNw3kLnourb786iaFIcSeR"
  "nombre" => "FC BARCELONA"
]
*/
    public function store(TipoClienteStoreRequest $request)
    {

        try {
            $data               =   $request->validated();
            $data['nombre']     =   mb_strtoupper($data['nombre'], 'UTF-8');

            $instance           =   TipoCliente::create($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Tipo Cliente registrado con éxito', 'item' => $instance]);
        } catch (Throwable $th) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['type' => 'error', 'message' => $th->getMessage()]);
            }

            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    // Actualizar una marca existente
    public function update(TipoClienteUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $data           =   $request->validated();
            $data['nombre'] =   $request->get('nombre_edit');

            $instance   =   TipoCliente::findOrFail($id);
            $instance->update($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Tipo cliente actualizado con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $instance           =   TipoCliente::findOrFail($id);
            $instance->estado   =   'ANULADO';
            $instance->update();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Tipo cliente eliminada con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

}
