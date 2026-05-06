<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Almacen\Marca\MarcaStoreRequest;
use App\Http\Requests\Almacen\Marca\MarcaUpdateRequest;
use App\Models\Almacenes\Marca\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Throwable;

class MarcaController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'marca.index');
        return view('almacenes.marcas.index');
    }

    public function getmarca()
    {
        $data = DB::table('marcas as m')->where('m.estado', 'ACTIVO');
        return DataTables::of($data)->toJson();
    }

    /*
array:3 [
  "_token" => "sqPdRIxe0sP2mRvCnoMJXnOtXfwZpuQRiQBZbaQv"
  "descripcion" => "test"
  "procedencia" => "etst"
]
*/
    public function store(MarcaStoreRequest $request)
    {
        $this->authorize('haveaccess', 'marca.index');
        DB::beginTransaction();
        try {

            $data           =   $request->validated();
            $data['marca']  =   $data['descripcion'];

            $instance   =   Marca::create($data);

            //Registro de actividad
            $descripcion    = "SE AGREGÓ LA MARCA CON EL NOMBRE: " . $instance->descripcion;
            $gestion        = "MARCA";
            crearRegistro($instance, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Marca registrada con éxito', 'data' => $instance]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    /*
array:4 [
  "_token" => "sqPdRIxe0sP2mRvCnoMJXnOtXfwZpuQRiQBZbaQv"
  "_method" => "PUT"
  "descripcion" => "JOLI EDIT"
  "procedencia" => "PERU"
]
*/
    public function update(MarcaUpdateRequest $request, int $id)
    {
        $this->authorize('haveaccess', 'marca.index');
        DB::beginTransaction();
        try {
            $data               =   $request->validated();
            $data['marca']  =   $data['descripcion'];

            $instance    =   Marca::findOrFail($id);
            $instance->update($data);

            //Registro de actividad
            $descripcion    = "SE MODIFICO LA MARCA CON EL NOMBRE: " . $instance->descripcion;
            $gestion        = "MARCA";
            modificarRegistro($instance, $descripcion, $gestion);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Marca modificada con éxito']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $this->authorize('haveaccess', 'marca.index');
        DB::beginTransaction();
        try {

            $instance = Marca::findOrFail($id);
            $instance->estado = 'ANULADO';
            $instance->update();

            //Registro de actividad
            $descripcion = "SE ELIMINÓ LA MARCA CON EL NOMBRE: " . $instance->descripcion;
            $gestion = "MARCA";
            eliminarRegistro($instance, $descripcion, $gestion);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Marca eliminada con éxito']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function exist(Request $request)
    {

        $data           = $request->all();

        $marca          = $data['marca'];
        $id             = $data['id'];
        $marca_existe   = null;

        if ($marca && $id) { // edit
            $marca_existe = Marca::where([
                ['marca', $data['marca']],
                ['id', '<>', $data['id']]
            ])->where('estado', '!=', 'ANULADO')->first();
        } else if ($marca && !$id) { // create
            $marca_existe = Marca::where('marca', $data['marca'])->where('estado', '!=', 'ANULADO')->first();
        }

        $result = ['existe' => ($marca_existe) ? true : false];

        return response()->json($result);
    }
}
