<?php

namespace App\Http\Controllers\Pos;

use App\Compras\Documento\Detalle;
use App\DetallesMovimientoCaja;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Persona\Persona;
use App\Pos\Caja;
use App\Pos\MovimientoCaja;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;

use function PHPUnit\Framework\returnSelf;

class CajaController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'caja.index');

        return view('pos.Cajas.index');
    }

    public function retirarColaborades(Request $request){
     // return $request;
       $idMovimiento=$request->movimiento;
       $colaboradores=$request->colaboradores;
        $usuarios=DetallesMovimientoCaja::select('*')
        ->where('detalles_movimiento_caja.movimiento_id','=',$idMovimiento)
        ->whereIn('detalles_movimiento_caja.usuario_id',$colaboradores)
        ->distinct()
        ->get();


        foreach($usuarios as $u){

            $colaborador=  DetallesMovimientoCaja::find($u->id);

            $colaborador->fecha_salida=date('Y-m-d h:i:s');
            $colaborador->save();

        }


        return redirect()->route('Caja.Movimiento.index');


    }

    public function getColaborades($id){
        $colaborades_desocupados=DetallesMovimientoCaja::select('*')
        ->join('users as u','u.id','=','detalles_movimiento_caja.usuario_id')
        ->where('detalles_movimiento_caja.movimiento_id','=',$id)
        ->whereNull('detalles_movimiento_caja.fecha_salida')
        ->get();

        return $colaborades_desocupados;
    }
    public function getCajas()
    {
        $datos = [];
        $cajas = Caja::where('estado', 'ACTIVO')->get();
        foreach ($cajas as $key => $value) {
            array_push($datos, [
                'id' => $value->id,
                'nombre' => $value->nombre,
                'created_at' => Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $value->created_at
                )->format('Y-m-d h:i:s'),
            ]);
        }
        return DataTables::of($datos)->toJson();
    }
    public function store(Request $request)
    {

        $this->authorize('haveaccess', 'caja.index');
        $caja = new Caja();
        $caja->nombre = $request->nombre;
        $caja->save();
        return redirect()->route('Caja.index');
    }
    public function update(Request $request, $id)
    {
        $caja = Caja::findOrFail($id);
        $caja->nombre = $request->nombre_editar;
        $caja->save();
        return redirect()->route('Caja.index');
    }
    public function destroy($id)
    {
        $caja = Caja::findOrFail($id);
        $caja->estado = 'ANULADO';
        $caja->save();
        return redirect()->route('Caja.index');
    }
    public function indexMovimiento()
    {
        //Obtengo id de usuarios cajeros ocupados
        $cajerosOcupados= DetallesMovimientoCaja::select('detalles_movimiento_caja.usuario_id')
        ->join('movimiento_caja as mc','mc.id','=','detalles_movimiento_caja.movimiento_id')
        ->join('caja as c','c.id','=','mc.caja_id')
        ->join('users as u','u.id','=','detalles_movimiento_caja.usuario_id')
        ->join('role_user as ru','ru.user_id','=','u.id')
        ->join('roles as r','r.id','=','ru.role_id')
        ->where('r.name','LIKE','%CAJE%')
        ->where('c.estado_caja','LIKE','%ABIERTA%')
        ->distinct()
        ->get();


        $getCajerosOcupados='';
        $cajerosDesocupados= null;
        if(count($cajerosOcupados) == 0){

            $cajerosDesocupados=User::select('users.id','users.usuario')
            ->join('role_user as ru','ru.user_id','=','users.id')
            ->join('roles as r','r.id','=','ru.role_id')
            ->orWhere('r.name','LIKE','%CAJERO%')
            ->get();


            if(count($cajerosDesocupados)==0){

            $cajerosDesocupados=[];
            }
        }else{
            foreach($cajerosOcupados as $u){

                $getCajerosOcupados=$u->usuario_id.','.$getCajerosOcupados;
            }
            $getCajerosOcupados=substr($getCajerosOcupados,0,-1);

            $cajerosDesocupados =   DB::select('select u.id,u.usuario 
                                from users as u inner join role_user as ru on ru.user_id=u.id 
                                inner join roles as r on r.id=ru.role_id 
                                where u.id  not in('.$getCajerosOcupados.') and (r.name LIKE "%CAJER%")');
        }
        // return $cajerosDesocupados;

        //=====================================


        // Obtengo los id de los usuarios ventas ocupados
        $usuariosOcupados= DetallesMovimientoCaja::select('detalles_movimiento_caja.usuario_id')
        ->join('movimiento_caja as mc','mc.id','=','detalles_movimiento_caja.movimiento_id')
        ->join('caja as c','c.id','=','mc.caja_id')
        ->join('users as u','u.id','=','detalles_movimiento_caja.usuario_id')
        ->join('role_user as ru','ru.user_id','=','u.id')
        ->join('roles as r','r.id','=','ru.role_id')
        ->where('r.name','LIKE','%venta%')
        ->where('c.estado_caja','LIKE','%abierta%')
        ->whereNull('detalles_movimiento_caja.fecha_salida')
        ->distinct()
        ->get();
     //  return $usuariosOcupados;

        $getUsersOcupados='';
        $usuariosDesocupados= null;

        if(count($usuariosOcupados) == 0){
            $usuariosDesocupados=User::select('users.id','users.usuario')
            ->join('role_user as ru','ru.user_id','=','users.id')
            ->join('roles as r','r.id','=','ru.role_id')
            ->orWhere('r.name','LIKE','%VENTA%')
            ->get();

            if(count($usuariosDesocupados)==0){
            $usuariosDesocupados=[];
            }
        }else{
          //Obtengo los usuarios ventas desocupados ;
            foreach($usuariosOcupados as $u){

                $getUsersOcupados=$u->usuario_id.','.$getUsersOcupados;
            }
            $getUsersOcupados=substr($getUsersOcupados,0,-1);

            $usuariosDesocupados=DB::select('select u.id,u.usuario 
                                from users as u 
                                inner join role_user as ru on ru.user_id=u.id 
                                inner join roles as r on r.id=ru.role_id 
                                where u.id  not in('.$getUsersOcupados.') and (r.name LIKE "%VENTA%" )');
        }


        $this->authorize('haveaccess', 'movimiento_caja.index');
        $lstAniosDB = DB::table('lote_productos')
            ->select(DB::raw('year(created_at) as value'))
            ->distinct()
            ->orderBy('value', 'desc')
            ->get();
        $lstAnios = collect();
        foreach($lstAniosDB as $item){
            $lstAnios->push([
                "value" =>$item->value
            ]);
        }
        $fecha_hoy = Carbon::now();
        $mes = date_format($fecha_hoy, 'm');
        $anio_ = date_format($fecha_hoy, 'Y');
        $search = array_search("$anio_",array_column($lstAnios->values()->all(),"value"));
        if($search === false){
            $lstAnios->push([
                "value" =>(int)$anio_
            ]);
        }
      //return $cajerosDesocupados;
        
        return view('pos.MovimientoCaja.indexMovimiento',[
            'lstAnios'=>json_decode(json_encode($lstAnios->sortByDesc("value")->values())),
            'mes'=>$mes,
            'anio_'=>$anio_,
            'usuariosDesocupados'=>$usuariosDesocupados,
            'cajerosDesocupados'=>$cajerosDesocupados
        ]);
    }
    public function getMovimientosCajas(Request $request)
    {
        $datos = [];
        $consulta = null;
        if ($request->filter=="ACTIVO") {
            $consulta = MovimientoCaja::where(DB::raw("CONVERT(fecha_apertura,date)"),">=",$request->desde)
            ->where(DB::raw("CONVERT(fecha_apertura,date)"),"<=",$request->hasta);
        } else {
            $consulta = MovimientoCaja::whereMonth(
                'fecha_apertura',
                $request->mes
            )->whereYear('fecha_apertura', $request->anio);
        }
        $movimientos = $consulta
            ->where('estado', 'ACTIVO')
            ->get([
                'id',
                'caja_id',
                'colaborador_id',
                'monto_inicial',
                'monto_final',
                'fecha_apertura',
                'fecha_cierre',
                'estado_movimiento',
                'fecha',
                'estado',
            ]);
        foreach ($movimientos as $key => $movimiento) {
            array_push($datos, [
                'id' => $movimiento->id,
                'caja' => $movimiento->caja->nombre,
                'cantidad_inicial' => $movimiento->monto_inicial,
                'cantidad_final' =>
                    $movimiento->monto_final == null
                        ? '-'
                        : $movimiento->monto_final,
                'fecha_Inicio' => $movimiento->fecha_apertura,
                'fecha_Cierre' =>
                    $movimiento->fecha_cierre == null
                        ? '-'
                        : $movimiento->fecha_cierre,
                'totales' => $this->ObtenerTotales($movimiento->id),
            ]);
        }
        return DataTables::of($datos)->toJson();
    }

    public function estadoCaja(Request $request)
    {
        $caja = Caja::findOrFail($request->id);
        return $caja->estado_caja == 'ABIERTA' ? 'true' : 'false';
    }

    public function aperturaCaja(Request $request)
    {
       
        // $this->authorize('haveaccess','pos.MovimientoCaja.indexMovimiento');
        $this->authorize('haveaccess','movimiento_caja.create');
        $data=$request->all();
        $rules=[
            'caja'              =>'required',
            'turno'             =>'required',
            'saldo_inicial'     =>'required',
            'colaborador_id'    =>  'required'
         ];

         $message = [
            'caja.required' => 'Seleccionar una caja  es Obligatorio',
            'colaborador_id.required' => 'El campo cajero es obligatorio',
            'turno.required'=>'El campo turno es obligatorio',
            'saldo_inicial.required'=>'Ingresar un saldo en caja',
        ];
        Validator::make($data, $rules, $message)->validate();

        $usuarios_ventas_id   =  $request->usuarioVentas;

        //====== BUSCAMOS EL ID COLABORADOR DEL USUARIO ID DEL CAJERO SELECCIONADO =====
        $colaborador_id =   DB::select('select c.id as colaborador_id from user_persona as up 
        inner join colaboradores as c on c.persona_id=up.persona_id
        where up.user_id=?',[$request->colaborador_id]);

        if(isset($request->usuarioVentas)){

          

            $movimiento = new MovimientoCaja();
            $movimiento->caja_id            = $request->caja;
            $movimiento->colaborador_id     = $colaborador_id[0]->colaborador_id;
            $movimiento->monto_inicial      = $request->saldo_inicial;
            $movimiento->estado_movimiento  = 'APERTURA';
            $movimiento->fecha_apertura     = date('Y-m-d h:i:s');
            $movimiento->fecha              = date('Y-m-d');
            $movimiento->save();

            $detallesMovimiento                 =   new DetallesMovimientoCaja();
            $detallesMovimiento->movimiento_id  =   $movimiento->id;
            $detallesMovimiento->usuario_id     =   $request->colaborador_id;
            $detallesMovimiento->fecha_entrada  =   date('Y-m-d h:i:s');
            $detallesMovimiento->save();

            foreach($usuarios_ventas_id as $usuario_venta_id){
                $detallesMovimiento                 =   new DetallesMovimientoCaja();
                $detallesMovimiento->movimiento_id  =   $movimiento->id;
                $detallesMovimiento->usuario_id     =   $usuario_venta_id;
                $detallesMovimiento->fecha_entrada  =   date('Y-m-d h:i:s');
                $detallesMovimiento->save();
            }

            $caja = Caja::findOrFail($request->caja);
            $caja->estado_caja = 'ABIERTA';
            $caja->save();
        return redirect()->route('Caja.Movimiento.index');
        }else{

            $movimiento                     = new MovimientoCaja();
            $movimiento->caja_id            = $request->caja;
            $movimiento->colaborador_id     = $colaborador_id[0]->colaborador_id;
            $movimiento->monto_inicial      = $request->saldo_inicial;
            $movimiento->estado_movimiento  = 'APERTURA';
            $movimiento->fecha_apertura     = date('Y-m-d h:i:s');
            $movimiento->fecha              = date('Y-m-d');
            $movimiento->save();

            $detallesMovimiento                 =   new DetallesMovimientoCaja();
            $detallesMovimiento->movimiento_id  =   $movimiento->id;
            $detallesMovimiento->usuario_id     =   $request->colaborador_id;
            $detallesMovimiento->fecha_entrada  =   date('Y-m-d h:i:s');
            $detallesMovimiento->save();

            $caja = Caja::findOrFail($request->caja);
            $caja->estado_caja = 'ABIERTA';
            $caja->save();
            return redirect()->route('Caja.Movimiento.index');
        }
    }

    public function cerrarCaja(Request $request)
    {
        $movimiento = MovimientoCaja::findOrFail($request->movimiento_id);
        $movimiento->estado_movimiento = 'CIERRE';
        $movimiento->fecha_cierre = date('Y-m-d h:i:s');
        $movimiento->monto_final = (float) $request->saldo;
        $movimiento->save();
        $caja = $movimiento->caja;
        $caja->estado_caja = 'CERRADA';
        $caja->save();

        $detalles= DetallesMovimientoCaja::select('*')
        ->where('detalles_movimiento_caja.movimiento_id','=',$movimiento->id)
        ->get();

        foreach($detalles as $d){
            $d->fecha_salida=date('Y-m-d h:i:s');
            $d->save();

        }

        return redirect()->route('Caja.Movimiento.index');
    }

    public function cajaDatosCierre(Request $request)
    {
        $movimiento = MovimientoCaja::findOrFail($request->id);
        $colaborador = $movimiento->colaborador;
        $ingresos =
            cuadreMovimientoCajaIngresosVentaResum($movimiento, 1) -
            cuadreMovimientoDevolucionesResum($movimiento, 1) +
            cuadreMovimientoCajaIngresosCobranzaResum($movimiento, 1);
        $egresos =
            cuadreMovimientoCajaEgresosEgresoResum($movimiento, 1) -
            cuadreMovimientoDevolucionesResum($movimiento, 1) +
            cuadreMovimientoCajaEgresosPagoResum($movimiento, 1);
        return [
            'caja' => $movimiento->caja->nombre,
            'monto_inicial' => $movimiento->monto_inicial,
            'colaborador' =>
                $colaborador->persona->apellido_paterno .
                ' ' .
                $colaborador->persona->apellido_paterno .
                ' ' .
                $colaborador->persona->nombre,
            'egresos' => $egresos,
            'ingresos' => $ingresos,
            'saldo' => $movimiento->monto_inicial + $ingresos - $egresos,
        ];
    }

    public function verificarEstadoUser()
    {
        try {
            if (
                MovimientoCaja::where(
                    'estado_movimiento',
                    'APERTURA'
                )->count() != 0
            ) {
                if (FullAccess() || PuntoVenta()) {
                    return response()->json([
                        'success' => true,
                    ]);
                } else {
                    if (Auth::user()->user->persona->colaborador) {
                        if (
                            MovimientoCaja::where(
                                'colaborador_id',
                                Auth::user()->user->persona->colaborador->id
                            )
                                ->where('estado_movimiento', 'APERTURA')
                                ->count() != 0
                        ) {
                            return response()->json([
                                'success' => true,
                            ]);
                        } else {
                            return response()->json([
                                'success' => false,
                                'mensaje' =>
                                    'No tienes ninguna apertura de caja disponible',
                            ]);
                        }
                    } else {
                        return response()->json([
                            'success' => false,
                            'mensaje' =>
                                'No eres un colaborador por favor registrarte como colaborador',
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No hay ninguna apertura de caja disponible',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
            ]);
        }
    }

    public function reporteMovimiento($id)
    {
        $movimiento = MovimientoCaja::findOrFail($id);
        $usuarios=DetallesMovimientoCaja::select('u.id','u.usuario','detalles_movimiento_caja.fecha_entrada','detalles_movimiento_caja.fecha_salida')
        ->join('users as u','u.id','=','detalles_movimiento_caja.usuario_id')
        ->join('user_persona as up','up.user_id','=','u.id')
        ->where('detalles_movimiento_caja.movimiento_id','=',$id)
        ->get();
        $empresa = Empresa::first();
        $fecha = Carbon::now()->toDateString();

        $pdf = PDF::loadview('pos.MovimientoCaja.Reportes.movimientocaja', [
            'movimiento' => $movimiento,
            'empresa' => $empresa,
            'fecha' => $fecha,
            'usuarios'=>$usuarios,
        ])
            ->setPaper('a4')
            ->setWarnings(false);
        return $pdf->stream();
    }
    private function ObtenerTotales($id)
    {
        $movimiento = MovimientoCaja::findOrFail($id);
        $TotalVentaDelDia =
            (float) cuadreMovimientoCajaIngresosVenta($movimiento) -
            (float) cuadreMovimientoDevoluciones($movimiento);
        return [
            'TotalVentaDelDia' => $TotalVentaDelDia,
        ];
    }
}
