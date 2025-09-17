<?php

namespace App\Http\Controllers\Mantenimiento\Cuentas;

use App\Http\Controllers\Controller;

use App\Http\Requests\Mantenimiento\Cuentas\CuentaStoreRequest;
use App\Http\Requests\Mantenimiento\Cuentas\CuentaUpdateRequest;
use App\Http\Requests\Mantenimiento\TipoPago\TipoPagoStoreRequest;
use App\Http\Requests\Mantenimiento\TipoPago\TipoPagoUpdateRequest;
use App\Mantenimiento\Cuenta\Cuenta;
use App\Mantenimiento\Tabla\Detalle;
use App\Mantenimiento\TipoPago\TipoPago;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CuentaController extends Controller
{
    public function index():View
    {
        $bancos     =   bancos();

        return view('mantenimiento.cuentas.index',compact('bancos'));
    }

    public function getCuentas(Request $request)
    {
        $cuentas   =   DB::table('cuentas as c')
            ->select(
                'c.id',
                'c.titular',
                'c.banco_id',
                'c.banco_nombre',
                'c.nro_cuenta',
                'c.cci',
                'c.celular',
                'c.moneda'
            )->where('estado','<>','ANULADO');

        return DataTables::of($cuentas)->make(true);
    }

/*
array:7 [
  "_token" => "VPUPeijASDvEEeC7xAp2CLHLGIsiEf0vaYzAnhpg"
  "titular" => "ALVA LUJAN LUIS DANIEL"
  "moneda" => "SOLES"
  "nro_cuenta" => "5709431246"
  "cci" => "960123123456789"
  "celular" => "923278040"
  "banco_id" => "3"
]
*/
    public function store(CuentaStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $data                   =   $request->validated();
            $data['titular']        =   mb_strtoupper($data['titular'], 'UTF-8');
            $data['moneda']         =   mb_strtoupper($data['moneda'], 'UTF-8');
            $data['banco_nombre']   =   Detalle::findOrFail($request->get('banco_id'))->descripcion;
            $data['editable']       =   1;
            $data['registrador_id'] =   Auth::user()->id;
            $data['registrador_nombre'] =   Auth::user()->usuario;

            Cuenta::create($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'CUENTA REGISTRADA CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

/*
array:5 [
  "titular" => "ALVA LUJAN LUIS DANIEL editado"
  "banco_id" => "5"
  "moneda" => "SOLES"
  "nro_cuenta" => "6554244215"
  "cci" => "445261223"
]
*/
    public function update(CuentaUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $data                   =   $request->validated();

            $data['titular']        =   mb_strtoupper($data['titular'], 'UTF-8');
            $data['moneda']         =   mb_strtoupper($data['moneda'], 'UTF-8');
            $data['banco_nombre']   =   Detalle::findOrFail($request->get('banco_id'))->descripcion;

            $cuenta              =   Cuenta::findOrFail($id);
            $cuenta->update($data);

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
            $cuenta                    =   Cuenta::findOrFail($id);
            $cuenta->estado            =   'ANULADO';
            $cuenta->update();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'CUENTA ELIMINADA']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

}
