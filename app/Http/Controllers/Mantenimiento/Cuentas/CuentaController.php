<?php

namespace App\Http\Controllers\Mantenimiento\Cuentas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\Cuentas\CuentaStoreRequest;
use App\Http\Requests\Mantenimiento\Cuentas\CuentaUpdateRequest;
use App\Http\Services\Mantenimiento\Cuentas\CuentaManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CuentaController extends Controller
{
    private CuentaManager $s_cuenta;

    public function __construct()
    {
        $this->s_cuenta = new CuentaManager();
    }

    public function index(): View
    {
        $bancos = bancos();
        return view('mantenimiento.cuentas.index', compact('bancos'));
    }

    public function getCuentas(Request $request)
    {
        $cuentas = DB::table('cuentas as c')
            ->select('c.id', 'c.nombre', 'c.titular', 'c.banco_id', 'c.banco_nombre', 'c.nro_cuenta', 'c.cci', 'c.celular', 'c.moneda')
            ->where('estado', '<>', 'ANULADO');

        return DataTables::of($cuentas)->make(true);
    }

    public function store(CuentaStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->s_cuenta->store($request->validated());
            DB::commit();
            return response()->json(['success' => true, 'message' => 'CUENTA REGISTRADA CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function update(CuentaUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->s_cuenta->update($request->validated(), (int) $id);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'CUENTA BANCARIA ACTUALIZADA CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->s_cuenta->destroy((int) $id);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'CUENTA ELIMINADA']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
