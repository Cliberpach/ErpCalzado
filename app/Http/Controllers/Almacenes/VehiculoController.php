<?php

namespace App\Http\Controllers\Almacenes;

use App\Almacenes\Vehiculo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Almacen\Vehiculo\VehiculoStoreRequest;
use App\Http\Requests\Almacen\Vehiculo\VehiculoUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class VehiculoController extends Controller
{
    public function index(){
        return view('almacenes.vehiculos.index');
    }

    public function getVehiculos(Request $request){

        $vehiculos = DB::table('vehiculos as v')
                    ->select(
                        'v.id', 
                        'v.placa',
                        'v.modelo',
                        'v.marca',
                        'v.created_at as fecha_registro',
                        'v.updated_at as fecha_modificacion'
                    )
                    ->where('v.estado','ACTIVO')
                    ->get();


        return DataTables::of($vehiculos)
                ->make(true);
    }

    public function create(){
        return view('almacenes.vehiculos.create');
    }

     /*
    array:4 [ // app\Http\Controllers\Registros\VehiculoController.php:19
        "_token"    => "NjS8X7BKeHqRNmrtCBOxXMTbKz4F5P1TIpsagVd6"
        "placa"     => "asdasd"     --6 a 8 caracteres
        "modelo"    => "asdsa"      --100 CARACTERES MÁXIMO
        "marca"     => "dasd"       --100 CARACTERES MÁXIMO
    ]
    */
    public function store(VehiculoStoreRequest $request){
        DB::beginTransaction();
        try {

            $vehiculo           =   new Vehiculo();
            $vehiculo->placa    =   mb_strtoupper($request->get('placa'), 'UTF-8');
            $vehiculo->modelo   =   mb_strtoupper($request->get('modelo'), 'UTF-8');
            $vehiculo->marca    =   mb_strtoupper($request->get('marca'), 'UTF-8');            
            $vehiculo->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'VEHÍCULO REGISTRADO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function edit($id){
        $vehiculo   =   Vehiculo::find($id);

        return view('almacenes.vehiculos.edit',compact('vehiculo'));
    }


    public function update($id,VehiculoUpdateRequest $request){
        DB::beginTransaction();
        try {
            $vehiculo           =   Vehiculo::find($id);
            $vehiculo->placa    =   mb_strtoupper($request->get('placa'), 'UTF-8');
            $vehiculo->modelo   =   mb_strtoupper($request->get('modelo'), 'UTF-8');
            $vehiculo->marca    =   mb_strtoupper($request->get('marca'), 'UTF-8');            
            $vehiculo->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'VEHÍCULO ACTUALIZADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try {
            $vehiculo                    =   Vehiculo::find($id);
            $vehiculo->estado            =   'ANULADO';
            $vehiculo->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'VEHÍCULO ELIMINADO']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }



}
