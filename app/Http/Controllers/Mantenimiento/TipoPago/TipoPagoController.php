<?php

namespace App\Http\Controllers\Mantenimiento\TipoPago;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\TipoPago\TipoPagoStoreRequest;
use App\Http\Requests\Mantenimiento\TipoPago\TipoPagoUpdateRequest;
use App\Http\Services\Mantenimiento\TipoPago\TipoPagoManager;
use App\Mantenimiento\Cuenta\Cuenta;
use App\Mantenimiento\TipoPago\TipoPago;
use App\Mantenimiento\TipoPago\TipoPagoCuenta;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class TipoPagoController extends Controller
{
    private TipoPagoManager $s_tipo_pago;

    public function __construct()
    {
        $this->s_tipo_pago = new TipoPagoManager();
    }

    public function index()
    {
        return view('mantenimiento.tipo_pago.index');
    }

    public function getTiposPago(Request $request)
    {
        $metodos_pago = DB::table('tipos_pago as tp')
            ->select('tp.id', 'tp.descripcion as nombre', 'tp.created_at as fecha_registro', 'tp.updated_at as fecha_modificacion')
            ->where('estado', '<>', 'ANULADO');

        return DataTables::of($metodos_pago)->make(true);
    }

    public function store(TipoPagoStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->s_tipo_pago->store($request->validated());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'TIPO PAGO REGISTRADO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function update(TipoPagoUpdateRequest $request, int $id)
    {
        DB::beginTransaction();
        try {
            $this->s_tipo_pago->update($request->validated(), $id);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'TIPO PAGO ACTUALIZADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->s_tipo_pago->destroy((int) $id);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'TIPO PAGO ELIMINADO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function asignarCuentasCreate(int $id): View
    {
        $tipo_pago         = TipoPago::findOrFail($id);
        $cuentas           = Cuenta::where('estado', 'ACTIVO')->get();
        $cuentas_asignadas = TipoPagoCuenta::where('tipo_pago_id', $id)->get();
        return view('mantenimiento.tipo_pago.asignar_cuentas', compact('tipo_pago', 'cuentas', 'cuentas_asignadas'));
    }

    public function asignarCuentasStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $tipo_pago_id = (int) $request->get('tipo_pago_id');
            $cuenta_ids   = json_decode($request->get('lstCuentasAsignadas'), true);

            $this->s_tipo_pago->asignarCuentasStore($tipo_pago_id, $cuenta_ids);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'CUENTAS ASIGNADAS CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
