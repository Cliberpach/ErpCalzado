<?php

namespace App\Http\Controllers\Ventas;

use App\Almacenes\Almacen;
use App\Almacenes\Conductor;
use Illuminate\Support\Facades\Storage;

use App\Almacenes\DetalleNotaSalidad;
use App\Almacenes\LoteProducto;
use App\Almacenes\NotaSalidad;
use App\Almacenes\Producto;
use App\Almacenes\Talla;
use App\Almacenes\Modelo;
use App\Almacenes\Vehiculo;
use App\Events\GuiaRegistrado;
use App\Events\NotifySunatEvent;
use App\Events\NumeracionGuiaRemision;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Tabla\Detalle as TablaDetalle;
use App\Ventas\Cliente;
use App\Ventas\DetalleGuia;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use App\Ventas\ErrorGuia;
use App\Ventas\Guia;
use App\Ventas\Tienda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Support\Facades\Auth;
use stdClass;
use Illuminate\Support\Facades\File;
use PhpZip\ZipFile;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response; 



use Greenter\Model\Client\Client;
use Greenter\Model\Despatch\Despatch;
use Greenter\Model\Despatch\DespatchDetail;
use Greenter\Model\Despatch\Direction;
use Greenter\Model\Despatch\Shipment;
use Greenter\Model\Despatch\Transportist;
use Greenter\Model\Response\CdrResponse;
use Greenter\Model\Response\SummaryResult;
use Greenter\Ws\Services\SunatEndpoints;
use App\Greenter\Utils\Util;
use App\Http\Requests\Ventas\Guias\GuiaStoreRequest;
use App\Mantenimiento\Sedes\Sede;
use App\User;
use Greenter\Model\Despatch\Driver;
use Greenter\Model\Despatch\Vehicle;
use Illuminate\Support\Facades\Cache;
use Throwable;

require __DIR__ . '/../../../../vendor/autoload.php';

class GuiaController extends Controller
{
    public function index()
    {
        $dato = "Message";
        broadcast(new NotifySunatEvent($dato));
        return view('ventas.guias.index');
    }

