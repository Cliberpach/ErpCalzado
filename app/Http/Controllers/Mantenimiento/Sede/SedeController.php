<?php

namespace App\Http\Controllers\Mantenimiento\Sede;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mantenimiento\Sedes\SedeStoreRequest;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Empresa\Numeracion;
use App\Mantenimiento\Sedes\Sede;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

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

    public function getSedes(){

        $sedes  =   DB::select('select 
                    es.id,
                    es.direccion,
                    CONCAT(es.departamento_nombre, " - ", es.provincia_nombre, " - ", es.distrito_nombre) AS ubigeo,
                    es.codigo_local, 
                    e.razon_social,
                    es.tipo_sede
                    from empresa_sedes as es
                    inner join empresas as e on e.id = es.empresa_id');
    
        return DataTables::of($sedes)->toJson();

    }

   

/*
array:14 [
  "_token"          => "NtBVgvSzRbuHIPf9aPqpHtk2YDKsJzlErNrAkxCs"
  "ruc"             => "20370146994"
  "razon_social"    => "CORPORACION ACEROS AREQUIPA S.A."
  "direccion"       => "AV CHAVIMOCHIC 1234"
  "telefono"        => null
  "correo"          => null
  "departmento"     => "01"
  "provincia"       => "0101"
  "distrito"        => "010101"
  "codigo_local"    => "0001"
  "serie"           =>  "0002"
    "img_empresa" => Illuminate\Http\UploadedFile {#2039
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
            
            $empresa            =   Empresa::find(1);

            $sede               =   new Sede();
            $sede->empresa_id   =   1;
            $sede->ruc          =   $empresa->ruc;
            $sede->razon_social =   $empresa->razon_social;
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
            $sede->tipo_sede                =   'SECUNDARIA';
            $sede->serie                    =   $request->get('serie');
            $sede->save();


            //======== CREANDO CARPETA PERSONAL PARA LA SEDE ========
            $nombre_carpeta_personal    = 'S'.$sede->id.'_'.$sede->ruc;
            $ruta                       = "public/{$nombre_carpeta_personal}";

            if (!Storage::exists($ruta)) {
                Storage::makeDirectory($ruta);
            }

            //======= GUARDANDO LOGO SEDE =======
            if ($request->hasFile('img_empresa')) {

                $imagen             =   $request->file('img_empresa');
                $nombre_imagen      =   'LOGO' . $nombre_carpeta_personal . '.' . $imagen->getClientOriginalExtension();
                $ruta               =   $nombre_carpeta_personal.'/logo/';
            
                $ruta_completa      =   $imagen->storeAs($ruta, $nombre_imagen, 'public');
            
                $ruta_imagen        =   $nombre_carpeta_personal.'/'.$nombre_imagen;

                $sede->logo_ruta    =   $ruta_imagen;
                $sede->logo_nombre  =   $nombre_imagen;
                $sede->update();
            
            }
            
            DB::commit();

            return response()->json(['success'=>true,'message'=>'SEDE REGISTRADA']);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function getNumeracion(Request $request){

        $sede_id    =   $request->get('sede_id');

        
        $numeracion =   DB::select('select 
                        td.descripcion as comprobante,
                        enf.serie,
                        enf.numero_iniciar as nro_inicio,
                        IF(enf.emision_iniciada = "1", "INICIADO", "NO INICIADO") AS iniciado
                        from empresa_numeracion_facturaciones as enf
                        inner join tabladetalles as td on td.id = enf.tipo_comprobante
                        where 
                        enf.sede_id = ?
                        and enf.estado = "ACTIVO"
                        and td.tabla_id = 21',[$sede_id]);

        return DataTables::of($numeracion)->toJson();

    }

    public function numeracionCreate($sede_id){
        
        $sede               =   DB::select('select 
                                es.* 
                                from empresa_sedes as es
                                where es.id = ?',[$sede_id])[0];

        $tipos_comprobantes = DB::select('
                                SELECT 
                                    td.id,
                                    td.descripcion,
                                    td.nombre,
                                    td.parametro
                                FROM 
                                    tabladetalles AS td
                                WHERE 
                                    td.tabla_id = 21
                                    AND td.estado = "ACTIVO"
                                    AND td.id NOT IN (
                                        SELECT enf.tipo_comprobante
                                        FROM empresa_numeracion_facturaciones AS enf
                                        WHERE enf.sede_id = ? and enf.empresa_id = 1
                                    )
                            ', [$sede_id]);
                            
                            

        return view('mantenimiento.sedes.numeracion',compact('sede_id','tipos_comprobantes','sede'));

    }

/*
array:4 [
  "comprobante_id"  => "128"
  "parametro"       => "B"
  "serie"           => "002"
  "nro_inicio"      => "1"
  "sede_id"         => "13"
]
*/ 
    public function numeracionStore(Request $request){
        
        DB::beginTransaction();
        try {

            $sede_id    =   $request->get('sede_id');

            if(!$sede_id){
                throw new Exception("FALTA EL ID DE LA SEDE EN LA PETICIÓN!!!");
            }

            $sede   =   Sede::where('id', $sede_id)
                        ->where('estado', 'ACTIVO')
                        ->first();

            if(!$sede){
                throw new Exception("LA SEDE NO EXISTE EN LA BD!!!");
            }

            //======== EVITAR DUPLICADOS =======
            $existe =   DB::select('select * from empresa_numeracion_facturaciones as enf
                        where 
                        enf.tipo_comprobante = ?
                        and enf.sede_id = ?
                        and enf.estado = "ACTIVO"',
                        [$request->get('comprobante_id'),
                        $request->get('sede_id')]);

            if(count($existe) !== 0){
                throw new Exception("EL TIPO DE COMPROBANTE YA FUE AGREGADO!!!");
            }

            //======== VALIDANDO EL TIPO COMPROBANTE ====== 
            $tipo_comprobante   =   DB::select('select 
                                    td.* 
                                    from tabladetalles as td
                                    where td.id = ?
                                    and td.parametro = ?
                                    and td.estado = "ACTIVO"',
                                    [$request->get('comprobante_id'),
                                    $request->get('parametro')]);

            if(count($tipo_comprobante) === 0){
                throw new Exception("EL TIPO DE COMPROBANTE NO EXISTE EN LA BD!!!");
            }

            //========= REGISTRANDO NUMERACIÓN =====
            $numeracion                     = new Numeracion();
            $numeracion->empresa_id         =   1;
            $numeracion->sede_id            =   $sede_id;
            $numeracion->serie              =   $tipo_comprobante[0]->parametro.$sede->serie; 
            $numeracion->tipo_comprobante   =   $tipo_comprobante[0]->id;
            $numeracion->numero_iniciar     =   $request->get('nro_inicio');
            $numeracion->emision_iniciada   =   '0';
            $numeracion->numero_fin         =   null;
            $numeracion->save();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'NUMERACIÓN AGREGADA A LA SEDE',
                                'comprobante_id'=>$request->get('comprobante_id')]);
          
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }

    }

}
