<?php

namespace App\Http\Controllers\Almacenes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Almacen\Almacen\AlmacenStoreRequest;
use App\Http\Requests\Almacen\Almacen\AlmacenUpdateRequest;
use App\Models\Almacenes\Almacen\Almacen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class AlmacenController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'almacen.almacen.index');

        $sede_id                =   Auth::user()->sede->id;

        $sede_have_principal    =   Almacen::where('estado', 'ACTIVO')
            ->where('sede_id', $sede_id)
            ->where('tipo_almacen', 'PRINCIPAL')
            ->exists();

        return view('almacenes.almacen.index', compact('sede_id', 'sede_have_principal'));
    }

    public function getRepository()
    {

        $sede_id   =   Auth::user()->sede->id;

        $almacenes  =   DB::table('almacenes as a')
            ->join('empresa_sedes as es', 'es.id', 'a.sede_id')
            ->select(
                'a.*',
                'es.direccion as sede_direccion',
                DB::raw('DATE_FORMAT(a.created_at, "%d/%m/%Y") as creado'),
                DB::raw('DATE_FORMAT(a.updated_at, "%d/%m/%Y") as actualizado')
            )
            ->where('a.estado', 'ACTIVO')
            ->where('a.sede_id', $sede_id)
            ->orderBy('a.id', 'DESC')
            ->get();

        return DataTables::of($almacenes)
            ->make(true);
    }

    /*
array:5 [
  "_token" => "sqPdRIxe0sP2mRvCnoMJXnOtXfwZpuQRiQBZbaQv"
  "sede_id" => "1"
  "descripcion" => "RESERVAS"
  "ubicacion" => "TEST"
  "tipo_almacen" => "SECUNDARIO"
]
*/
    public function store(AlmacenStoreRequest $request)
    {
        $this->authorize('haveaccess', 'almacen.almacen.index');
        DB::beginTransaction();

        try {

            $data   =   $request->validated();
            $data['sede_id']    =   $request->get('sede_id');

            $almacen    =   Almacen::create($data);

            //Registro de actividad
            $descripcion    = "SE AGREGÓ EL ALMACEN CON EL NOMBRE: " . $almacen->descripcion;
            $gestion        = "ALMACEN";
            crearRegistro($almacen, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Almacén registrado con éxito']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    /*
array:7 [
  "_token" => "sqPdRIxe0sP2mRvCnoMJXnOtXfwZpuQRiQBZbaQv"
  "sede_id_edit" => "1"
  "descripcion_edit" => "REGALO"
  "ubicacion_edit" => "REGALO"
  "tipo_almacen_edit" => "PRINCIPAL"
  "_method" => "PUT"
  "sede_id" => "1"
]
*/
    public function update(AlmacenUpdateRequest $request, int $id)
    {
        $this->authorize('haveaccess', 'almacen.almacen.index');
        DB::beginTransaction();
        try {

            $data               =   $request->validated();
            $data['sede_id']    =   $request->get('sede_id');

            $almacen    =   Almacen::findOrFail($id);
            $almacen->update($data);

            //Registro de actividad
            $descripcion    = "SE MODIFICO EL ALMACEN CON EL NOMBRE: " . $almacen->descripcion;
            $gestion        = "ALMACEN";
            modificarRegistro($almacen, $descripcion, $gestion);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Almacén modificado con éxito']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $this->authorize('haveaccess', 'almacen.almacen.index');
        DB::beginTransaction();
        try {

            $almacen = Almacen::findOrFail($id);
            $almacen->estado = 'ANULADO';
            $almacen->update();

            //Registro de actividad
            $descripcion = "SE ELIMINÓ EL ALMACEN CON EL NOMBRE: " . $almacen->descripcion;
            $gestion = "ALMACEN";
            eliminarRegistro($almacen, $descripcion, $gestion);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Almacén eliminado con éxito']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function exist(Request $request)
    {

        $data = $request->all();
        $descripcion = $data['descripcion'];
        $ubicacion = $data['ubicacion'];
        $id = $data['id'];
        $almacen = null;

        if ($descripcion && $id && $ubicacion) { // edit
            $almacen = Almacen::where([
                ['descripcion', $data['descripcion']],
                ['ubicacion', $data['ubicacion']],
                ['id', '<>', $data['id']]
            ])->where('estado', '!=', 'ANULADO')->first();
        } else if ($ubicacion && $descripcion && !$id) { // create
            $almacen = Almacen::where('descripcion', $data['descripcion'])->where('ubicacion', $data['ubicacion'])->where('estado', '!=', 'ANULADO')->first();
        }

        $result = ['existe' => ($almacen) ? true : false];

        return response()->json($result);
    }
}