    public function create()
    {
      
    
        $sede_id            =   Auth::user()->sede_id;
        $sede_origen        =   Sede::find($sede_id);

        $almacenes          =   Almacen::where('estado','ACTIVO')
                                ->where('tipo_almacen','PRINCIPAL')
                                ->where('sede_id',$sede_id)
                                ->get();


        $registrador        =   User::find(Auth::user()->id);
        $modelos            =   Modelo::where('estado','ACTIVO')->get();
        $tallas             =   Talla::where('estado','ACTIVO')->get();
        $empresas           =   Empresa::where('estado','ACTIVO')->get();
        $conductores        =   Conductor::where('estado','ACTIVO')->get();
        $vehiculos          =   Vehiculo::where('estado','ACTIVO')->get();
        //$documento        =   Documento::findOrFail($id);
        //$detalles         =   Detalle::where('documento_id',$id)->get();
        $clientes           =   Cliente::where('estado', 'ACTIVO')->get();
        $productos          =   Producto::where('estado', 'ACTIVO')->get();
        $tipos_documento    =   DB::select('select 
                                td.* 
                                from tabladetalles as td 
                                where td.tabla_id = 3');

        $sedes              =   Sede::where('estado','ACTIVO')
                                ->where('id','<>',$sede_id)
                                ->get();

        $motivos_traslado   =   DB::select('select 
                                td.*
                                from tabladetalles as td
                                where 
                                td.tabla_id = 34
                                AND td.simbolo IN ("01","04")');



        return view('ventas.guias.create',[

            // 'documento' => $documento,
            // 'detalles' => $detalles,
            'sede_origen'       =>  $sede_origen,
            'motivos_traslado'  =>  $motivos_traslado,
            'sede_id'           =>  $sede_id,
            'almacenes'         =>  $almacenes,
            'registrador'       =>  $registrador,
            'modelos'           =>  $modelos,
            'tallas'            =>  $tallas,
            'empresas'          =>  $empresas,
            'conductores'       =>  $conductores,
            'sedes'             =>  $sedes,
            'vehiculos'         =>  $vehiculos,
            // 'direccion_empresa' => $direccion_empresa,
            'clientes'          =>  $clientes,
            'productos'         =>  $productos,
            'tipos_documento'   =>  $tipos_documento
            // 'pesos_productos'   => $pesos_productos,
            // 'cantidad_productos' => $cantidad_productos,
        ]);

    }

    public function formatearArrayDetalle($detalles){
        $detalleFormateado=[];
        $productosProcesados = [];
        foreach ($detalles as $detalle) {
            $cod   =   $detalle->producto_id.'-'.$detalle->color_id;
            if (!in_array($cod, $productosProcesados)) {
                $producto=[];
                //======== obteniendo todas las detalle talla de ese producto_color =================
                $producto_color_tallas = $detalles->filter(function ($detalleFiltro) use ($detalle) {
                    return $detalleFiltro->producto_id == $detalle->producto_id && $detalleFiltro->color_id == $detalle->color_id;
                });
                
                $producto['producto_codigo'] = $detalle->codigo_producto;
                $producto['producto_id'] = $detalle->producto_id;
                $producto['color_id'] = $detalle->color_id;
                $producto['producto_nombre'] = $detalle->nombre_producto;
                $producto['color_nombre'] = $detalle->nombre_color;
                $producto['modelo_nombre'] = $detalle->nombre_modelo;
                $producto['precio_unitario'] = $detalle->precio_unitario;
                

                $tallas=[];
                $subtotal=0.0;
                $cantidadTotal=0;
                foreach ($producto_color_tallas as $producto_color_talla) {
                    $talla=[];
                    $talla['talla_id']=$producto_color_talla->talla_id;
                    $talla['cantidad']=$producto_color_talla->cantidad;
                    $talla['talla_nombre']=$producto_color_talla->nombre_talla;
                    $subtotal+=$talla['cantidad']*$producto['precio_unitario'];
                    $cantidadTotal+=$talla['cantidad'];
                   array_push($tallas,$talla);
                }
                
                $producto['tallas']=$tallas;
                $producto['subtotal']=$subtotal;
                $producto['cantidad_total']=$cantidadTotal;
                array_push($detalleFormateado,$producto);
                $productosProcesados[] = $detalle->producto_id.'-'.$detalle->color_id;
            }
        }
        return $detalleFormateado;
    }

    public function create_new()
    {
        //====== VERIFICAR SI LAS GUÍAS DE REMISIÓN ESTÁN ACTIVAS EN LA EMPRESA ========
        $isActiveGuia   = $this->isActiveGuia();
        if(!$isActiveGuia){
            Session::flash('error_guia_remision','GUÍA DE REMISIÓN NO ESTÁ ACTIVA EN LA EMPRESA, Configurar en Mantenimiento/Empresas');
            return back();
        }


        $empresas           = Empresa::where('estado','ACTIVO')->get();
        $clientes           = Cliente::where('estado', 'ACTIVO')->get();
        $empresa            = Empresa::first();
        $hoy                = Carbon::now()->toDateString();
        $tallas             =   Talla::where('estado','ACTIVO')->get();
        $modelos            =   Modelo::all();
        $fullaccess = false;

        if (count(Auth::user()->roles) > 0) {
            $cont = 0;
            while ($cont < count(Auth::user()->roles)) {
                if (Auth::user()->roles[$cont]['full-access'] == 'SI') {
                    $fullaccess = true;
                    $cont = count(Auth::user()->roles);
                }
                $cont = $cont + 1;
            }
        }

        return view('ventas.guias.create_new',[
            'empresas' => $empresas,
            'empresa' => $empresa,
            'clientes' => $clientes,
            'hoy' => $hoy,
            'fullaccess' => $fullaccess,
            'tallas'    =>  $tallas,
            'modelos'   =>  $modelos
        ]);



    }

    public function getGuias()
    {
        $guias  =   DB::table('guias_remision as gr')
                    ->leftJoin('cotizacion_documento as cd', 'gr.documento_id', '=', 'cd.id') 
                    ->leftJoin('traslados as t', 't.id', '=', 'gr.traslado_id')   
                    ->leftJoin('empresa_sedes as esgg','esgg.id','gr.sede_genera_guia')
                    ->leftJoin('empresa_sedes as esug','esug.id','gr.sede_usa_guia')
                    ->join('users as u','u.id','gr.registrador_id')
                    ->where('gr.estado', '!=', 'NULO')
                    ->orderByDesc('gr.id')
                    ->select(
                        'gr.id',
                        'u.usuario as registrador_nombre',
                        'esgg.nombre as sede_genera_guia',
                        'esug.nombre as sede_usa_guia',
                        DB::raw("CASE 
                            WHEN gr.documento_id IS NOT NULL THEN CONCAT(cd.serie, '-', cd.correlativo) 
                            WHEN gr.traslado_id IS NOT NULL THEN CONCAT('TR-', t.id) 
                            ELSE '-' 
                        END AS documento_afectado"),
                        DB::raw("CASE 
                            WHEN gr.documento_id IS NOT NULL THEN cd.created_at 
                            WHEN gr.traslado_id IS NOT NULL THEN t.created_at 
                            ELSE '-' 
                        END AS documento_fecha"),
                        'gr.estado',
                        DB::raw("CONCAT(gr.serie, '-', gr.correlativo) AS serie_guia"),
                        DB::raw("CONCAT(gr.cantidad_productos, ' NIU') AS cantidad"),
                        DB::raw("CONCAT(FORMAT(gr.peso, 2), ' ', gr.unidad) AS peso"),
                        'gr.ruta_comprobante_archivo',
                        'gr.nombre_comprobante_archivo',
                        'gr.sunat',
                        'gr.estado_sunat',
                        'gr.regularize',
                        'gr.ruta_xml',
                        'gr.ruta_cdr',
                        'gr.cdr_response_code',
                        'gr.ticket'
                    )
                    ->get();

    
  
        //======== PUEDEN VER TODAS LAS GUÍAS DE SU SEDE =====
     
        //$guias->where('cd.sede_usa_guia', Auth::user()->sede_id);
        
              
        return DataTables::of($guias)->toJson();
    }

    public static function comprobanteActivo($sede_id,$tipo_comprobante){
      
        $existe =   DB::select('select 
                    enf.*
                    from empresa_numeracion_facturaciones as enf
                    where 
                    enf.empresa_id = 1
                    AND enf.sede_id = ?
                    AND enf.tipo_comprobante = ?',
                    [$sede_id,$tipo_comprobante->id]);

        if(count($existe) === 0){
            throw new Exception($tipo_comprobante->descripcion.', NO ESTÁ ACTIVO EN LA EMPRESA!!!');
        }

    }

    public static function validacionStore($request){

        //========= VALIDAR LA SEDE ========
        if(!$request->get('sede_genera_guia')){
            throw new Exception("FALTA LA SEDE QUE GENERA LA GUÍA!!!");
        }
        if(!$request->get('sede_usa_guia')){
            throw new Exception("FALTA LA SEDE QUE USA LA GUÍA!!!");
        }

        $sede_genera_guia   =   DB::select('select 
                                es.* 
                                from empresa_sedes as es
                                where
                                es.id = ? 
                                and es.estado = "ACTIVO"',
                                [$request->get('sede_genera_guia')]);

        if (count($sede_genera_guia) === 0) {
            throw new Exception("NO EXISTE LA SEDE GENERADORA DE GUÍA EN LA BD!!!");
        }

        $sede_usa_guia  =   DB::select('select 
                            es.* 
                            from empresa_sedes as es
                            where
                            es.id = ? 
                            and es.estado = "ACTIVO"',
                            [$request->get('sede_usa_guia')]);

        if (count($sede_usa_guia) === 0) {
            throw new Exception("NO EXISTE LA SEDE QUE USARÁ LA GUÍA EN LA BD!!!");
        }

        //========== SI LA MODALIDAD TRASLADO ES PÚBLICO
        // Y EL INDICAR DE M1L ESTÁ APAGADO,  EL DOCUMENTO DEL TRANSPORTISTA DEBE SER RUC =======
        if( $request->get('modalidad_traslado') === '01'
        && !$request->has('categoria_M1L'))
        {
            $conductor  =   Conductor::find($request->get('conductor'));
            if($conductor->tipo_documento_nombre === 'DNI'){
                throw new Exception("EL DOCUMENTO DEL TRANSPORTISTA DEBE SER RUC 
                SI LA MODALIDAD TRASLADO ES PÚBLICO
                Y EL INDICADOR DE M1L ESTÁ APAGADO");
            }
        }

        //======== VALIDAR DETALLE VENTA ======
        $lstGuia   =   json_decode($request->get('lstGuia'));
     
        if(count($lstGuia) === 0){
            throw new Exception("EL DETALLE DE LA GUÍA ESTÁ VACÍO!!!");
        }


        //========= VALIDANDO TIPO COMPROBANTE ========        
        $tipo_comprobante   =   DB::select('select 
                                td.* 
                                from tabladetalles as td
                                where 
                                td.tabla_id = 21
                                AND td.simbolo = ?
                                AND td.parametro = ?',
                                ["09","T"])[0];


        $almacen    =   Almacen::find($request->get('almacen'));

        $datos_validados    =   (object)[
                                        'sede_genera_guia'      =>  $request->get('sede_genera_guia'),
                                        'sede_usa_guia'         =>  $request->get('sede_usa_guia'),
                                        'tipo_comprobante'      =>  $tipo_comprobante,
                                        'porcentaje_igv'        =>  Empresa::find(1)->igv,
                                        'almacen'               =>  $almacen,
                                        'lstGuia'               =>  $lstGuia,
                                        'monto_embalaje'        =>  $request->get('monto_embalaje'),
                                        'monto_envio'           =>  $request->get('monto_envio'),
                                        'empresa'               =>  Empresa::find(1),
                                        'observacion'           =>  $request->get('observacion'),
                                    ];  

        return  $datos_validados;
    }

/*
{#1844 // app\Http\Controllers\Ventas\RegistroVentaController.php:174
  +"correlativo": 1
  +"serie": "B001"
}
*/ 
public static function getCorrelativo($tipo_comprobante,$sede_id){

    $correlativo        =   null;
    $serie              =   null;

    //======= CONTABILIZANDO SI HAY DOCUMENTOS DE VENTA EMITIDOS PARA EL TYPE SALE ======
    $guias  =    DB::select('select 
                    count(*) as cant
                    from guias_remision as gr
                    where
                    gr.sede_usa_guia = ? ',
                    [
                        $sede_id,
                    ])[0];


    $serializacion     =   DB::select('select 
                                    enf.*
                                    from empresa_numeracion_facturaciones as enf
                                    where 
                                    enf.empresa_id = ?
                                    and enf.tipo_comprobante = ?
                                    and enf.sede_id = ?',
                                    [Empresa::find(1)->id,
                                    $tipo_comprobante->id,
                                    $sede_id])[0];


    //==== SI LA CANT ES 0 =====
    if($guias->cant === 0){
        
        //====== INICIAR DESDE EL STARTING NUMBER =======
        $correlativo        =   $serializacion->numero_iniciar;
        $serie              =   $serializacion->serie;
        
    }else{
        //======= EN CASO YA EXISTAN DOCUMENTOS DE VENTA DEL TYPE SALE ======
        $correlativo        =   $guias->cant  +   1;
        $serie              =   $serializacion->serie;
    }

    return (object)['correlativo'=>$correlativo,'serie'=>$serie];

}

/*
array:14 [
  "sede_genera_guia"
  "sede_usa_guia"
  "registrador_id"      => 1
  "sede_id"             => 1
  "categoria_M1L"       => "on"  --OPCIONAL
  "registrador"         => "ADMINISTRADOR"
  "almacen"             => "1"
  "fecha_emision"       => "2025-02-27"
  "cliente"             => "1"
  "motivo_traslado"     => "01"
  "modalidad_traslado"  => "02"
  "fecha_traslado"      => "2025-02-27"
  "peso"                => "0.1"
  "unidad"              => "KGM"
  "vehiculo"            => "1"
  "conductor"           => "4"
  "sede_origen"         => "1"
  "sede_destino"        => "2"
  "lstGuia"             => "[{"producto_id":"1","color_id":"1","producto_nombre":"PRODUCTO TEST","color_nombre":"BLANCO","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"1"}]},{"producto_id":"1","color_id":"2","producto_nombre":"PRODUCTO TEST","color_nombre":"AZUL","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"2"}]},{"producto_id":"1","color_id":"3","producto_nombre":"PRODUCTO TEST","color_nombre":"CELESTE","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"3"}]},{"producto_id":"1","color_id":"4","producto_nombre":"PRODUCTO TEST","color_nombre":"PLOMO","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"4"}]}]"
]
*/
/*
    //======= SI EL MOTIVO DE TRASLADO ES VENTA Y LA MODALIDAD DE TRASLADO ES TRANSPORTE PÚBLICO ,
    EL CONDUCTOR DEBE TENER SOLO RUC =======
*/ 
    public function store(GuiaStoreRequest $request){
       
        DB::beginTransaction();
        try {

            $datos_validados    =   GuiaController::validacionStore($request);

            //========== CORRELATIVO DE LA GUÍA DEBE SER EL DE LA SEDE QUE USARÁ LA GUÍA ========
            GuiaController::comprobanteActivo($datos_validados->sede_usa_guia,$datos_validados->tipo_comprobante);
            
            //======== OBTENER CORRELATIVO Y SERIE ======
            $datos_correlativo  =   GuiaController::getCorrelativo($datos_validados->tipo_comprobante,$datos_validados->sede_usa_guia);


            $motivo_traslado    =   DB::select('select 
                                    td.*
                                    from tabladetalles as td
                                    where 
                                    td.tabla_id = 34
                                    AND td.simbolo = ?',
                                    [$request->get('motivo_traslado')])[0];

            $guia                           =   new Guia();
            $guia->cantidad_productos       =   GuiaController::calcularCantidadProductos($datos_validados->lstGuia);
            $guia->peso_productos           =   $request->get('peso');
            $guia->almacen_id               =   $request->get('almacen');
            $guia->fecha_emision            =   $request->get('fecha_emision');

            //======== MOTIVO TRASLADO ========
            $guia->motivo_traslado_id       =   $motivo_traslado->id;
            $guia->motivo_traslado_simbolo  =   $request->get('motivo_traslado');
            $guia->motivo_traslado_nombre   =   $motivo_traslado->descripcion;

            //======== MODALIDAD TRASLADO =======
            if($request->get('modalidad_traslado') === '01'){
                $guia->modalidad_traslado_simbolo   =   $request->get('modalidad_traslado');
                $guia->modalidad_traslado_nombre    =   'TRANSPORTE PUBLICO';   
            }
            if($request->get('modalidad_traslado') === '02'){
                $guia->modalidad_traslado_simbolo   =   $request->get('modalidad_traslado');
                $guia->modalidad_traslado_nombre    =   'TRANSPORTE PRIVADO';   
            }

            $guia->fecha_traslado                   =   $request->get('fecha_traslado');
            $guia->peso                             =   $request->get('peso');
            $guia->unidad                           =   $request->get('unidad');

            if ($request->has('categoria_M1L')) {
                $guia->categoria_M1L                =   true;
            }else{
                $guia->vehiculo_id                      =   $request->get('vehiculo');
                $guia->conductor_id                     =   $request->get('conductor');
            }

            //======= PUNTO PARTIDA =====
            $sede_partida                           =   Sede::find($request->get('sede_usa_guia'));
            $guia->punto_partida_id                 =   $request->get('sede_usa_guia');
            $guia->ubigeo_partida                   =   $sede_partida->distrito_id;
            $guia->direccion_partida                =   $sede_partida->direccion;

            //======== PUNTO LLEGADA ======
            //======= TRASLADO ENTRE ESTABLECIMIENTOS ======
            if($request->get('motivo_traslado') === '04'){
                $sede_llegada                           =   Sede::find($request->get('sede_genera_guia'));
                $guia->punto_llegada_id                 =   $request->get('sede_genera_guia');
                $guia->ubigeo_llegada                   =   $sede_llegada->distrito_id;
                $guia->direccion_llegada                =   $sede_llegada->direccion;
            }

            //======== VENTA ======
            if($request->get('motivo_traslado') === '01'){
                $cliente                                =   Cliente::find($request->get('cliente'));

                if(!$cliente->distrito_id){
                    throw new Exception("EL CLIENTE NO TIENE UBIGEO!!!");  
                }
                if(!$cliente->direccion){
                    throw new Exception("EL CLIENTE NO TIENE DIRECCIÓN!!!");  
                }

                $guia->cliente_id                       =   $cliente->id;
                $guia->ubigeo_llegada                   =   $cliente->distrito_id;
                $guia->direccion_llegada                =   $cliente->direccion;
            }

            $guia->sede_genera_guia     =   $request->get('sede_genera_guia');
            $guia->sede_usa_guia        =   $request->get('sede_usa_guia');

            $registrador                =   User::find($request->get('registrador_id'));
            $guia->registrador_id       =   $request->get('registrador_id');
            $guia->registrador_nombre   =   $registrador->usuario;
            $guia->empresa_id           =   1;

            $guia->correlativo          =   $datos_correlativo->correlativo;
            $guia->serie                =   $datos_correlativo->serie;

            //========= EN CASO SEA TRASLADO =======
            if($request->has('traslado')){
                $guia->traslado_id  =   $request->get('traslado');
            }

            //====== EN CASO DE VENTA =====
            if($request->has('venta')){
                $guia->documento_id  =   $request->get('venta');
            }

            $guia->save();

            //========== GRABAR DETALLE =======
            foreach ($datos_validados->lstGuia as  $producto) {
                foreach ($producto->tallas as  $talla) {

                    //====== COMPROBAR SI EXISTE EL PRODUCTO COLOR TALLA EN EL ALMACÉN =====
                    $existe =   DB::select('select 
                                pct.*,
                                p.nombre as producto_nombre,
                                p.codigo as producto_codigo,
                                c.descripcion as color_nombre,
                                t.descripcion as talla_nombre,
                                m.descripcion as modelo_nombre
                                from producto_color_tallas as pct
                                inner join productos as p on p.id = pct.producto_id
                                inner join colores as c on c.id = pct.color_id
                                inner join tallas as t on t.id = pct.talla_id
                                inner join modelos as m on m.id = p.modelo_id
                                where 
                                pct.almacen_id = ?
                                AND pct.producto_id = ?
                                AND pct.color_id = ?
                                AND pct.talla_id = ?',
                                [$request->get('almacen'),
                                $producto->producto_id,
                                $producto->color_id,
                                $talla->talla_id]);

                    if(count($existe) === 0){
                        throw new Exception($producto->producto_nombre.'-'.$producto->color_nombre.'-'.$talla->talla_nombre.', NO EXISTE EN EL ALMACÉN!!!');
                    }

                    //======= VALIDAR STOCKS EN CASO NO SEA TRASLADO ====== 
                    if(!$request->has('traslado') && !$request->has('venta')){
                        if( ($talla->cantidad > $existe[0]->stock) || ($talla->cantidad > $existe[0]->stock_logico) ){
                            throw new Exception($producto->producto_nombre.'-'.$producto->color_nombre.'-'.$talla->talla_nombre.', STOCK INSUFICIENTE!!!');
                        }
                    }

                    $guia_detalle                   =   new DetalleGuia();
                    $guia_detalle->guia_id          =   $guia->id;
                    $guia_detalle->almacen_id       =   $guia->almacen_id;
                    $guia_detalle->producto_id      =   $producto->producto_id;
                    $guia_detalle->color_id         =   $producto->color_id;
                    $guia_detalle->talla_id         =   $talla->talla_id;
                    $guia_detalle->cantidad         =   $talla->cantidad;
                    $guia_detalle->codigo_producto  =   $existe[0]->producto_codigo;
                    $guia_detalle->nombre_modelo    =   $existe[0]->modelo_nombre;
                    $guia_detalle->nombre_producto  =   $existe[0]->producto_nombre;
                    $guia_detalle->nombre_color     =   $existe[0]->color_nombre;
                    $guia_detalle->nombre_talla     =   $existe[0]->talla_nombre;
                    $guia_detalle->unidad           =   'NIU';
                    $guia_detalle->save();

                    //===== ACTUALIZANDO STOCK SOLO SI LA GUÍA NO DEPENDE DE OTRO DOCUMENTO ===========
                    if(!$request->has('traslado') && !$request->has('venta')){
                        DB::update('UPDATE producto_color_tallas 
                        SET stock = stock - ?, stock_logico = stock_logico - ?
                        WHERE 
                        almacen_id = ?
                        AND producto_id = ? 
                        AND color_id = ? 
                        AND talla_id = ?', 
                        [
                            $talla->cantidad,
                            $talla->cantidad,
                            $guia->almacen_id,
                            $producto->producto_id, 
                            $producto->color_id, 
                            $talla->talla_id
                        ]);
                    }

                }
            }


            //========== ACTUALIZAR ESTADO FACTURACIÓN A INICIADA ======
            DB::table('empresa_numeracion_facturaciones')
            ->where('empresa_id', 1) 
            ->where('sede_id', $datos_validados->sede_usa_guia) 
            ->where('tipo_comprobante', $datos_validados->tipo_comprobante->id) 
            ->where('emision_iniciada', '0') 
            ->where('estado','ACTIVO')
            ->update([
                'emision_iniciada'       => '1',
                'updated_at'             => Carbon::now()
            ]);


            DB::commit();
            return response()->json([
                'success'   =>  true,
                'message'   =>  "GUÍA DE REMISIÓN REGISTRADA CON ÉXITO",
                'id'        =>  $guia->id
            ]);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
    }

    public static function calcularCantidadProductos($lstProductos){
        $cantidad   =   0;
        foreach ($lstProductos as $producto ) {
            foreach ($producto->tallas as $talla ) {
                $cantidad   +=  $talla->cantidad;     
            }
        }
        return $cantidad;
    }

    public function store_old(Request $request)
    {           
        try
        {
            DB::beginTransaction();
            $data = $request->all();
            $rules = [
                'documento_id'=> 'nullable',
                'cantidad_productos'=> 'required',
                'peso_productos'=> 'required',
                'tienda'=> 'nullable',
                'observacion' => 'nullable',
                'direccion_empresa' => 'required',
                'ubigeo_llegada'=> 'required',
                'ubigeo_partida'=> 'required',
                'motivo_traslado'=> 'required',
            ];
            $message = [
                'direccion_empresa.required' => 'El campo direccion de llegada es obligatorio.',
                'cantidad_productos.required' => 'El campo Cantidad de Productos es obligatorio.',
                'peso_productos.required' => 'El campo Peso de Productos es obligatorio.',
                'ubigeo_llegada.required' => 'El campo Ubigeo es obligatorio.',
                'ubigeo_partida.required' => 'El campo Ubigeo es obligatorio.',
                'motivo_traslado.required' => 'El campo Motivo de traslado es obligatorio.',
            ];
            Validator::make($data, $rules, $message)->validate();

          
            if($request->has('documento_id'))
            {   
            
                $guia = Guia::where('documento_id',$request->get('documento_id'))->get();
                $documento = Documento::find($request->get('documento_id'));
                if (count($guia) == 0) {
                    $guia = new Guia();
                    $guia->documento_id = $request->get('documento_id');

                    $guia->tienda = $request->get('tienda');

                    $guia->ruc_transporte_oficina = '-';
                    $guia->nombre_transporte_oficina = '-';

                    $guia->ruc_transporte_domicilio = '-';
                    $guia->nombre_transporte_domicilio = '-';
                    $guia->direccion_llegada = $request->get('direccion_tienda');

                    $guia->cantidad_productos = $request->get('cantidad_productos');
                    //$guia->peso_productos = $request->get('peso_productos');
                    $guia->peso_productos = 1;
                    $guia->observacion = $request->get('observacion');
                    $guia->ubigeo_llegada = str_pad($request->get('ubigeo_llegada'), 6, "0", STR_PAD_LEFT);
                    $guia->ubigeo_partida = str_pad($request->get('ubigeo_partida'), 6, "0", STR_PAD_LEFT);
                    $guia->dni_conductor = $request->get('dni_conductor');
                    $guia->placa_vehiculo = $request->get('placa_vehiculo');

                    $guia->motivo_traslado = $request->motivo_traslado;

                    $guia->fecha_emision = $documento->fecha_documento;
                    $guia->ruc_empresa = $documento->ruc_empresa;
                    $guia->empresa_id = $documento->empresa_id;
                    $guia->empresa = $documento->empresa;
                    $guia->direccion_empresa = $documento->direccion_fiscal_empresa;

                    $guia->tipo_documento_cliente = $documento->tipo_documento_cliente;
                    $guia->documento_cliente = $documento->documento_cliente;
                    $guia->direccion_cliente = $documento->direccion_cliente;
                    $guia->cliente = $documento->cliente;
                    $guia->cliente_id = $documento->cliente_id;
                    $guia->user_id = auth()->user()->id;
                    $guia->save();

                    $detalles = Detalle::where('documento_id',$request->documento_id)->get();

                    foreach($detalles as $detalle)
                    {
                        DetalleGuia::create([
                            'guia_id' => $guia->id,
                            'producto_id'   =>  $detalle->producto_id,
                            'color_id'      =>  $detalle->color_id,
                            'talla_id'      =>  $detalle->talla_id,
                            // 'producto_id' => $detalle->lote->producto_id,
                            // 'lote_id' => $detalle->lote_id,
                            'codigo_producto'   =>  $detalle->codigo_producto,
                            'nombre_producto'   =>  $detalle->nombre_producto,
                            'nombre_modelo'     =>  $detalle->nombre_modelo,
                            'nombre_color'      =>  $detalle->nombre_color,
                            'nombre_talla'      =>  $detalle->nombre_talla,
                            // 'unidad' => $detalle->unidad,
                            'cantidad' => $detalle->cantidad,
                        ]);
                    }


                    $envio_prev = self::sunat_prev($guia->id);

                    if(!$envio_prev['success'])
                    {
                        DB::rollBack();
                        Session::flash('error',$envio_prev['mensaje']);
                        return back()->with('sunat_error', 'error');
                    }


                    DB::commit();
                    //$envio_post = self::sunat_post($guia->id);
                    //$guia_pdf = self::guia_pdf($guia->id);
                    Session::flash('guia_exito','Guia de Remision creada.');
                    return redirect()->route('ventas.guiasremision.index');
                }else{
                    Session::flash('error_guia_remision','Guia de Remision ya ha sido creado.');
                    return redirect()->route('ventas.guiasremision.index');
                }
            }
            else{
              
                $productosJSON = $request->get('productos_tabla');
                $detalles = json_decode($productosJSON[0]);

                $fecha_hoy = Carbon::now()->toDateString();
                $fecha = Carbon::createFromFormat('Y-m-d', $fecha_hoy);
                $fecha = str_replace("-", "", $fecha);
                $fecha = str_replace(" ", "", $fecha);
                $fecha = str_replace(":", "", $fecha);
                $ngenerado = $fecha . (DB::table('nota_salidad')->count() + 1);

                $motivo         = TablaDetalle::find($request->motivo_traslado);
                $tabladetalle   = TablaDetalle::where('descripcion', $motivo->descripcion)->where('tabla_id', 29)->first();

                if(empty($tabladetalle))
                {
                    $destino = new TablaDetalle();
                    $destino->descripcion = $motivo->descripcion;
                    $destino->simbolo = $motivo->simbolo;
                    $destino->estado = 'ACTIVO';
                    $destino->editable = 1;
                    $destino->tabla_id = 29;
                    $destino->save();
                }
                else {
                    $destino = $tabladetalle;
                }
              
               
                $notasalidad                = new NotaSalidad();
                $notasalidad->numero        = $ngenerado;
                $notasalidad->fecha         = Carbon::now()->toDateString();
                $notasalidad->destino       = $destino->descripcion;
                $notasalidad->origen        = $request->origen;
                $notasalidad->usuario       = Auth()->user()->usuario;
                $notasalidad->observacion   = '/guiasremision/create_new';
                $notasalidad->save();

                foreach ($detalles as $fila) {
                    foreach ($fila->tallas as $talla) {
                        DetalleNotaSalidad::create([
                            'nota_salidad_id' => $notasalidad->id,
                            'color_id'      => $fila->color_id,
                            'talla_id'      => $talla->talla_id,
                            'cantidad'      => $talla->cantidad,
                            'producto_id'   => $fila->producto_id
                        ]);
                    }
                }


                $empresa = Empresa::first();
                $cliente = Cliente::findOrFail($request->cliente_id);

                $guia                   = new Guia();
                $guia->tienda           = $request->get('tienda');
                $guia->nota_salida_id   = $notasalidad->id;

                $guia->ruc_transporte_oficina       = '-';
                $guia->nombre_transporte_oficina    = '-';

                $guia->ruc_transporte_domicilio     = '-';
                $guia->nombre_transporte_domicilio  = '-';
                $guia->direccion_llegada            = $request->get('direccion_tienda');

                $guia->cantidad_productos           = $request->get('cantidad_productos');
                $guia->peso_productos               = 1;
                $guia->observacion                  = $request->get('observacion');
                $guia->ubigeo_llegada               = str_pad($request->get('ubigeo_llegada'), 6, "0", STR_PAD_LEFT);
                $guia->ubigeo_partida               = str_pad($request->get('ubigeo_partida'), 6, "0", STR_PAD_LEFT);
                $guia->dni_conductor                = $request->get('dni_conductor');
                $guia->placa_vehiculo               = $request->get('placa_vehiculo');
                
                $guia->motivo_traslado              = $request->motivo_traslado;

                $guia->fecha_emision                = $request->get('fecha_documento');
                $guia->ruc_empresa                  = $empresa->ruc;
                $guia->empresa                      = $empresa->razon_social;
                $guia->empresa_id                   = $empresa->id;
                $guia->direccion_empresa            = $empresa->direccion_fiscal;

                $guia->tipo_documento_cliente       = $cliente->tipo_documento;
                $guia->documento_cliente            = $cliente->documento;
                $guia->direccion_cliente            = $cliente->direccion;
                $guia->cliente                      = $cliente->nombre;
                $guia->cliente_id                   = $cliente->id;
                $guia->user_id                      = auth()->user()->id;
                $guia->save();
               
              
                foreach($detalles as $detalle)
                {
                    $producto = DB::select('select m.descripcion as nombre_modelo, p.codigo as codigo_producto
                    from productos as p
                    inner join modelos as m on p.modelo_id=m.id
                    where m.estado="ACTIVO" and p.id=?',[$detalle->producto_id]);

                    foreach ($detalle->tallas as $talla) {
                        DetalleGuia::create([
                            'guia_id' => $guia->id,
                            'producto_id'   =>  $detalle->producto_id,
                            'color_id'      =>  $detalle->color_id,
                            'talla_id'      =>  $talla->talla_id,
                            'codigo_producto'   =>  $producto[0]->codigo_producto,
                            'nombre_producto'   =>  $detalle->producto_nombre,
                            'nombre_modelo'     =>  $producto[0]->nombre_modelo,
                            'nombre_color'      =>  $detalle->color_nombre,
                            'nombre_talla'      =>  $talla->talla_nombre,
                            'cantidad'          =>  $talla->cantidad,
                        ]);
                    }
                }
               
               
                $envio_prev = self::sunat_prev($guia->id);
               
                //==== REPONER STOCKS LÓGICOS EN CASO ALGO FALLE =====
                if(!$envio_prev['success'])
                {
                    DB::rollBack();
                    $productosJSON = $request->get('productos_tabla');
                    $detalles = json_decode($productosJSON[0]);
                    foreach ($detalles as $detalle) {
                        foreach ($detalle->tallas as $talla) {
                            DB::table('producto_color_tallas')
                                ->where('producto_id', $detalle->producto_id)
                                ->where('color_id', $detalle->color_id)
                                ->where('talla_id', $talla->talla_id) 
                                ->increment('stock_logico', $talla->cantidad); 
                        }
                    }
                    Session::flash('error_guia_remision',$envio_prev['mensaje']);
                    return back();
                }
           
                DB::commit();
                //$envio_post = self::sunat_post($guia->id);
                //$guia_pdf = self::guia_pdf($guia->id);
                Session::flash('guia_exito','Guia de Remision creada.');
                return redirect()->route('ventas.guiasremision.index')->with('guardar', 'success');
            }
        }
        catch(Exception $e)
        {
            DB::rollBack();
            // $productosJSON = $request->get('productos_tabla');
            // $detalles = json_decode($productosJSON[0]);
            // foreach ($detalles as $detalle) {
            //     $lote = LoteProducto::find($detalle->lote_id);
            //     $lote->cantidad_logica = $lote->cantidad_logica + $detalle->cantidad;
            //     $lote->update();                
            // }
            return back()->with('error' , $e->getMessage());
        }
    }

    public function obtenerFecha($guia)
    {
        $date = strtotime($guia->fecha_emision);
        $fecha_emision = date('Y-m-d', $date);
        $hora_emision = date('H:i:s', $date);
        $fecha = $fecha_emision.'T'.$hora_emision.'-05:00';
        
        return $fecha;
    }

    public function obtenerProductos($guia)
    {
        $detalles = DetalleGuia::where('guia_id',$guia->id)->get();

        $arrayProductos = Array();
        for($i = 0; $i < count($detalles); $i++){

            $arrayProductos[] = array(
                "codigo" => $detalles[$i]->codigo_producto,
                "unidad" => $detalles[$i]->unidad,
                "descripcion"=> $detalles[$i]->nombre_modelo.'-'.$detalles[$i]->nombre_producto.'-'.$detalles[$i]->nombre_color.'-'.$detalles[$i]->nombre_talla,
                "cantidad" => intval($detalles[$i]->cantidad)
                // "codProdSunat" => '10',
            );
        }

        return $arrayProductos;
    }

    public function condicionReparto($guia)
    {
        $Transportista = array(
            "tipoDoc"=> "6",
            "numDoc"=> $guia->ruc_transporte_domicilio,
            "rznSocial"=> $guia->nombre_transporte_domicilio,
            "placa"=> $guia->placa_vehiculo,
            "choferTipoDoc"=> "1",
            "choferDoc"=> $guia->dni_conductor
        );

        return $Transportista;
    }

    public function limitarDireccion($cadena, $limite, $sufijo){

        if(strlen($cadena) > $limite){
            return substr($cadena, 0, $limite) . $sufijo;
        }

        return $cadena;
    }

    public function show($id)
    {   
        try {
        
            $guia           =   Guia::with(['documento','detalles'])->findOrFail($id);

            $destinatario   =   null;
            $partida        =   Sede::find($guia->punto_partida_id);

            if($guia->motivo_traslado_simbolo === '01'){//===== VENTA ====
                $destinatario   =   DB::select('select
                                    c.nombre,
                                    c.direccion,
                                    c.distrito_id,
                                    d.nombre as departamento_nombre,
                                    p.nombre as provincia_nombre,
                                    di.nombre as distrito_nombre
                                    from clientes as c
                                    inner join departamentos as d on d.id = c.departamento_id
                                    inner join provincias as p on p.id = c.provincia_id
                                    inner join distritos as di on di.id = c.distrito_id
                                    where c.id = ?',[$guia->cliente_id])[0];
            } 
            if($guia->motivo_traslado_simbolo === '04'){//===== TRASLADO INTERNO ====
                $destinatario   =   Sede::find($guia->punto_llegada_id);
            } 


            $vehiculo   =   Vehiculo::find($guia->vehiculo_id);
            $conductor  =   Conductor::find($guia->conductor_id);

            $empresa    =   Empresa::first();
            $sede       =   Sede::find($guia->sede_usa_guia);


            $pdf    =   PDF::loadview('ventas.guias.reportes.guia', [
                            'guia'          => $guia,
                            'empresa'       => $empresa,
                            'sede'          => $sede,
                            'conductor'     => $conductor,
                            'vehiculo'      => $vehiculo,
                            'destinatario'  => $destinatario,
                            'partida'       => $partida
                        ])->setPaper('a4')->setWarnings(false);


            return $pdf->stream($guia->serie . '-' . $guia->correlativo . '.pdf');    
           
        } catch (\Throwable $th) {
            dd($th);
        }

        $name = $guia->serie . "-" . $guia->correlativo . '.pdf';
                            
        $rutaPdfGuia = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'guia' . DIRECTORY_SEPARATOR . $name);
        
        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'guia'))) {
            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'guia'));
        }

     

       

      
        return response()->file($rutaPdfGuia);

        
    }

    public function controlConfiguracionGreenter($util){

        //==== OBTENIENDO CONFIGURACIÓN DE GREENTER ======
        $greenter_config    =   DB::select('select 
                                gc.ruta_certificado,
                                gc.id_api_guia_remision,
                                gc.modo,
                                gc.clave_api_guia_remision,
                                e.ruc,e.razon_social,
                                e.direccion_fiscal,
                                e.ubigeo,
                                e.direccion_llegada,
                                gc.sol_user,
                                gc.sol_pass
                                from greenter_config as gc
                                inner join empresas as e on e.id=gc.empresa_id
                                inner join configuracion as c on c.propiedad = gc.modo
                                where gc.empresa_id=1 and c.slug="AG"');



        if(count($greenter_config) === 0){
            throw new Exception('NO SE ENCONTRÓ NINGUNA CONFIGURACIÓN PARA GREENTER');
        }

        if(!$greenter_config[0]->sol_user){
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_USER');
        }
        if(!$greenter_config[0]->id_api_guia_remision){
            throw new Exception('DEBE ESTABLECER EL ID API GUÍA DE REMISIÓN');
        }
        if(!$greenter_config[0]->clave_api_guia_remision){
            throw new Exception('DEBE ESTABLECER LA CLAVE API GUÍA DE REMISIÓN');
        }
        if(!$greenter_config[0]->sol_pass){
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_PASS');
        }
        if ($greenter_config[0]->modo !== "BETA" && $greenter_config[0]->modo !== "PRODUCCION") {
            throw new Exception('NO SE HA CONFIGURADO EL AMBIENTE BETA O PRODUCCIÓN PARA GREENTER');
        }

        $see    =   null;
    
        $see = $util->getSeeApi($greenter_config[0]);
        

       
        if(!$see){
            throw new Exception('ERROR EN LA CONFIGURACIÓN DE GREENTER, SEE ES NULO');
        }

        return $see;
    }

    public function sunat_antiguo(){
          //ARREGLO GUIA
                    // $arreglo_guia = array(
                    //         "version"   => 2022,
                    //         "tipoDoc" => "09",
                    //         "serie" => $existe[0]->get('numeracion')->serie,
                    //         "correlativo"=> $guia->correlativo,
                    //         "fechaEmision" => self::obtenerFecha($guia),

                    //         "company" => array(
                    //             "ruc" => $guia->ruc_empresa,
                    //             "razonSocial" => $guia->empresa,
                    //             "nombreComercial"   =>  $guia->empresa,
                    //             "address" => array(
                    //                 "direccion" => $guia->direccion_empresa,
                    //             )),


                    //         "destinatario" => array(
                    //             "tipoDoc" =>  $guia->codTraslado() == "04" ? "6" : $guia->tipoDocumentoCliente(),
                    //             "numDoc" =>  $guia->codTraslado() == "04" ? $guia->ruc_empresa : $guia->documento_cliente,
                    //             "rznSocial" =>  $guia->codTraslado() == "04" ? $guia->empresa : $guia->cliente,
                    //             // "address" => array(
                    //             //     "direccion" =>  $guia->codTraslado() == "04" ? $guia->direccion_empresa : $guia->direccion_cliente,
                    //             // )
                    //         ),

                    //         "observacion" => $guia->observacion,

                    //         "envio" => array(
                    //             "modTraslado" =>  "01",
                    //             "codTraslado" =>  $guia->codTraslado(),
                    //             "desTraslado" =>  $guia->desTraslado(),
                    //             "fecTraslado" =>  self::obtenerFecha($guia),//FECHA DEL TRANSLADO
                    //             "codPuerto" => "123",
                    //             "indTransbordo"=> false,
                    //             "pesoTotal" => 1.0,
                    //             "undPesoTotal"=> "KGM",
                    //             "numContenedor" => "XD-2232",
                    //             "numBultos" => $guia->cantidad_productos,
                    //             "llegada" => array(
                    //                 "ubigueo" =>  $guia->ubigeo_llegada,
                    //                 "direccion" => self::limitarDireccion($guia->direccion_llegada,50,"..."),
                    //             ),
                    //             "partida" => array(
                    //                 "ubigueo" => $guia->ubigeo_partida,
                    //                 "direccion" => self::limitarDireccion($guia->direccion_empresa,50,"..."),
                    //             ),
                    //             "transportista"=> self::condicionReparto($guia)
                    //         ),

                    //         "details" =>  self::obtenerProductos($guia),
                    // );
    }


/*
Greenter\Model\Response\SummaryResult {#4268 ▼
  #ticket: "test-ca01da22-b685-483d-8929-b4e84bc0c95d"
  #success: true
  #error: null
}
  guia_id:1
*/

/*
    //======= SI EL MOTIVO DE TRASLADO ES VENTA Y LA MODALIDAD DE TRASLADO ES TRANSPORTE PÚBLICO ,
    EL CONDUCTOR DEBE TENER SOLO RUC =======
*/ 
    public function sunat(Request $request)
    {
        $id     =   $request->get('guia_id');
        $guia   =   Guia::findOrFail($id);
        
        try {

            if($guia->sunat === '1'){
                throw new Exception("LA GUÍA YA FUE ENVIADA A SUNAT!!!");
            }

            $util = Util::getInstance();

            $sede_partida   =   Sede::find($guia->punto_partida_id);
            $destinatario   =   null;
            if($guia->motivo_traslado_simbolo === '01'){//===== VENTA ====
             
                $destinatario   =   DB::select('select
                                    c.nombre,
                                    c.direccion,
                                    c.distrito_id,
                                    d.nombre as departamento_nombre,
                                    p.nombre as provincia_nombre,
                                    di.nombre as distrito_nombre
                                    from clientes as c
                                    inner join departamentos as d on d.id = c.departamento_id
                                    inner join provincias as p on p.id = c.provincia_id
                                    inner join distritos as di on di.id = c.distrito_id
                                    where c.id = ?',
                                    [$guia->cliente_id])[0];
            } 
            if($guia->motivo_traslado_simbolo === '04'){//===== TRASLADO INTERNO ====
                $destinatario   =   Sede::find($guia->punto_llegada_id);
            } 

            $envio = new Shipment();
            $envio
                ->setCodTraslado($guia->motivo_traslado_simbolo) // Cat.20 - Traslado entre establecimientos de la misma empresa 04 / 01 VENTA
                ->setModTraslado($guia->modalidad_traslado_simbolo) // Cat.18 - Transp. Privado 02  / 01 PUBLICO
                ->setFecTraslado(new \DateTime($guia->fecha_traslado))
                ->setPesoTotal($guia->peso)
                ->setUndPesoTotal($guia->unidad)
                ->setPartida((new Direction($sede_partida->distrito_id, $sede_partida->direccion))
                    ->setRuc($sede_partida->ruc)
                    ->setCodLocal($sede_partida->codigo_local));

            if($guia->motivo_traslado_simbolo === '04'){
                $envio->setLlegada((new Direction($destinatario->distrito_id, $destinatario->direccion))
                ->setRuc($destinatario->ruc)
                ->setCodLocal($destinatario->codigo_local)); // Código de establecimiento anexo
            }
            if($guia->motivo_traslado_simbolo === '01'){
                $envio->setLlegada((new Direction($destinatario->distrito_id, $destinatario->direccion))); // DATOS DE CLIENTE
            }


            //====== EN CASO DE VEHICULOS M1 y L ======
            if($guia->categoria_M1L == '1'){
                $envio->setIndicadores(['SUNAT_Envio_IndicadorTrasladoVehiculoM1L']); // Transp M1 y L
            }else{

            
                //======== TRANSPORTE PÚBLICO =======
                if($guia->modalidad_traslado_simbolo === '01'){
                    $conductor  =   Conductor::find($guia->conductor_id);
                    $tipo_doc   =   null;

                    if($conductor->tipo_documento_nombre === 'DNI'){
                        $tipo_doc   =   '1';
                    }
                    if($conductor->tipo_documento_nombre === 'RUC'){
                        $tipo_doc   =   '6';
                    }

                    $transp = new Transportist();
                    $transp->setTipoDoc($tipo_doc)
                    ->setNumDoc($conductor->nro_documento)
                    ->setRznSocial($conductor->nombre_completo)
                    ->setNroMtc($conductor->registro_mtc); //4DIGITOS

                    $envio->setTransportista($transp);
                }

                //======== PRIVADO ========
                if($guia->modalidad_traslado_simbolo === '02'){
                    $conductor  =   Conductor::find($guia->conductor_id);
                    $vehiculo   =   Vehiculo::find($guia->vehiculo_id);
                    $tipo_doc   =   null;

                    if($conductor->tipo_documento_nombre === 'DNI'){
                        $tipo_doc   =   '1';
                    }
                    if($conductor->tipo_documento_nombre === 'RUC'){
                        $tipo_doc   =   '6';
                    }

                    $chofer = (new Driver())
                    ->setTipo('Principal')
                    ->setTipoDoc($tipo_doc)
                    ->setNroDoc($conductor->nro_documento)
                    ->setLicencia($conductor->licencia)
                    ->setNombres($conductor->nombres)
                    ->setApellidos($conductor->apellidos);

                    $vehiculoPrincipal = (new Vehicle())
                    ->setPlaca($vehiculo->placa); 


                    $envio->setChoferes([$chofer])
                    ->setVehiculo($vehiculoPrincipal);

                }
            }

            $cliente_despatch   =   new Client();

            //======== TRASLADO INTERNO =====
            if($guia->motivo_traslado_simbolo === '04'){
                $cliente_despatch->setTipoDoc('6')
                ->setNumDoc($sede_partida->ruc)
                ->setRznSocial($sede_partida->razon_social);
            }

            //======== VENTA ======
            if($guia->motivo_traslado_simbolo === '01'){

                $cliente_venta  =   Cliente::find($guia->cliente_id);
                $tipo_documento =   null;

                if($cliente_venta->tipo_documento === 'DNI'){
                    $tipo_documento =   '1';
                }
                if($cliente_venta->tipo_documento === 'RUC'){
                    $tipo_documento =   '6';
                }

                $cliente_despatch->setTipoDoc($tipo_documento)
                ->setNumDoc($cliente_venta->documento)
                ->setRznSocial($cliente_venta->nombre);
            }
            

            //===== DESPACHO =======
            $despatch = new Despatch();
            $despatch->setVersion('2022')
            ->setTipoDoc('09')
            ->setSerie($guia->serie)
            ->setCorrelativo($guia->correlativo)
            ->setFechaEmision(new \DateTime($guia->fecha_emision))
            ->setCompany($util->getGRECompany())
            ->setDestinatario($cliente_despatch)
            ->setEnvio($envio);

                            
            //===== LLENANDO DETALLE =======
            $productos  =   self::obtenerProductos($guia);
            $detalles   =   [];

            foreach ($productos as $producto) {
                $detail = new DespatchDetail();
                $detail->setCantidad($producto['cantidad'])
                        ->setUnidad($producto['unidad'])
                        ->setDescripcion($producto['descripcion'])
                        ->setCodigo($producto['codigo']);
                $detalles[] =   $detail;
            }

            $despatch->setDetails($detalles);

            //===== obteniendo configuración de envío ==========
            $api = $this->controlConfiguracionGreenter($util); 


            //======== CONSTRUYENDO XML Y ENVIANDO A SUNAT ==========
            $res = $api->send($despatch);

   
            //======== RESPONSE ESTRUCTURA ========
            // ticket(string) | success(boolean) | error
            //==== GUARDANDO XML ====
            $util->writeXml($despatch, $api->getLastXml(),"GUIA REMISION",null);
            $guia->ruta_xml      =   'storage/greenter/guías_remisión/xml/'.$despatch->getName().'.xml';

                        
            //===== VERIFICANDO CONEXIÓN CON SUNAT =======
            if($res->isSuccess()){
                            
                //==== OBTENER Y GUARDAR TICKET ====
                $ticket         =   $res->getTicket();
                $guia->ticket   =   $ticket;
                $guia->sunat    =   '1';
                $guia->regularize   =   '0';
                $guia->despatch_name    =   $despatch->getName();
                $guia->update();
                            
                return response()->json(['success'=>true,'message'=>'GUÍA DE REMISIÓN ENVIADA A SUNAT']);

            } else{

                //COMO SUNAT NO LO ADMITE VUELVE A SER 0
                $guia->sunat        = '0';
                $guia->regularize   = '1';
                $guia->despatch_name    =   $despatch->getName();
                $guia->update();

               throw new Exception("ERROR AL ENVIAR GUÍA A SUNAT");
               
            }

        } catch (\Throwable $th) {
           return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
        }
               

    }


/*
array:1 [
  "id" => 4
]
*/ 
    public function consulta_ticket(Request $request){
        try {

            //==== OBTENER LA GUÍA POR SU ID =====
            $id     =   $request->get('id');
            $guia   =   Guia::findOrFail($id);
            $ticket =   $guia->ticket;

            if(!$ticket){
                throw new Exception("LA GUÍA NO TIENE UN TICKET");
            }

            if($ticket){
               
                $util = Util::getInstance();
                //===== iniciar greenter api ====
                $api = $this->controlConfiguracionGreenter($util); 
                //======== CONSULTANDO ESTADO DE LA GUÍA =====
                $res = $api->getStatus($ticket);

            
                //======== response estructura =======
                    /*  code: 99(envío con error)   |   cdrResponse (null o con contenido)
                        code: 98(envío en proceso)  |   cdrResponse(aún sin cdr)
                        code: 0(envío ok)           |   cdrResponse(con contenido)    
                    */
                $code_estado    =   $res->getCode();
                $cdr_response   =   $res->getCdrResponse();
                $descripcion    =   null;

                $guia->response_success =   $res->isSuccess();

                if($code_estado == 0){
                    $descripcion            =   'ACEPTADA';
                    $guia->sunat            =   '1';
                    $guia->regularize       =   '0';
                    $guia->response_code    =   $code_estado;
                    

                    //==== GUARDANDO DATOS DEL CDRZIP =====
                    $guia->cdr_response_id          =   $cdr_response->getId();
                    $guia->cdr_response_code        =   $cdr_response->getCode();
                    $guia->cdr_response_description =   $cdr_response->getDescription();
                    $guia->cdr_response_reference   =   $cdr_response->getReference();



                    //========= GUARDANDO NOTES ======
                    $response_notes =   '';
                    foreach ($cdr_response->getNotes() as $note) {
                       $response_notes.= '|'.$note.'|';
                    }
                    $guia->cdr_response_notes   =   $response_notes;
                    
                    
                    //====== GUARDANDO CDR  =========== 
                    $util->writeCdr(null, $res->getCdrZip(), "GUIA REMISION",$guia->despatch_name);
                    $guia->ruta_cdr      =   'storage/greenter/guías_remisión/cdr/'.$guia->despatch_name.'.zip';
                    


                    //========= GENERANDO QR =========
                    $miQr = QrCode::format('svg')
                    ->size(130) //defino el tamaño
                    ->backgroundColor(0, 0, 0) //defino el fondo
                    ->color(255, 255, 255)
                    ->margin(1) //defino el margen
                    ->generate($cdr_response->getReference());

                    $pathToFile_qr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR . 'guia' . DIRECTORY_SEPARATOR . $guia->despatch_name.'.svg');

                    if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public'  . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR . 'guia'))) {
                        mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR . 'guia'));
                    }

                    file_put_contents($pathToFile_qr, $miQr);
                    
                    $guia->ruta_qr  =   $pathToFile_qr;   
                    $guia->update();
                }

                if($code_estado == 98){
                    $descripcion            = 'EN PROCESO';
                    $guia->sunat            = '1';
                    $guia->regularize       = '0';
                    $guia->response_code    = $code_estado;
                    $guia->update();
                }

                if($code_estado == 99 && $cdr_response){
                    $descripcion            = 'EN PROCESO';
                    $guia->sunat            = '1';
                    $guia->regularize       = '1';
                    $guia->response_code    = $code_estado;
                   

                    $descripcion    =   'ENVÍO CON ERROR CON GENERACIÓN DE CDR';

                    //==== GUARDANDO DATOS DEL CDRZIP =====
                    $guia->cdr_response_id          =   $cdr_response->getId();
                    $guia->cdr_response_code        =   $cdr_response->getCode();
                    $guia->cdr_response_description =   $cdr_response->getDescription();
                    $guia->cdr_response_reference   =   $cdr_response->getReference();
  
                    //========= GUARDANDO NOTES ======
                    $response_notes =   '';
                    foreach ($cdr_response->getNotes() as $note) {
                        $response_notes.= '|'.$note.'|';
                    }
                    $guia->cdr_response_notes   =   $response_notes;
                      
                   //====== GUARDANDO CDR  =========== 
                   $util->writeCdr(null, $res->getCdrZip(), "GUIA REMISION",$guia->despatch_name);
                   $guia->ruta_cdr      =   'storage/greenter/guías_remisión/cdr/'.$guia->despatch_name.'.zip';
                   $guia->update();
                }

                if($code_estado == '99' && !$cdr_response){
                    $descripcion            =   'ENVÍO CON ERROR SIN GENERACIÓN DE CDR';
                    $guia->sunat            =   '1';
                    $guia->regularize       =   '1';
                    $guia->response_code    =   $code_estado;
                    $guia->update();
                }

                //======= ARCHIVO YA PRESENTADO ANTERIORMENTE ========
                if($guia->cdr_response_code == 2223 ){
                    //======== MARCAR COMO ENVIADO =======
                    $guia->regularize       =   '0';
                    $guia->sunat            =   '1';
                    $guia->update();
                }
            

                $response = [   'code_estado'       =>  $code_estado,
                                'cdr'               =>  $cdr_response?1:0,
                                'descripcion'       =>  $descripcion,
                                'guia_actualizada'  =>  $guia];

                return response()->json([  'success' => true,'message'=>$descripcion ]);
            }

        } catch (Throwable $th) {
           
           return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
        
    }

