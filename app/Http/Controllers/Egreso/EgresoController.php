<?php

namespace App\Http\Controllers\Egreso;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilidadesController;
use App\Http\Requests\Caja\Egreso\EgresoStoreRequest;
use App\Http\Requests\Caja\Egreso\EgresoUpdateRequest;
use App\Http\Services\Caja\Egreso\EgresoManager;
use App\Mantenimiento\Empresa\Empresa;
use App\Pos\DetalleMovimientoEgresosCaja;
use App\Pos\Egreso;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;

class EgresoController extends Controller
{
    private EgresoManager $s_egreso;

    public function __construct()
    {
        $this->s_egreso =   new EgresoManager();
    }

    public function index()
    {
        return view('Egreso.index');
    }
    public function getEgresos()
    {
        $datos  =   DB::table('egreso as e')
            ->join('tabladetalles as td', 'td.id', 'e.tipodocumento_id')
            ->join('tabladetalles as td2', 'td2.id', 'e.cuenta_id')
            ->select(
                'e.id',
                'e.descripcion',
                'e.monto',
                'e.estado',
                'e.usuario',
                'e.created_at',
                'td.descripcion as tipoDocumento',
                'e.documento',
                'td2.descripcion as cuenta_nombre'
            )
            ->where('e.estado', 'ACTIVO');

        //========= FILTRO POR ROLES ======
        $roles = DB::table('role_user as rl')
            ->join('roles as r', 'r.id', '=', 'rl.role_id')
            ->where('rl.user_id', Auth::user()->id)
            ->pluck('r.name')
            ->toArray();

        //======== ADMIN PUEDE VER TODOS LOS EGRESOS DE SU SEDE =====
        if (in_array('ADMIN', $roles)) {
            $datos->where('e.sede_id', Auth::user()->sede_id);
        } else {

            //====== USUARIOS PUEDEN VER SUS PROPIOS EGRESOS ======
            $datos->where('e.sede_id', Auth::user()->sede_id)
                ->where('e.user_id', Auth::user()->id);
        }


        // $data = array();
        // foreach ($datos as $key => $value) {
        //     array_push($data, array(
        //         'id' => $value->id,
        //         'descripcion' => $value->descripcion,
        //         'monto' => $value->importe + $value->efectivo,
        //         'estado' => $value->estado,
        //         'usuario' => $value->usuario,
        //         'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $value->created_at)->format('Y-m-d h:i:s'),
        //         'tipoDocumento' => $value->tipoDocumento->descripcion,
        //         'documento' => $value->documento == null ? "-" : $value->documento
        //     ));
        // }


        return DataTables::of($datos)->toJson();
    }


    /*
array:12 [
  "_token" => "dT0Ydk9Qiq1qOUdk7PkjnjEvXd61OFhsw2ajAPaM"
  "cuenta" => "168"
  "tipo_documento" => "RECIBO"
  "monto" => "10.00"
  "documento" => "N001-7"
  "descripcion" => null
  "efectivo" => "0.00"
  "modo_pago" => "3"
  "importe" => "10"
  "cuenta_bancaria" => "7"
  "nro_operacion" => "03012039"
  "fecha_operacion" => "2025-08-28"
]
*/
    public function store(EgresoStoreRequest $request)
    {
        DB::beginTransaction();

        try {

            $this->s_egreso->store($request->toArray());
            DB::commit();

            return response()->json(['success' => true, 'message' => 'EGRESO REGISTRADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }

        return redirect()->route('Egreso.index');
    }

    /*
array:12 [
  "_token" => "MF3ERVEGCkGQSlSLaa1anJkBQJ6bpm577rgzjXhU"
  "cuenta_edit" => "168"
  "tipo_documento_edit" => "RECIBO"
  "monto_edit" => "40.00"
  "documento_edit" => "V000-7"
  "descripcion_edit" => null
  "efectivo_edit" => "0.00"
  "modo_pago_edit" => "3"
  "importe_edit" => "40.00"
  "cuenta_bancaria_edit" => "7"
  "nro_operacion_edit" => "OP-123"
  "fecha_operacion_edit" => "2025-08-28"
]
*/
    public function update(EgresoUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $this->s_egreso->update($request->toArray(), $id);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'EGRESO ACTUALIZADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage(),'line'=>$th->getLine(),'file'=>$th->getFile()]);
        }
    }

    public function getEgreso(int $id)
    {
        try {
            $egreso =   Egreso::findOrFail($id);
            return response()->json(['success' => true, 'message' => 'EGRESO OBTENIDO CON ÉXITO', 'data' => $egreso]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->s_egreso->destroy($id);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'EGRESO ELIMINADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function recibo(Request $request, $size)
    {
        $egreso = Egreso::findOrFail($request->egreso_id);
        $empresa = Empresa::first();
        if ($size == 80) {
            $pdf = PDF::loadView('Egreso.Imprimir.ticket', compact('egreso', 'empresa'));
            $pdf->setpaper([0, 0, 226.772, 651.95]);
        } else {
            $pdf = PDF::loadView('Egreso.Imprimir.normal', compact('egreso', 'empresa'));
        }
        return $pdf->stream('recibo.pdf');
    }
}
