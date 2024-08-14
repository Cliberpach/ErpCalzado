<?php

namespace App\Http\Controllers\Ventas;

use stdClass;
use Exception;
use Carbon\Carbon;
use App\Ventas\Cliente;
use App\Ventas\Retencion;
use App\Ventas\Cotizacion;
use App\Ventas\ErrorVenta;
use App\Ventas\EnvioVenta;
use App\Almacenes\Producto;
use App\Pos\MovimientoCaja;
use Illuminate\Http\Request;
use App\Almacenes\LoteDetalle;
use App\Almacenes\Kardex;
use App\Almacenes\LoteProducto;
use App\Events\VentasCajaEvent;
use App\Events\NotifySunatEvent;
use App\Mantenimiento\Condicion;
use App\Ventas\RetencionDetalle;
use App\Ventas\CotizacionDetalle;
use App\Ventas\Documento\Detalle;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Events\DocumentoNumeracion;
use App\Ventas\Documento\Documento;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Empresa\Banco;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Events\ComprobanteRegistrado;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Pos\DetalleMovimientoVentaCaja;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;
use Yajra\DataTables\Facades\DataTables;
use App\Mantenimiento\Empresa\Numeracion;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
//CONVERTIR DE NUMEROS A LETRAS
use App\Ventas\Documento\Pago\Transferencia;
use App\Notifications\FacturacionNotification;
use App\Mantenimiento\Tabla\Detalle as TablaDetalle;


use App\Almacenes\ProductoColorTalla;
use App\Almacenes\Talla;
use App\Almacenes\Modelo;
use App\Almacenes\Color;
use Illuminate\Support\Facades\Cache;
use App\Classes\ValidatedDetail;

use App\Almacenes\DetalleNotaIngreso;
use App\Almacenes\NotaIngreso;

use App\Almacenes\DetalleNotaSalidad;
use App\Almacenes\NotaSalidad;

use Illuminate\Support\Facades\Response; 
use App\Ventas\CambioTalla;