    private function isActiveGuia(){
        $validacion     =   DB::select('select enf.* from empresa_numeracion_facturaciones as enf
                            inner join tabladetalles as td on td.id=enf.tipo_comprobante
                            where td.estado="ACTIVO" and enf.estado="ACTIVO" and td.tabla_id=21 
                            and td.simbolo="09" and td.parametro="T"');
        if(count($validacion) === 0){
            return false;
        }
        if(count($validacion) === 1){
            return true;
        }
        return false;
    }

    public function sunat_prev($id)
    {               

        try
        {
            $guia = Guia::findOrFail($id);
            //OBTENER CORRELATIVO DE LA GUIA DE REMISION
            $existe = event(new NumeracionGuiaRemision($guia));
         
            if($existe[0]){

                if ($existe[0]->get('existe') == true) {
                    return array('success' => true,'mensaje' => 'Guia validada.');
                }else{
                    $errorGuia = new ErrorGuia();
                    $errorGuia->guia_id = $guia->id;
                    $errorGuia->tipo = 'sunat-existe';
                    $errorGuia->descripcion = 'Error al crear serie y correlativo';
                    $errorGuia->ecxepcion = 'Guia de remision no se encuentra registrado en la empresa.';
                    $errorGuia->save();
                    return array('success' => false,'mensaje' => 'Guia de remision no se encuentra registrado en la empresa.');
                    // Session::flash('error','Guia de remision no se encuentra registrado en la empresa.');
                    // return redirect()->route('ventas.guiasremision.index')->with('sunat_existe', 'error');
                }
            }else{

                $errorGuia = new ErrorGuia();
                $errorGuia->guia_id = $guia->id;
                $errorGuia->tipo = 'sunat-existe';
                $errorGuia->descripcion = 'Error al crear serie y correlativo';
                $errorGuia->ecxepcion = 'Empresa sin parametros para emitir Guia de remisión remitente electrónica.';
                $errorGuia->save();
                return array('success' => false,'mensaje' => 'Empresa sin parametros para emitir Guia de remisión remitente electrónica.');
                // Session::flash('error','Empresa sin parametros para emitir Guia de remisión remitente electrónica.');
                // return redirect()->route('ventas.guiasremision.index');
            }
        }
        catch(Exception $e)
        {
            $guia = Guia::findOrFail($id);

            $errorGuia = new ErrorGuia();
            $errorGuia->guia_id = $guia->id;
            $errorGuia->tipo = 'sunat-existe';
            $errorGuia->descripcion = 'Error crear serie y correlativo';
            $errorGuia->ecxepcion = $e->getMessage();
            $errorGuia->save();
            return array('success' => false,'mensaje' => $e->getMessage());
        }
    }

    public function sunat_post($id)
    {
        try{
            $guia = Guia::findOrFail($id);
            if ($guia->sunat != '1') {
                //ARREGLO GUIA
                $arreglo_guia = array(
                        "tipoDoc" => "09",
                        "serie" => $guia->serie,
                        "correlativo"=> $guia->correlativo,
                        "fechaEmision" => self::obtenerFecha($guia),

                        "company" => array(
                            "ruc" => $guia->ruc_empresa,
                            "razonSocial" => $guia->empresa,
                            "address" => array(
                                "direccion" => $guia->direccion_empresa,
                            )),


                        "destinatario" => array(
                            "tipoDoc" =>  $guia->codTraslado() == "04" ? "6" : $guia->tipoDocumentoCliente(),
                            "numDoc" =>  $guia->codTraslado() == "04" ? $guia->ruc_empresa : $guia->documento_cliente,
                            "rznSocial" =>  $guia->codTraslado() == "04" ? $guia->empresa : $guia->cliente,
                            "address" => array(
                                "direccion" =>  $guia->codTraslado() == "04" ? $guia->direccion_empresa : $guia->direccion_cliente,
                            )
                        ),

                        "observacion" => $guia->observacion,

                        "envio" => array(
                            "modTraslado" =>  "01",
                            "codTraslado" =>  $guia->codTraslado(),
                            "desTraslado" =>  $guia->desTraslado(),
                            "fecTraslado" =>  self::obtenerFecha($guia),//FECHA DEL TRANSLADO
                            "codPuerto" => "123",
                            "indTransbordo"=> false,
                            "pesoTotal" => $guia->peso_productos,
                            "undPesoTotal"=> "KGM",
                            "numBultos" => $guia->cantidad_productos,
                            "llegada" => array(
                                "ubigueo" =>  $guia->ubigeo_llegada,
                                "direccion" => self::limitarDireccion($guia->direccion_llegada,50,"..."),
                            ),
                            "partida" => array(
                                "ubigueo" => $guia->ubigeo_partida,
                                "direccion" => self::limitarDireccion($guia->direccion_empresa,50,"..."),
                            ),
                            "transportista"=> self::condicionReparto($guia)
                        ),

                        "details" =>  self::obtenerProductos($guia),
                );

                $data = enviarGuiaapi(json_encode($arreglo_guia));
                //RESPUESTA DE LA SUNAT EN JSON
                $json_sunat = json_decode($data);

                if ($json_sunat->sunatResponse->success == true) {
                    if($json_sunat->sunatResponse->cdrResponse->code == "0") {
                        $guia->sunat = '1';
                        $respuesta_cdr = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                        $respuesta_cdr = json_decode($respuesta_cdr, true);
                        $guia->getCdrResponse = $respuesta_cdr;
                        $data = pdfGuiaapi(json_encode($arreglo_guia));
                        $name = $guia->serie . "-" . $guia->correlativo . '.pdf';
                        $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'guia' . DIRECTORY_SEPARATOR . $name);
                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'guia'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . 'guia'));
                        }

                        //file_put_contents($pathToFile, $data);


                        $guia->nombre_comprobante_archivo = $name;
                        $guia->ruta_comprobante_archivo = 'public/sunat/guia/' . $name;
                        $guia->update();

                        //Registro de actividad
                        $descripcion = "SE AGREGÓ LA GUIA DE REMISION ELECTRONICA: " . $guia->serie . "-" . $guia->correlativo;
                        $gestion = "GUIA DE REMISION ELECTRONICA";
                        crearRegistro($guia, $descripcion, $gestion);

                        return array('success' => true, 'mensaje' => 'Guia de remisión enviada a Sunat con exito.');
                    }
                    else {
                        $guia->sunat = '0';
                        $id_sunat = $json_sunat->sunatResponse->cdrResponse->code;
                        $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;

                        $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                        $respuesta_error = json_decode($respuesta_error, true);
                        $guia->getCdrResponse = $respuesta_error;

                        $guia->update();
                        return array('success' => false, 'mensaje' => $descripcion_sunat);
                    }
                }else{

                    //COMO SUNAT NO LO ADMITE VUELVE A SER 0
                    $guia->sunat = '0';
                    $guia->regularize = '1';

                    if ($json_sunat->sunatResponse->error) {
                        $id_sunat = $json_sunat->sunatResponse->error->code;
                        $descripcion_sunat = $json_sunat->sunatResponse->error->message;
                        $obj_erro = new stdClass;
                        $obj_erro->code = $json_sunat->sunatResponse->error->code;
                        $obj_erro->description = $json_sunat->sunatResponse->error->message;
                        $respuesta_error = json_encode($obj_erro, true);
                        $respuesta_error = json_decode($respuesta_error, true);
                        $guia->getRegularizeResponse = $respuesta_error;
                    }else {
                        $id_sunat = $json_sunat->sunatResponse->cdrResponse->id;
                        $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;
                        $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                        $respuesta_error = json_decode($respuesta_error, true);
                        $guia->getCdrResponse = $respuesta_error;
                    };


                    $errorGuia = new ErrorGuia();
                    $errorGuia->guia_id = $guia->id;
                    $errorGuia->tipo = 'sunat-envio';
                    $errorGuia->descripcion = 'Error al enviar a sunat';
                    $errorGuia->ecxepcion = $descripcion_sunat;
                    $errorGuia->save();

                    $guia->update();
                    return array('success' => false, 'mensaje' => $descripcion_sunat);
                }
            }else{
                $guia->sunat = '1';
                $guia->update();
                return array('success' => false, 'mensaje' => 'Guia de remision ya fue enviado a Sunat.');
            }
        }
        catch(Exception $e)
        {
            $guia = Guia::find($id);

            $errorGuia = new ErrorGuia();
            $errorGuia->guia_id = $guia->id;
            $errorGuia->tipo = 'sunat-envio';
            $errorGuia->descripcion = 'Error al enviar a sunat';
            $errorGuia->ecxepcion = $e->getMessage();
            $errorGuia->save();
            return array('success' => false, 'mensaje' => $e->getMessage());
        }
    }

    public function guia_pdf($id)
    {
        
        try
        {
            $guia = Guia::find($id);
            $empresa = Empresa::first();
            PDF::loadview('ventas.guias.reportes.guia',[
                'guia' => $guia,
                'empresa' => $empresa,
                ])->setPaper('a4')->setWarnings(false)
                ->save(public_path().'/storage/sunat/guia/'.$guia->nombre_comprobante_archivo);
            return array('success' => true,'mensaje' => 'Guia de remision validado.');
        }
        catch(Exception $e)
        {
            $guia = Guia::find($id);

            $errorGuia = new ErrorGuia();
            $errorGuia->guia_id = $guia->id;
            $errorGuia->tipo = 'pdf';
            $errorGuia->descripcion = 'Error al generar pdf';
            $errorGuia->ecxepcion = $e->getMessage();
            $errorGuia->save();
            return array('success' => false,'mensaje' => 'Guia de remision no validado.');
        }
    }

    public function destroy(Request $request)
    {
        
        try {
            $id =   $request->get('guia_id');
            $guia = Guia::findOrFail($id);
            if ($guia->documento) {
                Session::flash('error_guia_remision', 'No puedes eliminar esta guia, ha sido creada a de un documento de venta.');
                return redirect()->route('ventas.guiasremision.index');
            } else if ($guia->sunat == '1') {
                Session::flash('error_guia_remision', 'No puedes eliminar esta guia, ya ha sido enviada a sunat.');
                return redirect()->route('ventas.guiasremision.index');
            } else {

                //======== DEVOLVIENDO CANTIDADES ========
                $nota = NotaSalidad::find($guia->nota_salida_id);

                $nota_detalle = DetalleNotaSalidad::where('nota_salidad_id', $nota->id)->get();
                   
                    
                if ($nota_detalle) {
                    foreach ($nota_detalle as $detalle) {
                        DB::table('producto_color_tallas')
                            ->where('producto_id', $detalle->producto_id)
                            ->where('color_id', $detalle->color_id)
                            ->where('talla_id', $detalle->talla_id)
                            ->update([        
                                'stock'         => DB::raw('stock + ' . $detalle->cantidad),
                                'stock_logico'  => DB::raw('stock_logico + ' . $detalle->cantidad),
                                'updated_at'    => Carbon::now()
                        ]);
                    }
                }
            

                $guia->estado = "NULO";
                $guia->update();

                Session::flash('guia_exito', 'Guia eliminada correctamente, stocks devueltos.');

                return redirect()->route('ventas.guiasremision.index');
            }
        } catch (Exception $e) {
            Session::flash('error_guia_remision', $e->getMessage());
            return redirect()->route('ventas.guiasremision.index');
        }
    }


    public function getXml($guia_id){
        $guia        =   Guia::find($guia_id);
        $nombreArchivo  =   basename($guia->ruta_xml);
        

        $headers = [
            'Content-Type' => 'text/xml',
        ];
    
        return Response::download($guia->ruta_xml, $nombreArchivo, $headers);
    }

    public function getCdr($guia_id){
        $guia           =   Guia::find($guia_id);
        $nombreArchivo  =   basename($guia->ruta_cdr);

        $headers = [
            'Content-Type' => 'text/xml',
        ];
    
        return Response::download($guia->ruta_cdr, $nombreArchivo, $headers);
    }

    public function getProductos(Request $request){

        try {
        
            $search         = $request->query('search'); // Palabra clave para la búsqueda
            $almacenId      = $request->query('almacen_id'); // ID del almacén
            $page           = $request->query('page', 1);  

            if(!$almacenId){
                throw new Exception("FALTA SELECCIONAR UN ALMACÉN!!!");
            }
        
            $productos  =   DB::table('productos as p')
                            ->join('categorias as c','c.id','p.categoria_id')
                            ->join('marcas as ma','ma.id','p.marca_id')
                            ->join('modelos as mo','mo.id','p.modelo_id')
                            ->leftJoin('producto_color_tallas as pct','p.id','pct.producto_id')
                            ->select(
                            DB::raw("CONCAT(c.descripcion, ' - ', ma.marca, ' - ', mo.descripcion, ' - ', p.nombre) as producto_completo"),
                            'c.descripcion as categoria_nombre',
                            'ma.marca as marca_nombre',
                            'mo.descripcion as modelo_nombre',
                            'p.id as producto_id',
                            'p.nombre as producto_nombre',
                            'pct.almacen_id'
                            )
                            ->where(DB::raw("CONCAT(c.descripcion, ' - ', ma.marca, ' - ', mo.descripcion, ' - ', p.nombre)"), 'LIKE', "%$search%") 
                            ->where('pct.almacen_id',$almacenId)
                            ->where('p.estado','ACTIVO')
                            ->where('pct.stock','>', 0)
                            ->groupBy('pct.almacen_id','p.id', 'c.descripcion', 'ma.marca', 'mo.descripcion', 'p.nombre','pct.stock')
                            ->paginate(10, ['*'], 'page', $page); 

            return response()->json([
                'success' => true,
                'message' => 'PRODUCTOS OBTENIDOS',
                'productos' => $productos->items(),
                'more' => $productos->hasMorePages() 
            ]);

        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=> $th->getMessage()]);
        }
    }


    public function getColoresTallas($almacen_id,$producto_id){
        
        try {

            $precios_venta  =   DB::select('SELECT 
                                p.id AS producto_id,
                                p.nombre AS producto_nombre,
                                p.precio_venta_1,
                                p.precio_venta_2,
                                p.precio_venta_3
                                FROM 
                                    productos AS p 
                                WHERE 
                                    p.id = ? AND p.estado = "ACTIVO" ',
                                [$producto_id]);  

           
            $colores    =   DB::select('SELECT 
                                p.id AS producto_id,
                                p.nombre AS producto_nombre,
                                c.id AS color_id,
                                c.descripcion AS color_nombre,
                                p.codigo as producto_codigo
                            FROM 
                                producto_colores AS pc 
                                inner join productos as p on p.id = pc.producto_id
                                inner join colores as c on c.id = pc.color_id
                            WHERE 
                                pc.almacen_id = ?
                                AND pc.producto_id = ? 
                                AND p.estado = "ACTIVO" 
                                AND c.estado = "ACTIVO" ',
                            [$almacen_id,$producto_id]);

            $stocks =   DB::select('select  
                        pct.producto_id,
                        pct.color_id,
                        pct.talla_id,
                        pct.stock,
                        pct.stock_logico, 
                        t.descripcion as talla_nombre
                        from producto_color_tallas as pct
                        inner join productos as p on p.id = pct.producto_id
                        inner join colores as c on c.id = pct.color_id 
                        inner join tallas as t on t.id = pct.talla_id
                        where 
                        p.estado = "ACTIVO" 
                        AND c.estado = "ACTIVO" 
                        AND t.estado = "ACTIVO"
                        AND pct.almacen_id = ?
                        AND p.id = ?
                        AND pct.stock > 0',
                        [$almacen_id,$producto_id]);

            $tallas =   Talla::where('estado','ACTIVO')->orderBy('id')->get();   

            $producto_color_tallas  =   null;
            if(count($colores) > 0){
                $producto_color_tallas  =   $this->formatearColoresTallas($colores,$stocks,$precios_venta,$tallas);
            }
         
            return response()->json(['success' => true,'producto_color_tallas'=>$producto_color_tallas]);
        } catch (\Throwable $th) {
    
            return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }

    public function formatearColoresTallas($colores, $stocks, $precios_venta, $tallas)
    {
        
        $producto = [];

        // Verifica si $colores no está vacío
        if (count($colores) > 0) {
            $producto['id']     = $colores[0]->producto_id;
            $producto['nombre'] = $colores[0]->producto_nombre;
            $producto['codigo'] = $colores[0]->producto_codigo;
        } else {
            // Maneja el caso cuando $colores está vacío
            $producto['id']     = null;
            $producto['nombre'] = null;
            $producto['codigo'] = null;

        }

        // Verifica si $precios_venta no está vacío
        if (count($precios_venta) > 0) {
            $producto['precio_venta_1'] = $precios_venta[0]->precio_venta_1;
            $producto['precio_venta_2'] = $precios_venta[0]->precio_venta_2;
            $producto['precio_venta_3'] = $precios_venta[0]->precio_venta_3;
        } else {
            // Maneja el caso cuando $precios_venta está vacío
            $producto['precio_venta_1'] = null;
            $producto['precio_venta_2'] = null;
            $producto['precio_venta_3'] = null;
        }

        $lstColores = [];

        //======== RECORRIENDO COLORES =======
        foreach ($colores as $color) {
            $item_color = [];
            $item_color['id']       =   $color->color_id;
            $item_color['nombre']   =   $color->color_nombre;

            //======== OBTENIENDO TALLAS DEL COLOR =======
            $lstTallas = [];

            foreach ($tallas as $talla) {
                $item_talla = [];
                $item_talla['id'] = $talla->id;
                $item_talla['nombre'] = $talla->descripcion;

                // Filtrar stocks para color y talla actuales
                $stock_filtrado = array_filter($stocks, function ($stock) use ($producto, $color, $talla) {
                    return $stock->producto_id == $producto['id'] &&
                        $stock->color_id == $color->color_id &&
                        $stock->talla_id == $talla->id;
                });

                // Asignar stock y stock lógico si existe, o establecer en 0
                if (!empty($stock_filtrado)) {
                    $first_stock = reset($stock_filtrado); // Obtiene el primer elemento del array filtrado
                    $item_talla['stock'] = $first_stock->stock;
                    $item_talla['stock_logico'] = $first_stock->stock_logico;
                } else {
                    $item_talla['stock'] = 0;
                    $item_talla['stock_logico'] = 0;
                }

                $lstTallas[] = $item_talla;
            }

            $item_color['tallas'] = $lstTallas;
            $lstColores[] = $item_color;
        }

        $producto['colores'] = $lstColores;

        return $producto;
    }

}
