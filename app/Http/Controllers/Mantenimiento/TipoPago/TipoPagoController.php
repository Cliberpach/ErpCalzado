<?php

namespace App\Http\Controllers\Mantenimiento\TipoPago;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\TipoPago\TipoPagoStoreRequest;
use App\Http\Requests\Mantenimiento\TipoPago\TipoPagoUpdateRequest;
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
    public function index()
    {
        return view('mantenimiento.tipo_pago.index');
    }

    public function getTiposPago(Request $request)
    {

        $metodos_pago   =   DB::table('tipos_pago as tp')
            ->select(
                'tp.id',
                'tp.descripcion as nombre',
                'tp.created_at as fecha_registro',
                'tp.updated_at as fecha_modificacion',
            )->where('estado','<>','ANULADO');

        return DataTables::of($metodos_pago)->make(true);
    }

/*
array:3 [ // app\Http\Controllers\General\Herramientas\MetodoPagoController.php:44
  "_token" => "bT8GT81OAYVc3KgyWzXlWzDO8LjS0gH11XnCow3R"
  "descripcion" => "YAPE"
  "subsistema" => "GRIFO"
]
*/
    public function store(TipoPagoStoreRequest $request)
    {

        DB::beginTransaction();
        try {

            $data                   =   $request->validated();
            $data['descripcion']    =   mb_strtoupper($data['descripcion'], 'UTF-8');
            $data['simbolo']        =   mb_strtoupper($data['descripcion'], 'UTF-8');
            $data['editable']       =   1;

            TipoPago::create($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'TIPO PAGO REGISTRADO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    /*
array:2 [ // app\Http\Controllers\General\Herramientas\MetodoPagoController.php:81
  "descripcion" => "TRANSFERENCIA"
  "subsistema" => "MARKET"
]
*/
    public function update(TipoPagoUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $data                   =   $request->validated();
            $data['descripcion']    =   mb_strtoupper($data['descripcion'], 'UTF-8');

            $tipo_pago            =   TipoPago::findOrFail($id);
            $tipo_pago->update($data);

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
            $metodo_pago                    =   TipoPago::find($id);
            $metodo_pago->estado            =   'ANULADO';
            $metodo_pago->update();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'TIPO PAGO ELIMINADO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function asignarCuentasCreate(int $id):View{

        $tipo_pago          =   TipoPago::findOrFail($id);
        $cuentas            =   Cuenta::where('estado','ACTIVO')->get();
        $cuentas_asignadas  =   TipoPagoCuenta::where('tipo_pago_id',$id)->get();
        return view('mantenimiento.tipo_pago.asignar_cuentas',compact('tipo_pago','cuentas','cuentas_asignadas'));
    }

/*
array:2 [
  "lstCuentasAsignadas" => "[7,6]"
  "tipo_pago_id" => "3"
]
*/
    public function asignarCuentasStore(Request $request){
        DB::beginTransaction();
        try {

            //======== BORRAR LAS CUENTAS ANTERIORES ========
            $tipo_pago_id   =   $request->get('tipo_pago_id');
            DB::delete('DELETE FROM tipo_pago_cuentas WHERE tipo_pago_id = ?', [$tipo_pago_id]);

            $lstCuentasAsignadas    =   json_decode($request->get('lstCuentasAsignadas'));
            foreach ($lstCuentasAsignadas as $cuenta_asignada) {
                $cuenta_nueva               =   new TipoPagoCuenta();
                $cuenta_nueva->tipo_pago_id =   $tipo_pago_id;
                $cuenta_nueva->cuenta_id    =   $cuenta_asignada;
                $cuenta_nueva->save();
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'CUENTAS ASIGNADAS CON ÉXITO']);

        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

}