class DocumentoController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'documento_venta.index');
        $dato = "Message";
        broadcast(new NotifySunatEvent($dato));
       
        //dd(Cache::get('ultimo'));
        return view('ventas.documentos.index');
    }
    public function indexAntiguo()
    {
        $this->authorize('haveaccess', 'documento_venta.index');
        $dato = "Message";
        broadcast(new NotifySunatEvent($dato));
        return view('ventas.documentos.index-antiguo');
    }
    
    public function getDocument(Request $request)
    {
       
        $colleccion = collect([]);
        $documentos = Documento::query()
            ->select([
                'cotizacion_documento.id',
                'cotizacion_documento.tipo_venta',
                DB::raw('(CONCAT(cotizacion_documento.serie, "-" ,cotizacion_documento.correlativo)) as numero_doc'),
                'cotizacion_documento.serie',
                'cotizacion_documento.correlativo',
                'cotizacion_documento.cliente',
                'cotizacion_documento.empresa',
                'cotizacion_documento.importe',
                'cotizacion_documento.efectivo',
                'cotizacion_documento.tipo_pago_id',
                'cotizacion_documento.ruta_pago',
                'cotizacion_documento.cliente_id',
                'cotizacion_documento.convertir',
                'cotizacion_documento.empresa_id',
                'cotizacion_documento.cotizacion_venta',
                'cotizacion_documento.fecha_documento',
                'cotizacion_documento.estado_pago',
                'cotizacion_documento.condicion_id',
                'cotizacion_documento.sunat',
                'cotizacion_documento.regularize',
                'cotizacion_documento.contingencia',
                'cotizacion_documento.sunat_contingencia',
                'cotizacion_documento.documento_cliente',
                'cotizacion_documento.estado',
                'cotizacion_documento.cambio_talla',
                DB::raw('json_unquote(json_extract(cotizacion_documento.getRegularizeResponse, "$.code")) as code'),
                'cotizacion_documento.total',
                'cotizacion_documento.total_pagar',
                DB::raw('DATEDIFF( now(),cotizacion_documento.fecha_documento) as dias'),
                DB::raw('(select count(nota_electronica.id) from nota_electronica where nota_electronica.documento_id = cotizacion_documento.id) as notas'),
                'envios_ventas.estado AS estado_despacho'
            ])    
            ->leftJoin('envios_ventas', 'cotizacion_documento.id', '=', 'envios_ventas.documento_id') 
            ->with([
                "condicion" => function ($query) {
                    return $query->select();
                },
                "tablaDetalles" => function ($query) {
                    return $query->select();
                },
                "clienteEntidad"=>function($query){
                    return $query->select(['id','correo_electronico','telefono_movil','telefono_fijo',]);
                }
            ])->where('cotizacion_documento.estado', '<>', "ANULADO");
        if (!PuntoVenta() && !FullAccess()) {
            $documentos = $documentos->where('user_id', Auth::user()->id);
        }

        if ($request->has('fechaInicial')) {
            $documentos = $documentos->where('fecha_documento', '>=', "{$request->get('fechaInicial')}");
        }

        if ($request->has("cliente")) {
            $cliente = $request->get("cliente");

            if (is_numeric($cliente)) {
                $documentos = $documentos->where('documento_cliente', 'LIKE', "%{$request->get('cliente')}%");
            } else {
                $documentos = $documentos->where('cliente', 'LIKE', "%{$request->get('cliente')}%");
            }
        }

        if ($request->has("numero_doc")) {
            $numero_doc = $request->get("numero_doc");
            if ($request->has("numero_doc")) {
                $numero_doc = $request->get("numero_doc");
                $documentos = $documentos->where(DB::raw('(CONCAT(serie, "-" ,correlativo))'), 'LIKE', "%{$numero_doc}%");
            }            
        }

        $documentos = $documentos->orderBy('id', 'desc')->paginate($request->tamanio);

        
        foreach ($documentos as $key => $value) {
            $colleccion->push([
                'id'                =>  $value->id,
                "tipo_venta"        =>  $value->tablaDetalles->nombre,
                'numero_doc'        =>  $value->numero_doc,
                'serie'             =>  $value->serie,
                'correlativo'       =>  $value->correlativo,
                'cliente'           =>  $value->cliente,
                'empresa'           =>  $value->empresa,
                'importe'           =>  $value->importe,
                'efectivo'          =>  $value->efectivo,
                'tipo_pago_id'      =>  $value->tipo_pago_id,
                'ruta_pago'         =>  $value->ruta_pago,
                'cliente_id'        =>  $value->cliente_id,
                'convertir'         =>  $value->convertir,
                'doc_convertido'    =>  $value->doc_convertido(),
                'empresa_id'        =>  $value->empresa_id,
                'cotizacion_venta'  =>  $value->cotizacion_venta,
                'fecha_documento'   =>  $value->fecha_documento,
                'estado_pago'       =>  $value->estado_pago,
                'condicion_id'      =>  $value->condicion_id,
                'sunat'             =>  $value->sunat,
                'regularize'        =>  $value->regularize,
                'contingencia'      =>  $value->contingencia,
                'sunat_contingencia'=>  $value->sunat_contingencia,
                'documento_cliente' =>  $value->documento_cliente,
                'code'              =>  $value->code,
                'total'             =>  $value->total,
                'total_pagar'       =>  $value->total_pagar,
                'dias'              =>  $value->dias,
                'notas'             =>  $value->notas,
                "condicion"         =>  $value->condicion->descripcion,
                "tipo_venta_id"     =>  $value->tipo_venta,
                "correo"            =>  $value->clienteEntidad->correo_electronico,
                "telefonoMovil"     =>  $value->clienteEntidad->telefono_movil,
                "estado"            =>  $value->estado,
                "estado_despacho"   =>  $value->estado_despacho,
                "cambio_talla"      =>  $value->cambio_talla
            ]);
        }

        return response()->json([
            'pagination' => [
                'currentPage' => $documentos->currentPage(),
                'from' => $documentos->firstItem(),
                'lastPage' => $documentos->lastPage(),
                'perPage' => $documentos->perPage(),
                'to' => $documentos->lastPage(),
                'total' => $documentos->total(),
            ],
            "documentos" => $colleccion,
            "modos_pago"=>modos_pago()
        ]);
       
    }
    public function getDocumentAntiguo(Request $request){
        
        $documentos = DB::table('cotizacion_documento as cd')
            ->join('tabladetalles as td', 'td.id', '=', 'cd.tipo_venta')
            ->join('condicions as c', 'c.id', '=', 'cd.condicion_id')
            ->select(
                [
                    'cd.id',
                    'td.nombre as tipo_venta',
                    'cd.tipo_venta as tipo_venta_id',
                    DB::raw('(CONCAT(cd.serie, "-" ,cd.correlativo)) as numero_doc'),
                    'cd.serie',
                    'cd.correlativo',
                    'cd.cliente',
                    'cd.empresa',
                    'cd.importe',
                    'cd.efectivo',
                    'cd.tipo_pago_id',
                    'cd.ruta_pago',
                    'cd.cliente_id',
                    'cd.convertir',
                    'cd.empresa_id',
                    'cd.cotizacion_venta',
                    'cd.fecha_documento',
                    'cd.estado_pago',
                    'c.descripcion as condicion',
                    'cd.condicion_id',
                    'cd.sunat',
                    'cd.regularize',
                    'cd.contingencia',
                    'cd.sunat_contingencia',
                    'cd.documento_cliente',
                    DB::raw('json_unquote(json_extract(cd.getRegularizeResponse, "$.code")) as code'),
                    'cd.total',
                    DB::raw('DATEDIFF( now(),cd.fecha_documento) as dias'),
                    DB::raw('(select count(id) from nota_electronica where documento_id = cd.id) as notas')
                ]
            );
            
        return Datatables::of($documentos)
            ->filter(function ($query) use ($request) {

                if (!PuntoVenta() && !FullAccess()) {
                    $documentos = $documentos->where('user_id', Auth::user()->id);
                }

                $query->orderBy('id', 'desc');

                $query->where('cd.estado', '<>', "ANULADO");
            
                
                if ($request->has('fechaInicial')) {
                    $query->where('cd.fecha_documento', '>=', "{$request->get('fechaInicial')}");
                }

                if($request->has("cliente")){
                    $cliente = $request->get("cliente");

                    if(is_numeric($cliente)){
                        $query->where('cd.documento_cliente', 'LIKE', "%{$request->get('cliente')}%");
                    }else{
                        $query->where('cd.cliente', 'LIKE', "%{$request->get('cliente')}%");
                    }
                }
            })->make(true);
    
    }
    public function getDocumentClient(Request $request)
    {
        $documentos = Documento::where('estado', '!=', 'ANULADO')->where('cliente_id', $request->cliente_id)->where('estado_pago', 'PENDIENTE')->where('condicion_id', $request->condicion_id)->where('sunat', '!=', '2')->orderBy('id', 'desc')->get();
        $coleccion = collect([]);

        $hoy = Carbon::now();
        foreach ($documentos as $documento) {

            $transferencia = 0.00;
            $otros = 0.00;
            $efectivo = 0.00;

            if ($documento->tipo_pago_id) {
                if ($documento->tipo_pago_id == 1) {
                    $efectivo = $documento->importe;
                } else if ($documento->tipo_pago_id == 2) {
                    $transferencia = $documento->importe;
                    $efectivo = $documento->efectivo;
                } else {
                    $otros = $documento->importe;
                    $efectivo = $documento->efectivo;
                }
            }

            $fecha_v = $documento->created_at;
            $diff = $fecha_v->diffInDays($hoy);

            $cantidad_notas = count($documento->notas);

            $coleccion->push([
                'id' => $documento->id,
                'tipo_venta' => $documento->nombreTipo(),
                'tipo_venta_id' => $documento->tipo_venta,
                'empresa' => $documento->empresaEntidad->razon_social,
                'tipo_pago' => $documento->tipo_pago_id,
                'numero_doc' => $documento->serie . '-' . $documento->correlativo,
                'serie' => $documento->serie,
                'correlativo' => $documento->correlativo,
                'cliente' => $documento->tipo_documento_cliente . ': ' . $documento->documento_cliente . ' - ' . $documento->cliente,
                'empresa' => $documento->empresa,
                'empresa_id' => $documento->empresa_id,
                'convertir' => $documento->convertir,
                'cotizacion_venta' => $documento->cotizacion_venta,
                'fecha_documento' => Carbon::parse($documento->fecha_documento)->format('d/m/Y'),
                'estado' => $documento->estado_pago,
                'condicion' => $documento->condicion->descripcion,
                'sunat' => $documento->sunat,
                'otros' => 'S/. ' . number_format($otros, 2, '.', ''),
                'efectivo' => 'S/. ' . number_format($efectivo, 2, '.', ''),
                'transferencia' => 'S/. ' . number_format($transferencia, 2, '.', ''),
                'total' => number_format($documento->total_pagar, 2, '.', ''),
                'dias' => (int) (4 - $diff < 0 ? 0 : 4 - $diff),
                'notas' => $cantidad_notas,
            ]);
        }

        return response()->json([
            'success' => true,
            'ventas' => $coleccion,
        ]);
    }

    public function storePago(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();

            $rules = [
                'tipo_pago_id'  => 'required',
                'efectivo'      => 'required',
                'importe'       => 'required',

            ];

            $message = [
                'tipo_pago_id.required' => 'El campo modo de pago es obligatorio.',
                'importe.required'      => 'El campo importe es obligatorio.',
                'efectivo.required'     => 'El campo efectivo es obligatorio.',
            ];

            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                $clase = $validator->getMessageBag()->toArray();
                $cadena = "";
                foreach ($clase as $clave => $valor) {
                    $cadena = $cadena . "$valor[0] ";
                }

                Session::flash('error', $cadena);
                DB::rollBack();
                return redirect()->route('ventas.documento.index');
            }

            
            $documento = Documento::find($request->venta_id);

            $documento->tipo_pago_id        = $request->get('tipo_pago_id');
            $documento->importe             = $request->get('importe');
            $documento->efectivo            = $request->get('efectivo');
            $documento->estado_pago         = 'PAGADA';
            $documento->banco_empresa_id    = $request->get('cuenta_id');

            if ($request->hasFile('imagen')) {
                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'))) {
                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'));
                }
                $extension              =   $request->file('imagen')->getClientOriginalExtension();
                $nombreImagenPago       =   $documento->serie.'-'.$documento->correlativo.'.'.$extension;
                $documento->ruta_pago   =   $request->file('imagen')->storeAs('public/pagos',$nombreImagenPago);
            }

            if ($request->hasFile('imagen2')) {
                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'))) {
                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'));
                }
                $extension              =   $request->file('imagen2')->getClientOriginalExtension();
                $nombreImagenPago       =   $documento->serie.'-'.$documento->correlativo.'-2'.'.'.$extension;
                $documento->ruta_pago_2   =   $request->file('imagen2')->storeAs('public/pagos',$nombreImagenPago);
            }

            $documento->update();

            if ($documento->convertir) {
                $doc_convertido                     = Documento::find($documento->convertir);
                $doc_convertido->estado_pago        = $documento->estado_pago;
                $doc_convertido->importe            = $documento->importe;
                $doc_convertido->efectivo           = $documento->efectivo;
                $doc_convertido->tipo_pago_id       = $documento->tipo_pago_id;
                $doc_convertido->banco_empresa_id   = $documento->banco_empresa_id;
                $doc_convertido->ruta_pago          = $documento->ruta_pago;
                $doc_convertido->update();
            }


            //========== CANJEANDO RECIBOS DE CAJA ========
            if($request->get('modo_pago') === "4-RECIBO DE CAJA"){
                //======== OBTENEMOS TODOS LOS RECIBOS DE CAJA DEL CLIENTE ==========
                $recibos_caja_cliente   =   DB::select('select * from recibos_caja as rc 
                                            where rc.cliente_id=? and rc.saldo>0
                                            and rc.estado="ACTIVO" 
                                            and (rc.estado_servicio="LIBRE" or rc.estado_servicio="USANDO")
                                            order by rc.created_at',
                                            [$documento->cliente_id]);

                $total_pendiente    =   $documento->total_pagar;  

                //========= RESTAMOS SALDO EN ORDEN ASC POR FECHA DE CREACIÓN =========
                foreach ($recibos_caja_cliente as $recibo) {

                    $saldo_recibo       =   $recibo->saldo;

                    //======= SI EL TOTAL PENDIENTE >= SALDO DEL RECIBO CAJA ========
                    if($total_pendiente >= $saldo_recibo){
                        //======= GUARDAMOS SALDO ANTERIOR DEL RECIBO =======
                        $saldo_anterior_recibo          =       $recibo->saldo;
                        //======= CONSUMIR TODO EL SALDO DEL RECIBO ========
                        $nuevo_saldo_recibo             =       0;
                        //======= NUEVO ESTADO DEL RECIBO ========
                        $nuevo_estado_servicio_recibo   =   'CANJEADO';
                        //========= TOTAL PENDIENTE BAJA SEGÚN EL SALDO DEL RECIBO =========
                        $total_pendiente                -=      $saldo_recibo;

                        //======= ACTUALIZAMOS EL RECIBO ========
                        DB::table('recibos_caja')
                        ->where('id', $recibo->id)
                        ->update(['saldo' => $nuevo_saldo_recibo ,
                        'estado_servicio' => $nuevo_estado_servicio_recibo,
                        'updated_at' => now()
                        ]);

                        //========= GRABAMOS EL DETALLE DE USO DEL RECIBO ======
                        DB::table('recibos_caja_detalle')
                        ->insert([
                            'recibo_id'    => $recibo->id,
                            'documento_id' => $documento->id,
                            'saldo_antes'  => $saldo_anterior_recibo,
                            'monto_usado'  => $saldo_recibo,
                            'saldo_despues'=> $nuevo_saldo_recibo,
                            'created_at'   => now(),
                            'updated_at'   => now()
                        ]);
                        
                    }else{
                    //======= SI EL TOTAL PENDIENTE ES MENOR AL SALDO DEL RECIBO ========
                        //======== SALDO ANTERIOR RECIBO =========
                        $saldo_anterior_recibo          =   $recibo->saldo;
                        //======== MONTO USADO ===========
                        $monto_usado                    =   $total_pendiente;
                        //======== CONSUMIR UNA PARTE DEL SALDO DEL RECIBO =======
                        $nuevo_saldo_recibo             =   $recibo->saldo  -   $total_pendiente;
                        //======== TOTAL PENDIENTE BAJA A 0 =======
                        $total_pendiente                =   0;
                        //======== ACTUALIZAR ESTADO DEL RECIBO =========
                        $nuevo_estado_servicio_recibo   =   'USANDO';

                        //======= ACTUALIZAMOS EL RECIBO ========
                        DB::table('recibos_caja')
                        ->where('id', $recibo->id)
                        ->update(['saldo' => $nuevo_saldo_recibo ,
                        'estado_servicio' => $nuevo_estado_servicio_recibo,
                        'updated_at' => now()
                        ]);

                        //========= GRABAMOS EL DETALLE DE USO DEL RECIBO ======
                        DB::table('recibos_caja_detalle')
                        ->insert(['recibo_id'   =>  $recibo->id ,
                          'documento_id'          =>  $documento->id,
                          'saldo_antes'           =>  $saldo_anterior_recibo,
                          'monto_usado'           =>  $monto_usado,
                          'saldo_despues'         =>  $nuevo_saldo_recibo, 
                          'created_at'            =>  now(),
                          'updated_at'            =>  now()
                        ]);
                    }
                    
                    //======== DETENER EL BUCLE SI EL TOTAL PENDIENTE ES 0 ========
                    if($total_pendiente === 0){
                        break;
                    }
                }   
            }


            DB::commit();
            Session::flash('success', 'Documento pagado con exito.');
            return redirect()->route('ventas.documento.index');
        } catch (Exception $e) {
            DB::rollBack();
            dd( $e->getMessage());
            Session::flash('error', $e->getMessage());
            return redirect()->route('ventas.documento.index');
        }
    }

    public function updatePago(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();

            $rules = [
                'tipo_pago_id' => 'required',
                'efectivo' => 'required',
                'importe' => 'required',

            ];

            $message = [
                'tipo_pago_id.required' => 'El campo modo de pago es obligatorio.',
                'importe.required' => 'El campo importe es obligatorio.',
                'efectivo.required' => 'El campo efectivo es obligatorio.',
            ];

            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                $clase = $validator->getMessageBag()->toArray();
                $cadena = "";
                foreach ($clase as $clave => $valor) {
                    $cadena = $cadena . "$valor[0] ";
                }

                Session::flash('error', $cadena);
                DB::rollBack();
                return redirect()->route('ventas.documento.index');
            }

            $documento = Documento::find($request->venta_id);

            $documento->tipo_pago_id = $request->get('tipo_pago_id');
            $documento->importe = $request->get('importe');
            $documento->efectivo = $request->get('efectivo');
            $documento->estado_pago = 'PAGADA';
            $documento->banco_empresa_id = $request->get('cuenta_id');
            $ruta_pago = $documento->ruta_pago;
            if ($request->hasFile('imagen')) {
                //Eliminar Archivo anterior
                if ($ruta_pago) {
                    self::deleteImage($ruta_pago);
                }
                //Agregar nuevo archivo
                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'))) {
                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'));
                }
                $documento->ruta_pago = $request->file('imagen')->store('public/pagos');
            } else {
                if ($request->get('ruta_pago') == null || $request->get('ruta_pago') == "") {
                    $documento->ruta_pago = "";
                    if ($ruta_pago) {
                        self::deleteImage($ruta_pago);
                    }
                }
            }
            $documento->update();

            if ($documento->convertir) {
                $doc_convertido = Documento::find($documento->convertir);
                $doc_convertido->estado_pago = $documento->estado_pago;
                $doc_convertido->importe = $documento->importe;
                $doc_convertido->efectivo = $documento->efectivo;
                $doc_convertido->tipo_pago_id = $documento->tipo_pago_id;
                $doc_convertido->banco_empresa_id = $documento->banco_empresa_id;
                $doc_convertido->ruta_pago = $documento->ruta_pago;
                $doc_convertido->update();
            }

            DB::commit();
            Session::flash('success', 'Pago editado con exito.');
            return redirect()->route('ventas.documento.index');
        } catch (Exception $e) {
            DB::rollBack();
            Session::flash('error', $e->getMessage());
            return redirect()->route('ventas.documento.index');
        }
    }

    public function deleteImage($ruta_pago)
    {
        try {
            $sRutaImagenActual = str_replace('/storage', 'public', $ruta_pago);
            $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
            Storage::disk('public')->delete($sNombreImagenActual);
            return array('success' => true, 'mensaje' => 'Imagen eliminada');
        } catch (Exception $e) {
            return array('success' => false, 'mensaje' => $e->getMessage());
        }
    }

    public function getCuentas(Request $request)
    {
        $cuentas = Banco::where('empresa_id', $request->empresa_id)->where('estado', 'ACTIVO')->get();
        return response()->json([
            'success' => true,
            'cuentas' => $cuentas,
        ]);
    }

    public function create(Request $request)
    {
        
        $this->authorize('haveaccess', 'documento_venta.index');
        $empresas = Empresa::where('estado', 'ACTIVO')->get();
        $clientes = Cliente::where('estado', 'ACTIVO')->get();
        $fecha_hoy = Carbon::now()->toDateString();
        $productos = Producto::where('estado', 'ACTIVO')->get();
        $condiciones = Condicion::where('estado', 'ACTIVO')->get();

        // $dolar_aux = json_encode(precio_dolar(), true);
        // $dolar_aux = json_decode($dolar_aux, true);

        // $dolar = (float)$dolar_aux['original']['venta'];
        $dolar = 0;

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
        $cotizacion = '';
        $detalles = '';
        $vista = 'create';
        if (CEC() == 'NO') {
            $vista = 'create_new';
        }
       

        if ($request->get('cotizacion')) {
            //COLECCION DE ERRORES
            $errores = collect();
            $devolucion = false;
            $cotizacion = Cotizacion::findOrFail($request->get('cotizacion'));
            $detalles   = CotizacionDetalle::where('cotizacion_id', $request->get('cotizacion'))
                        ->with('producto', 'color', 'talla')->get();
            
            //================ VALIDANDO STOCKS_LOGICOS Y CANTIDADES SOLICITADAS =====================
            $validaciones = self::validacionStockCantidad($detalles);
            
            //========= OBTENER LOS DETALLES CON STOCK INSUFICIENTE ============
            $detallesWithStockInsuficiente = array_filter($validaciones, function($validacion) {
                return $validacion->getTipo() == 'STOCK LOGICO INSUFICIENTE';
            });

            //========= OBTENER LOS DETALLES CON STOCK LÓGICO VÁLIDO O SUFICIENTE ============
            $detallesWithStockValido = array_filter($validaciones, function($validacion) {
                return $validacion->getTipo() == 'STOCK LOGICO VÁLIDO';
            });

             //========= OBTENER LOS DETALLES QUE NO EXISTEN EN PRODUCTO COLOR TALLAS ============
             $detallesNotExists = array_filter($validaciones, function($validacion) {
                return $validacion->getTipo() == 'NO EXISTE EL PRODUCTO COLOR TALLA';
            });

            $cantidadErrores =  count($detallesWithStockInsuficiente)+ count($detallesNotExists);

            //============= SEPARAR STOCK_LOGICO ===========
            if($cantidadErrores==0){
               foreach ($validaciones as $itemValidado) {
                DB::table('producto_color_tallas')
                ->where('producto_id', $itemValidado->getProductoId())
                ->where('color_id', $itemValidado->getColorId())
                ->where('talla_id', $itemValidado->getTallaId())
                ->update([
                    'stock_logico' => DB::raw('stock_logico - ' . $itemValidado->getCantidadSolicitada())
                ]); 
               }
            }

            $detalleValidado = [];
            //======= CONVIRTIENDO A UN FORMATO QUE JSON PUEDA COMPRENDER =======
            foreach ($validaciones as $itemValidado) {
                $detalleArray = [
                    'producto_id' => $itemValidado->getProductoId(),
                    'color_id' => $itemValidado->getColorId(),
                    'talla_id' => $itemValidado->getTallaId(),
                    'producto_nombre' => $itemValidado->getProductoNombre(),
                    'color_nombre' => $itemValidado->getColorNombre(),
                    'talla_nombre' => $itemValidado->getTallaNombre(),
                    'stock_logico' => $itemValidado->getStockLogico(),
                    'cantidad_solicitada' => $itemValidado->getCantidadSolicitada(),
                    'precio_unitario' => $itemValidado->getPrecioUnitario(),
                    'porcentaje_descuento' =>$itemValidado->getPorcentajeDescuento(),
                    'precio_unitario_nuevo' =>$itemValidado->getPrecioUnitarioNuevo(),
                    'tipo' => $itemValidado->getTipo(),
                ];

                $detalleValidado[] = $detalleArray;
            }

            $tallas = Talla::all();
           
            return view('ventas.documentos.create-venta-cotizacion', [
                'cotizacion'    =>  $cotizacion,
                'empresas'      =>  $empresas,
                'clientes'      =>  $clientes,
                'productos'     =>  $productos,
                'condiciones'   =>  $condiciones,
                // 'lotes' =>  $nuevoDetalle,
                'errores'       =>  $errores,
                'fecha_hoy'     =>  $fecha_hoy,
                'fullaccess'    =>  $fullaccess,
                'dolar'         =>  $dolar,
                'detalle'       =>  $detalleValidado,
                'tallas'        =>  $tallas,
                'cantidadErrores'   =>  $cantidadErrores,
                'departamentos'     =>  departamentos()
            ]);
           

            // $cantidad_valido    =   $validaciones->where('tipo', 'VALIDO')->count();
            // $cantidad_total     =   $validaciones->count();

            // $lotes = self::cotizacionLote($detalles);
            
            // $nuevoDetalle = collect();
            // $detalleValidado = [];
           
            
            //================= EN CASO TODOS LOS PRODUCTOS SEAN NO VALIDOS(stock_logico<cantidadSolicitada) ===============
            // if (count($lotes) === 0) {
            // // if ($cantidad_no_valido === $cantidad_total) {
            //     $coll = new Collection();
            //     $coll->producto = '. No hay stock para ninguno de los productos';
            //     $coll->cantidad = '.';
            //     $errores->push($coll);

            //     return view('ventas.documentos.create-venta-cotizacion',[
            //         'cotizacion' => $cotizacion,
            //         'empresas' => $empresas,
            //         'clientes' => $clientes,
            //         'productos' => $productos,
            //         'condiciones' => $condiciones,
            //         //'lotes' => $nuevoDetalle,
            //         'errores' => $errores,
            //         'fecha_hoy' => $fecha_hoy,
            //         'fullaccess' => $fullaccess,
            //         'dolar' => $dolar,
            //     ]);
            // }

            

            // foreach ($detalles as $detalle) {
           
            //     //$cantidadDetalle = $lotes->where('producto', $detalle->producto_id)->sum('cantidad');
            //     $cantidadDetalle   = [];
            //     $cantidadDetalle = $lotes->where('producto', $detalle->producto_id)
            //                         ->where('color', $detalle->color_id)
            //                         ->where('talla', $detalle->talla_id)
            //                         ->first();
                
              
            //     if($cantidadDetalle){
            //         if ($cantidadDetalle->cantidad != $detalle->cantidad) {
                        
            //         //     //dd(' != '. $cantidadDetalle[0]->cantidad);
            //             $devolucion = true;
            //             // $devolucionLotes = $lotes->where('producto', $detalle->producto_id);
            //             $devolucionLotes = $lotes->where('producto',$detalle->producto_id)
            //                                         ->where('color',$detalle->color_id)
            //                                         ->where('talla',$detalle->talla_id)
            //                                         ->first();
            //             //dd($devolucionLotes);
            //             //LLENAR ERROR CANTIDAD SOLICITADA MAYOR AL STOCK
            //             $coll = new Collection();
            //             $coll->producto = $devolucionLotes->descripcion_producto;
            //             $coll->tipo     =   'stocklogico';
            //             $coll->cantidad = $detalle->cantidad;
            //             $errores->push($coll);
                        
            //             self::devolverCantidad($lotes->where('producto',$detalle->producto_id)
            //                                             ->where('color',$detalle->color_id)
            //                                             ->where('talla',$detalle->talla_id)
            //                                             ->first());
            //         } else {
            //                 $nuevoSindevoluciones = $lotes->where('producto',$detalle->producto_id)
            //                                         ->where('color',$detalle->color_id)
            //                                         ->where('talla',$detalle->talla_id)
            //                                         ->first();
                            
            //                 //dd($nuevoSindevoluciones);
            //                 $coll = new Collection();
            //                 $col  = [];
            //                 // $coll->producto_id = $devolucion->producto_id;

            //                 $coll->producto_id = $nuevoSindevoluciones->producto;
            //                 $coll->color_id = $nuevoSindevoluciones->color;
            //                 $coll->talla_id = $nuevoSindevoluciones->talla;
            //                 $coll->cantidad             = $nuevoSindevoluciones->cantidad;
            //                 $coll->precio_unitario      = $nuevoSindevoluciones->precio_unitario;
            //                 $coll->importe              = $nuevoSindevoluciones->importe;

            //                 $col = [
            //                     'producto_id' => $nuevoSindevoluciones->producto,
            //                     'color_id' => $nuevoSindevoluciones->color,
            //                     'talla_id' => $nuevoSindevoluciones->talla,
            //                     'cantidad' => $nuevoSindevoluciones->cantidad,
            //                     'precio_unitario' => $nuevoSindevoluciones->precio_unitario,
            //                     'importe' => $nuevoSindevoluciones->importe,
            //                     'producto_nombre'   =>  Producto::where('id', $nuevoSindevoluciones->producto)->first()->nombre,
            //                     'color_nombre'      =>  Color::where('id',  $nuevoSindevoluciones->color)->first()->descripcion,
            //                 ];   

            //                 // $coll->precio_unitario = $devolucion->precio_unitario;
            //                 // $coll->precio_inicial = $devolucion->precio_inicial;
            //                 // $coll->precio_nuevo = $devolucion->precio_nuevo;
            //                 // $coll->descuento = $devolucion->descuento;
            //                 // $coll->dinero = $devolucion->dinero;
            //                 // $coll->valor_unitario = $devolucion->valor_unitario;
            //                 // $coll->valor_venta = $devolucion->valor_venta;
            //                 // $coll->unidad = $devolucion->unidad;
            //                 $coll->descripcion_producto = $nuevoSindevoluciones->descripcion_producto;
            //                 //$coll->presentacion = $devolucion->presentacion;
            //                 //$coll->producto = $devolucion->producto;
            //                 $nuevoDetalle->push($coll);
                            
            //                 $detalleValidado[] = $col;
            //         }
            //     }else{
            //         $coll           = new Collection();
            //         $coll->producto = $detalle->producto->nombre.' - '.$detalle->color->descripcion.' - '.$detalle->talla->descripcion;
            //         $coll->tipo     =   'producto_no_existe';
            //         $errores->push($coll);
            //     }
            // }
           
            
           
        }

        if (empty($cotizacion)) {
            return view('ventas.documentos.' . $vista, [
                'empresas' => $empresas,
                'clientes' => $clientes,
                'productos' => $productos,
                'condiciones' => $condiciones,
                'fecha_hoy' => $fecha_hoy,
                'fullaccess' => $fullaccess,
                'dolar' => $dolar,
            ]);
        }
    }

    public function ObtenerCotizacionForVenta(Request $request){
        
    }
    public function getCreate(Request $request){
        try{

            $this->authorize('haveaccess', 'documento_venta.index');

            $empresas = Empresa::where('estado', 'ACTIVO')->get();
            $clientes = Cliente::where('estado', 'ACTIVO')->get([
                "id","tabladetalles_id","tipo_documento","documento","nombre"
            ]);

            $condiciones = Condicion::where('estado', 'ACTIVO')->get();
            $dolar = 0;
            $fullaccess = false;
            $tipos_ventas = tipos_venta();
            $tipoVentaArray=collect();

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

            $vista = 'create';
            if (CEC() == 'NO') {
                $vista = 'create_new';
            }
            foreach($tipos_ventas as $tipo){
                if(ifComprobanteSeleccionado($tipo->id) && ($tipo->tipo == 'VENTA' || $tipo->tipo == 'AMBOS')){
                    $tipoVentaArray->push([
                        "id"=>$tipo->id,
                        "nombre"=>$tipo->nombre,
                    ]);
                }
            }

            return response()->json([
                "initData"=>[
                    'empresas'      =>  $empresas,
                    'clientes'      =>  $clientes,
                    'condiciones'   =>  $condiciones,
                    'fullaccess'    =>  $fullaccess,
                    'dolar'         =>  $dolar,
                    'vista'         =>  $vista,
                    "tipoVentas"    =>  $tipoVentaArray,
                    "modelos"       =>  Modelo::all(),
                    "tallas"        =>  Talla::where('estado', 'ACTIVO')->get(),
                ],
                "succes"=>true
            ]);

        }catch(\Exception $ex){
            return response()->json([
                "success"=>false,
                "initData"=> null
            ]);
        }
    }
    public function devolverCantidad($devolucion)
    {                 
            // if ($devolucion->producto_id != 0) {   
            if ($devolucion->producto != 0) {
                //$lote = LoteProducto::findOrFail($devolucion->producto_id);
                // $lote = ProductoColorTalla::where('producto_id', $devolucion->producto)
                //                         ->where('color_id', $devolucion->color)
                //                         ->where('talla_id', $devolucion->talla)
                //                         ->firstOrFail();                
                // $lote->cantidad_logica = $lote->cantidad_logica + $devolucion->cantidad;
                // $lote->cantidad = $lote->cantidad_logica;
                // $lote->estado = '1';
                // $lote->update();
                //$stock_logico_repuesto  =   $lote->stock_logico + $devolucion->cantidad;  
                // $lote->stock_logico     =   $stock_logico_repuesto;
                // $lote->stock            = $stock_logico_repuesto;

                // Guardar los cambios en la base de datos
                DB::table('producto_color_tallas')
                ->where('producto_id', $devolucion->producto)
                ->where('color_id', $devolucion->color)
                ->where('talla_id', $devolucion->talla)
                ->update([
                    'stock_logico' => DB::raw('stock_logico + ' . $devolucion->cantidad)
                    // 'stock' => DB::raw('stock_logico')
                ]);
                
            }
        
        
        
    }


    public function validacionStockCantidad($detalles){
        $validaciones = [];
        //======== recorriendo cada producto del detalle de la cotización ===========
        foreach ($detalles as $detalle) {
            //=========== obteniendo stock_logico de un producto =============
            $productoExiste   =   DB::select('select stock_logico from producto_color_tallas as pct
                                where pct.producto_id=? and pct.color_id=? and pct.talla_id=?',
                                [$detalle->producto_id,$detalle->color_id,$detalle->talla_id]);

            $item_producto_nombre   =   Producto::findOrFail($detalle->producto_id)->nombre;
            $item_color_nombre      =   Color::findOrFail($detalle->color_id)->descripcion;
            $item_talla_nombre      =   Talla::findOrFail($detalle->talla_id)->descripcion;

            //===== EN CASO EXISTA EL PRODUCTO COLOR TALLA =====
            if(count($productoExiste)>0){
                $stock_logico   =   $productoExiste[0]->stock_logico;
                if($stock_logico<$detalle->cantidad){
                    $registro                           = new ValidatedDetail();
                    $registro->setStockLogico($stock_logico);
                    $registro->setTipo('STOCK LOGICO INSUFICIENTE');

                }else{
                    $registro                           = new ValidatedDetail();
                    $registro->setStockLogico($stock_logico);
                    $registro->setTipo('STOCK LOGICO VÁLIDO');
                }
            }else{
                    $registro                           = new ValidatedDetail();
                    $registro->setStockLogico(null);
                    $registro->setTipo('NO EXISTE EL PRODUCTO COLOR TALLA');
            }

            $registro->setProductoId($detalle->producto_id);
            $registro->setColorId($detalle->color_id);
            $registro->setTallaId($detalle->talla_id);
            $registro->setProductoNombre($item_producto_nombre);
            $registro->setColorNombre($item_color_nombre);
            $registro->setTallaNombre($item_talla_nombre);
            $registro->setCantidadSolicitada($detalle->cantidad);
            $registro->setPrecioUnitario($detalle->precio_unitario);
            $registro->setPrecioUnitarioNuevo($detalle->precio_unitario_nuevo);
            $registro->setPorcentajeDescuento($detalle->porcentaje_descuento);
            $validaciones[]                     =   $registro;  

        }

        return $validaciones;
    }

 

    public function cotizacionLote($detalles)
    {
        
        $nuevoDetalle = collect();
        
        foreach ($detalles as  $detalle) {
            $producto_existencia = DB::select('select pct.*,p.nombre as producto_nombre,
                                    c.descripcion as color_nombre,t.descripcion as talla_nombre
                                    from producto_color_tallas as pct
                                    inner join productos as p
                                    on p.id = pct.producto_id
                                    inner join colores as c
                                    on c.id = pct.color_id
                                    inner join tallas as t
                                    on t.id = pct.talla_id
                                    where producto_id = ? and color_id = ? and talla_id = ?'
                                    ,[$detalle->producto_id,$detalle->color_id,$detalle->talla_id]);    

            $cantidadSolicitada = $detalle->cantidad;
            if(count($producto_existencia) > 0 ){
                $producto_existencia = $producto_existencia[0];
                if($cantidadSolicitada > 0){
                    $cantidadLogica     = $producto_existencia->stock_logico;
                
                    if($cantidadLogica == $cantidadSolicitada){
                        $coll = new Collection();
                        //$coll->producto_id = $lote->id;
                        $coll->cantidad         =   $cantidadSolicitada;
                        $coll->precio_unitario  =   $detalle->precio_unitario;  //precio_unitario
                        $coll->importe          =   $detalle->importe;
                        $coll->producto         =   $detalle->producto_id;
                        $coll->color            =   $detalle->color_id;
                        $coll->talla            =   $detalle->talla_id;
                        //$coll->precio_inicial = $detalle->precio_inicial;
                        //$coll->precio_nuevo = $detalle->precio_nuevo;
                        //$coll->descuento = $detalle->descuento;
                        //$coll->dinero = $detalle->dinero;
                        //$coll->valor_unitario = $detalle->valor_unitario;
                        //$coll->valor_venta = $detalle->valor_venta;
                        //$coll->unidad = $lote->producto->medidaCompleta();
                        //$coll->descripcion_producto = $lote->producto->nombre . ' - ' . $lote->codigo_lote;
                        $coll->descripcion_producto = $producto_existencia->producto_nombre . ' - ' . $producto_existencia->color_nombre;
                        //$coll->presentacion = $lote->producto->medida;
                        //$coll->producto = $lote->producto->id;
                        $nuevoDetalle->push($coll);
                        //$nuevoDetalle->push($coll);
                        //ACTUALIZAMOS EL LOTE
                        //$lote->cantidad_logica = $lote->cantidad_logica - $cantidadSolicitada;
                        //REDUCIMOS LA CANTIDAD SOLICITADA
                        //$producto_existencia->update();
                        DB::table('producto_color_tallas')
                            ->where('producto_id', $detalle->producto_id)
                            ->where('color_id', $detalle->color_id)
                            ->where('talla_id', $detalle->talla_id)
                            ->update([
                                'stock_logico' => DB::raw('stock_logico - ' . $cantidadSolicitada)
                        ]);
                        $cantidadSolicitada = 0;
                    }else{
                        if($cantidadLogica < $cantidadSolicitada){
                            //CREAMOS EL NUEVO DETALLE
                            $coll = new Collection();
            //              $coll->producto_id = $lote->id;
                            $coll->cantidad         =   $producto_existencia->stock_logico;
                            $coll->precio_unitario  =   $detalle->precio_unitario;          //precio_unitario
                            $coll->importe          =   $detalle->importe;
                            $coll->producto         =   $detalle->producto_id;
                            $coll->color            =   $detalle->color_id;
                            $coll->talla            =   $detalle->talla_id;
            //              $coll->precio_inicial = $detalle->precio_inicial;
            //              $coll->precio_nuevo = $detalle->precio_nuevo;
            //              $coll->descuento = $detalle->descuento;
            //              $coll->dinero = $detalle->dinero;
            //              $coll->valor_unitario = $detalle->valor_unitario;
            //              $coll->valor_venta = $detalle->valor_venta;
            //              $coll->unidad = $lote->producto->medidaCompleta();
                            //$coll->descripcion_producto = $lote->producto->nombre . ' - ' . $lote->codigo_lote;
                            $coll->descripcion_producto = $producto_existencia->producto_nombre . ' - ' . $producto_existencia->color_nombre;
                            //$coll->presentacion = $lote->producto->medida;
            //              $coll->producto = $lote->producto->id;
                            $nuevoDetalle->push($coll);
            //              //REDUCIMOS LA CANTIDAD SOLICITADA
                            $cantidadSolicitada = $cantidadSolicitada - $cantidadLogica;
            //              //ACTUALIZAMOS EL LOTE
            //              $lote->cantidad_logica = 0;
            //              $lote->update();
                            DB::table('producto_color_tallas')
                                ->where('producto_id', $detalle->producto_id)
                                ->where('color_id', $detalle->color_id)
                                ->where('talla_id', $detalle->talla_id)
                                ->update([
                                    'stock_logico' => 0
                            ]);
                        }else if($cantidadLogica    >   $cantidadSolicitada)
                        {
                            //CREAMOS EL NUEVO DETALLE
                            $coll = new Collection();
                //          $coll->producto_id = $lote->id;
                            $coll->cantidad = $cantidadSolicitada;
                            $coll->precio_unitario  =   $detalle->precio_unitario;  //precio_unitario
                            $coll->importe          =   $detalle->importe;
                            $coll->producto         =   $detalle->producto_id;
                            $coll->color            =   $detalle->color_id;
                            $coll->talla            =   $detalle->talla_id;
            //              $coll->precio_unitario = $detalle->precio_unitario;
            //              $coll->precio_inicial = $detalle->precio_inicial;
            //              $coll->precio_nuevo = $detalle->precio_nuevo;
            //              $coll->descuento = $detalle->descuento;
            //              $coll->dinero = $detalle->dinero;
            //              $coll->valor_unitario = $detalle->valor_unitario;
            //              $coll->valor_venta = $detalle->valor_venta;
            //              $coll->unidad = $lote->producto->medidaCompleta();
                            //$coll->descripcion_producto = $lote->producto->nombre . ' - ' . $lote->codigo_lote;
                            $coll->descripcion_producto = $producto_existencia->producto_nombre . ' - ' . $producto_existencia->color_nombre;
                            //$coll->presentacion = $lote->producto->medida;
            //              $coll->producto = $lote->producto->id;
                            $nuevoDetalle->push($coll);
            //              //ACTUALIZAMOS EL LOTE
                            //$lote->cantidad_logica = $lote->cantidad_logica - $cantidadSolicitada;
            //              //REDUCIMOS LA CANTIDAD SOLICITADA
                            //$cantidadSolicitada = 0;
            //              $lote->update();
                                DB::table('producto_color_tallas')
                                ->where('producto_id', $detalle->producto_id)
                                ->where('color_id', $detalle->color_id)
                                ->where('talla_id', $detalle->talla_id)
                                ->update([
                                    'stock_logico' => DB::raw('stock_logico - ' . $cantidadSolicitada)
                                ]);
                            $cantidadSolicitada = 0;
                        }
                    }
                }else{
                    $coll = new Collection();
                    //$coll->producto_id = 0;
                    $coll->cantidad = 0;
                    $coll->precio_unitario  =   $detalle->precio_unitario; //precio_unitario
                    $coll->importe          =   $detalle->importe;
                    $coll->producto         =   $detalle->producto_id;
                    $coll->color            =   $detalle->color_id;
                    $coll->talla            =   $detalle->talla_id;
                    //$coll->precio_inicial = $detalle->precio_inicial;
                    //$coll->precio_nuevo = $detalle->precio_nuevo;
                    //$coll->descuento = $detalle->descuento;
                    //$coll->dinero = $detalle->dinero;
                    //$ coll->valor_unitario = $detalle->valor_unitario;
                    //$coll->valor_venta = $detalle->valor_venta;
                    //$coll->unidad = '';
                    $coll->descripcion_producto = $producto_existencia->producto_nombre . ' - ' . $producto_existencia->color_nombre;
                    //$coll->presentacion = "";
                    //$coll->producto = $detalle->producto->id;
                    $nuevoDetalle->push($coll);
                }
                
            }   
        }
        return $nuevoDetalle;


        // $nuevoDetalle = collect();
        // foreach ($detalles as $detalle) {
        //     $lotes = LoteProducto::where('producto_id', $detalle->producto_id)
        //         ->where('estado', '1')
        //         ->where('cantidad_logica', '>', 0)
        //         ->orderBy('fecha_vencimiento', 'asc')
        //         ->get();
        //     //INICIO CON LA CANTIDAD DEL DETALLE
        //     $cantidadSolicitada = $detalle->cantidad;

        //     if (count($lotes) > 0) {
        //         foreach ($lotes as $lote) {
        //             //SE OBTUVO LA CANTIDAD SOLICITADA DEL LOTE
        //             if ($cantidadSolicitada > 0) {
        //                 //CANTIDAD LOGICA DEL LOTE ES IGUAL A LA CANTIDAD SOLICITADA
        //                 $cantidadLogica = $lote->cantidad_logica;
        //                 if ($cantidadLogica == $cantidadSolicitada) {
        //                     //CREAMOS EL NUEVO DETALLE
        //                     $coll = new Collection();
        //                     $coll->producto_id = $lote->id;
        //                     $coll->cantidad = $cantidadSolicitada;
        //                     $coll->precio_unitario = $detalle->precio_unitario;
        //                     $coll->precio_inicial = $detalle->precio_inicial;
        //                     $coll->precio_nuevo = $detalle->precio_nuevo;
        //                     $coll->descuento = $detalle->descuento;
        //                     $coll->dinero = $detalle->dinero;
        //                     $coll->valor_unitario = $detalle->valor_unitario;
        //                     $coll->valor_venta = $detalle->valor_venta;
        //                     $coll->unidad = $lote->producto->medidaCompleta();
        //                     $coll->descripcion_producto = $lote->producto->nombre . ' - ' . $lote->codigo_lote;
        //                     $coll->presentacion = $lote->producto->medida;
        //                     $coll->producto = $lote->producto->id;
        //                     $nuevoDetalle->push($coll);
        //                     //ACTUALIZAMOS EL LOTE
        //                     $lote->cantidad_logica = $lote->cantidad_logica - $cantidadSolicitada;
        //                     //REDUCIMOS LA CANTIDAD SOLICITADA
        //                     $cantidadSolicitada = 0;
        //                     $lote->update();
        //                 } else {
        //                     if ($lote->cantidad_logica < $cantidadSolicitada) {
        //                         //CREAMOS EL NUEVO DETALLE
        //                         $coll = new Collection();
        //                         $coll->producto_id = $lote->id;
        //                         $coll->cantidad = $lote->cantidad_logica;
        //                         $coll->precio_unitario = $detalle->precio_unitario;
        //                         $coll->precio_inicial = $detalle->precio_inicial;
        //                         $coll->precio_nuevo = $detalle->precio_nuevo;
        //                         $coll->descuento = $detalle->descuento;
        //                         $coll->dinero = $detalle->dinero;
        //                         $coll->valor_unitario = $detalle->valor_unitario;
        //                         $coll->valor_venta = $detalle->valor_venta;
        //                         $coll->unidad = $lote->producto->medidaCompleta();
        //                         $coll->descripcion_producto = $lote->producto->nombre . ' - ' . $lote->codigo_lote;
        //                         $coll->presentacion = $lote->producto->medida;
        //                         $coll->producto = $lote->producto->id;
        //                         $nuevoDetalle->push($coll);
        //                         //REDUCIMOS LA CANTIDAD SOLICITADA
        //                         $cantidadSolicitada = $cantidadSolicitada - $lote->cantidad_logica;
        //                         //ACTUALIZAMOS EL LOTE
        //                         $lote->cantidad_logica = 0;
        //                         $lote->update();
        //                     } else {
        //                         if ($lote->cantidad_logica > $cantidadSolicitada) {
        //                             //CREAMOS EL NUEVO DETALLE
        //                             $coll = new Collection();
        //                             $coll->producto_id = $lote->id;
        //                             $coll->cantidad = $cantidadSolicitada;
        //                             $coll->precio_unitario = $detalle->precio_unitario;
        //                             $coll->precio_inicial = $detalle->precio_inicial;
        //                             $coll->precio_nuevo = $detalle->precio_nuevo;
        //                             $coll->descuento = $detalle->descuento;
        //                             $coll->dinero = $detalle->dinero;
        //                             $coll->valor_unitario = $detalle->valor_unitario;
        //                             $coll->valor_venta = $detalle->valor_venta;
        //                             $coll->unidad = $lote->producto->medidaCompleta();
        //                             $coll->descripcion_producto = $lote->producto->nombre . ' - ' . $lote->codigo_lote;
        //                             $coll->presentacion = $lote->producto->medida;
        //                             $coll->producto = $lote->producto->id;
        //                             $nuevoDetalle->push($coll);
        //                             //ACTUALIZAMOS EL LOTE
        //                             $lote->cantidad_logica = $lote->cantidad_logica - $cantidadSolicitada;
        //                             //REDUCIMOS LA CANTIDAD SOLICITADA
        //                             $cantidadSolicitada = 0;
        //                             $lote->update();
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }else {
        //         $coll = new Collection();
        //         $coll->producto_id = 0;
        //         $coll->cantidad = 0;
        //         $coll->precio_unitario = $detalle->precio_unitario;
        //         $coll->precio_inicial = $detalle->precio_inicial;
        //         $coll->precio_nuevo = $detalle->precio_nuevo;
        //         $coll->descuento = $detalle->descuento;
        //         $coll->dinero = $detalle->dinero;
        //         $ coll->valor_unitario = $detalle->valor_unitario;
        //         $coll->valor_venta = $detalle->valor_venta;
        //         $coll->unidad = '';
        //         $coll->descripcion_producto = $detalle->producto->nombre;
        //         $coll->presentacion = "";
        //         $coll->producto = $detalle->producto->id;
        //         $nuevoDetalle->push($coll);
        //     }
        // }

        // return $nuevoDetalle;
    }

    public function storeNuevo(Request $request){
        
        ini_set("max_execution_time", 60000);
        try{
            DB::beginTransaction();
            $data = $request->all();

            $rules = [
                'fecha_documento_campo' => 'required',
                'fecha_atencion_campo' => 'required',
                'tipo_venta' => 'required',
                'tipo_pago_id' => 'nullable',
                'efectivo' => 'required',
                'importe' => 'required',
                'empresa_id' => 'required',
                'condicion_id' => 'required',
                'cliente_id' => 'required',
                'observacion' => 'nullable',
                'igv' => 'required_if:igv_check,==,on|numeric|digits_between:1,3',

            ];

            $message = [
                'fecha_documento_campo.required' => 'El campo Fecha de Emisión es obligatorio.',
                'tipo_venta.required' => 'El campo tipo de venta es obligatorio.',
                'condicion_id.required' => 'El campo condición de pago es obligatorio.',
                'importe.required' => 'El campo importe es obligatorio.',
                'efectivo.required' => 'El campo efectivo es obligatorio.',
                'fecha_atencion_campo.required' => 'El campo Fecha de Entrega es obligatorio.',
                'empresa_id.required' => 'El campo Empresa es obligatorio.',
                'cliente_id.required' => 'El campo Cliente es obligatorio.',
                'igv.required_if' => 'El campo Igv es obligatorio.',
                'igv.digits' => 'El campo Igv puede contener hasta 3 dígitos.',
                'igv.numeric' => 'El campo Igv debe se numérico.',
            ];

            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => true,
                    'data' => array('mensajes' => $validator->getMessageBag()->toArray()),
                ]);
            }

            $documento = new Documento();
            $documento->fecha_documento = $request->get('fecha_documento_campo');
            $documento->fecha_atencion = $request->get('fecha_atencion_campo');
            $documento->fecha_vencimiento = $request->get('fecha_vencimiento_campo');
            //EMPRESA
            $empresa = Empresa::findOrFail($request->get('empresa_id'));
            $documento->ruc_empresa = $empresa->ruc;
            $documento->empresa = $empresa->razon_social;
            $documento->direccion_fiscal_empresa = $empresa->direccion_fiscal;
            $documento->empresa_id = $request->get('empresa_id'); //OBTENER NUMERACION DE LA EMPRESA
            //CLIENTE
            $cliente = Cliente::findOrFail($request->get('cliente_id'));
            $documento->tipo_documento_cliente = $cliente->tipo_documento;
            $documento->documento_cliente = $cliente->documento;
            $documento->direccion_cliente = $cliente->direccion;
            $documento->cliente = $cliente->nombre;
            $documento->cliente_id = $request->get('cliente_id'); //OBTENER TIENDA DEL CLIENTE

            $documento->tipo_venta = $request->get('tipo_venta');

            //CONDICION
            $cadena = explode('-', $request->get('condicion_id'));
            $condicion = Condicion::findOrFail($cadena[0]);
            $documento->condicion_id = $condicion->id;

            $documento->observacion = $request->get('observacion');
            $documento->user_id = auth()->user()->id;
            $documento->sub_total = $request->get('monto_sub_total');
            $documento->total_igv = $request->get('monto_total_igv');
            $documento->total = $request->get('monto_total');
            $documento->igv = $request->get('igv') ? $request->get('igv') : 18;
            $documento->moneda = 1;

            $documento->tipo_pago_id = $request->get('tipo_pago_id');
            $documento->importe = $request->get('importe');
            $documento->efectivo = $request->get('efectivo');

            if ($request->convertir) {
                $documento->convertir = $request->convertir;
            } else {
                $documento->convertir = null;
            }

            if (!empty($request->get('tipo_pago_id')) && $condicion->descripcion == 'CONTADO') {
                $documento->estado_pago = 'PAGADA';
            }

            if ($request->get('igv_check') == "on" || $request->get('igv_check') == true) {
                $documento->igv_check = "1";
            };

            $documento->cotizacion_venta = $request->get('cotizacion_id');

            $documento->save();

            $numero_doc = $documento->id;
            $documento->numero_doc = 'VENTA-' . $numero_doc;
            $documento->update();
            //Llenado de los articulos
            $productosJSON = $request->get('productos_tabla');
            $productotabla = json_decode($productosJSON);
            if ($request->convertir) {
                foreach ($productotabla as $producto) {
                    $lote = LoteProducto::findOrFail($producto->producto_id);
                    $lote->cantidad = $lote->cantidad + $producto->cantidad;
                    $lote->update();
                }
            }

            foreach ($productotabla as $producto) {
                $lote = LoteProducto::findOrFail($producto->producto_id);
                Detalle::create([
                    'documento_id' => $documento->id,
                    'lote_id' => $producto->producto_id, //LOTE
                    'codigo_producto' => $lote->producto->codigo,
                    'unidad' => $lote->producto->getMedida(),
                    'nombre_producto' => $lote->producto->nombre,
                    'codigo_lote' => $lote->codigo_lote,
                    'cantidad' => $producto->cantidad,
                    'precio_unitario' => $producto->precio_unitario,
                    'precio_inicial' => $producto->precio_inicial,
                    'precio_nuevo' => $producto->precio_nuevo,
                    'dinero' => $producto->dinero,
                    'descuento' => $producto->descuento,
                    'valor_unitario' => $producto->valor_unitario,
                    'valor_venta' => $producto->valor_venta,
                ]);

                $lote->cantidad = $lote->cantidad - $producto->cantidad;
                if ($lote->cantidad == 0) {
                    $lote->estado = '0';
                }
                $lote->update();
            }

            if ($request->convertir) {
                $doc_a_convertir = Documento::find($request->convertir);
                $doc_a_convertir->convertir = $documento->id;
                $doc_a_convertir->update();

                $documento = Documento::find($documento->id);
                $documento->estado = $doc_a_convertir->estado;
                $documento->estado_pago = $doc_a_convertir->estado_pago;
                $documento->fecha_documento = Carbon::now()->toDateString();
                $documento->convertir = $doc_a_convertir->id;
                $documento->importe = $doc_a_convertir->importe;
                $documento->efectivo = $doc_a_convertir->efectivo;
                $documento->tipo_pago_id = $doc_a_convertir->tipo_pago_id;

                $documento->update();
            }

            $detalle = new DetalleMovimientoVentaCaja();
            $detalle->cdocumento_id = $documento->id;
            $detalle->mcaja_id = movimientoUser()->id;
            
            $detalle->save();

            $envio_prev = self::sunat($documento->id);
            if (!$envio_prev['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'mensaje' => $envio_prev['mensaje'],
                ]);
            }

            if ($request->tipo_venta == '127' && $cliente->agente_retencion == '1' && $documento->total >= $cliente->monto_mayor) {
                self::generarComprobanteRetencion($documento->id);
            }

            $documento = Documento::find($documento->id);
            $documento->nombre_comprobante_archivo = $documento->serie . '-' . $documento->correlativo . '.pdf';
            $documento->update();

            //Registro de actividad
            $descripcion = "SE AGREGÓ EL DOCUMENTO DE VENTA CON LA FECHA: " . Carbon::parse($documento->fecha_documento)->format('d/m/y');
            $gestion = "DOCUMENTO DE VENTA";
            crearRegistro($documento, $descripcion, $gestion);

            if ((int) $documento->tipo_venta == 127 || (int) $documento->tipo_venta == 128) {
                $dato = 'Actualizar';
                broadcast(new VentasCajaEvent($dato));
                DB::commit();
                if ($request->envio_sunat) {
                    $envio_ = self::sunat_valida($documento->id);
                }
                // Session::flash('success', 'Documento de venta creado.');

                return response()->json([
                    'success' => true,
                    'documento_id' => $documento->id,
                ]);
            } else {
                $dato = 'Actualizar';
                broadcast(new VentasCajaEvent($dato));
                DB::commit();
                //$vp = self::venta_comprobante($documento->id);
                //$ve = self::venta_email($documento->id);
                // Session::flash('success', 'Documento de venta creado.');
                return response()->json([
                    'success' => true,
                    'documento_id' => $documento->id,
                ]);
            }
        }catch(\Exception $ex){
            DB::rollBack();
            Log::info($ex);
            return response()->json([
                "success"=>false,
                "message"=>$ex->getMessage()
            ]);
        }
       
    }

    public function store(Request $request)
    {
        

        $this->authorize('haveaccess', 'documento_venta.index');
        ini_set("max_execution_time", 60000);
        try {

            DB::beginTransaction();
            $data = $request->all();

            $rules = [
                'fecha_documento_campo' => 'required',
                'fecha_atencion_campo' => 'required',
                'tipo_venta' => 'required',
                'tipo_pago_id' => 'nullable',
                'efectivo' => 'required',
                'importe' => 'required',
                'empresa_id' => 'required',
                'condicion_id' => 'required',
                'cliente_id' => 'required',
                'observacion' => 'nullable',
                'igv' => 'required_if:igv_check,==,on|numeric|digits_between:1,3',
                'regularizar'   =>  'nullable'
            ];

            $message = [
                'fecha_documento_campo.required' => 'El campo Fecha de Emisión es obligatorio.',
                'tipo_venta.required' => 'El campo tipo de venta es obligatorio.',
                'condicion_id.required' => 'El campo condición de pago es obligatorio.',
                'importe.required' => 'El campo importe es obligatorio.',
                'efectivo.required' => 'El campo efectivo es obligatorio.',
                'fecha_atencion_campo.required' => 'El campo Fecha de Entrega es obligatorio.',
                'empresa_id.required' => 'El campo Empresa es obligatorio.',
                'cliente_id.required' => 'El campo Cliente es obligatorio.',
                'igv.required_if' => 'El campo Igv es obligatorio.',
                'igv.digits' => 'El campo Igv puede contener hasta 3 dígitos.',
                'igv.numeric' => 'El campo Igv debe se numérico.',
            ];

            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return response()->json([
                    'errors'    => true,
                    'data'      => array('mensajes' => $validator->getMessageBag()->toArray()),
                    'success'   =>  false,
                ]);
            }

           
      
            $documento = new Documento();
            $documento->fecha_documento = $request->get('fecha_documento_campo');
            $documento->fecha_atencion = $request->get('fecha_atencion_campo');
            $documento->fecha_vencimiento = $request->get('fecha_vencimiento_campo');

            //EMPRESA
            $empresa = Empresa::findOrFail($request->get('empresa_id'));
            $documento->ruc_empresa = $empresa->ruc;
            $documento->empresa = $empresa->razon_social;
            $documento->direccion_fiscal_empresa = $empresa->direccion_fiscal;
            $documento->empresa_id = $request->get('empresa_id'); //OBTENER NUMERACION DE LA EMPRESA

           

            //CLIENTE
            $cliente = Cliente::findOrFail($request->get('cliente_id'));
            $documento->tipo_documento_cliente  = $cliente->tipo_documento;
            $documento->documento_cliente       = $cliente->documento;
            $documento->direccion_cliente       = $cliente->direccion;
            $documento->cliente                 = $cliente->nombre;
            $documento->cliente_id              = $request->get('cliente_id'); //OBTENER TIENDA DEL CLIENTE

            $documento->tipo_venta              = $request->get('tipo_venta');   //boleta,factura,nota_venta

            //CONDICION(TIPO DE PAGO: CONTADO O CREDITO)
            $cadena = explode('-', $request->get('condicion_id'));
            $condicion = Condicion::findOrFail($cadena[0]);
            $documento->condicion_id = $condicion->id;


            $documento->observacion = $request->get('observacion');
            $documento->user_id     = auth()->user()->id;

            $monto_sub_total    =   str_replace('S/', '', $request->get('monto_sub_total'));
            $monto_embalaje     =   str_replace('S/', '', $request->get('monto_embalaje') ?? 0);
            $monto_envio        =   str_replace('S/', '', $request->get('monto_envio') ?? 0);
            $monto_total        =   str_replace('S/', '', $request->get('monto_total'));
            $monto_total_igv    =   str_replace('S/', '', $request->get('monto_total_igv'));
            $monto_total_pagar  =   str_replace('S/', '', $request->get('monto_total_pagar'));            
            $monto_descuento    =   $request->get('monto_descuento')??0;
            $porcentaje_descuento = ($monto_descuento*100)/($monto_total_pagar);

          
            
            $documento->sub_total           = $monto_sub_total;
            $documento->monto_embalaje      = $monto_embalaje;  
            $documento->monto_envio         = $monto_envio;  
            $documento->total               = $monto_total;  
            $documento->total_igv           = $monto_total_igv;
            $documento->total_pagar         = $monto_total_pagar;  
            $documento->igv                 = $request->get('igv') ? $request->get('igv') : 18;
               
            $documento->monto_descuento         =   $monto_descuento;
            $documento->porcentaje_descuento    =   $porcentaje_descuento;   
            $documento->moneda                  =   1;

            $documento->tipo_pago_id    = $request->get('tipo_pago_id');
            $documento->importe         = $request->get('importe');
            $documento->efectivo        = $request->get('efectivo');

            

            //======= DESDE STORE -> CONVERTIR HAS FALSE ======
            if ($request->convertir) {
                $documento->convertir = $request->convertir;
            } else {
                $documento->convertir = null;
            }

            if (!empty($request->get('tipo_pago_id')) && $condicion->descripcion == 'CONTADO') {
                 $documento->estado_pago = 'PAGADA';
            }

            if ($request->get('igv_check') == "on" || $request->get('igv_check') == true) {
                 $documento->igv_check = "1";
            };

            $documento->cotizacion_venta = $request->get('cotizacion_id'); //correcto

            if($request->has('facturar') && $request->has('pedido_id')){
                $documento->pedido_id   =   $request->get('pedido_id');
            }

            $documento->save();

            


            //========== VERIFICANDO SI EL USUARIO ESTÁ PARTICIPANDO DE ALGUNA CAJA ACTUALMENTE ==========
            $caja_usuario           =   movimientoUser();
          
            if(count($caja_usuario) == 1){
                $detalle                = new DetalleMovimientoVentaCaja();
                $detalle->cdocumento_id = $documento->id;
                $detalle->mcaja_id      = movimientoUser()[0]->movimiento_id;
                if($request->has('facturado') && $request->get('facturado') === 'SI'){
                    $detalle->cobrar            =   'NO';
                    $documento->estado_pago     =   'PAGADA';
                }
                $detalle->save();
            }
            
            if(count($caja_usuario) == 0 ){
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'mensaje' => "USTED NO SE ENCUENTRA PARTICIPANDO EN NINGUNA CAJA ACTUALMENTE",
                ]);
            }
            

            //=========== NUMERO DOC VENTA =======
            $numero_doc = $documento->id;
            $documento->numero_doc = 'VENTA-' . $numero_doc;
            $documento->update();

            
            //===== OBTENIENDO CORRELATIVO Y SERIE =====
            $envio_prev =   self::sunat($documento->id);
          
            
            //====== VERIFICANDO SI EL TIPO DE DOCUMENTO ESTÁ ACTIVO EN LA EMPRESA =======
            if (!$envio_prev['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'mensaje' => $envio_prev['mensaje'],
                ]);
           }

          
          
            //=========== DETALLE DEL DOCUMENTO =======
            $productosJSON = $request->get('productos_tabla');
            $productotabla = json_decode($productosJSON);

            
       
            $producto_control    =   null;
           
            foreach ($productotabla as $producto) {
                /*======== EN CASO SEA UNA FACTURACIÓN DE PEDIDO Y EL PRODUCTO NO EXISTA ========
                 ========= CREAMOS EL PRODUCTO COLOR TALLA CON STOCKS EN CERO            ========*/
                if($request->has('facturar')){
                    $item   =   DB::select('select pct.producto_id from producto_color_tallas as pct
                                where pct.producto_id = ?  and pct.color_id = ? and pct.talla_id = ?',
                                [$producto->producto_id,$producto->color_id,$producto->talla_id]);
                    
                    if(count($item) === 0){
                        $nuevo_producto =   new ProductoColorTalla();
                        $nuevo_producto->producto_id    =   $producto->producto_id;
                        $nuevo_producto->color_id       =   $producto->color_id;
                        $nuevo_producto->talla_id       =   $producto->talla_id;
                        $nuevo_producto->stock          =   0;
                        $nuevo_producto->stock_logico   =   0;
                        $nuevo_producto->save();
                    }
                }

                $lote = ProductoColorTalla::where('producto_id', $producto->producto_id)
                        ->where('color_id', $producto->color_id)
                        ->where('talla_id', $producto->talla_id)
                        ->with('producto')
                        ->with('color')
                        ->with('talla')
                        ->firstOrFail();

                $producto_control                       =   $lote;
                $producto_control->cantidad_solicitada  =   $producto->cantidad;

                //==== CALCULANDO MONTOS PARA EL DETALLE ====
                $importe                =   floatval($producto->cantidad) * floatval($producto->precio_unitario);
                $precio_unitario        =   $producto->porcentaje_descuento==0?$producto->precio_unitario:$producto->precio_unitario_nuevo;

                    Detalle::create([
                        'documento_id'      =>  $documento->id,
                        'producto_id'       =>  $producto->producto_id,
                        'color_id'          =>  $producto->color_id,
                        'talla_id'          =>  $producto->talla_id,
                        'codigo_producto'   =>  $lote->producto->codigo,
                        'nombre_producto'   =>  $lote->producto->nombre,
                        'nombre_color'      =>  $lote->color->descripcion,
                        'nombre_talla'      =>  $lote->talla->descripcion,
                        'nombre_modelo'     =>  $lote->producto->modelo->descripcion,
                        'cantidad'          =>  floatval($producto->cantidad),
                        'precio_unitario'   =>  floatval($producto->precio_unitario),
                        'importe'           =>  $importe,
                        'precio_unitario_nuevo'     =>  floatval($precio_unitario),
                        'porcentaje_descuento'      =>  floatval($producto->porcentaje_descuento),
                        'monto_descuento'           =>  floatval($importe)*floatval($producto->porcentaje_descuento)/100,
                        'importe_nuevo'             =>  floatval($precio_unitario) * floatval($producto->cantidad),  
                        'cantidad_sin_cambio'       => (int) $producto->cantidad

                    ]);
                
                   
                    //====== RESTAR STOCK SI NO ES CONVERSIÓN NI REGULARIZACIÓN =======
                    if(!$request->has('convertir') && !$request->has('regularizar') && !$request->has('facturar')){
                        
                        //===== ACTUALIZANDO STOCK ===========
                        DB::update('UPDATE producto_color_tallas 
                        SET stock = stock - ? 
                        WHERE producto_id = ? AND color_id = ? AND talla_id = ?', 
                        [$producto->cantidad, $producto->producto_id, $producto->color_id, $producto->talla_id]);

                        $nuevo_stock = DB::table('producto_color_tallas')
                                        ->where('producto_id', $producto->producto_id)
                                        ->where('color_id', $producto->color_id)
                                        ->where('talla_id', $producto->talla_id)
                                        ->value('stock');

                        //======= KARDEX CON STOCK YA MODIFICADO =======
                        $kardex = new Kardex();
                        $kardex->origen         =   'VENTA';
                        $kardex->accion         =   'REGISTRO';
                        $kardex->documento_id   =   $documento->id;
                        $kardex->numero_doc     =   $envio_prev['serie_correlativo'];
                        $kardex->fecha          =   $documento->fecha_documento;
                        $kardex->cantidad       =   floatval($producto->cantidad);
                        $kardex->producto_id    =   $producto->producto_id;
                        $kardex->color_id       =   $producto->color_id;
                        $kardex->talla_id       =   $producto->talla_id;
                        $kardex->descripcion    =   $documento->cliente;
                        $kardex->precio         =   $producto->precio_unitario;   
                        $kardex->importe        =   $producto->precio_unitario * $producto->cantidad;
                        $kardex->stock          =   $nuevo_stock;
                        $kardex->save();
                    }

                    if($request->has('convertir')){
                        $nuevo_stock = DB::table('producto_color_tallas')
                        ->where('producto_id', $producto->producto_id)
                        ->where('color_id', $producto->color_id)
                        ->where('talla_id', $producto->talla_id)
                        ->value('stock');

                         //======= KARDEX CON STOCK YA MODIFICADO =======
                         $kardex = new Kardex();
                         $kardex->origen         =   'VENTA';
                         $kardex->accion         =   'CONVERTIR';
                         $kardex->documento_id   =   $documento->id;
                         $kardex->numero_doc     =   $envio_prev['serie_correlativo'];
                         $kardex->fecha          =   $documento->fecha_documento;
                         $kardex->cantidad       =   floatval($producto->cantidad);
                         $kardex->producto_id    =   $producto->producto_id;
                         $kardex->color_id       =   $producto->color_id;
                         $kardex->talla_id       =   $producto->talla_id;
                         $kardex->descripcion    =   $documento->cliente;
                         $kardex->precio         =   $producto->precio_unitario;   
                         $kardex->importe        =   $producto->precio_unitario * $producto->cantidad;
                         $kardex->stock          =   $nuevo_stock;
                         $kardex->save();
                    }
                    
                    if($request->get('regularizar') == 'SI'){
                        $nuevo_stock = DB::table('producto_color_tallas')
                        ->where('producto_id', $producto->producto_id)
                        ->where('color_id', $producto->color_id)
                        ->where('talla_id', $producto->talla_id)
                        ->value('stock');
                        
                        //======= KARDEX CON STOCK YA MODIFICADO =======
                        $kardex = new Kardex();
                        $kardex->origen         =   'VENTA';
                        $kardex->accion         =   'REGULARIZAR';
                        $kardex->documento_id   =   $documento->id;
                        $kardex->numero_doc     =   $envio_prev['serie_correlativo'];
                        $kardex->fecha          =   $documento->fecha_documento;
                        $kardex->cantidad       =   floatval($producto->cantidad);
                        $kardex->producto_id    =   $producto->producto_id;
                        $kardex->color_id       =   $producto->color_id;
                        $kardex->talla_id       =   $producto->talla_id;
                        $kardex->descripcion    =   $documento->cliente;
                        $kardex->precio         =   $producto->precio_unitario;   
                        $kardex->importe        =   $producto->precio_unitario * $producto->cantidad;
                        $kardex->stock          =   $nuevo_stock;
                        $kardex->save();
                    }     
            }
           
            
            //======== GUARDANDO DATA DE ENVIO =========
            //======= CONTROL PARA EVITAR QUE SE GENERE DATA ENVIO AL EDITAR O CONVERTIR DOC VENTA O FACTURAR PEDIDO ==========
            if($request->has('data_envio') && !$request->has('facturar')){

                $data_envio     =   json_decode($request->get('data_envio'));
           
                if (!empty((array)$data_envio)) {
                    $envio_venta                        =   new EnvioVenta();
                    $envio_venta->documento_id          =   $documento->id;
                    $envio_venta->departamento          =   $data_envio->departamento->nombre;
                    $envio_venta->provincia             =   $data_envio->provincia->text;
                    $envio_venta->distrito              =   $data_envio->distrito->text;
                    $envio_venta->empresa_envio_id      =   $data_envio->empresa_envio->id;
                    $envio_venta->empresa_envio_nombre  =   $data_envio->empresa_envio->empresa;
                    $envio_venta->sede_envio_id         =   $data_envio->sede_envio->id;
                    $envio_venta->sede_envio_nombre     =   $data_envio->sede_envio->direccion;
                    $envio_venta->tipo_envio            =   $data_envio->tipo_envio->descripcion;
                    $envio_venta->destinatario_tipo_doc =   $data_envio->destinatario->tipo_documento;
                    $envio_venta->destinatario_nro_doc  =   $data_envio->destinatario->nro_documento;
                    $envio_venta->destinatario_nombre   =   $data_envio->destinatario->nombres;
                    $envio_venta->cliente_id            =   $documento->cliente_id;
                    $envio_venta->cliente_nombre        =   $documento->cliente;
                    $envio_venta->tipo_pago_envio       =   $data_envio->tipo_pago_envio->descripcion;
                    $envio_venta->monto_envio           =   $documento->monto_envio;
                    $envio_venta->entrega_domicilio     =   $data_envio->entrega_domicilio?"SI":"NO";
                    $envio_venta->direccion_entrega     =   $data_envio->direccion_entrega;
                    $envio_venta->documento_nro         =   $envio_prev['serie_correlativo'];
                    $envio_venta->fecha_envio_propuesta =   $data_envio->fecha_envio_propuesta;
                    $envio_venta->origen_venta          =   $data_envio->origen_venta->descripcion;
                    $envio_venta->obs_rotulo            =   $data_envio->obs_rotulo;
                    $envio_venta->obs_despacho          =   $data_envio->obs_despacho;
                    $envio_venta->cliente_celular       =   $documento->clienteEntidad->telefono_movil;
                    $envio_venta->user_vendedor_id      =   $documento->user_id;
                    $envio_venta->user_vendedor_nombre  =   $documento->user->usuario;
                    $envio_venta->save();
                }else{
                   
                    
                    //======== OBTENER EMPRESA ENVÍO =======
                    $empresa_envio                      =   DB::select('select ee.id,ee.empresa,ee.tipo_envio
                                                            from empresas_envio as ee')[0];
                    
                    $sede_envio                         =   DB::select('select ees.id,ees.direccion 
                                                            from empresa_envio_sedes as ees
                                                            where ees.empresa_envio_id=?',[$empresa_envio->id])[0];
                
                    $envio_venta                        =   new EnvioVenta();
                    $envio_venta->documento_id          =   $documento->id;
                    $envio_venta->departamento          =   "LA LIBERTAD";
                    $envio_venta->provincia             =   "TRUJILLO";
                    $envio_venta->distrito              =   "TRUJILLO";
                    $envio_venta->empresa_envio_id      =   $empresa_envio->id;
                    $envio_venta->empresa_envio_nombre  =   $empresa_envio->empresa;
                    $envio_venta->sede_envio_id         =   $sede_envio->id;
                    $envio_venta->sede_envio_nombre     =   $sede_envio->direccion;
                    $envio_venta->tipo_envio            =   $empresa_envio->tipo_envio;
                    $envio_venta->destinatario_tipo_doc =   $documento->tipo_documento_cliente;
                    $envio_venta->destinatario_nro_doc  =   $documento->documento_cliente;
                    $envio_venta->destinatario_nombre   =   $documento->cliente;
                    $envio_venta->cliente_id            =   $documento->cliente_id;
                    $envio_venta->cliente_nombre        =   $documento->cliente;
                    $envio_venta->tipo_pago_envio       =   "-";
                    $envio_venta->monto_envio           =   $documento->monto_envio;
                    $envio_venta->entrega_domicilio     =   "NO";
                    $envio_venta->direccion_entrega     =   null;
                    $envio_venta->documento_nro         =   $envio_prev['serie_correlativo'];
                    $envio_venta->fecha_envio_propuesta =   null;
                    $envio_venta->origen_venta          =   "WHATSAPP";
                    $envio_venta->obs_despacho          =   null;
                    $envio_venta->obs_rotulo            =   null;
                    $envio_venta->estado                =   'DESPACHADO';
                    $envio_venta->cliente_celular       =   $documento->clienteEntidad->telefono_movil;
                    $envio_venta->user_vendedor_id      =   $documento->user_id;
                    $envio_venta->user_vendedor_nombre  =   $documento->user->usuario;
                    $envio_venta->user_despachador_id   =   $documento->user_id;
                    $envio_venta->user_despachador_nombre   =   $documento->user->usuario;
                    $envio_venta->save();
                }

            }
            
         
            // if ($request->convertir) {
            //     $doc_a_convertir = Documento::find($request->convertir);
            //     $doc_a_convertir->convertir = $documento->id;
            //     $doc_a_convertir->update();

            //     $documento = Documento::find($documento->id);
            //     $documento->estado = $doc_a_convertir->estado;
            //     $documento->estado_pago = $doc_a_convertir->estado_pago;
            //     $documento->fecha_documento = Carbon::now()->toDateString();
            //     $documento->convertir = $doc_a_convertir->id;
            //     $documento->importe = $doc_a_convertir->importe;
            //     $documento->efectivo = $doc_a_convertir->efectivo;
            //     $documento->tipo_pago_id = $doc_a_convertir->tipo_pago_id;

            //     $documento->update();
            // }


            //$detalle->mcaja_id      = movimientoUser()->id;
           

            ////$envio_prev =   $this->ObtenerCorrelativoVentas($documento);
            //$envio_prev =   self::sunat($documento->id);
            // if (!$envio_prev['success']) {
            //      DB::rollBack();
            //      return response()->json([
            //          'success' => false,
            //          'mensaje' => $envio_prev['mensaje'],
            //      ]);
            // }

            // if ($request->tipo_venta == '127' && $cliente->agente_retencion == '1' && $documento->total >= $cliente->monto_mayor) {
            //      self::generarComprobanteRetencion($documento->id);
            // }

            $documento = Documento::find($documento->id);
            $documento->nombre_comprobante_archivo = $documento->serie . '-' . $documento->correlativo . '.pdf';
            $documento->update();
            

            //========= REGISTRO DE ACTIVIDAD =========
            $descripcion = "SE AGREGÓ EL DOCUMENTO DE VENTA CON LA FECHA: " . Carbon::parse($documento->fecha_documento)->format('d/m/y');
            $gestion = "DOCUMENTO DE VENTA";

            if($request->get('regularizar') == 'SI'){
                $gestion = "DOCUMENTO DE VENTA REGULARIZADO";
            }
            if($request->has('convertir')){
                $gestion = "DOCUMENTO DE VENTA CONVERTIDO";
            }
            crearRegistro($documento, $descripcion, $gestion);

            if ((int) $documento->tipo_venta == 127 || (int) $documento->tipo_venta == 128) {
                $dato = 'Actualizar';
                broadcast(new VentasCajaEvent($dato));
                DB::commit();
                if ($request->envio_sunat) {
                    $envio_ = self::sunat_valida($documento->id);
                }
                Session::flash('success', 'Documento de venta creado.');


                return response()->json([
                    'success' => true,
                    'documento_id' => $documento->id,
                ]);
            } else {
                 $dato = 'Actualizar';
                 broadcast(new VentasCajaEvent($dato));
                DB::commit();
                 //$vp = self::venta_comprobante($documento->id);
                 //$ve = self::venta_email($documento->id);
                 // Session::flash('success', 'Documento de venta creado.');
                 return response()->json([
                     'success' => true,
                     'documento_id' => $documento->id,
                 ]);
            
            //DB::commit();
            }
            return response()->json([
                'success' => false,
                'mensaje' => "exito",
            ]);
            
        } catch (Exception $e) {

           
            //====== REVERTIR ACCIONES EN LA BD =========
            DB::rollBack();
            
         
            //========== DETALLAR ERROR DE STOCK NEGATIVO ========
            if($e->getCode() == "22003" && strpos($e->getMessage(), "UPDATE producto_color_tallas") !== false && strpos($e->getMessage(), "SET stock") !== false){

                return response()->json([
                    'success'       =>  false,
                    'mensaje'       =>  "ERROR EN EL SERVIDOR", 
                    'excepcion'     =>  $e->getMessage(),
                    'codigo'        =>  $e->getCode(),
                    'producto'      =>  $producto_control
                ]);

            }

            return response()->json([
                'success'   => false,
                'mensaje'   => "ERROR EN EL SERVIDOR", 
                'excepcion' => $e->getMessage(),
                'codigo'    => $e->getCode()
            ]);
        }
    }

    public function generarComprobanteRetencion($id)
    {
        $documento = Documento::findOrFail($id);
        $impRetenido = $documento->total * ($documento->clienteEntidad->tasa_retencion / 100);
        $impPagar = $documento->total - $impRetenido;
        $documento->total = $impPagar;
        $documento->update();
        //REGISTROO COMPROBANTE RETENCION
        $retencion = new Retencion();
        $retencion->documento_id = $documento->id;
        $retencion->fechaEmision = $documento->fecha_documento;

        $retencion->ruc = $documento->ruc_empresa;
        $retencion->razonSocial = $documento->empresa;
        $retencion->nombreComercial = $documento->empresa;
        $retencion->direccion_empresa = $documento->direccion_fiscal_empresa;
        //UBIGEO EMPRESA
        $ubigeo = Distrito::find($documento->empresaEntidad->ubigeo);
        $retencion->provincia_empresa = $ubigeo ? $ubigeo->provincia : 'TRUJILLO';
        $retencion->departamento_empresa = $ubigeo ? $ubigeo->departamento : 'LA LIBERTAD';
        $retencion->distrito_empresa = $ubigeo ? $ubigeo->nombre : 'TRUJILLO';
        $retencion->ubigeo_empresa = $ubigeo->id;

        $retencion->tipoDoc = $documento->tipoDocumentoCliente();
        $retencion->numDoc = $documento->documento_cliente;
        $retencion->rznSocial = $documento->cliente;
        //UBIGEO CLIENTE
        $ubigeo_cliente = Distrito::find($documento->clienteEntidad->distrito_id);
        $retencion->direccion_proveedor = $documento->direccion_cliente;
        $retencion->provincia_proveedor = $ubigeo_cliente ? $ubigeo_cliente->provincia : 'TRUJILLO';
        $retencion->departamento_proveedor = $ubigeo_cliente ? $ubigeo_cliente->departamento : 'LA LIBERTAD';
        $retencion->distrito_proveedor = $ubigeo_cliente ? $ubigeo_cliente->nombre : 'TRUJILLO';
        $retencion->ubigeo_proveedor = $ubigeo_cliente->id;

        $retencion->observacion = $documento->cliente . ' - ' . $impPagar;
        $retencion->impRetenido = $impRetenido;
        $retencion->impPagado = $impPagar;
        $retencion->regimen = '01';
        $retencion->tasa = $documento->clienteEntidad->tasa_retencion;
        $retencion->save();

        $retencion_detalle = new RetencionDetalle();
        $retencion_detalle->retencion_id = $retencion->id;
        $retencion_detalle->documento_id = $documento->id;
        $retencion_detalle->tipoDoc = $documento->tipoDocumento();
        $retencion_detalle->numDoc = $documento->serie . '-' . $documento->correlativo;
        $retencion_detalle->fechaEmision = $documento->fecha_documento;
        $retencion_detalle->fechaRetencion = $documento->fecha_documento;
        $retencion_detalle->moneda = $documento->simboloMoneda();
        $retencion_detalle->impTotal = $impPagar + $impRetenido;
        $retencion_detalle->impPagar = $impPagar;
        $retencion_detalle->impRetenido = $impRetenido;
        $retencion_detalle->moneda_pago = $documento->simboloMoneda();
        $retencion_detalle->importe_pago = $impPagar + $impRetenido;
        $retencion_detalle->fecha_pago = $documento->fecha_documento;
        $retencion_detalle->fecha_tipo_cambio = $documento->fecha_documento;
        $retencion_detalle->factor = 1;
        $retencion_detalle->monedaObj = $documento->simboloMoneda();
        $retencion_detalle->monedaRef = $documento->simboloMoneda();
        $retencion_detalle->save();

        self::obtenerCorrelativo($retencion);
    }

    public function obtenerCorrelativo($retencion)
    {
        if (empty($retencion->correlativo)) {
            $serie_comprobantes = DB::table('retencions')
                ->join('cotizacion_documento', 'cotizacion_documento.id', '=', 'retencions.documento_id')
                ->join('empresas', 'cotizacion_documento.empresa_id', '=', 'empresas.id')
                ->where('cotizacion_documento.empresa_id', $retencion->documento->empresa_id)
                ->where('cotizacion_documento.tipo_venta', '127')
                ->select('retencions.*')
                ->orderBy('retencions.correlativo', 'DESC')
                ->get();

            if (count($serie_comprobantes) == 1) {
                //OBTENER EL DOCUMENTO INICIADO
                $retencion->correlativo = 1;
                $retencion->serie = 'R001'; //$numeracion->serie;
                $retencion->update();
            } else {
                //NOTA ES NUEVO EN SUNAT
                if ($retencion->sunat != '1') {
                    $ultimo_comprobante = $serie_comprobantes->first();
                    $retencion->correlativo = $ultimo_comprobante->correlativo + 1;
                    $retencion->serie = 'R001'; //$numeracion->serie;
                    $retencion->update();
                }
            }
        }
    }

    public function edit($id)
    {
        $this->authorize('haveaccess', 'documento_venta.index');
        $empresas       =   Empresa::where('estado', 'ACTIVO')->get();
        $clientes       =   Cliente::where('estado', 'ACTIVO')->get();
        $productos      =   Producto::where('estado', 'ACTIVO')->get();
        $tallas         =   Talla::where('estado','ACTIVO')->get();
        $modelos        =   Modelo::where('estado','ACTIVO')->get();
        $documento      =   Documento::findOrFail($id);
        $detalles       =   Detalle::where('documento_id', $id)->where('estado', 'ACTIVO')->with(['lote', 'lote.producto'])->get();
        $condiciones    =   Condicion::where('estado', 'ACTIVO')->get();
        $fullaccess     =   false;
        $fecha_hoy      =   Carbon::now()->toDateString();

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
        return view('ventas.documentos.edit', [
            'documento'     =>  $documento,
            'detalles'      =>  $detalles,
            'empresas'      =>  $empresas,
            'clientes'      =>  $clientes,
            'productos'     =>  $productos,
            'condiciones'   =>  $condiciones,
            'fullaccess'    =>  $fullaccess,
            'fecha_hoy'     =>  $fecha_hoy,
            'tallas'        =>  $tallas,
            'modelos'       =>  $modelos,
            'departamentos'     =>  departamentos()
        ]);
    }

    public function venta_comprobante($id)
    {
        try {
            $documento = Documento::find($id);
            $detalles = Detalle::where('estado', 'ACTIVO')->where('documento_id', $id)->get();
            $empresa = Empresa::findOrFail($documento->empresa_id);

            $legends = self::obtenerLeyenda($documento);
            $legends = json_encode($legends, true);
            $legends = json_decode($legends, true);

            if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantessiscom'))) {
                mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantessiscom'));
            }
            $pdf_condicion = $empresa->condicion === '1' ? 'comprobante_normal_nuevo' : 'comprobante_normal';

            PDF::loadview('ventas.documentos.impresion.' . $pdf_condicion, [
                'documento' => $documento,
                'detalles' => $detalles,
                'moneda' => $documento->simboloMoneda(),
                'empresa' => $empresa,
                "legends" => $legends,
            ])->setPaper('a4')->setWarnings(false)
                ->save(public_path() . '/storage/comprobantessiscom/' . $documento->nombre_comprobante_archivo);

            return array('success' => true, 'mensaje' => 'Documento validado.');
        } catch (Exception $e) {
            $documento = Documento::find($id);

            $errorVenta = new ErrorVenta();
            $errorVenta->documento_id = $documento->id;
            $errorVenta->tipo = 'pdf';
            $errorVenta->descripcion = 'Error al generar pdf';
            $errorVenta->ecxepcion = $e->getMessage();
            $errorVenta->save();
            return array('success' => false, 'mensaje' => 'Documento no validado.');
        }
    }

    public function venta_email($id)
    {
        try {
            $documento = Documento::find($id);

            if ((int) $documento->tipo_venta === 127 || (int) $documento->tipo_venta === 128) {
                if ($documento->clienteEntidad->correo_electronico) {
                    Mail::send('ventas.documentos.mail.cliente_mail', compact("documento"), function ($mail) use ($documento) {
                        $mail->to($documento->clienteEntidad->correo_electronico);
                        $mail->subject('SISCOM ' . $documento->nombreDocumento());
                        $mail->attach(storage_path('app/public/comprobantessiscom/' . $documento->nombre_comprobante_archivo), [
                            'foto' => '' . $documento->nombre_comprobante_archivo,
                        ]);
                        $mail->attach(storage_path('app/public/xml/' . $documento->xml), [
                            'foto' => '' . $documento->xml,
                        ]);
                        $mail->from('developer.limpiecito@gmail.com', 'SISCOM');
                    });
                }
            } else {
                if ($documento->clienteEntidad->correo_electronico) {
                    Mail::send('ventas.documentos.mail.cliente_mail', compact("documento"), function ($mail) use ($documento) {
                        $mail->to($documento->clienteEntidad->correo_electronico);
                        $mail->subject('SISCOM ' . $documento->nombreDocumento());
                        $mail->attach(storage_path('app/public/comprobantessiscom/' . $documento->nombre_comprobante_archivo), [
                            'foto' => '' . $documento->nombre_comprobante_archivo,
                        ]);
                        $mail->from('developer.limpiecito@gmail.com', 'SISCOM');
                    });
                }
            }

            return array('success' => true, 'mensaje' => 'Documento validado.');
        } catch (Exception $e) {
            $documento = Documento::find($id);

            $errorVenta = new ErrorVenta();
            $errorVenta->documento_id = $documento->id;
            $errorVenta->tipo = 'email';
            $errorVenta->descripcion = 'Error al enviar email';
            $errorVenta->ecxepcion = $e->getMessage();
            $errorVenta->save();
            return array('success' => false, 'mensaje' => 'Documento no validado.');
        }
    }

    public function destroy($id)
    {
        $this->authorize('haveaccess', 'documento_venta.index');
        $documento = Documento::findOrFail($id);
        $documento->estado = 'ANULADO';
        $documento->update();

        $detalles = Detalle::where('documento_id', $id)->where('estado', 'ACTIVO')->get();
        foreach ($detalles as $detalle) {
            //ANULAMOS EL DETALLE
            $detalle->eliminado = "0";
            $detalle->update();
            $lote = LoteProducto::find($detalle->lote_id);
            $cantidad = $detalle->cantidad - $detalle->detalles->sum('cantidad');
            $lote->cantidad = $lote->cantidad + $cantidad;
            $lote->cantidad_logica = $lote->cantidad_logica + $cantidad;
            $lote->update();
        }

        //Registro de actividad
        $descripcion = "SE ELIMINÓ EL DOCUMENTO DE VENTA CON LA FECHA: " . Carbon::parse($documento->fecha_documento)->format('d/m/y');
        $gestion = "DOCUMENTO DE VENTA";
        eliminarRegistro($documento, $descripcion, $gestion);

        Session::flash('success', 'Documento de Venta eliminada.');
        return redirect()->route('ventas.documento.index')->with('eliminar', 'success');
    }

    public function show($id)
    {
        $this->authorize('haveaccess', 'documento_venta.index');
        $documento = Documento::findOrFail($id);
        $nombre_completo = $documento->user->persona->apellido_paterno . ' ' . $documento->user->persona->apellido_materno . ' ' . $documento->user->persona->nombres;
        $detalles = Detalle::where('documento_id', $id)->where('estado', 'ACTIVO')->get();
        //TOTAL EN LETRAS
        $formatter = new NumeroALetras();
        $convertir = $formatter->toInvoice($documento->total, 2, 'SOLES');

        return view('ventas.documentos.show', [
            'documento' => $documento,
            'detalles' => $detalles,
            'nombre_completo' => $nombre_completo,
            'cadena_valor' => $convertir,
        ]);
    }

    public function report($id)
    {
        $documento = Documento::findOrFail($id);
        $nombre_completo = $documento->user->persona->apellido_paterno . ' ' . $documento->user->persona->apellido_materno . ' ' . $documento->user->persona->nombres;
        $detalles = Detalle::where('documento_id', $id)->where('estado', 'ACTIVO')->get();
        $subtotal = 0;
        $igv = '';
        $tipo_moneda = '';
        foreach ($detalles as $detalle) {
            $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
        }
        foreach (tipos_moneda() as $moneda) {
            if ($moneda->descripcion == $documento->moneda) {
                $tipo_moneda = $moneda->simbolo;
            }
        }

        if (!$documento->igv) {
            $igv = $subtotal * 0.18;
            $total = $subtotal + $igv;
            $decimal_subtotal = number_format($subtotal, 2, '.', '');
            $decimal_total = number_format($total, 2, '.', '');
            $decimal_igv = number_format($igv, 2, '.', '');
        } else {
            $calcularIgv = $documento->igv / 100;
            $base = $subtotal / (1 + $calcularIgv);
            $nuevo_igv = $subtotal - $base;
            $decimal_subtotal = number_format($base, 2, '.', '');
            $decimal_total = number_format($subtotal, 2, '.', '');
            $decimal_igv = number_format($nuevo_igv, 2, '.', '');
        }

        $presentaciones = presentaciones();
        $paper_size = array(0, 0, 360, 360);
        $pdf = PDF::loadview('compras.documentos.reportes.detalle', [
            'documento' => $documento,
            'nombre_completo' => $nombre_completo,
            'detalles' => $detalles,
            'presentaciones' => $presentaciones,
            'subtotal' => $decimal_subtotal,
            'moneda' => $tipo_moneda,
            'igv' => $decimal_igv,
            'total' => $decimal_total,
        ])->setPaper('a4')->setWarnings(false);
        return $pdf->stream();
    }

    public function TypePay($id)
    {
        DB::table('cotizacion_documento_pago_detalle_cajas')
            ->join('cotizacion_documento_pagos', 'cotizacion_documento_pagos.id', '=', 'cotizacion_documento_pago_detalle_cajas.pago_id')
            ->join('cotizacion_documento_pago_cajas', 'cotizacion_documento_pago_cajas.id', '=', 'cotizacion_documento_pago_detalle_cajas.caja_id')
            ->select('cotizacion_documento_pago_cajas.*', 'cotizacion_documento_pagos.*')
            ->where('cotizacion_documento_pagos.documento_id', '=', $id)
        // //ANULAR
            ->where('cotizacion_documento_pagos.estado', '!=', 'ANULADO')
            ->update(['cotizacion_documento_pago_cajas.estado' => 'ANULADO']);

        //TIPO DE DOCUMENTO
        $documento = Documento::findOrFail($id);
        $documento->tipo_pago = null;
        $documento->estado = 'PENDIENTE';
        $documento->update();

        Session::flash('success', 'Tipo de pagos anulados, puede crear nuevo pago.');
        return redirect()->route('ventas.documento.index')->with('exitosa', 'success');
    }

    public function obtenerFecha($documento)
    {
        $date = strtotime($documento->fecha_documento);
        $fecha_emision = date('Y-m-d', $date);
        $hora_emision = date('H:i:s', $date);
        $fecha = $fecha_emision . 'T' . $hora_emision . '-05:00';

        return $fecha;
    }

    public function voucher($value)
    {   

        try {
            $cadena = explode('-', $value);
            $id     = $cadena[0];
            $size   = (int) $cadena[1];
            $qr     = self::qr_code($id);

            $mostrar_cuentas    =   DB::select('select c.propiedad 
                                    from configuracion as c 
                                    where c.slug = "MCB"')[0]->propiedad;
            
            $documento = Documento::findOrFail($id);
            $detalles = Detalle::where('documento_id', $id)->where('eliminado', '0')->get();
            if ((int) $documento->tipo_venta == 127 || (int) $documento->tipo_venta == 128) {
                if ($documento->sunat == '0' || $documento->sunat == '2') {
                   
                    //ARREGLO COMPROBANTE
                    // $arreglo_comprobante = array(
                    //     "tipoOperacion" => $documento->tipoOperacion(),
                    //     "tipoDoc" => $documento->tipoDocumento(),
                    //     "serie" => '000',
                    //     "correlativo" => '000',
                    //     "fechaEmision" => self::obtenerFecha($documento),
                    //     "observacion" => $documento->observacion,
                    //     "tipoMoneda" => $documento->simboloMoneda(),
                    //     "client" => array(
                    //         "tipoDoc" => $documento->tipoDocumentoCliente(),
                    //         "numDoc" => $documento->documento_cliente,
                    //         "rznSocial" => $documento->cliente,
                    //         "address" => array(
                    //             "direccion" => $documento->direccion_cliente,
                    //         ),
                    //     ),
                    //     "company" => array(
                    //         "ruc" => $documento->ruc_empresa,
                    //         "razonSocial" => $documento->empresa,
                    //         "address" => array(
                    //             "direccion" => $documento->direccion_fiscal_empresa,
                    //         ),
                    //     ),
                    //     "mtoOperGravadas" => $documento->sub_total,
                    //     "mtoOperExoneradas" => 0,
                    //     "mtoIGV" => $documento->total_igv,

                    //     "valorVenta" => $documento->sub_total,
                    //     "totalImpuestos" => $documento->total_igv,
                    //     "mtoImpVenta" => $documento->total,
                    //     "ublVersion" => "2.1",
                    //     "details" => self::obtenerProductos($documento->id),
                    //     "legends" => self::obtenerLeyenda($documento),
                    // );

                    // $comprobante = json_encode($arreglo_comprobante);
                    
                    //$data = generarComprobanteapi($comprobante, $documento->empresa_id);
                    $name = $documento->id . '.pdf';
                    $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantes' . DIRECTORY_SEPARATOR . $name);
                    if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantes'))) {
                        mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantes'));
                    }
                    //file_put_contents($pathToFile, $data);
                    //return response()->file($pathToFile);
                    $empresa            =   Empresa::first();
             

                    $legends = self::obtenerLeyenda($documento);
                    $legends = json_encode($legends, true);
                    $legends = json_decode($legends, true);
                    if ($size === 80) {
                        $pdf = PDF::loadview('ventas.documentos.impresion.comprobante_ticket', [
                            'documento'         =>  $documento,
                            'detalles'          =>  $detalles,
                            'moneda'            =>  $documento->simboloMoneda(),
                            'empresa'           =>  $empresa,
                            "legends"           =>  $legends,
                            'mostrar_cuentas'   =>  $mostrar_cuentas
                        ])->setPaper([0, 0, 226.772, 651.95]);
                        return $pdf->stream($documento->serie . '-' . $documento->correlativo . '.pdf');
                    } else {
                        $pdf_condicion = $empresa->condicion == '1' ? 'comprobante_normal_nuevo' : 'comprobante_normal';
                        $pdf = PDF::loadview('ventas.documentos.impresion.' . $pdf_condicion, [
                            'documento'         =>  $documento,
                            'detalles'          =>  $detalles,
                            'moneda'            =>  $documento->simboloMoneda(),
                            'empresa'           =>  $empresa,
                            "legends"           =>  $legends,
                            'mostrar_cuentas'   =>  $mostrar_cuentas
                        ])->setPaper('a4')->setWarnings(false);

                        return $pdf->stream($documento->serie . '-' . $documento->correlativo . '.pdf');
                    }
                } else {
                    
                    //OBTENER CORRELATIVO DEL COMPROBANTE ELECTRONICO
                    //$comprobante = event(new ComprobanteRegistrado($documento, $documento->serie));
                    //ENVIAR COMPROBANTE PARA LUEGO GENERAR PDF
                    //$data = generarComprobanteapi($comprobante[0], $documento->empresa_id);
                    $name = $documento->id . '.pdf';
                    $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantes' . DIRECTORY_SEPARATOR . $name);
                    if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantes'))) {
                        mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantes'));
                    }
                    //file_put_contents($pathToFile, $data);

                    $empresa = Empresa::first();

                    $legends = self::obtenerLeyenda($documento);
                    $legends = json_encode($legends, true);
                    $legends = json_decode($legends, true);

                    if ($size === 80) {
                        $pdf = PDF::loadview('ventas.documentos.impresion.comprobante_ticket', [
                            'documento' => $documento,
                            'detalles' => $detalles,
                            'moneda' => $documento->simboloMoneda(),
                            'empresa' => $empresa,
                            "legends" => $legends,
                            'mostrar_cuentas'   =>  $mostrar_cuentas
                        ])->setPaper([0, 0, 226.772, 651.95]);
                        return $pdf->stream($documento->serie . '-' . $documento->correlativo . '.pdf');
                    } else {
                        $pdf_condicion = $empresa->condicion === '1' ? 'comprobante_normal_nuevo' : 'comprobante_normal';
                        $pdf = PDF::loadview('ventas.documentos.impresion.' . $pdf_condicion, [
                            'documento' => $documento,
                            'detalles' => $detalles,
                            'moneda' => $documento->simboloMoneda(),
                            'empresa' => $empresa,
                            "legends" => $legends,
                            'mostrar_cuentas'   =>  $mostrar_cuentas
                        ])->setPaper('a4')->setWarnings(false);

                        return $pdf->stream($documento->serie . '-' . $documento->correlativo . '.pdf');
                    }
                }
            } else {

                if (empty($documento->correlativo)) {
                    event(new DocumentoNumeracion($documento));
                }
                $empresa = Empresa::first();

                $legends = self::obtenerLeyenda($documento);
                $legends = json_encode($legends, true);
                $legends = json_decode($legends, true);

                if ($size === 80) {
                    $pdf = PDF::loadview('ventas.documentos.impresion.comprobante_ticket', [
                        'documento' => $documento,
                        'detalles' => $detalles,
                        'moneda' => $documento->simboloMoneda(),
                        'empresa' => $empresa,
                        "legends" => $legends,
                        'mostrar_cuentas'   =>  $mostrar_cuentas
                    ])->setPaper([0, 0, 226.772, 651.95]);
                    return $pdf->stream($documento->serie . '-' . $documento->correlativo . '.pdf');
                } else {
                    $pdf_condicion = $empresa->condicion === '1' ? 'comprobante_normal_nuevo' : 'comprobante_normal';
                    $pdf = PDF::loadview('ventas.documentos.impresion.' . $pdf_condicion, [
                        'documento' => $documento,
                        'detalles' => $detalles,
                        'moneda' => $documento->simboloMoneda(),
                        'empresa' => $empresa,
                        "legends" => $legends,
                        'mostrar_cuentas'   =>  $mostrar_cuentas
                    ])->setPaper('a4')->setWarnings(false);

                    return $pdf->stream($documento->serie . '-' . $documento->correlativo . '.pdf');
                }
            }
        } catch (Exception $e) {
            $cadena = explode('-', $value);
            $id = $cadena[0];
            $size = (int) $cadena[1];
            $documento = Documento::findOrFail($id);
            $detalles = Detalle::where('documento_id', $id)->where('estado', 'ACTIVO')->get();
            $empresa = Empresa::first();

            $legends = self::obtenerLeyenda($documento);
            $legends = json_encode($legends, true);
            $legends = json_decode($legends, true);

            if ($size === 80) {
                $pdf = PDF::loadview('ventas.documentos.impresion.comprobante_ticket', [
                    'documento'         => $documento,
                    'detalles'          => $detalles,
                    'moneda'            => $documento->simboloMoneda(),
                    'empresa'           => $empresa,
                    "legends"           => $legends,
                    'mostrar_cuentas'   =>  $mostrar_cuentas

                ])->setPaper([0, 0, 226.772, 651.95]);
                return $pdf->stream($documento->serie . '-' . $documento->correlativo . '.pdf');
            } else {
                $pdf_condicion = $empresa->condicion === '1' ? 'comprobante_normal_nuevo' : 'comprobante_normal';
                $pdf = PDF::loadview('ventas.documentos.impresion.' . $pdf_condicion, [
                    'documento' => $documento,
                    'detalles' => $detalles,
                    'moneda' => $documento->simboloMoneda(),
                    'empresa' => $empresa,
                    "legends" => $legends,
                    'mostrar_cuentas'   =>  $mostrar_cuentas
                ])->setPaper('a4')->setWarnings(false);

                return $pdf->stream($documento->serie . '-' . $documento->correlativo . '.pdf');
            }
        }
    }

    public function xml($id){
        try {
            $documento      =   Documento::find($id);
            $nombreArchivo  =   basename($documento->ruta_xml);
            
            if (!file_exists($documento->ruta_xml)) {
                throw new \Exception("El archivo XML del DOCUMENTO: ".$documento->serie.'-'.$documento->correlativo. " ,no existe en la ruta especificada");
            }
    
            $headers = [
                'Content-Type' => 'text/xml',
            ];
        
            return Response::download($documento->ruta_xml, $nombreArchivo, $headers);
        } catch (\Throwable $th) {
            Session::flash('doc_error_get_xml',$th->getMessage());
            return back();
        }
       
    }

    // public function xml($id)
    // {

    //     $documento = Documento::findOrFail($id);
    //     if ((int) $documento->tipo_venta === 127 || (int) $documento->tipo_venta === 128) {
    //         if ($documento->sunat == '0' || $documento->sunat == '2') {
    //             //ARREGLO COMPROBANTE
    //             $arreglo_comprobante = array(
    //                 "tipoOperacion" => $documento->tipoOperacion(),
    //                 "tipoDoc" => $documento->tipoDocumento(),
    //                 "serie" => '000',
    //                 "correlativo" => '000',
    //                 "fechaEmision" => self::obtenerFecha($documento),
    //                 "observacion" => $documento->observacion,
    //                 "tipoMoneda" => $documento->simboloMoneda(),
    //                 "client" => array(
    //                     "tipoDoc" => $documento->tipoDocumentoCliente(),
    //                     "numDoc" => $documento->documento_cliente,
    //                     "rznSocial" => $documento->cliente,
    //                     "address" => array(
    //                         "direccion" => $documento->direccion_cliente,
    //                     ),
    //                 ),
    //                 "company" => array(
    //                     "ruc" =>  $documento->ruc_empresa,
    //                     "razonSocial" => $documento->empresa,
    //                     "address" => array(
    //                         "direccion" => $documento->direccion_fiscal_empresa,
    //                         "provincia" =>  "TRUJILLO",
    //                         "departamento"=> "LA LIBERTAD",
    //                         "distrito"=> "TRUJILLO",
    //                         "ubigueo"=> "130101"
    //                     )),
    //                 //"mtoOperGravadas" => (float)$documento->sub_total,
    //                 "mtoOperGravadas" => (float)$documento->total, //=== nuestro subtotal ===
    //                 "mtoOperExoneradas" => 0,
    //                 "mtoIGV" => (float)$documento->total_igv,
    //                 // "valorVenta" => (float)$documento->sub_total,
    //                 "valorVenta" => (float)$documento->total, //=== nuestro subtotal ===
    //                 "totalImpuestos" => (float)$documento->total_igv,
    //                 // "subTotal" => (float)$documento->total + ($documento->retencion ? $documento->retencion->impRetenido : 0),
    //                 // "mtoImpVenta" => (float)$documento->total + ($documento->retencion ? $documento->retencion->impRetenido : 0),
    //                 "subTotal" => (float)$documento->total_pagar + ($documento->retencion ? $documento->retencion->impRetenido : 0),
    //                 "mtoImpVenta" => (float)$documento->total_pagar + ($documento->retencion ? $documento->retencion->impRetenido : 0),
    //                 // "sumDsctoGlobal" => (float)$documento->monto_descuento,

    //                 "ublVersion" => "2.1",
    //                 "details" => self::obtenerProductos($documento->id),
    //                 "legends" => self::obtenerLeyenda($documento),
    //             );

    //             $comprobante = json_encode($arreglo_comprobante);
    //             $data = generarXmlapi($comprobante, $documento->empresa_id);
    //             $name = $documento->serie . '-' . $documento->correlativo . '.xml';
    //             $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . $name);
    //             if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml'))) {
    //                 mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml'));
    //             }
    //             file_put_contents($pathToFile, $data);

    //             //$ruta = public_path() . '/storage/xml/' . $name;
    //             $ruta   =   $pathToFile;
                
    //             return response()->download($ruta);
    //             // return response()->file($pathToFile);

    //         } else {

    //             //OBTENER CORRELATIVO DEL COMPROBANTE ELECTRONICO
    //             $comprobante = event(new ComprobanteRegistrado($documento, $documento->serie));
    //             //ENVIAR COMPROBANTE PARA LUEGO GENERAR XML
    //             $data = generarXmlapi($comprobante[0], $documento->empresa_id);
    //             $name = $documento->serie . '-' . $documento->correlativo . '.xml';
    //             $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . $name);
    //             if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml'))) {
    //                 mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml'));
    //             }
    //             file_put_contents($pathToFile, $data);
    //             //$ruta = public_path() . '/storage/xml/' . $name;
    //             $ruta   =   $pathToFile;

    //             return response()->download($ruta);
    //             //return response()->file($pathToFile);
    //         }
    //     } else {
    //         Session::flash('error', 'Este documento no retorna este formato.');
    //         return back();
    //     }
    // }

    public function qr_code($id)
    {
        try {
            $documento = Documento::findOrFail($id);
            $name_qr = '';

            if ($documento->contingencia == '0') {
                $name_qr = $documento->serie . "-" . $documento->correlativo . '.svg';
            } else {
                $name_qr = $documento->serie_contingencia . "-" . $documento->correlativo . '.svg';
            }
            if ($documento->sunat == '1') {
                $arreglo_qr = array(
                    "ruc" => $documento->ruc_empresa,
                    "tipo" => $documento->tipoDocumento(),
                    "serie" => $documento->contingencia == '0' ? $documento->serie : $documento->serie_contingencia,
                    "numero" => $documento->correlativo,
                    "emision" => self::obtenerFechaEmision($documento),
                    "igv" => 18,
                    "total" => (float) $documento->total,
                    "clienteTipo" => $documento->tipoDocumentoCliente(),
                    "clienteNumero" => $documento->documento_cliente,
                );

                /********************************/
                $data_qr = generarQrApi(json_encode($arreglo_qr), $documento->empresa_id);

                $pathToFile_qr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR . $name_qr);

                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'))) {
                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'));
                }

                file_put_contents($pathToFile_qr, $data_qr);

                $documento->ruta_qr = 'public/qrs/' . $name_qr;
                $documento->update();

                return array('success' => true, 'mensaje' => 'QR creado exitosamente');
            }

            if ($documento->sunat == '0' && $documento->contingencia == '0') {
                $miQr = QrCode::format('svg')
                    ->size(130) //defino el tamaño
                    ->backgroundColor(0, 0, 0) //defino el fondo
                    ->color(255, 255, 255)
                    ->margin(1) //defino el margen
                    ->generate($documento->ruc_empresa . '|' . $documento->tipoDocumento() . '|' . $documento->serie . '|' . $documento->correlativo . '|' . $documento->total_igv . '|' . $documento->total . '|' . getFechaFormato($documento->fecha_emision, 'd/m/Y'));

                $pathToFile_qr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR . $name_qr);

                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'))) {
                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'));
                }

                file_put_contents($pathToFile_qr, $miQr);

                $documento->ruta_qr = 'public/qrs/' . $name_qr;
                $documento->update();
                return array('success' => false, 'mensaje' => 'Ya tiene QR');
            }

            if ($documento->sunat_contingencia == '0' && $documento->contingencia == '1') {
                $miQr = QrCode::format('svg')
                    ->size(130) //defino el tamaño
                    ->backgroundColor(0, 0, 0) //defino el fondo
                    ->color(255, 255, 255)
                    ->margin(1) //defino el margen
                    ->generate($documento->ruc_empresa . '|' . $documento->tipoDocumento() . '|' . $documento->serie . '|' . $documento->correlativo . '|' . $documento->total_igv . '|' . $documento->total . '|' . getFechaFormato($documento->fecha_emision, 'd/m/Y'));

                $pathToFile_qr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR . $name_qr);

                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'))) {
                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'));
                }

                file_put_contents($pathToFile_qr, $miQr);

                $documento->ruta_qr = 'public/qrs/' . $name_qr;
                $documento->update();
                return array('success' => false, 'mensaje' => 'Ya tiene QR');
            }
        } catch (Exception $e) {
            return array('success' => false, 'mensaje' => $e->getMessage());
        }
    }

    public function obtenerLeyenda($documento)
    {
        $formatter = new NumeroALetras();
        // $convertir = $formatter->toInvoice($documento->total, 2, 'SOLES');
        $convertir = $formatter->toInvoice($documento->total_pagar, 2, 'SOLES');

        //CREAR LEYENDA DEL COMPROBANTE
        $arrayLeyenda = array();
        $arrayLeyenda[] = array(
            "code" => "1000",
            "value" => $convertir,
        );
        return $arrayLeyenda;
    }

    public function obtenerProductos($id)
    {
        
        $detalles = Detalle::where('documento_id',$id)->where('eliminado', '0')->where('estado', 'ACTIVO')->get();
        $documento = Documento::findOrFail($id);

        $arrayProductos = Array();
        for($i = 0; $i < count($detalles); $i++){

            $arrayProductos[] = array(
                "codProducto" => $detalles[$i]->codigo_producto,
                "unidad" => $detalles[$i]->unidad,
                // "descripcion"=> $detalles[$i]->nombre_producto.' - '.$detalles[$i]->codigo_lote,                "descripcion"=> $detalles[$i]->nombre_producto.' - '.$detalles[$i]->codigo_lote,
                "descripcion"=> $detalles[$i]->nombre_producto.' - '.$detalles[$i]->nombre_color.' - '.$detalles[$i]->nombre_talla,
                "cantidad" => (float)$detalles[$i]->cantidad,
                // "mtoValorUnitario" => (float)($detalles[$i]->precio_nuevo / 1.18),
                "mtoValorUnitario" => (float)($detalles[$i]->precio_unitario_nuevo / 1.18),

                // "mtoValorVenta" => (float)($detalles[$i]->valor_venta / 1.18),
                "mtoValorVenta" => (float)($detalles[$i]->importe_nuevo / 1.18),
                // "mtoBaseIgv" => (float)($detalles[$i]->valor_venta / 1.18),
                "mtoBaseIgv" => (float)($detalles[$i]->importe_nuevo / 1.18),
                "porcentajeIgv" => 18,
                // "igv" => (float)($detalles[$i]->valor_venta - ($detalles[$i]->valor_venta / 1.18)),
                "igv" => (float)($detalles[$i]->importe_nuevo - ($detalles[$i]->importe_nuevo / 1.18)),
                "tipAfeIgv" => 10,
                // "totalImpuestos" =>  (float)($detalles[$i]->valor_venta - ($detalles[$i]->valor_venta / 1.18)),
                "totalImpuestos" =>  (float)($detalles[$i]->importe_nuevo - ($detalles[$i]->importe_nuevo / 1.18)),
                // "mtoPrecioUnitario" => (float)$detalles[$i]->precio_nuevo,
                "mtoPrecioUnitario" => (float)$detalles[$i]->precio_unitario_nuevo
            );
        }

        //======== agregando embalaje y envío como productos ===========
        if($documento->monto_embalaje!=0){
            $arrayProductos[] = array(
                "codProducto" => 'PE00',
                "unidad" => 'NIU',
                // "descripcion" => $detalles[$i]->nombre_producto . ' - ' . $detalles[$i]->codigo_lote,
                "descripcion" => 'EMBALAJE',
                "cantidad" => (float) 1,
                // // "mtoValorUnitario" => (float) ($detalles[$i]->precio_nuevo / 1.18),
                "mtoValorUnitario" => (float) ($documento->monto_embalaje / 1.18),
                // "mtoValorVenta" => (float) ($detalles[$i]->valor_venta / 1.18),
                // "mtoBaseIgv" => (float) ($detalles[$i]->valor_venta / 1.18),
                "mtoValorVenta" => (float) ($documento->monto_embalaje / 1.18),
                "mtoBaseIgv" => (float) ($documento->monto_embalaje / 1.18),
                "porcentajeIgv" => 18,
                // "igv" => (float) ($detalles[$i]->valor_venta - ($detalles[$i]->valor_venta / 1.18)),
                "igv" => (float) ($documento->monto_embalaje - ($documento->monto_embalaje / 1.18)),
                "tipAfeIgv" => 10,
                // "totalImpuestos" => (float) ($detalles[$i]->valor_venta - ($detalles[$i]->valor_venta / 1.18)),
                "totalImpuestos" => (float) ($documento->monto_embalaje - ($documento->monto_embalaje / 1.18)),
                // // "mtoPrecioUnitario" => (float) $detalles[$i]->precio_nuevo,
                "mtoPrecioUnitario" => (float) $documento->monto_embalaje,
            );
        }
       
        if($documento->monto_envio!=0){
            $arrayProductos[] = array(
                "codProducto" => 'PE01',
                "unidad" => 'NIU',
                // "descripcion" => $detalles[$i]->nombre_producto . ' - ' . $detalles[$i]->codigo_lote,
                "descripcion" => 'ENVIO',
                "cantidad" => (float) 1,
                // // "mtoValorUnitario" => (float) ($detalles[$i]->precio_nuevo / 1.18),
                "mtoValorUnitario" => (float) ($documento->monto_envio / 1.18),
                // "mtoValorVenta" => (float) ($detalles[$i]->valor_venta / 1.18),
                // "mtoBaseIgv" => (float) ($detalles[$i]->valor_venta / 1.18),
                "mtoValorVenta" => (float) ($documento->monto_envio / 1.18),
                "mtoBaseIgv" => (float) ($documento->monto_envio / 1.18),
                "porcentajeIgv" => 18,
                // "igv" => (float) ($detalles[$i]->valor_venta - ($detalles[$i]->valor_venta / 1.18)),
                "igv" => (float) ($documento->monto_envio - ($documento->monto_envio / 1.18)),
                "tipAfeIgv" => 10,
                // "totalImpuestos" => (float) ($detalles[$i]->valor_venta - ($detalles[$i]->valor_venta / 1.18)),
                "totalImpuestos" => (float) ($documento->monto_envio - ($documento->monto_envio / 1.18)),
                // // "mtoPrecioUnitario" => (float) $detalles[$i]->precio_nuevo,
                "mtoPrecioUnitario" => (float) $documento->monto_envio,
            );
        }
        


        return $arrayProductos;
    }

    public function obtenerFechaEmision($documento)
    {
        $date = strtotime($documento->fecha_documento);
        $fecha_emision = date('Y-m-d', $date);
        $hora_emision = date('H:i:s', $date);
        $fecha = $fecha_emision . 'T' . $hora_emision . '-05:00';

        return $fecha;
    }

    public function obtenerFechaVencimiento($documento)
    {
        $date = strtotime($documento->fecha_vencimiento);
        $fecha_emision = date('Y-m-d', $date);
        $hora_emision = date('H:i:s', $date);
        $fecha = $fecha_emision . 'T' . $hora_emision . '-05:00';

        return $fecha;
    }
    private function ObtenerCorrelativoVentas(Documento $documento){
        try{
            $numeracion_factura = Numeracion::where("empresa_id",$documento->empresa_id)
            ->where("estado","ACTIVO")
            ->where("tipo_comprobante",$documento->tipo_venta)
            ->first();

            if(!$numeracion_factura)
                throw new \Exception("Tipo de Comprobante no registrado en la empresa.");

            DB::select("CALL sp_updateserializacion(?,?)",[$documento->id,$numeracion_factura->id]);
            
            return array(
                "success"=>true,
                "mensaje"=>"Documento validado"
            );

        }catch(\Exception $ex){
            return array(
                "success"=>false,
                "mensaje"=>$ex->getMessage()
            );
        }
    }
    public function sunat($id)
    {
        
        try {
            $documento = Documento::findOrFail($id);
            
            //OBTENER CORRELATIVO DEL COMPROBANTE ELECTRONICO
            $existe = event(new DocumentoNumeracion($documento));
           
           
            if ($existe[0]) {
                if ($existe[0]->get('existe') == true) {
                    return array('success' => true,'mensaje' => 'Documento validado.',
                    'serie_correlativo'=>$existe[0]->get('correlativo_datos')['serie'].'-'.$existe[0]->get('correlativo_datos')['correlativo']);
                } else {
                    return array('success' => false, 'mensaje' => 'Tipo de Comprobante no registrado en la empresa.');
                }
            } else {
                return array('success' => false, 'mensaje' => 'Empresa sin parametros para emitir comprobantes electronicos.');
            }
        } catch (Exception $e) {
            
            return array('success' => false, 'mensaje' => $e->getMessage());
        }
    }

    public function sunat_valida($id)
    {
        try {
            $documento = Documento::find($id);
            if ($documento->sunat != '1') {
                //ARREGLO COMPROBANTE
                $arreglo_comprobante = array(
                    "tipoOperacion" => $documento->tipoOperacion(),
                    "tipoDoc" => $documento->tipoDocumento(),
                    "serie" => $documento->serie,
                    "correlativo" => $documento->correlativo,
                    "fechaEmision" => self::obtenerFechaEmision($documento),
                    "fecVencimiento" => self::obtenerFechaVencimiento($documento),
                    "observacion" => $documento->observacion,
                    "formaPago" => array(
                        "moneda" => $documento->simboloMoneda(),
                        "tipo" => $documento->forma_pago(),
                        "monto" => (float) $documento->total,
                    ),
                    "cuotas" => self::obtenerCuotas($documento->id),
                    "tipoMoneda" => $documento->simboloMoneda(),
                    "client" => array(
                        "tipoDoc" => $documento->tipoDocumentoCliente(),
                        "numDoc" => $documento->documento_cliente,
                        "rznSocial" => $documento->cliente,
                        "address" => array(
                            "direccion" => $documento->direccion_cliente,
                        ),
                    ),
                    "company" => array(
                        "ruc" => $documento->ruc_empresa,
                        "razonSocial" => $documento->empresa,
                        "address" => array(
                            "direccion" => $documento->direccion_fiscal_empresa,
                        ),
                    ),
                    // "mtoOperGravadas" => (float) $documento->sub_total,
                    "mtoOperGravadas" => (float) $documento->total,
                    "mtoOperExoneradas" => 0,
                    "mtoIGV" => (float) $documento->total_igv,

                    // "valorVenta" => (float) $documento->sub_total,
                    "valorVenta" => (float) $documento->total,
                    "totalImpuestos" => (float) $documento->total_igv,
                    // "subTotal" => (float) $documento->total + ($documento->retencion ? $documento->retencion->impRetenido : 0),
                    // "mtoImpVenta" => (float) $documento->total + ($documento->retencion ? $documento->retencion->impRetenido : 0),
                    "subTotal" => (float) $documento->total_pagar + ($documento->retencion ? $documento->retencion->impRetenido : 0),
                    "mtoImpVenta" => (float) $documento->total_pagar + ($documento->retencion ? $documento->retencion->impRetenido : 0),
                    
                    "ublVersion" => "2.1",
                    "details" => self::obtenerProductos($documento->id),
                    "legends" => self::obtenerLeyenda($documento),
                );

                //OBTENER JSON DEL COMPROBANTE EL CUAL SE ENVIARA A SUNAT
                $data = enviarComprobanteapi(json_encode($arreglo_comprobante), $documento->empresa_id);

                //RESPUESTA DE LA SUNAT EN JSON
                $json_sunat = json_decode($data);
                if ($json_sunat->sunatResponse->success == true) {
                    if ($json_sunat->sunatResponse->cdrResponse->code == "0") {
                        $documento->sunat = '1';
                        $respuesta_cdr = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                        $respuesta_cdr = json_decode($respuesta_cdr, true);
                        $documento->getCdrResponse = $respuesta_cdr;

                        $data_comprobante = generarComprobanteapi(json_encode($arreglo_comprobante), $documento->empresa_id);
                        $name = $documento->serie . "-" . $documento->correlativo . '.pdf';

                        $data_cdr = base64_decode($json_sunat->sunatResponse->cdrZip);
                        $name_cdr = 'R-' . $documento->serie . "-" . $documento->correlativo . '.zip';

                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat'));
                        }

                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'cdr'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'cdr'));
                        }

                        $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sunat' . DIRECTORY_SEPARATOR . $name);
                        $pathToFile_cdr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . $name_cdr);

                        file_put_contents($pathToFile, $data_comprobante);
                        file_put_contents($pathToFile_cdr, $data_cdr);

                        $arreglo_qr = array(
                            "ruc" => $documento->ruc_empresa,
                            "tipo" => $documento->tipoDocumento(),
                            "serie" => $documento->serie,
                            "numero" => $documento->correlativo,
                            "emision" => self::obtenerFechaEmision($documento),
                            "igv" => 18,
                            "total" => (float) $documento->total,
                            "clienteTipo" => $documento->tipoDocumentoCliente(),
                            "clienteNumero" => $documento->documento_cliente,
                        );

                        /********************************/
                        $data_qr = generarQrApi(json_encode($arreglo_qr), $documento->empresa_id);

                        $name_qr = $documento->serie . "-" . $documento->correlativo . '.svg';

                        $pathToFile_qr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR . $name_qr);

                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'));
                        }

                        file_put_contents($pathToFile_qr, $data_qr);

                        /********************************/

                        $data_xml = generarXmlapi(json_encode($arreglo_comprobante), $documento->empresa_id);
                        $name_xml = $documento->serie . '-' . $documento->correlativo . '.xml';
                        $pathToFile_xml = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR . $name_xml);
                        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml'))) {
                            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'xml'));
                        }
                        file_put_contents($pathToFile_xml, $data_xml);

                        /********************************* */

                        $documento->nombre_comprobante_archivo = $name;
                        $documento->hash = $json_sunat->hash;
                        $documento->xml = $name_xml;
                        $documento->ruta_comprobante_archivo = 'public/sunat/' . $name;
                        $documento->ruta_qr = 'public/qrs/' . $name_qr;
                        $documento->update();

                        //Registro de actividad
                        $descripcion = "SE AGREGÓ EL COMPROBANTE ELECTRONICO: " . $documento->serie . "-" . $documento->correlativo;
                        $gestion = "COMPROBANTES ELECTRONICOS";
                        crearRegistro($documento, $descripcion, $gestion);

                        // Session::flash('success','Documento de Venta enviada a Sunat con exito.');
                        // return view('ventas.documentos.index',[

                        //     'id_sunat' => $json_sunat->sunatResponse->cdrResponse->id,
                        //     'descripcion_sunat' => $json_sunat->sunatResponse->cdrResponse->description,
                        //     'notas_sunat' => $json_sunat->sunatResponse->cdrResponse->notes,
                        //     'sunat_exito' => true

                        // ])->with('sunat_exito', 'success');
                        return array('success' => true, 'mensaje' => 'Documento de Venta enviada a Sunat con exito.');
                    } else {
                        $documento->sunat = '0';

                        $id_sunat = $json_sunat->sunatResponse->cdrResponse->code;
                        $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;

                        $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                        $respuesta_error = json_decode($respuesta_error, true);
                        $documento->getCdrResponse = $respuesta_error;

                        $documento->update();

                        $errorVenta = new ErrorVenta();
                        $errorVenta->documento_id = $documento->id;
                        $errorVenta->tipo = 'sunat-envio';
                        $errorVenta->descripcion = 'Error al enviar a sunat';
                        $errorVenta->ecxepcion = $descripcion_sunat;
                        $errorVenta->save();

                        return array('success' => false, 'mensaje' => $descripcion_sunat);
                    }
                } else {

                    //COMO SUNAT NO LO ADMITE VUELVE A SER 0
                    $documento->sunat = '0';
                    $documento->regularize = '1';

                    if ($json_sunat->sunatResponse->error) {
                        $id_sunat = $json_sunat->sunatResponse->error->code;
                        $descripcion_sunat = $json_sunat->sunatResponse->error->message;

                        $obj_erro = new stdClass();
                        $obj_erro->code = $json_sunat->sunatResponse->error->code;
                        $obj_erro->description = $json_sunat->sunatResponse->error->message;
                        $respuesta_error = json_encode($obj_erro, true);
                        $respuesta_error = json_decode($respuesta_error, true);
                        $documento->getRegularizeResponse = $respuesta_error;
                    } else {
                        $id_sunat = $json_sunat->sunatResponse->cdrResponse->id;
                        $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;

                        $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                        $respuesta_error = json_decode($respuesta_error, true);
                        $documento->getCdrResponse = $respuesta_error;
                    };
                    $documento->update();

                    $errorVenta = new ErrorVenta();
                    $errorVenta->documento_id = $documento->id;
                    $errorVenta->tipo = 'sunat-envio';
                    $errorVenta->descripcion = 'Error al enviar a sunat';
                    $errorVenta->ecxepcion = $descripcion_sunat;
                    $errorVenta->save();

                    return array('success' => false, 'mensaje' => $descripcion_sunat);
                }
            } else {
                $documento->sunat = '1';
                $documento->update();
                // Session::flash('error','Documento de venta fue enviado a Sunat.');
                // return redirect()->route('ventas.documento.index')->with('sunat_existe', 'error');

                return array('success' => false, 'mensaje' => 'Documento de venta fue enviado a Sunat.');
            }
        } catch (Exception $e) {
            $documento = Documento::find($id);

            $documento->regularize = '1';
            $documento->sunat = '0';
            $obj_erro = new stdClass();
            $obj_erro->code = 6;
            $obj_erro->description = $e->getMessage();
            $respuesta_error = json_encode($obj_erro, true);
            $respuesta_error = json_decode($respuesta_error, true);
            $documento->getRegularizeResponse = $respuesta_error;
            $documento->update();

            $errorVenta = new ErrorVenta();
            $errorVenta->documento_id = $documento->id;
            $errorVenta->tipo = 'sunat-envio';
            $errorVenta->descripcion = 'Error al enviar a sunat';
            $errorVenta->ecxepcion = $e->getMessage();
            $errorVenta->save();
            return array('success' => false, 'mensaje' => $e->getMessage());
        }
    }

    public function vouchersAvaible(Request $request)
    {
        $data = $request->all();
        $empresa_id = $data['empresa_id'];
        $tipo = $data['tipo_id'];
        $detalle = TablaDetalle::findOrFail($tipo);
        $empresa = Empresa::findOrFail($empresa_id);
        $resultado = (Numeracion::where('empresa_id', $empresa_id)->where('estado', 'ACTIVO')->where('tipo_comprobante', $tipo))->exists();

        $enviar = [
            'existe' => ($resultado == true) ? true : false,
            'comprobante' => $detalle->descripcion,
            'empresa' => $empresa->razon_social,
        ];

        return response()->json($enviar);
    }

    public function obtenerCuotas($id)
    {
        $documento = Documento::find($id);
        $arrayCuotas = array();
        $condicion = Condicion::find($documento->condicion_id);
        if (strtoupper($condicion->descripcion) == 'CREDITO' || strtoupper($condicion->descripcion) == 'CRÉDITO') {
            $arrayCuotas[] = array(
                "moneda" => "PEN",
                "monto" => (float) $documento->total,
                "fechaPago" => self::obtenerFechaVencimiento($documento),

            );
        }
        /*if($documento->cuenta)
        {
        foreach($documento->cuenta->detalles as $item)
        {
        $arrayCuotas[] = array(
        "moneda" => "PEN",
        "monto" => (float)$item->monto,
        "fechaPago" => self::obtenerFechaCuenta($item->fecha)

        );
        }
        }*/

        return $arrayCuotas;
    }

    public function obtenerFechaCuenta($fecha)
    {
        $date = strtotime($fecha);
        $fecha_emision = date('Y-m-d', $date);
        $hora_emision = date('H:i:s', $date);
        $fecha = $fecha_emision . 'T' . $hora_emision . '-05:00';

        return $fecha;
    }

    public function customers_all(Request $request)
    {
        $clientes = Cliente::where('estado', '!=', 'ANULADO')->get();

        $enviar = [
            'clientes' => $clientes,
        ];

        return response()->json($enviar);
    }

    public function customers(Request $request)
    {
        $data = $request->all();
        $tipo = $data['tipo_id'];
        $pun_tipo = '';

        if ($tipo == '127') {
            $clientes = Cliente::where('estado', '!=', 'ANULADO')
                ->where('tipo_documento', 'RUC')
                ->get();
            $pun_tipo = '1';
        } else {
            $clientes = Cliente::where('estado', '!=', 'ANULADO')
                ->where('tipo_documento', '!=', 'RUC')
                ->get();
            $pun_tipo = '0';
        }

        $enviar = [
            'clientes' => $clientes,
            'tipo' => $pun_tipo,
        ];

        return response()->json($enviar);
    }

    //LOTES PARA BUSQUEDA
    public function getLot($tipo_cliente,$tipocomprobante)
    {

        $facturacion = (int)$tipocomprobante == 129 ? "NO" : "SI";
        $datos = null;

        if($facturacion =="NO"){
            $datos= DB::table('lote_productos')
            ->join('productos', 'productos.id', '=', 'lote_productos.producto_id')
            ->join('productos_clientes', 'productos_clientes.producto_id', '=', 'productos.id')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->join('tabladetalles', 'tabladetalles.id', '=', 'productos.medida')
            ->leftJoin('detalle_nota_ingreso', 'detalle_nota_ingreso.lote_id', '=', 'lote_productos.id')
            ->leftJoin('nota_ingreso', 'nota_ingreso.id', '=', 'detalle_nota_ingreso.nota_ingreso_id')
            ->leftJoin('compra_documento_detalles', 'compra_documento_detalles.lote_id', '=', 'lote_productos.id')
            ->leftJoin('compra_documentos', 'compra_documentos.id', '=', 'compra_documento_detalles.documento_id')
            ->select(
                'nota_ingreso.moneda as moneda_ingreso',
                'compra_documentos.moneda as moneda_compra',
                'compra_documentos.tipo_cambio as dolar_compra',
                'compra_documentos.igv_check as igv_compra',
                'compra_documento_detalles.precio_soles',
                'compra_documento_detalles.precio as precio_compra',
                'compra_documento_detalles.costo_flete_soles',
                'compra_documento_detalles.costo_flete_dolares',
                'compra_documento_detalles.cantidad as cantidad_comprada',
                'detalle_nota_ingreso.costo as precio_ingreso',
                'detalle_nota_ingreso.costo_soles as precio_ingreso_soles',
                'nota_ingreso.dolar as dolar_ingreso',
                'compra_documento_detalles.precio_mas_igv_soles',
                'lote_productos.*',
                'productos.nombre',
                'productos.igv',
                'productos.codigo_barra',
                'productos.facturacion',
                DB::raw('ifnull((select porcentaje
                from productos_clientes pc
                where pc.producto_id = lote_productos.producto_id
                and pc.cliente = 121
                and pc.estado = "ACTIVO"
            order by id desc
            limit 1),20) as porcentaje_normal'),
                DB::raw('ifnull((select porcentaje
                from productos_clientes pc
                where pc.producto_id = lote_productos.producto_id
                and pc.cliente = 122
                and pc.estado = "ACTIVO"
            order by id desc
            limit 1),20) as porcentaje_distribuidor'),
                'productos_clientes.cliente',
                'productos_clientes.moneda',
                'productos_clientes.porcentaje',
                'tabladetalles.simbolo as unidad_producto',
                'categorias.descripcion as categoria',
                'marcas.marca',
                DB::raw('DATE_FORMAT(lote_productos.fecha_vencimiento, "%d/%m/%Y") as fecha_venci')
            )
            ->where('lote_productos.cantidad_logica', '>', 0)
            ->where('lote_productos.estado', '1')
            ->where('productos_clientes.cliente', $tipo_cliente)
            ->where('productos_clientes.moneda', '1')
            ->orderBy('lote_productos.id', 'ASC')
            ->where('productos_clientes.estado', 'ACTIVO');
        }else{

            $datos= DB::table('lote_productos')
            ->join('productos', 'productos.id', '=', 'lote_productos.producto_id')
            ->join('productos_clientes', 'productos_clientes.producto_id', '=', 'productos.id')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->join('tabladetalles', 'tabladetalles.id', '=', 'productos.medida')
            ->leftJoin('detalle_nota_ingreso', 'detalle_nota_ingreso.lote_id', '=', 'lote_productos.id')
            ->leftJoin('nota_ingreso', 'nota_ingreso.id', '=', 'detalle_nota_ingreso.nota_ingreso_id')
            ->leftJoin('compra_documento_detalles', 'compra_documento_detalles.lote_id', '=', 'lote_productos.id')
            ->leftJoin('compra_documentos', 'compra_documentos.id', '=', 'compra_documento_detalles.documento_id')
            ->select(
                'nota_ingreso.moneda as moneda_ingreso',
                'compra_documentos.moneda as moneda_compra',
                'compra_documentos.dolar as dolar_compra',
                'compra_documentos.igv_check as igv_compra',
                'compra_documento_detalles.precio_soles',
                'compra_documento_detalles.precio as precio_compra',
                'compra_documento_detalles.costo_flete_soles',
                'compra_documento_detalles.costo_flete_dolares',
                'compra_documento_detalles.cantidad as cantidad_comprada',
                'detalle_nota_ingreso.costo as precio_ingreso',
                'detalle_nota_ingreso.costo_soles as precio_ingreso_soles',
                'nota_ingreso.dolar as dolar_ingreso',
                'compra_documento_detalles.precio_mas_igv_soles',
                'lote_productos.*',
                'productos.nombre',
                'productos.igv',
                'productos.codigo_barra',
                'productos.facturacion',
                DB::raw('ifnull((select porcentaje
                from productos_clientes pc
                where pc.producto_id = lote_productos.producto_id
                and pc.cliente = 121
                and pc.estado = "ACTIVO"
            order by id desc
            limit 1),20) as porcentaje_normal'),
                DB::raw('ifnull((select porcentaje
                from productos_clientes pc
                where pc.producto_id = lote_productos.producto_id
                and pc.cliente = 122
                and pc.estado = "ACTIVO"
            order by id desc
            limit 1),20) as porcentaje_distribuidor'),
                'productos_clientes.cliente',
                'productos_clientes.moneda',
                'productos_clientes.porcentaje',
                'tabladetalles.simbolo as unidad_producto',
                'categorias.descripcion as categoria',
                'marcas.marca',
                DB::raw('DATE_FORMAT(lote_productos.fecha_vencimiento, "%d/%m/%Y") as fecha_venci')
            )
            ->where('lote_productos.cantidad_logica', '>', 0)
            ->where('lote_productos.estado', '1')
            ->where('productos_clientes.cliente', $tipo_cliente)
            ->where('productos_clientes.moneda', '1')
            ->orderBy('lote_productos.id', 'ASC')
            ->where('productos_clientes.estado', 'ACTIVO')
            ->where("productos.facturacion","=",$facturacion);
        }
        
        return datatables()->query($datos)->toJson();
    }
    public function getLoteProductos(Request $request)
    {
        sleep(.5);
        $tipo_cliente = $request->tipo_cliente;
        $tipocomprobante = $request->tipocomprobante;

        $facturacion = (int)$tipocomprobante == 129 ? "NO" : "SI";
        $datos = null;
        $search = $request->search;

        if($facturacion =="NO"){
            $datos= DB::table('lote_productos')
            ->join('productos', 'productos.id', '=', 'lote_productos.producto_id')
            ->join('productos_clientes', 'productos_clientes.producto_id', '=', 'productos.id')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->join('tabladetalles', 'tabladetalles.id', '=', 'productos.medida')
            ->leftJoin('detalle_nota_ingreso', 'detalle_nota_ingreso.lote_id', '=', 'lote_productos.id')
            ->leftJoin('nota_ingreso', 'nota_ingreso.id', '=', 'detalle_nota_ingreso.nota_ingreso_id')
            ->leftJoin('compra_documento_detalles', 'compra_documento_detalles.lote_id', '=', 'lote_productos.id')
            ->leftJoin('compra_documentos', 'compra_documentos.id', '=', 'compra_documento_detalles.documento_id')
            ->select(
                'nota_ingreso.moneda as moneda_ingreso',
                'compra_documentos.moneda as moneda_compra',
                'compra_documentos.dolar as dolar_compra',
                'compra_documentos.igv_check as igv_compra',
                'compra_documento_detalles.precio_soles',
                'compra_documento_detalles.precio as precio_compra',
                'compra_documento_detalles.costo_flete_soles',
                'compra_documento_detalles.costo_flete',
                'compra_documento_detalles.costo_flete_dolares',
                'compra_documento_detalles.cantidad as cantidad_comprada',
                'detalle_nota_ingreso.costo as precio_ingreso',
                'detalle_nota_ingreso.costo_soles as precio_ingreso_soles',
                'nota_ingreso.dolar as dolar_ingreso',
                'compra_documento_detalles.precio_mas_igv_soles',
                'lote_productos.*',
                'productos.nombre',
                'productos.igv',
                'productos.codigo_barra',
                'productos.facturacion',
                DB::raw('ifnull((select porcentaje
                from productos_clientes pc
                where pc.producto_id = lote_productos.producto_id
                and pc.cliente = 121
                and pc.estado = "ACTIVO"
            order by id desc
            limit 1),20) as porcentaje_normal'),
                DB::raw('ifnull((select porcentaje
                from productos_clientes pc
                where pc.producto_id = lote_productos.producto_id
                and pc.cliente = 122
                and pc.estado = "ACTIVO"
            order by id desc
            limit 1),20) as porcentaje_distribuidor'),
                'productos_clientes.cliente',
                'productos_clientes.moneda',
                'productos_clientes.porcentaje',
                'tabladetalles.simbolo as unidad_producto',
                'categorias.descripcion as categoria',
                'marcas.marca',
                DB::raw('DATE_FORMAT(lote_productos.fecha_vencimiento, "%d/%m/%Y") as fecha_venci')
            )
            ->where('lote_productos.cantidad_logica', '>', 0)
            ->where('lote_productos.estado','=','1')
            ->where('productos_clientes.cliente', $tipo_cliente)
            ->where('productos_clientes.moneda', '1')
            ->orderBy('lote_productos.id', 'ASC')
            ->where('productos_clientes.estado', 'ACTIVO')
            ->where(function($query) use ($search){
                $query->orWhere("productos.nombre","LIKE","%$search%")
                ->orWhere("lote_productos.codigo_lote","LIKE","%$search%");
            })->paginate(10);
        }else{

            $datos= DB::table('lote_productos')
            ->join('productos', 'productos.id', '=', 'lote_productos.producto_id')
            ->join('productos_clientes', 'productos_clientes.producto_id', '=', 'productos.id')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->join('tabladetalles', 'tabladetalles.id', '=', 'productos.medida')
            ->leftJoin('detalle_nota_ingreso', 'detalle_nota_ingreso.lote_id', '=', 'lote_productos.id')
            ->leftJoin('nota_ingreso', 'nota_ingreso.id', '=', 'detalle_nota_ingreso.nota_ingreso_id')
            ->leftJoin('compra_documento_detalles', 'compra_documento_detalles.lote_id', '=', 'lote_productos.id')
            ->leftJoin('compra_documentos', 'compra_documentos.id', '=', 'compra_documento_detalles.documento_id')
            ->select(
                'nota_ingreso.moneda as moneda_ingreso',
                'compra_documentos.moneda as moneda_compra',
                'compra_documentos.dolar as dolar_compra',
                'compra_documentos.igv_check as igv_compra',
                'compra_documento_detalles.precio_soles',
                'compra_documento_detalles.precio as precio_compra',
                'compra_documento_detalles.costo_flete_soles',
                'compra_documento_detalles.costo_flete',
                'compra_documento_detalles.costo_flete_dolares',
                'compra_documento_detalles.cantidad as cantidad_comprada',
                'detalle_nota_ingreso.costo as precio_ingreso',
                'detalle_nota_ingreso.costo_soles as precio_ingreso_soles',
                'nota_ingreso.dolar as dolar_ingreso',
                'compra_documento_detalles.precio_mas_igv_soles',
                'lote_productos.*',
                'productos.nombre',
                'productos.igv',
                'productos.codigo_barra',
                'productos.facturacion',
                DB::raw('ifnull((select porcentaje
                from productos_clientes pc
                where pc.producto_id = lote_productos.producto_id
                and pc.cliente = 121
                and pc.estado = "ACTIVO"
            order by id desc
            limit 1),20) as porcentaje_normal'),
                DB::raw('ifnull((select porcentaje
                from productos_clientes pc
                where pc.producto_id = lote_productos.producto_id
                and pc.cliente = 122
                and pc.estado = "ACTIVO"
            order by id desc
            limit 1),20) as porcentaje_distribuidor'),
                'productos_clientes.cliente',
                'productos_clientes.moneda',
                'productos_clientes.porcentaje',
                'tabladetalles.simbolo as unidad_producto',
                'categorias.descripcion as categoria',
                'marcas.marca',
                DB::raw('DATE_FORMAT(lote_productos.fecha_vencimiento, "%d/%m/%Y") as fecha_venci')
            )
            ->where('lote_productos.cantidad_logica', '>', 0)
            ->where('lote_productos.estado', '=','1')
            ->where('productos_clientes.cliente', $tipo_cliente)
            ->where('productos_clientes.moneda','1')
            ->orderBy('lote_productos.id', 'ASC')
            ->where('productos_clientes.estado', 'ACTIVO')
            ->where("productos.facturacion","=",$facturacion)
            ->where(function($query) use ($search){
                $query->orWhere("productos.nombre","LIKE","%$search%")
                ->orWhere("lote_productos.codigo_lote","LIKE","%$search%");
            })->paginate(10);
        }
        
        return response()->json([
            "lotes"=>$datos,
        ]);
    }

    public function getLotProcedure($tipo_cliente, $tipocomprobante,Request $request)
    {
        
        try{
            $facturacion = (int) $tipocomprobante == 129 ? "NO" : "SI";
            $search = $request->has("search") ? ($request->search["value"] ==null ? "%%" : "%{$request->search['value']}%") : "%%";
            $datos = DB::select("call LISTA_LOTEPRODUCTOS(?,?,?)",[$tipo_cliente,$facturacion,$search]);
            return DataTables::of($datos)->make(true);
        }catch(\Exception $ex){
            return response()->json([
                "all"=>"%{$request->search['value']}%",
                "ex"=>$ex->getMessage()
            ]);
        }
    }
   
    //CAMBIAR CANTIDAD LOGICA DEL LOTE
    public function quantity(Request $request)
    {
        $data           =   $request->all();
        $producto_id    =   $data['producto_id'];
        $color_id       =   $data['color_id'];
        $talla_id       =   $request->input('talla_id', null);
        $cantidad       =   $request->input('cantidad', null);
        $condicion      =   $data['condicion'];
        $modo           =   $data['modo'];
        $tallas         =   $request->input('tallas', null);
        $mensaje        = '';

      
        //$lote = LoteProducto::findOrFail($producto_id);

        //DISMINUIR
        // if ($lote->cantidad_logica >= $cantidad && $condicion == '1') {
        //     $nuevaCantidad = $lote->cantidad_logica - $cantidad;
        //     $lote->cantidad_logica = $nuevaCantidad;
        //     $lote->update();
        //     $mensaje = 'Cantidad aceptada';
        // }
       
        if ($condicion == '1' && $modo=='nuevo') {
            //$producto->stock_logico = $nuevaCantidad;
            DB::table('producto_color_tallas')
            ->where('producto_id', $producto_id)
            ->where('color_id', $color_id)
            ->where('talla_id', $talla_id)
            ->when(DB::raw('stock_logico >= ' . $cantidad), function ($query) use ($cantidad) {
                $query->decrement('stock_logico', $cantidad);
            });
            //$lote->update();
            $mensaje    = 'Cantidad aceptada';
        }

        if($modo == 'editar' && $condicion == '1'){
            $cantidadAnterior   =   $data['cantidadAnterior'];
           
            DB::table('producto_color_tallas')
            ->where('producto_id', $producto_id)
            ->where('color_id', $color_id)
            ->where('talla_id', $talla_id)
            ->when(DB::raw('stock_logico >= ' . $cantidad), function ($query) use ($cantidadAnterior, $cantidad) {
                $query->increment('stock_logico', ($cantidadAnterior - $cantidad));
            });
            $mensaje = 'Cantidad editada';
        }

        //REGRESAR STOCK LOGICO
        if($condicion == '0' && $modo='eliminar'){
            //======= devolviendo stock logico ============
            foreach ($tallas as $talla) {
                DB::table('producto_color_tallas')
                    ->where('producto_id', $producto_id)
                    ->where('color_id', $color_id)
                    ->where('talla_id', $talla['talla_id'])
                    ->increment('stock_logico', $talla['cantidad']);
            }       

            $mensaje = 'Cantidades devuelta';
        }


        //AUMENTAR
        // if ($condicion == '0') {
        //     $nuevaCantidad = $lote->cantidad_logica + $cantidad;
        //     $lote->cantidad_logica = $nuevaCantidad;
        //     $lote->update();
        //     $mensaje = 'Cantidad regresada';
        // }

        return $mensaje;
    }

    //DEVOLVER CANTIDAD LOGICA AL CERRAR VENTANA
    public function returnQuantity(Request $request)
    {
        
        $mensaje        =   false;

        if($request->has('carrito')){
            $data           =   $request->all();
            $carrito        =   $data['carrito'];
            $productosJSON  =   json_decode($carrito);

            foreach ($productosJSON as $producto) {
                $mensaje=true;
                foreach ($producto->tallas as $talla) {
        
                    DB::table('producto_color_tallas')
                        ->where('producto_id', $producto->producto_id)
                        ->where('color_id', $producto->color_id)
                        ->where('talla_id', $talla->talla_id) 
                        ->increment('stock_logico', $talla->cantidad); 
                }
            }
        }

        // $cantidades = $data['cantidades'];
        // $productosJSON = $cantidades;
        // $productotabla = json_decode($productosJSON);
        // $mensaje = true;
        // foreach ($productotabla as $detalle) {
        //     //DEVOLVEMOS CANTIDAD AL LOTE Y AL LOTE LOGICO
        //     $lote = LoteProducto::findOrFail($detalle->producto_id);
        //     $lote->cantidad_logica = $lote->cantidad_logica + $detalle->cantidad;
        //     //$lote->cantidad =  $lote->cantidad_logica;
        //     $lote->estado = '1';
        //     $lote->update();
        //     $mensaje = true;
        // };

        return $mensaje;
    }
    
    

    //DEVOLVER LOTE
    public function returnLote(Request $request)
    {
        $data = $request->all();
        $lote_id = $data['lote_id'];
        $lote = LoteProducto::find($lote_id);

        if ($lote) {
            return response()->json([
                'success' => true,
                'lote' => $lote,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    //ACTUALIZAR LOTE E EDICION DE CANTIDAD
    public function updateLote(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $lote_id = $data['lote_id'];
            $cantidad_sum = $data['cantidad_sum'];
            $cantidad_res = $data['cantidad_res'];
            $lote = LoteProducto::find($lote_id);

            if ($lote) {
                $lote->cantidad_logica = $lote->cantidad_logica + ($cantidad_sum - $cantidad_res);
                $lote->update();
                DB::commit();
                return response()->json([
                    'success' => true,
                    'lote' => $lote,
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
            ]);
        }
    }

    //====== REGULARIZAR VENTAS ======
    //====== BOLETAS O FACTURAS =======
    public function regularizarVenta(Request $request){
        try {
            DB::beginTransaction();
            
            $documento_id   =   $request->get('documento_id');

            //======= OBTENIENDO DOCUMENTO DE VENTA ANTIGUO ====
            $documento_venta_antiguo    =   Documento::find($documento_id);

            if($documento_venta_antiguo->tipo_venta == "129"){
                return response()->json(['success'=>false,'message'=>'SOLO SE PERMITE REGULARIZAR FACTURAS O BOLETAS']);
            }

            //====== BUSCANDO DETALLES DEL DOC VENTA ANTIGUO ======
            $detalles_documento_venta_antiguo   =   Detalle::where('documento_id',$documento_id)->get();

            //===== CREANDO REQUEST =====
            $request_doc_nuevo = new Request();

            //====== OBTENIENDO FECHA ACTUAL ======
            $fecha_documento_campo = Carbon::now();

            $anio   = $fecha_documento_campo->year;
            $mes    = $fecha_documento_campo->month;
            $dia    = $fecha_documento_campo->day;
    
            $fecha_documento_campo = $fecha_documento_campo->format('Y-m-d');

            
            $request_doc_nuevo->merge([
                'fecha_documento_campo'     =>  $fecha_documento_campo,
                'fecha_atencion_campo'      =>  $fecha_documento_campo,
                'fecha_vencimiento_campo'   =>  $documento_venta_antiguo->fecha_vencimiento,
                'tipo_venta'            =>  $documento_venta_antiguo->tipo_venta,
                'tipo_pago_id'          =>  $documento_venta_antiguo->tipo_pago_id,
                'efectivo'              =>  $documento_venta_antiguo->efectivo,
                'importe'               =>  $documento_venta_antiguo->importe,
                'empresa_id'            =>  $documento_venta_antiguo->empresa_id,
                'condicion_id'          =>  $documento_venta_antiguo->condicion_id,
                'cliente_id'            =>  $documento_venta_antiguo->cliente_id,
                'observacion'           =>  $documento_venta_antiguo->observacion,
                'observacion'           =>  $documento_venta_antiguo->observacion,
                'igv'                   =>  intval($documento_venta_antiguo->igv),
                'regularizar'           =>  'SI',
                'productos_tabla'       =>  json_encode($detalles_documento_venta_antiguo),
                'monto_sub_total'       =>  $documento_venta_antiguo->sub_total,
                'monto_embalaje'        =>  $documento_venta_antiguo->monto_embalaje,
                'monto_envio'           =>  $documento_venta_antiguo->monto_envio,
                'monto_total'           =>  $documento_venta_antiguo->total,
                'monto_total_igv'       =>  $documento_venta_antiguo->total_igv,
                'monto_total_pagar'     =>  $documento_venta_antiguo->total_pagar
            ]);

        
            //====== GENERANDO NUEVO DOC DE VENTA ======
            $res_store  =   $this->store($request_doc_nuevo);
            
            $res_store  =   $res_store->getData();

            
            
            //====== MANEJANDO RESPUESTA ======
            //====== CASO I - DOC VENTA NUEVO GENERADO CORRECTAMENTE =======
            if($res_store->success){
               //===== ANULAR DOC VENTA ANTIGUO ======
               $documento_venta_antiguo->estado =   'ANULADO';
               $documento_venta_antiguo->save();

                //===== BUSCAMOS EL NUEVO DOC PARA EXTRAER SU CORRELATIVO Y SERIE =====
                $documento_venta_nuevo           =   Documento::find($res_store->documento_id);
                $serie_correlativo_doc_nuevo     =   $documento_venta_nuevo->serie.'-'.$documento_venta_nuevo->correlativo;

                $serie_correlativo_doc_antiguo   =   $documento_venta_antiguo->serie.'-'.$documento_venta_antiguo->correlativo;
                //==== PREPARAMOS EL MENSAJE ======
                $message    =   "SE CREO EL NUEVO DOCUMENTO DE VENTA: ".$serie_correlativo_doc_nuevo.", 
                                Y SE ANULÓ EL DOCUMENTO DE VENTA: ".$serie_correlativo_doc_antiguo;

                //===== COMMITEAMOS =====
                DB::commit();

               return response()->json(['success'   =>true,
                'documento_nuevo_id'     =>$res_store->documento_id,
                'documento_antiguo_id'   =>$documento_id,
                'message'=>$message]);

            }else{
                
                //====== ERROR AL GENERAR EL DOC VENTA NUEVO ======
                //======= VERIFICAMOS SI EL ERROR ES DE VALIDACIÓN ======
                if (property_exists($res_store, 'errors')) {
                    return response()->json(['success'=>false,
                    'errors'    =>  $res_store->errors,
                    'message'   =>  'ERROR DE VALIDACIÓN',
                    'data'      =>  $res_store->data,
                    'type'      =>  'VALIDATION']);
                }
                
                //======== ERROR EN LAS OPERACIONES SOBRE LA BD =======
                return response()->json(['success'=>false,
                'message'   =>$res_store->mensaje,
                'exception' =>property_exists($res_store, 'excepcion')?$res_store->excepcion:'',
                'type'      =>  "DB"]);
            }

        } catch (\Throwable $th) {
            DB::rollback();

            //===== ELIMINANDO DOC DE VENTA NUEVO GENERADO ====
            DB::table('cotizacion_documento')
              ->where('id', $res_store->documento_id)
              ->delete();

            return response()->json(['res'=>$th->getMessage(),'errorrr']);

        }

    }

    public function getRecibosCaja($cliente_id){
        try {
            $recibos_caja   =   DB::select('select rc.*,u.usuario as user_nombre
                                from recibos_caja as rc
                                inner join users as u on u.id=rc.user_id
                                where rc.cliente_id=? and rc.saldo > 0 and rc.estado="ACTIVO" and rc.estado_servicio <> "CANJEADO"
                                order by rc.created_at desc',[$cliente_id]);

            return response()->json(['success'=>true,'recibos_caja'=>$recibos_caja]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>'ERROR AL OBTENER RECIBOS DE CAJA DEL CLIENTE',
            'exception'=>$th->getMessage()]);
        }
    }

    //=============== CAMBIO DE TALLAS ==============
    public function cambiarTallasCreate($documento_id){
        try {
            $documento      =   Documento::find($documento_id);
            $cambios_tallas =   DB::select('select * from cambios_tallas as ct
                                where ct.estado = "ACTIVO" and ct.documento_id = ?',[$documento_id]);
            $detalles       =   $documento->detalles;
            
            return view('ventas.documentos.cambios_tallas.index',compact('documento','detalles','cambios_tallas'));
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }


    public function getTallas($producto_id,$color_id){
        try {
            $tallas     =   DB::select('select pct.producto_id,pct.color_id,pct.talla_id,
                            t.descripcion as talla_nombre,p.nombre as producto_nombre,c.descripcion as color_nombre,
                            pct.stock,pct.stock_logico
                            from producto_color_tallas as pct 
                            inner join productos as p on p.id=pct.producto_id
                            inner join colores as c on c.id=pct.color_id
                            inner join tallas as t on t.id=pct.talla_id
                            where pct.producto_id=? and pct.color_id=? and pct.estado="ACTIVO" and 
                            pct.stock_logico>0 and pct.stock>0',[$producto_id,$color_id]);
            
            return response()->json(['success'=>true,'tallas'=>$tallas]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>"ERROR AL OBTENER LAS TALLAS",
            'exception'=>$th->getMessage()]);
        }
    }

    public function getStock($producto_id,$color_id,$talla_id){
        try {
            $stock     =   DB::select('select pct.stock
                            from producto_color_tallas as pct 
                            where pct.producto_id=? and pct.color_id=? and pct.talla_id=?
                            and pct.estado="ACTIVO"',[$producto_id,$color_id,$talla_id]);
        
            return response()->json(['success'=>true,'stock'=>$stock]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'=>"ERROR AL OBTENER STOCK ACTUAL DE LA TALLA",
            'exception'=>$th->getMessage()]);
        }
    }

    public function validarCantCambiar($documento_id,$detalle_id,$cantidad){
        try {
            //====== OBTENER LA CANTIDAD DEL DETALLE ==========
            $cantidad_item     =   DB::select('select cdd.cantidad from cotizacion_documento_detalles as cdd
                                where cdd.documento_id=? and cdd.id=? and cdd.estado="ACTIVO"',
                                [$documento_id,$detalle_id,$cantidad]);


            if(count($cantidad_item) === 0){
                throw new Exception('NO SE ENCONTRÓ EL DETALLE #'.$detalle_id.' EN LA BASE DE DATOS');
            }


            //====== SI LA CANTIDAD DEL ITEM ES MAYOR O IGUAL A LA CANTIDAD QUE SE QUIERE CAMBIAR ========
            if($cantidad_item[0]->cantidad >= $cantidad){
                return response()->json(["success"=>true,"message"=>"CANTIDAD A CAMBIAR VÁLIDA"]);
            }else {
                return response()->json(["success"=>false,
                "message"=>"CANTIDAD A CAMBIAR DEBE SER MENOR O IGUAL A LA CANTIDAD DEL DETALLE"]);
            }

            
        } catch (\Throwable $th) {
            return response()->json(["success"=>false,"message"=>"ERROR EN EL SERVIDOR AL VALIDAR LA CANTIDAD A CAMBIAR",
            "exception"=>$th->getMessage()]);
        }
    }

    public function validarStock(Request $request){
        
        try {
            $nuevo_cambio       =   json_decode($request->get('nuevo_cambio'));
            
            //======= OBTENIENDO CONTENIDO =====
            $producto_cambiado  =   $nuevo_cambio->producto_cambiado;
            $producto_reemplazante  =   $nuevo_cambio->producto_reemplazante;
            $documento_id           =   $nuevo_cambio->documento_id;
            $cantidad_cambiar       =   $nuevo_cambio->cantidad;

            if(!$cantidad_cambiar){
                throw new Exception('EL PRODUCTO '.$producto_cambiado->producto_nombre.'-'.$producto_cambiado->color_nombre.'-'.
                $producto_cambiado->talla_nombre.' NO TIENE UNA CANTIDAD ASIGNADA PARA CAMBIO');
            }


            //======= OBTENIENDO STOCKS DEL PRODUCTO REEMPLAZANTE ========
            $stocks_producto_reemplazante    =   DB::select('select pct.stock,pct.stock_logico 
                                                    from producto_color_tallas as pct
                                                    inner join productos as p on p.id=pct.producto_id
                                                    where p.estado="ACTIVO" and pct.estado="ACTIVO" and pct.producto_id=? 
                                                    and pct.color_id=? and pct.talla_id=?',
                                                    [$producto_reemplazante->producto_id,$producto_reemplazante->color_id,
                                                    $producto_reemplazante->talla_id]);

            if(count($stocks_producto_reemplazante) === 0){
                throw new Exception('EL PRODUCTO '.$producto_reemplazante->producto_nombre.'-'.$producto_reemplazante->color_nombre.'-'.
                $producto_reemplazante->talla_nombre.' REEMPLAZANTE  NO FUE ENCONTRADO EN LA BASE DE DATOS');
            }   
            
            $stock_logico_producto_reemplazante =   $stocks_producto_reemplazante[0]->stock_logico;
            if($stock_logico_producto_reemplazante >= $cantidad_cambiar){

                //========== SEPARAMOS STOCK LÓGICO DEL PRODUCTO REEMPLAZANTE ======
                DB::update('UPDATE producto_color_tallas SET stock_logico = stock_logico - ? 
                WHERE producto_id = ? AND color_id = ? AND talla_id = ?', 
                [$cantidad_cambiar, $producto_reemplazante->producto_id, $producto_reemplazante->color_id, 
                $producto_reemplazante->talla_id]);

                return response()->json(['success'=>true,'message'  =>  'STOCK LÓGICO SEPARADO PRODUCTO: '.$producto_reemplazante->producto_nombre.
                '-'.$producto_reemplazante->color_nombre.'-'.$producto_reemplazante->talla_nombre]);
            }else{
                throw new Exception('STOCK LÓGICO INSUFICIENTE PRODUCTO: '.$producto_reemplazante->producto_nombre.
                '-'.$producto_reemplazante->color_nombre.'-'.$producto_reemplazante->talla_nombre);

            }
            
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'message'  =>  'ERROR EN EL SERVIDOR AL VALIDAR STOCKS',
            'exception'=>$th->getMessage()]);
        }
    }

    public function devolverStockLogico(Request $request){
        DB::beginTransaction();

        try {
            $cambios_devolver  =  json_decode($request->get('cambios_devolver'));
        
            $productos_message  =   '';
    
            foreach ($cambios_devolver as $cambio) {
               
                //======= OBTENIENDO PARAMETROS ========
                $producto_reemplazante  =   $cambio->producto_reemplazante;
                $cantidad               =   (int)$cambio->cantidad;
    
                DB::update('UPDATE producto_color_tallas SET stock_logico = stock_logico + ? 
                WHERE producto_id = ? AND color_id = ? AND talla_id = ?', 
                [$cantidad, $producto_reemplazante->producto_id, $producto_reemplazante->color_id, 
                $producto_reemplazante->talla_id]);
    
                $productos_message .= ' '.$producto_reemplazante->producto_nombre.'-'.
                                        $producto_reemplazante->color_nombre.'-'.$producto_reemplazante->talla_nombre;
            }
    
            DB::commit();
            return response()->json(['success'=>true,'message'=>'CANTIDAD DEVUELTA PRODUCTOS: '.$productos_message]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['success'=>false,
            'message'=>'ERROR AL DEVOLVER STOCKS LÓGICOS',
            'exception'=>$th->getMessage(),
            'line'=>$th->getLine()],
            );
        }

    }


    public function cambiarTallasStore(Request $request){
        DB::beginTransaction();
        try {
            $documento_id   =   $request->get('documento_id');
            $documento      =   Documento::find($documento_id);

            if(!$documento){
                throw new Exception('NO SE ENCONTRÓ EL DOCUMENTO DE VENTA EN LA BASE DE DATOS');
            }

            $detalles   =   $documento->detalles;
            
            //======= GRABANDO CAMBIOS ======
            $cambios    =   json_decode($request->get('cambios'));

            //======= CREANDO NOTA DE INGRESO PARA PRODUCTOS CAMBIADOS =======
            $notaingreso    = new NotaIngreso();
            $fecha_hoy      = Carbon::now()->toDateString();
            $fecha          = Carbon::createFromFormat('Y-m-d', $fecha_hoy);
            
            $fecha          = str_replace("-", "", $fecha);
            $fecha          = str_replace(" ", "", $fecha);
            $fecha          = str_replace(":", "", $fecha);
            
            $notaingreso->numero    =   $fecha . (DB::table('nota_ingreso')->count() + 1);
            $notaingreso->fecha     =   $fecha_hoy;
            $notaingreso->destino   =   'ALMACÉN';
            $notaingreso->origen    =   'IMPORT EXCEL';
            $notaingreso->usuario       = Auth()->user()->usuario;
            $notaingreso->observacion   =   'CAMBIO DE TALLAS EN EL DOCUMENTO '.$documento->serie.'-'.$documento->correlativo;
            $notaingreso->save();

            //======== CREANDO NOTA DE SALIDA PARA PRODUCTOS REEMPLAZANTES =======
            $notasalidad            =   new NotaSalidad();
            $notasalidad->numero    =   $fecha.(DB::table('nota_salidad')->count()+1);
            $notasalidad->fecha     =   $fecha_hoy;
            $notasalidad->destino   =   "CENTRAL";
            $notasalidad->origen    =   "CAMBIOS";
            $notasalidad->observacion   =   "CAMBIO DE TALLAS EN EL DOCUMENTO " .$documento->serie.'-'.$documento->correlativo;
            $notasalidad->usuario       =   Auth()->user()->usuario;
            $notasalidad->save();


            foreach ($cambios as $cambio) {
                $producto_cambiado      =   $cambio->producto_cambiado;
                $producto_reemplazante  =   $cambio->producto_reemplazante;
                $cantidad               =   $cambio->cantidad;

                //======== GENERANDO DETALLE DE LA NOTA DE INGRESO ========
                $detalleNotaIngreso                     =   new   DetalleNotaIngreso();
                $detalleNotaIngreso->nota_ingreso_id    =   $notaingreso->id;
                $detalleNotaIngreso->producto_id        =   $producto_cambiado->producto_id;
                $detalleNotaIngreso->color_id           =   $producto_cambiado->color_id;
                $detalleNotaIngreso->talla_id           =   $producto_cambiado->talla_id;
                $detalleNotaIngreso->cantidad           =   $cantidad;
                $detalleNotaIngreso->save();


                //========= GENERANDO DETALLE DE LA NOTA DE SALIDA =======
                $detalleNotaSalida                     =   new   DetalleNotaSalidad();
                $detalleNotaSalida->nota_salidad_id    =   $notasalidad->id;
                $detalleNotaSalida->producto_id        =   $producto_reemplazante->producto_id;
                $detalleNotaSalida->color_id           =   $producto_reemplazante->color_id;
                $detalleNotaSalida->talla_id           =   $producto_reemplazante->talla_id;
                $detalleNotaSalida->cantidad           =   $cantidad;
                $detalleNotaSalida->disableDecrementarStockLogico();
                $detalleNotaSalida->save();

                 //===== GRABANDO CAMBIO ======
                 $cambio_talla                           =   new CambioTalla();
                 $cambio_talla->documento_id             =   $documento_id;
                 $cambio_talla->detalle_id               =   $producto_cambiado->detalle_id;
                 $cambio_talla->producto_reemplazado_id  =   $producto_cambiado->producto_id;
                 $cambio_talla->color_reemplazado_id     =   $producto_cambiado->color_id;
                 $cambio_talla->talla_reemplazado_id     =   $producto_cambiado->talla_id;
                 $cambio_talla->producto_reemplazado_nombre  =   $producto_cambiado->producto_nombre;
                 $cambio_talla->color_reemplazado_nombre     =   $producto_cambiado->color_nombre;
                 $cambio_talla->talla_reemplazado_nombre     =   $producto_cambiado->talla_nombre;
                 $cambio_talla->cantidad_detalle         =   $producto_cambiado->cantidad_detalle;
                 $cambio_talla->producto_reemplazante_id =   $producto_reemplazante->producto_id;
                 $cambio_talla->color_reemplazante_id    =   $producto_reemplazante->color_id;
                 $cambio_talla->talla_reemplazante_id    =   $producto_reemplazante->talla_id;
                 $cambio_talla->producto_reemplazante_nombre     =   $producto_reemplazante->producto_nombre;
                 $cambio_talla->color_reemplazante_nombre        =   $producto_reemplazante->color_nombre;
                 $cambio_talla->talla_reemplazante_nombre        =   $producto_reemplazante->talla_nombre;
                 $cambio_talla->cantidad_cambiada        =   $cantidad;
                 $cambio_talla->cantidad_sin_cambio      =   $producto_cambiado->cantidad_detalle - $cantidad;
                 $cambio_talla->user_id                  =   Auth::user()->id;
                 $cambio_talla->user_nombre              =   Auth::user()->usuario;
                 $cambio_talla->save();
 
                 //====== ACTUALIZANDO ESTADO DEL DETALLE DEL DOCUMENTO =======
                 DB::table('cotizacion_documento_detalles')
                 ->where('documento_id', $documento->id)
                 ->where('id', $producto_cambiado->detalle_id)
                 ->where('producto_id', $producto_cambiado->producto_id)
                 ->where('color_id', $producto_cambiado->color_id)
                 ->where('talla_id', $producto_cambiado->talla_id)
                 ->update(['estado_cambio_talla' => "CON CAMBIOS",
                 'cantidad_cambiada'     => $cantidad,
                 'cantidad_sin_cambio'   => $producto_cambiado->cantidad_detalle - $cantidad]);
 
            }

            //======== MARCANDO DOC VENTA CON CAMBIO DE TALLA ========
            $documento->cambio_talla = '1';
            $documento->update();

            DB::commit();
            return response()->json(['success'=>true,'message'=>'ÉXITO, SE CAMBIARON LAS TALLAS DEL DOC N° '.$documento->serie.'-'.$documento->correlativo]);

          
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,
            'message'=>'ERROR AL CAMBIAR TALLAS DEL DOC N° '.$documento->serie.'-'.$documento->correlativo,
            'exception'=>$th->getMessage()]);
        }
    }

    public function getHistorialCambiosTallas($detalle_id,$documento_id){
        try {
            $cambios_tallas =   DB::select('select * from cambios_tallas as ct
                                where ct.documento_id = ? and ct.detalle_id = ?',
                                [$documento_id,$detalle_id]);
            
            return response()->json(['success' => true,'cambios_tallas'=>$cambios_tallas]);
        } catch (\Throwable $th) {
           return response()->json(['success'=>false,'message'=>$th->getMessage()]);
        }
    }
}
