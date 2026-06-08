<?php

namespace App\Http\Controllers\Mantenimiento\Colaborador;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilidadesController;
use App\Http\Requests\Mantenimiento\Colaborador\ColaboradorStoreRequest;
use App\Http\Requests\Mantenimiento\Colaborador\ColaboradorUpdateRequest;
use App\Http\Services\Mantenimiento\Colaborador\ColaboradorManager;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Persona\Persona;
use App\Mantenimiento\Sedes\Sede;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Throwable;

class ColaboradorController extends Controller
{
    private ColaboradorManager $s_manager;

    public function __construct()
    {
        $this->s_manager = new ColaboradorManager();
    }

    public function index()
    {
        $this->authorize('haveaccess', 'colaborador.index');
        return view('mantenimiento.colaboradores.index');
    }

    public function getColaboradores()
    {
        $colaboradores = DB::table('colaboradores as co')
            ->join('tabladetalles as td', 'td.id', '=', 'co.cargo_id')
            ->join('empresa_sedes as es', 'es.id', 'co.sede_id')
            ->select(
                'co.id',
                'es.nombre as sede_nombre',
                'co.nombre',
                'co.direccion',
                'co.telefono',
                'co.nro_documento',
                'co.dias_trabajo',
                'co.dias_descanso',
                'co.pago_mensual',
                'td.descripcion as cargo_nombre',
                'co.estado',
                'co.tipo_documento_nombre'
            )
            ->where('co.estado', 'ACTIVO')
            ->get();

        return DataTables::of($colaboradores)->make(true);
    }

    public function create()
    {
        $this->authorize('haveaccess', 'colaborador.index');

        $sedes = Sede::where('estado', 'ACTIVO')->get();

        return view('mantenimiento.colaboradores.create', compact('sedes'));
    }

    public function store(ColaboradorStoreRequest $request)
    {
        $this->authorize('haveaccess', 'colaborador.index');
        DB::beginTransaction();
        try {
            $colaborador = $this->s_manager->store($request->all());

            $descripcion = 'SE AGREGÓ EL COLABORADOR: ' . $colaborador->nombre;
            $gestion     = 'COLABORADORES';
            crearRegistro($colaborador, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'COLABORADOR REGISTRADO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function edit(int $id)
    {
        $this->authorize('haveaccess', 'colaborador.index');

        $colaborador = Colaborador::findOrFail($id);
        $sedes       = Sede::where('estado', 'ACTIVO')->get();

        return view('mantenimiento.colaboradores.edit', [
            'colaborador' => $colaborador,
            'sedes'       => $sedes,
        ]);
    }

    public function update(ColaboradorUpdateRequest $request, int $id)
    {
        $this->authorize('haveaccess', 'colaborador.index');
        DB::beginTransaction();
        try {
            $colaborador = $this->s_manager->update($id, $request->all());

            $descripcion = 'SE MODIFICÓ EL COLABORADOR: ' . $colaborador->nombre;
            $gestion     = 'COLABORADORES';
            modificarRegistro($colaborador, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'COLABORADOR ACTUALIZADO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function show(int $id)
    {
        $colaborador = Colaborador::findOrFail($id);
        return view('mantenimiento.colaboradores.show', [
            'colaborador' => $colaborador,
        ]);
    }

    public function destroy(int $id)
    {
        $this->authorize('haveaccess', 'colaborador.index');
        DB::beginTransaction();
        try {
            $colaborador = $this->s_manager->destroy($id);

            $descripcion = 'SE ELIMINÓ EL COLABORADOR: ' . $colaborador->nombre;
            $gestion     = 'COLABORADORES';
            eliminarRegistro($colaborador, $descripcion, $gestion);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'COLABORADOR ELIMINADO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getDni(Request $request)
    {
        $data         = $request->all();
        $existe       = false;
        $igualPersona = false;

        if (!is_null($data['tipo_documento']) && !is_null($data['documento'])) {
            if (!is_null($data['id'])) {
                $persona = Persona::findOrFail($data['id']);
                if ($persona->tipo_documento == $data['tipo_documento'] && $persona->documento == $data['documento']) {
                    $igualPersona = true;
                } else {
                    $persona = Persona::where([
                        ['tipo_documento', '=', $data['tipo_documento']],
                        ['documento', $data['documento']],
                        ['estado', 'ACTIVO'],
                    ])->first();
                }
            } else {
                $persona = Persona::where([
                    ['tipo_documento', '=', $data['tipo_documento']],
                    ['documento', $data['documento']],
                    ['estado', 'ACTIVO'],
                ])->first();
            }

            if (!is_null($persona) && (!is_null($persona->colaborador) || !is_null($persona->vendedor))) {
                $existe = true;
            }
        }

        return response()->json([
            'existe'        => $existe,
            'igual_persona' => $igualPersona,
        ]);
    }

    public function consultarDni(int $dni)
    {
        try {
            if (strlen($dni) !== 8) {
                throw new Exception('EL DNI DEBE CONTAR CON 8 DÍGITOS');
            }

            $existe = DB::select(
                'SELECT c.id FROM colaboradores AS c WHERE c.nro_documento = ? AND c.estado = "ACTIVO"',
                [$dni]
            );

            if (count($existe) > 0) {
                throw new Exception('El dni ya existe en la tabla colaboradores');
            }

            $res_consulta_api = UtilidadesController::apiDni($dni);
            $res              = $res_consulta_api->getData();

            if ($res->success) {
                return response()->json(['success' => true, 'data' => $res->data, 'message' => 'OPERACIÓN COMPLETADA']);
            } else {
                throw new Exception($res->message);
            }
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
