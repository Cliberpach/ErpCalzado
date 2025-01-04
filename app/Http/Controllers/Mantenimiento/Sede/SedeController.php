<?php

namespace App\Http\Controllers\Mantenimiento\Sede;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\Sedes\SedeStoreRequest;
use App\Mantenimiento\Sedes\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SedeController extends Controller
{
    public function index(){
        return view('mantenimiento.sedes.index');
    }

    public function create(){

        $departamentos  =   DB::select('select * from departamentos');
        $provincias     =   DB::select('select * from provincias');
        $distritos      =   DB::select('select * from distritos');

        return view('mantenimiento.sedes.create',compact('departamentos','provincias','distritos'));
    }


/*
array:14 [
  "_token"          => "NtBVgvSzRbuHIPf9aPqpHtk2YDKsJzlErNrAkxCs"
  "ruc"             => "20370146994"
  "razon_social"    => "CORPORACION ACEROS AREQUIPA S.A."
  "direccion"       => "AV CHAVIMOCHIC 1234"
  "telefono"        => null
  "correo"          => null
  "departmento"      => "01"
  "provincia"        => "0101"
  "distrito"        => "010101"
  "codigo_local"    => "0001"
  "usuario_sol"     => null
  "clave_sol"       => null
  "id_api_guia"     => null
  "clave_api_guia"  => null

    "certificado" => Illuminate\Http\UploadedFile {#2039
        -test: false
        -originalName: "certificate_test.pem"
        -mimeType: "application/octet-stream"
        -error: 0
        #hashName: null
        path: "D:\xampp\tmp"
        filename: "php49A5.tmp"
        basename: "php49A5.tmp"
        pathname: "D:\xampp\tmp\php49A5.tmp"
        extension: "tmp"
        realPath: "D:\xampp\tmp\php49A5.tmp"
        aTime: 2025-01-04 11:40:25
        mTime: 2025-01-04 11:40:25
        cTime: 2025-01-04 11:40:25
        inode: 38843546786703131
        size: 5332
        perms: 0100666
        owner: 0
        group: 0
        type: "file"
        writable: true
        readable: true
        executable: false
        file: true
        dir: false
        link: false
        linkTarget: "D:\xampp\tmp\php49A5.tmp"
    }
]
*/ 
    public function store(SedeStoreRequest $request){
        DB::beginTransaction();
        try {
            dd($request->all());
            $sede               =   new Sede();
            $sede->empresa_id   =   1;
            $sede->ruc          =   $request->get('ruc');
            $sede->razon_social =   $request->get('razon_social');
            $sede->direccion    =   $request->get('direccion');
            $sede->telefono     =   $request->get('telefono');
            $sede->correo       =   $request->get('correo');

            $departamento_id    =   $request->get('departamento');
            $provincia_id       =   $request->get('provincia');
            $distrito_id        =   $request->get('distrito');
    
            $departamento_id    = str_pad($departamento_id, 2, '0', STR_PAD_LEFT);
            $provincia_id       = str_pad($provincia_id, 4, '0', STR_PAD_LEFT);
            $distrito_id        = str_pad($distrito_id, 6, '0', STR_PAD_LEFT);
    
            $sede->departamento_id   =   $departamento_id;
            $sede->provincia_id      =   $provincia_id;
            $sede->distrito_id       =   $distrito_id;
    
            $departamento               =   DB::select('select d.nombre from departamentos as d where d.id=?',[$departamento_id])[0]->nombre;
            $provincia                  =   DB::select('select p.nombre from provincias as p where p.id=?',[$provincia_id])[0]->nombre;
            $distrito                   =   DB::select('select d.nombre from distritos as d where d.id=?',[$distrito_id])[0]->nombre;
    
            $sede->departamento_nombre      =   $departamento;
            $sede->provincia_nombre         =   $provincia;
            $sede->distrito_nombre          =   $distrito;

            $sede->codigo_local             =   $request->get('codigo_local');
            $sede->usuario_sol              =   $request->get('usuario_sol');
            $sede->clave_sol                =   $request->get('clave_sol');
            $sede->id_api_guia_remision     =   $request->get('id_api_guia_remision');
            $sede->clave_api_guia_remision  =   $request->get('clave_api_guia_remision');
            $sede->tipo_sede                =   'SECUNDARIA';
            $sede->save();


            //======== CREANDO CARPETA PERSONAL PARA LA SEDE ========
            $nombre_carpeta_personal    = 'S'.$sede->id.'_'.$sede->ruc;
            $ruta                       = "public/{$nombre_carpeta_personal}";

            if (!Storage::exists($ruta)) {
                Storage::makeDirectory($ruta);
            }

            //======= GUARDANDO CERTIFICADO SEDE =======
            if ($request->hasFile('certificado')) {
                $nombre_certificado     =   'CERT'.$nombre_carpeta_personal;
            }

            return response()->json(['success'=>true,'message'=>'SEDE REGISTRADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
