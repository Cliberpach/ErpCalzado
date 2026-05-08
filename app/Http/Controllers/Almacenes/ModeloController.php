<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Almacenes\Modelo;
use App\Http\Requests\Almacen\Modelo\ModeloStoreRequest;
use App\Http\Requests\Almacen\Modelo\ModeloUpdateRequest;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Throwable;

class ModeloController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'almacen.modelo.index');
        return view('almacenes.modelos.index');
    }

    public function getRepository(Request $request)
    {
        $data = DB::table('modelos as m')
            ->where('m.estado', 'ACTIVO');

        return DataTables::of($data)->toJson();
    }

    public function store(ModeloStoreRequest $request)
    {
        $this->authorize('haveaccess', 'almacen.modelo.index');
        DB::beginTransaction();

        try {

            $data = $request->validated();

            $instance = Modelo::create($data);

            // Registro de actividad
            $descripcion = "SE AGREGÓ EL MODELO CON EL NOMBRE: " . $instance->descripcion;
            $gestion = "MODELO";
            crearRegistro($instance, $descripcion, $gestion);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Modelo registrado con éxito',
                'data' => $instance
            ]);
        } catch (Throwable $th) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function update(ModeloUpdateRequest $request, int $id)
    {
        $this->authorize('haveaccess', 'almacen.modelo.index');
        DB::beginTransaction();

        try {

            $data = $request->validated();

            $instance = Modelo::findOrFail($id);
            $instance->update($data);

            // Registro de actividad
            $descripcion = "SE MODIFICÓ EL MODELO CON EL NOMBRE: " . $instance->descripcion;
            $gestion = "MODELO";
            modificarRegistro($instance, $descripcion, $gestion);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Modelo modificado con éxito'
            ]);
        } catch (Throwable $th) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }


    public function destroy($id)
    {
        $this->authorize('haveaccess', 'almacen.modelo.index');
        DB::beginTransaction();

        try {

            $instance = Modelo::findOrFail($id);
            $instance->estado = 'ANULADO';
            $instance->update();

            // Registro de actividad
            $descripcion = "SE ELIMINÓ EL MODELO CON EL NOMBRE: " . $instance->descripcion;
            $gestion = "MODELO";
            eliminarRegistro($instance, $descripcion, $gestion);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Modelo eliminado con éxito'
            ]);
        } catch (Throwable $th) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function modelSearch(Request $request)
    {
        try {
            $query = trim($request->get('q', ''));

            $models = DB::table('modelos as m')->where('m.estado', 'ACTIVO')->where('m.tipo','MODELO');

            if ($query) {
                $models->whereRaw("m.descripcion LIKE ?", ["%{$query}%"]);
            }

            $results = $models->limit(20)->get([
                'm.id',
                'm.descripcion',
            ]);

            $data = $results->map(fn($c) => [
                'id' => $c->id,
                'full_name' => $c->descripcion,
            ]);

            return response()->json(['success' => true, 'message' => 'MODELOS OBTENIDOS', 'data' => $data]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
