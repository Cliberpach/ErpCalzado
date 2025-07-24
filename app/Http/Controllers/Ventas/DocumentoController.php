<?php

namespace App\Http\Controllers\Ventas;

use App\Almacenes\Almacen;
use App\Almacenes\Categoria;
use stdClass;
use Exception;
use Carbon\Carbon;
use App\Ventas\Cliente;
use App\Ventas\Retencion;
use App\Ventas\Cotizacion;
use App\Ventas\ErrorVenta;
use App\Ventas\EnvioVenta;
use App\Almacenes\Producto;
use Illuminate\Http\Request;
use App\Almacenes\Kardex;
use App\Almacenes\LoteProducto;
use App\Events\VentasCajaEvent;
use App\Events\NotifySunatEvent;
use App\Mantenimiento\Condicion;
use App\Ventas\RetencionDetalle;
use App\Ventas\CotizacionDetalle;
use App\Ventas\Documento\Detalle;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Events\DocumentoNumeracion;
use App\Ventas\Documento\Documento;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Empresa\Banco;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mantenimiento\Empresa\Empresa;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Pos\DetalleMovimientoVentaCaja;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;
use App\Mantenimiento\Empresa\Numeracion;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
//CONVERTIR DE NUMEROS A LETRAS
use App\Mantenimiento\Tabla\Detalle as TablaDetalle;

use App\Almacenes\ProductoColorTalla;
use App\Almacenes\Talla;
use App\Almacenes\Modelo;
use App\Almacenes\Color;
use App\Almacenes\Conductor;
use App\Classes\ValidatedDetail;
use App\Almacenes\DetalleNotaIngreso;
use App\Almacenes\NotaIngreso;
use Yajra\DataTables\Facades\DataTables;
use App\Almacenes\DetalleNotaSalidad;
use App\Almacenes\Marca;
use App\Almacenes\NotaSalidad;
use App\Almacenes\ProductoColor;
use App\Almacenes\Vehiculo;
use App\Http\Controllers\UtilidadesController;
use App\Http\Requests\Ventas\DocVenta\DocVentaStoreRequest;
use App\Http\Requests\Ventas\DocVenta\DocVentaUpdateRequest;
use App\Http\Requests\Ventas\Guias\GuiaStoreRequest;
use App\Mantenimiento\Sedes\Sede;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Provincia;
use App\Pos\ReciboCaja;
use App\User;
use Illuminate\Support\Facades\Response;
use App\Ventas\CambioTalla;
use Throwable;

class DocumentoController extends Controller
{
    public function index()
    {
        $this->authorize('haveaccess', 'documento_venta.index');
        $dato = "Message";

        $departamentos  =   Departamento::all();
        $provincias     =   Provincia::all();
        $distritos      =   Distrito::all();
        $almacenes      =   Almacen::where('estado', 'ACTIVO')->where('tipo_almacen', 'PRINCIPAL')->get();
        $registrador    =   Auth::user();
        $sede           =   Sede::find($registrador->sede_id);
        $modos_pago     =   modos_pago();


        //broadcast(new NotifySunatEvent($dato));

        return view(
            'ventas.documentos.index',
            compact('departamentos', 'provincias', 'distritos', 'almacenes', 'registrador', 'sede', 'modos_pago')
        );
    }

    public function getVentas(Request $request)
    {
        $filtro_fecha_inicio    =   $request->get('fechaInicio');
        $filtro_fecha_fin       =   $request->get('fechaFin');

        $ventas = DB::table('cotizacion_documento as cd')
            ->select(
                'cd.regularizado_de_serie',
                'cd.cdr_response_code',
                'cd.guia_id',
                'cd.convert_de_serie',
                'cd.convert_en_id',
                'cd.convert_de_id',
                'co.id as cotizacion_id',
                'u.usuario as registrador_nombre',
                'es.nombre as sede_nombre',
                'cd.almacen_nombre',
                'cd.id',
                'cd.tipo_venta_id as tipo_venta',
                DB::raw('CONCAT(cd.serie, "-", cd.correlativo) as numero_doc'),
                'cd.serie',
                'cd.correlativo',
                'cd.pedido_id',
                'cd.tipo_doc_venta_pedido',
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
                'cd.condicion_id',
                'cd.sunat',
                'cd.regularize',
                'cd.contingencia',
                'cd.sunat_contingencia',
                'cd.documento_cliente',
                'cd.estado',
                'cd.cambio_talla',
                'cd.total',
                'cd.total_pagar',
                DB::raw('DATEDIFF(NOW(), cd.fecha_documento) as dias'),
                DB::raw('(SELECT COUNT(nota_electronica.id) FROM nota_electronica WHERE nota_electronica.documento_id = cd.id) as notas'),
                'envios_ventas.estado AS estado_despacho',
                'condicions.descripcion as condicion',
                'clientes.correo_electronico as correo',
                'clientes.telefono_movil as telefonoMovil'
            )
            ->leftJoin('envios_ventas', 'cd.id', '=', 'envios_ventas.documento_id')
            ->leftJoin('condicions', 'cd.condicion_id', '=', 'condicions.id')
            ->leftJoin('clientes', 'cd.cliente_id', '=', 'clientes.id')
            ->leftJoin('empresa_sedes as es', 'es.id', 'cd.sede_id')
            ->leftJoin('users as u', 'u.id', 'cd.user_id')
            ->leftJoin('cotizaciones as co', 'co.id', 'cd.cotizacion_venta')
            ->orderByDesc('cd.id');

        if ($filtro_fecha_inicio) {
            $ventas->whereDate('cd.created_at', '>=', $filtro_fecha_inicio);
        }
        if ($filtro_fecha_fin) {
            $ventas->whereDate('cd.created_at', '<=', $filtro_fecha_fin);
        }

        //========= FILTRO POR ROLES ======
        $roles = DB::table('role_user as rl')
            ->join('roles as r', 'r.id', '=', 'rl.role_id')
            ->where('rl.user_id', Auth::user()->id)
            ->pluck('r.name')
            ->toArray();

        //======== ADMIN PUEDE VER TODAS LAS VENTAS DE SU SEDE =====
        if (in_array('ADMIN', $roles)) {
            $ventas->where('cd.sede_id', Auth::user()->sede_id);
        } else {

            //====== USUARIOS PUEDEN VER SUS PROPIAS VENTAS ======
            $ventas->where('cd.sede_id', Auth::user()->sede_id)
                ->where('cd.user_id', Auth::user()->id);
        }

        return DataTables::of($ventas)
            ->filterColumn('numero_doc', function ($query, $keyword) {
                $query->whereRaw("LOWER(CONCAT(cd.serie, '-', cd.correlativo)) like ?", ["%" . strtolower($keyword) . "%"]);
            })
            ->make(true);
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
        $documentos = DB::table('cotizacion_documento as cd')
            ->select(
                'cd.regularizado_de_serie',
                'cd.cdr_response_code',
                'cd.guia_id',
                'cd.convert_de_serie',
                'cd.convert_en_id',
                'cd.convert_de_id',
                'co.id as cotizacion_id',
                'u.usuario as registrador_nombre',
                'es.nombre as sede_nombre',
                'cd.almacen_nombre',
                'cd.id',
                'cd.tipo_venta_id as tipo_venta',
                DB::raw('CONCAT(cd.serie, "-", cd.correlativo) as numero_doc'),
                'cd.serie',
                'cd.correlativo',
                'cd.pedido_id',
                'cd.tipo_doc_venta_pedido',
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
                'cd.condicion_id',
                'cd.sunat',
                'cd.regularize',
                'cd.contingencia',
                'cd.sunat_contingencia',
                'cd.documento_cliente',
                'cd.estado',
                'cd.cambio_talla',
                'cd.total',
                'cd.total_pagar',
                DB::raw('DATEDIFF(NOW(), cd.fecha_documento) as dias'),
                DB::raw('(SELECT COUNT(nota_electronica.id) FROM nota_electronica WHERE nota_electronica.documento_id = cd.id) as notas'),
                'envios_ventas.estado AS estado_despacho',
                'condicions.descripcion as condicion',
                'clientes.correo_electronico as correo',
                'clientes.telefono_movil as telefonoMovil'
            )
            ->leftJoin('envios_ventas', 'cd.id', '=', 'envios_ventas.documento_id')
            ->leftJoin('condicions', 'cd.condicion_id', '=', 'condicions.id')
            ->leftJoin('clientes', 'cd.cliente_id', '=', 'clientes.id')
            ->leftJoin('empresa_sedes as es', 'es.id', 'cd.sede_id')
            ->leftJoin('users as u', 'u.id', 'cd.user_id')
            ->leftJoin('cotizaciones as co', 'co.id', 'cd.cotizacion_venta');
        // ->where('cd.estado', '<>', 'ANULADO');

        /*if (!PuntoVenta() && !FullAccess()) {
            $documentos->where('cotizacion_documento.user_id', Auth::user()->id);
        }*/

        if ($request->has('fechaInicial')) {
            $documentos->where('cd.fecha_documento', '>=', $request->get('fechaInicial'));
        }

        if ($request->has('cliente')) {
            $cliente = $request->get('cliente');
            if (is_numeric($cliente)) {
                $documentos->where('cd.documento_cliente', 'LIKE', "%{$cliente}%");
            } else {
                $documentos->where('cd.cliente', 'LIKE', "%{$cliente}%");
            }
        }

        if ($request->has('numero_doc')) {
            $numero_doc = $request->get('numero_doc');
            $documentos->where(DB::raw('CONCAT(cd.serie, "-", cd.correlativo)'), 'LIKE', "%{$numero_doc}%");
        }

        //========= FILTRO POR ROLES ======
        $roles = DB::table('role_user as rl')
            ->join('roles as r', 'r.id', '=', 'rl.role_id')
            ->where('rl.user_id', Auth::user()->id)
            ->pluck('r.name')
            ->toArray();

        //======== ADMIN PUEDE VER TODAS LAS VENTAS DE SU SEDE =====
        if (in_array('ADMIN', $roles)) {
            $documentos->where('cd.sede_id', Auth::user()->sede_id);
        } else {

            //====== USUARIOS PUEDEN VER SUS PROPIAS VENTAS ======
            $documentos->where('cd.sede_id', Auth::user()->sede_id)
                ->where('cd.user_id', Auth::user()->id);
        }

        $documentos = $documentos->orderBy('cd.id', 'desc')->paginate($request->tamanio);

        return response()->json([
            'pagination' => [
                'currentPage' => $documentos->currentPage(),
                'from' => $documentos->firstItem(),
                'lastPage' => $documentos->lastPage(),
                'perPage' => $documentos->perPage(),
                'to' => $documentos->lastPage(),
                'total' => $documentos->total(),
            ],
            'documentos' => $documentos->items(),
            'modos_pago' => modos_pago()
        ]);
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

    public function getProductos(Request $request)
    {
        try {

            $categoria_id   =   $request->get('categoria_id');
            $marca_id       =   $request->get('marca_id');
            $modelo_id      =   $request->get('modelo_id');



            $query = 'SELECT p.id, p.nombre
                    FROM productos AS p
                    WHERE p.estado = "ACTIVO"';

            $params = [];

            if ($modelo_id) {
                $query .= ' AND p.modelo_id = ?';
                $params[] = $modelo_id;
            }

            if ($marca_id) {
                $query .= ' AND p.marca_id = ?';
                $params[] = $marca_id;
            }

            if ($categoria_id) {
                $query .= ' AND p.categoria_id = ?';
                $params[] = $categoria_id;
            }

            $productos = DB::select($query, $params);

            return response()->json(['success' => true, 'productos' => $productos]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getColoresTallas($almacen_id, $producto_id)
    {

        try {

            $stocks =  DB::select(
                'select p.id as producto_id, p.nombre as producto_nombre,
                                    p.precio_venta_1,p.precio_venta_2,p.precio_venta_3,
                                    pct.color_id,c.descripcion as color_name,
                                    pct.talla_id,t.descripcion as talla_name,pct.stock,
                                    pct.stock_logico,
                                    pct.almacen_id
                                    from producto_color_tallas as pct
                                    inner join productos as p on p.id = pct.producto_id
                                    inner join colores as c on c.id = pct.color_id
                                    inner join tallas as t on t.id = pct.talla_id
                                    where p.id = ?
                                    AND c.estado="ACTIVO"
                                    AND t.estado="ACTIVO"
                                    AND p.estado="ACTIVO"
                                    AND pct.almacen_id = ?
                                    order by p.id,c.id,t.id',
                [$producto_id, $almacen_id]
            );

            $producto_colores   =   DB::select(
                'select
                                    pc.almacen_id,
                                    p.id as producto_id,
                                    p.nombre as producto_nombre,
                                    c.id as color_id,
                                    c.descripcion as color_nombre,
                                    p.precio_venta_1,
                                    p.precio_venta_2,
                                    p.precio_venta_3
                                    from producto_colores as pc
                                    inner join productos as p on p.id = pc.producto_id
                                    inner join colores as c on c.id = pc.color_id
                                    where
                                    p.id = ?
                                    AND pc.almacen_id = ?
                                    AND c.estado = "ACTIVO"
                                    AND p.estado = "ACTIVO"
                                    group by pc.almacen_id,p.id,p.nombre,c.id,c.descripcion,
                                    p.precio_venta_1,p.precio_venta_2,p.precio_venta_3
                                    order by p.id,c.id',
                [$producto_id, $almacen_id]
            );

            $precios_venta  =   DB::select('SELECT
                                p.precio_venta_1,
                                p.precio_venta_2,
                                p.precio_venta_3
                                FROM
                                    productos AS p
                                WHERE
                                p.id = ? AND p.estado = "ACTIVO" ', [$producto_id]);

            if (!empty($precios_venta)) {
                $precios_venta_array = array_filter([
                    $precios_venta[0]->precio_venta_1,
                    $precios_venta[0]->precio_venta_2,
                    $precios_venta[0]->precio_venta_3,
                ]);
            } else {
                $precios_venta_array = [];
            }

            $productosProcesados = [];
            foreach ($producto_colores as $pc) {
                if (!in_array($pc->producto_id, $productosProcesados)) {
                    $pc->printPreciosVenta = TRUE;
                    array_push($productosProcesados, $pc->producto_id);
                } else {
                    $pc->printPreciosVenta = FALSE;
                }
            }

            return response()->json([
                "success"          =>  true,
                "stocks"            =>  $stocks,
                "producto_colores" =>  $producto_colores,
                'precios_venta'     =>  $precios_venta_array
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
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
                $nombreImagenPago       =   $documento->serie . '-' . $documento->correlativo . '.' . $extension;
                $documento->ruta_pago   =   $request->file('imagen')->storeAs('public/pagos', $nombreImagenPago);
            }

            if ($request->hasFile('imagen2')) {
                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'))) {
                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pagos'));
                }
                $extension              =   $request->file('imagen2')->getClientOriginalExtension();
                $nombreImagenPago       =   $documento->serie . '-' . $documento->correlativo . '-2' . '.' . $extension;
                $documento->ruta_pago_2   =   $request->file('imagen2')->storeAs('public/pagos', $nombreImagenPago);
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
            if ($request->get('modo_pago') === "4-RECIBO DE CAJA") {
                //======== OBTENEMOS TODOS LOS RECIBOS DE CAJA DEL CLIENTE ==========
                $recibos_caja_cliente   =   DB::select(
                    'select * from recibos_caja as rc
                                            where rc.cliente_id=? and rc.saldo>0
                                            and rc.estado="ACTIVO"
                                            and (rc.estado_servicio="LIBRE" or rc.estado_servicio="USANDO")
                                            order by rc.created_at',
                    [$documento->cliente_id]
                );

                $total_pendiente    =   $documento->total_pagar;

                //========= RESTAMOS SALDO EN ORDEN ASC POR FECHA DE CREACIÓN =========
                foreach ($recibos_caja_cliente as $recibo) {

                    $saldo_recibo       =   $recibo->saldo;

                    //======= SI EL TOTAL PENDIENTE >= SALDO DEL RECIBO CAJA ========
                    if ($total_pendiente >= $saldo_recibo) {
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
                            ->update([
                                'saldo' => $nuevo_saldo_recibo,
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
                                'saldo_despues' => $nuevo_saldo_recibo,
                                'created_at'   => now(),
                                'updated_at'   => now()
                            ]);
                    } else {
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
                            ->update([
                                'saldo' => $nuevo_saldo_recibo,
                                'estado_servicio' => $nuevo_estado_servicio_recibo,
                                'updated_at' => now()
                            ]);

                        //========= GRABAMOS EL DETALLE DE USO DEL RECIBO ======
                        DB::table('recibos_caja_detalle')
                            ->insert([
                                'recibo_id'   =>  $recibo->id,
                                'documento_id'          =>  $documento->id,
                                'saldo_antes'           =>  $saldo_anterior_recibo,
                                'monto_usado'           =>  $monto_usado,
                                'saldo_despues'         =>  $nuevo_saldo_recibo,
                                'created_at'            =>  now(),
                                'updated_at'            =>  now()
                            ]);
                    }

                    //======== DETENER EL BUCLE SI EL TOTAL PENDIENTE ES 0 ========
                    if ($total_pendiente === 0) {
                        break;
                    }
                }
            }


            DB::commit();
            Session::flash('success', 'Documento pagado con exito.');
            return redirect()->route('ventas.documento.index');
        } catch (Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
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

        $empresas       = Empresa::where('estado', 'ACTIVO')->get();
        $clientes       = Cliente::where('estado', 'ACTIVO')->get();
        $fecha_hoy      = Carbon::now()->toDateString();
        $condiciones    = Condicion::where('estado', 'ACTIVO')->get();

        $almacenes      =   Almacen::where('estado', 'ACTIVO')->where('tipo_almacen', 'PRINCIPAL')->get();

        $departamentos  =   Departamento::all();
        $provincias     =   Provincia::all();
        $distritos      =   Distrito::all();

        $registrador    =   Auth::user();
        $sede           =   Sede::find($registrador->sede_id);

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
            $errores    =   collect();
            $devolucion =   false;
            $cotizacion =   Cotizacion::findOrFail($request->get('cotizacion'));
            $detalles   =   CotizacionDetalle::where('cotizacion_id', $request->get('cotizacion'))
                ->with('producto', 'color', 'talla')->get();

            //================ VALIDANDO STOCKS_LOGICOS Y CANTIDADES SOLICITADAS =====================
            $validaciones = self::validacionStockCantidad($detalles);

            //========= OBTENER LOS DETALLES CON STOCK INSUFICIENTE ============
            $detallesWithStockInsuficiente = array_filter($validaciones, function ($validacion) {
                return $validacion->getTipo() == 'STOCK LOGICO INSUFICIENTE';
            });

            //========= OBTENER LOS DETALLES CON STOCK LÓGICO VÁLIDO O SUFICIENTE ============
            $detallesWithStockValido = array_filter($validaciones, function ($validacion) {
                return $validacion->getTipo() == 'STOCK LOGICO VÁLIDO';
            });

            //========= OBTENER LOS DETALLES QUE NO EXISTEN EN PRODUCTO COLOR TALLAS ============
            $detallesNotExists = array_filter($validaciones, function ($validacion) {
                return $validacion->getTipo() == 'NO EXISTE EL PRODUCTO COLOR TALLA';
            });

            $cantidadErrores =  count($detallesWithStockInsuficiente) + count($detallesNotExists);

            //============= SEPARAR STOCK_LOGICO CUANDO NO HAY ERRORES ===========
            if ($cantidadErrores == 0) {
                foreach ($validaciones as $itemValidado) {
                    DB::table('producto_color_tallas')
                        ->where('almacen_id', $itemValidado->getAlmacenId())
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
                    'producto_id'           =>  $itemValidado->getProductoId(),
                    'color_id'              =>  $itemValidado->getColorId(),
                    'talla_id'              =>  $itemValidado->getTallaId(),
                    'producto_nombre'       =>  $itemValidado->getProductoNombre(),
                    'color_nombre'          =>  $itemValidado->getColorNombre(),
                    'talla_nombre'          =>  $itemValidado->getTallaNombre(),
                    'stock_logico'          =>  $itemValidado->getStockLogico(),
                    'cantidad_solicitada'   =>  $itemValidado->getCantidadSolicitada(),
                    'precio_unitario'       =>  $itemValidado->getPrecioUnitario(),
                    'porcentaje_descuento'  =>  $itemValidado->getPorcentajeDescuento(),
                    'precio_unitario_nuevo' =>  $itemValidado->getPrecioUnitarioNuevo(),
                    'tipo'                  =>  $itemValidado->getTipo(),
                ];

                $detalleValidado[] = $detalleArray;
            }

            $tallas = Talla::all();
            $origenes_ventas    =   DB::select('SELECT
                                    td.id,
                                    td.descripcion
                                    FROM tabladetalles AS td
                                    WHERE
                                    td.tabla_id="36"
                                    AND td.estado="ACTIVO"');

            return view('ventas.documentos.cotizacion_a_docventa.index', [
                'cotizacion'    =>  $cotizacion,
                'empresas'      =>  $empresas,
                'clientes'      =>  $clientes,
                'condiciones'   =>  $condiciones,
                'errores'       =>  $errores,
                'fecha_hoy'     =>  $fecha_hoy,
                'fullaccess'    =>  $fullaccess,
                'dolar'         =>  $dolar,
                'detalle'       =>  $detalleValidado,
                'tallas'        =>  $tallas,
                'cantidadErrores'   =>  $cantidadErrores,
                'departamentos'     =>  departamentos(),
                'origenes_ventas' =>  $origenes_ventas,
            ]);
        }

        if (empty($cotizacion)) {
            return view('ventas.documentos.' . $vista, [
                'departamentos' =>  $departamentos,
                'provincias'    =>  $provincias,
                'distritos'     =>  $distritos,
                'almacenes'     =>  $almacenes,
                'registrador'   =>  $registrador,
                'sede'          =>  $sede
            ]);
        }
    }

    public function ObtenerCotizacionForVenta(Request $request) {}
    public function getCreate(Request $request)
    {
        try {

            $this->authorize('haveaccess', 'documento_venta.index');

            $sede_id        =   Auth::user()->sede_id;

            $almacenes          =   Almacen::where('estado', 'ACTIVO')->where('tipo_almacen', 'PRINCIPAL')->get();


            $empresas   =   Empresa::where('estado', 'ACTIVO')->get();
            $clientes   =   Cliente::where('estado', 'ACTIVO')->get([
                "id",
                "tabladetalles_id",
                "tipo_documento",
                "documento",
                "nombre"
            ]);

            $condiciones    =   Condicion::where('estado', 'ACTIVO')->get();
            $fullaccess     =   false;
            $tipos_ventas   =   tipos_venta();
            $tipoVentaArray =   collect();
            $departamentos  =   Departamento::all();
            $provincias     =   Provincia::all();
            $distritos      =   Distrito::all();

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
            foreach ($tipos_ventas as $tipo) {
                if (ifComprobanteSeleccionado($tipo->id) && ($tipo->tipo == 'VENTA' || $tipo->tipo == 'AMBOS')) {
                    $tipoVentaArray->push([
                        "id" => $tipo->id,
                        "nombre" => $tipo->nombre,
                    ]);
                }
            }

            return response()->json([
                "initData" => [
                    'empresas'      =>  $empresas,
                    'clientes'      =>  $clientes,
                    'condiciones'   =>  $condiciones,
                    'fullaccess'    =>  $fullaccess,
                    'vista'         =>  $vista,
                    "tipoVentas"    =>  $tipoVentaArray,
                    "modelos"       =>  Modelo::where('estado', 'ACTIVO')->get(),
                    "marcas"        =>  Marca::where('estado', 'ACTIVO')->get(),
                    "categorias"    =>  Categoria::where('estado', 'ACTIVO')->get(),
                    "tallas"        =>  Talla::where('estado', 'ACTIVO')->get(),
                    'almacenes'     =>  [],
                    'sede_id'       =>  $sede_id,
                    'departamentos' =>  $departamentos,
                    'provincias'    =>  $provincias,
                    'distritos'     =>  $distritos
                ],
                "succes" => true
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                "success" => false,
                "initData" => null
            ]);
        }
    }
    public function devolverCantidad($devolucion)
    {
        if ($devolucion->producto != 0) {
            DB::table('producto_color_tallas')
                ->where('producto_id', $devolucion->producto)
                ->where('color_id', $devolucion->color)
                ->where('talla_id', $devolucion->talla)
                ->update([
                    'stock_logico' => DB::raw('stock_logico + ' . $devolucion->cantidad)
                ]);
        }
    }


    public function validacionStockCantidad($detalles)
    {

        $validaciones = [];
        //======== RECORRIENDO CADA PRODUCTO DEL DETALLE DE LA COTIZACIÓN ===========
        foreach ($detalles as $detalle) {

            //=========== OBTENIENDO STOCK LÓGICO DE UN PRODUCTO =============
            $productoExiste =   DB::select(
                'select
                                stock_logico
                                from producto_color_tallas as pct
                                where
                                pct.almacen_id = ?
                                and pct.producto_id = ?
                                and pct.color_id = ?
                                and pct.talla_id = ?',
                [
                    $detalle->almacen_id,
                    $detalle->producto_id,
                    $detalle->color_id,
                    $detalle->talla_id
                ]
            );

            $item_producto_nombre   =   Producto::findOrFail($detalle->producto_id)->nombre;
            $item_color_nombre      =   Color::findOrFail($detalle->color_id)->descripcion;
            $item_talla_nombre      =   Talla::findOrFail($detalle->talla_id)->descripcion;

            //===== EN CASO EXISTA EL PRODUCTO COLOR TALLA =====
            if (count($productoExiste) > 0) {
                $stock_logico   =   $productoExiste[0]->stock_logico;
                if ($stock_logico < $detalle->cantidad) {
                    $registro                           = new ValidatedDetail();
                    $registro->setStockLogico($stock_logico);
                    $registro->setTipo('STOCK LOGICO INSUFICIENTE');
                } else {
                    $registro                           = new ValidatedDetail();
                    $registro->setStockLogico($stock_logico);
                    $registro->setTipo('STOCK LOGICO VÁLIDO');
                }
            } else {
                $registro                           = new ValidatedDetail();
                $registro->setStockLogico(null);
                $registro->setTipo('NO EXISTE EL PRODUCTO COLOR TALLA');
            }

            $registro->setAlmacenId($detalle->almacen_id);
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
            $validaciones[] =   $registro;
        }

        return $validaciones;
    }


    /*
array:27 [
  "fecha_documento_campo"   => "2025-02-10"
  "fecha_atencion_campo"    => "2025-02-10"
  "fecha_vencimiento_campo" => "2025-02-10"
  "tipo_venta"              => 129
  "condicion_id"            => "1-CONTADO"
  "cliente_id"              => 1
  "tipo_pago_id"            => null
  "efectivo"                => 0
  "importe"                 => 0
  "empresa_id"              => 1
  "observacion"             => null
  "igv"                     => 18
  "igv_check"               => true
  "cotizacion_id"           => null
  "productos_tabla"         => "[{"producto_id":"1","color_id":"2","producto_nombre":"PRODUCTO TEST","color_nombre":"AZUL","precio_venta":"2.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"1"}],"subtotal":2},{"producto_id":"1","color_id":"3","producto_nombre":"PRODUCTO TEST","color_nombre":"CELESTE","precio_venta":"2.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"2"}],"subtotal":4}]"
  "envio_sunat"             => false
  "monto_sub_total"         => 6
  "monto_total_igv"         => 0.91525423728813
  "monto_total"             => 5.0847457627119
  "tipo_cliente_documento"  => null
  "moneda"                  => "SOLES"
  "data_envio"              => "{}"
  "monto_embalaje"          => 0
  "monto_envio"             => 0
  "monto_total_pagar"       => 6
  "monto_descuento"         => 0
  "almacenSeleccionado"     => 2
  "sede_id"                 => "1"
  "data_envio"              => "{"departamento":{"id":13,"nombre":"LA LIBERTAD","zona":"NORTE"},"provincia":{"id":1301,"text":"TRUJILLO"},"distrito":{"id":130101,"text":"TRUJILLO"},"tipo_envio":{"id":188,"descripcion":"AGENCIA"},"empresa_envio":{"id":2,"empresa":"EMTRAFESA","tipo_envio":"AGENCIA","estado":"ACTIVO","created_at":"2025-02-12 17:43:16","updated_at":"2025-02-12 17:43:16"},"sede_envio":{"id":2,"empresa_envio_id":2,"direccion":"AV TUPAC AMARU 123","departamento":"LA LIBERTAD","provincia":"TRUJILLO","distrito":"TRUJILLO","estado":"ACTIVO","created_at":"2025-02-12 17:45:21","updated_at":"2025-02-12 17:45:21"},"destinatario":{"tipo_documento":"DNI","nro_documento":"75563122","nombres":"ALTRUCAZ"},"direccion_entrega":"AV UNION 342","entrega_domicilio":true,"origen_venta":{"descripcion":"FACEBOOK"},"fecha_envio_propuesta":"2025-02-12","obs_rotulo":"OBS ROTULADO","obs_despacho":"OBS DESPACHADO","tipo_pago_envio":{"descripcion":"PAGAR ENVÍO"}}"
  "documento_convertido"    => 8150   //====== PRESENTE SOLO EN CONVERSIÓN =====
  "regularizar"             => "SI" "NO"
  "doc_regularizar_id"      => id

  "modo"                    => "ATENCION" "RESERVA"
]
*/
    public function store(DocVentaStoreRequest $request)
    {

        $this->authorize('haveaccess', 'documento_venta.index');
        ini_set("max_execution_time", 60000);

        DB::beginTransaction();
        try {

            //========= VALIDACIONES COMPLEJAS ======
            $datos_validados    =   DocumentoController::validacionStore($request);

            DocumentoController::comprobanteActivo($datos_validados->sede_id, $datos_validados->tipo_venta);

            //======== OBTENER CORRELATIVO Y SERIE ======
            $datos_correlativo  =   DocumentoController::getCorrelativo($datos_validados->tipo_venta, $datos_validados->sede_id);

            //========== CALCULAR MONTOS ======
            $montos =   DocumentoController::calcularMontos($datos_validados->lstVenta, $datos_validados);

            //======== OBTENIENDO LEYENDA ======
            $legenda                =   UtilidadesController::convertNumeroLetras($montos->monto_total_pagar);

            //====== GRABAR MAESTRO VENTA =====
            $documento                      = new Documento();

            //========= FECHAS ========
            $documento->fecha_documento     = Carbon::now()->toDateString();
            $documento->fecha_atencion      = Carbon::now()->toDateString();

            if ($datos_validados->condicion->id != 1) {
                $nro_dias                       = $datos_validados->condicion->dias;
                $documento->fecha_vencimiento   = Carbon::now()->addDays($nro_dias)->toDateString();
            } else {
                $documento->fecha_vencimiento   = Carbon::now()->toDateString();
            }

            //======== EMPRESA ========
            $documento->ruc_empresa                 = $datos_validados->empresa->ruc;
            $documento->empresa                     = $datos_validados->empresa->razon_social;
            $documento->direccion_fiscal_empresa    = $datos_validados->empresa->direccion_fiscal;
            $documento->empresa_id                  = $datos_validados->empresa->id;

            //========= CLIENTE =======
            $documento->tipo_documento_cliente  = $datos_validados->cliente->tipo_documento;
            $documento->documento_cliente       = $datos_validados->cliente->documento;
            $documento->direccion_cliente       = $datos_validados->cliente->direccion;
            $documento->cliente                 = $datos_validados->cliente->nombre;
            $documento->cliente_id              = $datos_validados->cliente->id;

            //======== TIPO VENTA ======
            $documento->tipo_venta_id           = $datos_validados->tipo_venta->id;   //boleta,factura,nota_venta
            $documento->tipo_venta_nombre       = $datos_validados->tipo_venta->descripcion;

            //========= CONDICIÓN PAGO ======
            $documento->condicion_id            = $datos_validados->condicion->id;


            $documento->observacion = $request->get('observacion');
            $documento->user_id     = $datos_validados->usuario->id;

            //========= MONTOS Y MONEDA ========
            $documento->sub_total               =   $montos->monto_subtotal;
            $documento->monto_embalaje          =   $montos->monto_embalaje;
            $documento->monto_envio             =   $montos->monto_envio;
            $documento->total                   =   $montos->monto_total;
            $documento->total_igv               =   $montos->monto_igv;
            $documento->total_pagar             =   $montos->monto_total_pagar;
            $documento->igv                     =   $datos_validados->empresa->igv;
            $documento->monto_descuento         =   $montos->monto_descuento;
            $documento->porcentaje_descuento    =   $montos->porcentaje_descuento;
            $documento->moneda                  =   1;

            //======== SUNAT ========
            $documento->mto_oper_gravadas_sunat =   $montos->mtoOperGravadasSunat;
            $documento->mto_igv_sunat           =   $montos->mtoIgvSunat;
            $documento->total_impuestos_sunat   =   $montos->totalImpuestosSunat;
            $documento->valor_venta_sunat       =   $montos->valorVentaSunat;
            $documento->sub_total_sunat         =   $montos->subTotalSunat;
            $documento->mto_imp_venta_sunat     =   $montos->mtoImpVentaSunat;

            //======= SERIE Y CORRELATIVO ======
            $documento->serie       =   $datos_correlativo->serie;
            $documento->correlativo =   $datos_correlativo->correlativo;

            $documento->legenda     =   $legenda;

            $documento->sede_id         =   $datos_validados->sede_id;

            $documento->almacen_id      =   $datos_validados->almacen->id;
            $documento->almacen_nombre  =   $datos_validados->almacen->descripcion;

            //======== DOCS VENTA DE TIPO ANTICIPO ========
            if ($request->has('facturar') && $request->has('pedido_id')) {
                $documento->pedido_id               =   $request->get('pedido_id');
                $documento->es_anticipo             =   true;
                $documento->tipo_doc_venta_pedido   =   "FACTURACIÓN";
                $documento->pedido_id               =   $request->get('pedido_id');
            }
            if ($request->get('modo') === 'CONSUMO') {
                $documento->pedido_id               =   $request->get('pedido_id');
            }

            //========= DOCS VENTA PAGADAS CON ANTICIPO PARCIAL O TOTAL ========
            if (
                $request->get('facturado') === 'SI'
                || $request->get('modo') === 'CONSUMO'
            ) {
                $documento->estado_pago  =   'PAGADA';
            }

            if ($request->get('anticipo_consumido_id')) {
                $doc_anticipo                   =   Documento::find($request->get('anticipo_consumido_id'));
                $doc_anticipo->saldo_anticipo   -=  $request->get('anticipo_monto_consumido');
                $doc_anticipo->update();

                $documento->anticipo_consumido_id               =   $request->get('anticipo_consumido_id');
                $documento->anticipo_monto_consumido            =   $request->get('anticipo_monto_consumido');
                $documento->anticipo_monto_consumido_sin_igv    =   $request->get('anticipo_monto_consumido') / (($datos_validados->porcentaje_igv + 100) / 100);
                $documento->anticipo_consumido_serie            =   $doc_anticipo->serie;
                $documento->anticipo_consumido_correlativo      =   $doc_anticipo->correlativo;
            }

            //======== EN CASO DE CONVERSIÓN DE DOCUMENTO =====
            if ($request->has('documento_convertido')) {
                $documento_convertido           =   Documento::find($request->get('documento_convertido'));
                $documento->convert_de_id       =   $request->get('documento_convertido');
                $documento->convert_de_serie    =   $documento_convertido->serie . '-' . $documento_convertido->correlativo;
                $documento->estado_pago         =   'PAGADA';
            }

            //======= EN CASO DE REGULARIZACIÓN =====
            if ($request->has('doc_regularizar_id')) {
                $doc_regularizar                    =   Documento::find($request->get('doc_regularizar_id'));
                $documento->regularizado_de_id      =   $doc_regularizar->id;
                $documento->regularizado_de_serie   =   $doc_regularizar->serie . '-' . $doc_regularizar->correlativo;
                $documento->estado_pago             =   'PAGADA';
            }

            $documento->modo    =   $request->get('modo') ?? 'VENTA';
            if ($request->has('modo')) {
                $documento->tipo_doc_venta_pedido   =   "ATENCION";
                $documento->pedido_id               =   $request->get('pedido_id') ?? null;
            }
            if ($request->get('tipo_doc_venta_pedido')  === 'CONSUMO') {
                $documento->tipo_doc_venta_pedido   =   "CONSUMO";
            }
            $documento->save();

            foreach ($datos_validados->lstVenta as $item) {
                foreach ($item->tallas as $talla) {

                    /*======== EN CASO SEA UNA FACTURACIÓN DE PEDIDO Y EL PRODUCTO NO EXISTA ========
                    ========= CREAMOS EL PRODUCTO COLOR TALLA CON STOCKS EN CERO ========*/
                    if ($request->has('facturar')) {

                        $item_pedido   =    DB::select(
                            'select
                                            pct.producto_id
                                            from producto_color_tallas as pct
                                            where
                                            pct.almacen_id = ?
                                            AND pct.producto_id = ?
                                            AND pct.color_id = ?
                                            AND pct.talla_id = ?',
                            [
                                $documento->almacen_id,
                                $item->producto_id,
                                $item->color_id,
                                $talla->talla_id
                            ]
                        );

                        if (count($item_pedido) === 0) {
                            $nuevo_producto                 =   new ProductoColorTalla();
                            $nuevo_producto->almacen_id     =   $documento->almacen_id;
                            $nuevo_producto->producto_id    =   $item->producto_id;
                            $nuevo_producto->color_id       =   $item->color_id;
                            $nuevo_producto->talla_id       =   $talla->talla_id;
                            $nuevo_producto->stock          =   0;
                            $nuevo_producto->stock_logico   =   0;
                            $nuevo_producto->save();
                        }
                    }

                    //====== COMPROBAR SI EXISTE EL PRODUCTO COLOR TALLA EN EL ALMACÉN =====
                    $existe =   DB::select(
                        'select
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
                        [
                            $datos_validados->almacen->id,
                            $item->producto_id,
                            $item->color_id,
                            $talla->talla_id
                        ]
                    );

                    if (count($existe) === 0) {
                        throw new Exception($item->producto_nombre . '-' . $item->color_nombre . '-' . $talla->talla_nombre . ', NO EXISTE EN EL ALMACÉN!!!');
                    }

                    //========== GRABAR DETALLE VENTA =====
                    $importe                =   floatval($talla->cantidad) * floatval($item->precio_venta);
                    $precio_unitario        =   $item->porcentaje_descuento == 0 ? $item->precio_venta : $item->precio_venta_nuevo;

                    $detalle                            =   new Detalle();
                    $detalle->documento_id              =   $documento->id;
                    $detalle->almacen_id                =   $datos_validados->almacen->id;
                    $detalle->producto_id               =   $item->producto_id;
                    $detalle->color_id                  =   $item->color_id;
                    $detalle->talla_id                  =   $talla->talla_id;
                    $detalle->almacen_nombre            =   $datos_validados->almacen->descripcion;
                    $detalle->codigo_producto           =   $existe[0]->producto_codigo;
                    $detalle->nombre_producto           =   $existe[0]->producto_nombre;
                    $detalle->nombre_color              =   $existe[0]->color_nombre;
                    $detalle->nombre_talla              =   $existe[0]->talla_nombre;
                    $detalle->nombre_modelo             =   $existe[0]->modelo_nombre;
                    $detalle->cantidad                  =   floatval($talla->cantidad);
                    $detalle->precio_unitario           =   floatval($item->precio_venta);
                    $detalle->importe                   =   $importe;
                    $detalle->precio_unitario_nuevo     =   floatval($precio_unitario);
                    $detalle->porcentaje_descuento      =   floatval($item->porcentaje_descuento);
                    $detalle->monto_descuento           =   floatval($importe) * floatval($item->porcentaje_descuento) / 100;
                    $detalle->importe_nuevo             =   floatval($precio_unitario) * floatval($talla->cantidad);
                    $detalle->cantidad_sin_cambio       =   (int) $talla->cantidad;
                    $detalle->save();

                    //====== RESTAR STOCK SI NO ES CONVERSIÓN NI REGULARIZACIÓN =======
                    if (
                        !$request->has('documento_convertido')
                        && !$request->has('regularizar')
                        && !$request->has('facturar')
                        && $request->get('modo') !== 'CONSUMO'
                    ) {

                        //===== ACTUALIZANDO STOCK ===========
                        DB::update(
                            'UPDATE producto_color_tallas
                        SET stock = stock - ?
                        WHERE
                        almacen_id = ?
                        AND producto_id = ?
                        AND color_id = ?
                        AND talla_id = ?',
                            [
                                $talla->cantidad,
                                $datos_validados->almacen->id,
                                $item->producto_id,
                                $item->color_id,
                                $talla->talla_id
                            ]
                        );

                        $nuevo_stock    =    DB::table('producto_color_tallas')
                            ->where('almacen_id', $datos_validados->almacen->id)
                            ->where('producto_id', $item->producto_id)
                            ->where('color_id', $item->color_id)
                            ->where('talla_id', $talla->talla_id)
                            ->value('stock');

                        //======= KARDEX CON STOCK YA MODIFICADO =======
                        $kardex                     =   new Kardex();
                        $kardex->sede_id            =   $datos_validados->sede_id;
                        $kardex->almacen_id         =   $datos_validados->almacen->id;
                        $kardex->producto_id        =   $item->producto_id;
                        $kardex->color_id           =   $item->color_id;
                        $kardex->talla_id           =   $talla->talla_id;
                        $kardex->almacen_nombre     =   $datos_validados->almacen->descripcion;
                        $kardex->producto_nombre    =   $existe[0]->producto_nombre;
                        $kardex->color_nombre       =   $existe[0]->color_nombre;
                        $kardex->talla_nombre       =   $existe[0]->talla_nombre;
                        $kardex->cantidad           =   $talla->cantidad;
                        $kardex->precio             =   $precio_unitario;
                        $kardex->importe            =   $detalle->importe_nuevo;
                        $kardex->accion             =   'VENTA';
                        $kardex->stock              =   $nuevo_stock;
                        $kardex->numero_doc         =   $documento->serie . '-' . $documento->correlativo;
                        $kardex->documento_id       =   $documento->id;
                        $kardex->registrador_id     =   $documento->user_id;
                        $kardex->registrador_nombre =   $datos_validados->usuario->usuario;
                        $kardex->fecha              =   Carbon::today()->toDateString();
                        $kardex->descripcion        =   mb_strtoupper($request->get('observacion'), 'UTF-8');
                        $kardex->save();
                    }
                }
            }

            //========== GUARDAR DATOS DE DESPACHO =======
            $data_envio     =   json_decode($request->get('data_envio'));

            //========= HAY DATOS DE DESPACHO ======
            if (
                !empty((array)$data_envio)
                && !$request->has('facturar')
                && !$request->has('documento_convertido')
                && $request->get('tipo_doc_venta_pedido') !== 'CONSUMO'
            ) {

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
                $envio_venta->entrega_domicilio     =   $data_envio->entrega_domicilio ? "SI" : "NO";
                $envio_venta->direccion_entrega     =   $data_envio->direccion_entrega;
                $envio_venta->documento_nro         =   $documento->serie . '-' . $documento->correlativo;
                $envio_venta->fecha_envio_propuesta =   $data_envio->fecha_envio_propuesta;
                $envio_venta->origen_venta          =   $data_envio->origen_venta->descripcion;
                $envio_venta->obs_rotulo            =   $data_envio->obs_rotulo;
                $envio_venta->obs_despacho          =   $data_envio->obs_despacho;
                $envio_venta->cliente_celular       =   $documento->clienteEntidad->telefono_movil;
                $envio_venta->user_vendedor_id      =   $documento->user_id;
                $envio_venta->user_vendedor_nombre  =   $documento->user->usuario;
                $envio_venta->almacen_id            =   $documento->almacen_id;
                $envio_venta->almacen_nombre        =   $documento->almacen_nombre;
                $envio_venta->sede_id               =   $documento->sede_id;
                $envio_venta->sede_despachadora_id  =   $datos_validados->almacen->sede_id;
                $envio_venta->modo                  =   $request->get('modo') ?? 'VENTA';
                $envio_venta->save();
            }

            //======= SIN DESPACHO ======
            if (
                empty((array)$data_envio)
                && !$request->has('facturar')
                && !$request->has('documento_convertido')
                && $request->get('tipo_doc_venta_pedido') !== 'CONSUMO'
            ) {

                //======== OBTENER EMPRESA ENVÍO =======
                $empresa_envio                      =   DB::select('select ee.id,ee.empresa,ee.tipo_envio
                                                            from empresas_envio as ee')[0];

                $sede_envio                         =   DB::select('select ees.id,ees.direccion
                                                        from empresa_envio_sedes as ees
                                                        where ees.empresa_envio_id=?', [$empresa_envio->id])[0];

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
                $envio_venta->documento_nro         =   $documento->serie . '-' . $documento->correlativo;
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
                $envio_venta->almacen_id            =   $documento->almacen_id;
                $envio_venta->almacen_nombre        =   $documento->almacen_nombre;
                $envio_venta->sede_id               =   $documento->sede_id;
                $envio_venta->sede_despachadora_id  =   $datos_validados->almacen->sede_id;
                $envio_venta->modo                  =   $request->get('modo') ?? 'VENTA';
                $envio_venta->save();
            }

            //======== ASOCIAR LA VENTA CON EL MOVIMIENTO CAJA DEL COLABORADOR ====
            $movimiento_venta                   =   new DetalleMovimientoVentaCaja();
            $movimiento_venta->cdocumento_id    =   $documento->id;
            $movimiento_venta->mcaja_id         =   $datos_validados->caja_movimiento->movimiento_id;
            if (
                $request->get('facturado') === 'SI'
                || $request->get('tipo_doc_venta_pedido') == 'CONSUMO'
            ) {
                $movimiento_venta->cobrar       =   'NO';
            }
            $movimiento_venta->save();

            //======== EN CASO DE CONVERSIÓN DE DOCUMENTO =====
            if ($request->has('documento_convertido')) {
                $documento_convertido                       =   Documento::find($request->get('documento_convertido'));
                $documento_convertido->convert_en_id        =   $documento->id;
                $documento_convertido->convert_en_serie     =   $documento->serie . '-' . $documento->correlativo;
                $documento_convertido->update();
            }

            //========== ACTUALIZAR ESTADO FACTURACIÓN A INICIADA ======
            DB::table('empresa_numeracion_facturaciones')
                ->where('empresa_id', Empresa::find(1)->id)
                ->where('sede_id', $datos_validados->sede_id)
                ->where('tipo_comprobante', $datos_validados->tipo_venta->id)
                ->where('emision_iniciada', '0')
                ->where('estado', 'ACTIVO')
                ->update([
                    'emision_iniciada'       => '1',
                    'updated_at'             => Carbon::now()
                ]);


            if ($request->get('facturado') === 'SI' && $request->get('generar_recibo_caja') === 'SI') {

                //======= OBTENIENDO MOVIMIENTO ID DEL COLABORADOR ======
                $usuario    =   User::find($request->get('user_id'));

                $movimiento =   DB::select(
                    "SELECT
                                        dmc.movimiento_id
                                    FROM detalles_movimiento_caja AS dmc
                                    WHERE
                                        dmc.colaborador_id = ?
                                    AND dmc.fecha_salida IS NULL",
                    [$usuario->colaborador_id]
                );

                if (count($movimiento) !== 1) {
                    throw new Exception("ERROR AL OBTENER EL MOVIMIENTO DE CAJA DEL USUARIO");
                }

                //====== GENERAR RECIBO DE CAJA DEL EXCEDENTE =====
                $recibo_caja                    =   new ReciboCaja();
                $recibo_caja->movimiento_id     =   $movimiento[0]->movimiento_id;
                $recibo_caja->user_id           =   $request->get('user_id');
                $recibo_caja->cliente_id        =   $request->get('cliente_id');
                $recibo_caja->monto             =   $request->get('excedente');
                $recibo_caja->saldo             =   0;
                $recibo_caja->metodo_pago       =   "EFECTIVO";
                $recibo_caja->estado_servicio   =   "CANJEADO";
                $recibo_caja->observacion       =   "CREADO A PARTIR DEL EXCEDENTE DE " . "S/." . $request->get('excedente') .
                    " PRESENTE EN LA ATENCIÓN " . $documento->serie . '-' . $documento->correlativo .
                    " DEL PEDIDO PE-" .  $request->get('pedido_nro');
                $recibo_caja->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'DOCUMENTO VENTA REGISTRADO CON ÉXITO',
                'documento_id' => $documento->id
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'line' => $th->getLine(), 'file' => $th->getFile()]);
        }
    }


    /*
{#2086
  +"monto_subtotal": 38.0
  +"monto_total_pagar": 38.0
  +"monto_total": 32.203389830508
  +"monto_igv": 5.7966101694915
  +"porcentaje_descuento": 0.0
  +"monto_descuento": 0.0
  +"monto_embalaje": "0.00"
  +"monto_envio": "0.00"
}
*/
    public static function calcularMontos($lstVenta, $datos_validados)
    {

        $monto_embalaje =   $datos_validados->monto_embalaje;
        $monto_envio    =   $datos_validados->monto_envio;

        //======= CALCULANDO MONTOS ========
        $monto_subtotal     =   0.0;
        $monto_embalaje     =   $monto_embalaje ?? 0;
        $monto_envio        =   $monto_envio ?? 0;
        $monto_total        =   0.0;
        $monto_igv          =   0.0;
        $monto_total_pagar  =   0.0;
        $monto_descuento    =   0;
        $monto_anticipo     =   $datos_validados->anticipo_monto_consumido;

        foreach ($lstVenta as $producto) {
            foreach ($producto->tallas as $talla) {
                $monto_descuento    +=  (float)$producto->monto_descuento;
                if (floatval($producto->porcentaje_descuento) == 0) {
                    $monto_subtotal     +=  ($talla->cantidad * $producto->precio_venta);
                } else {
                    $monto_subtotal     +=  ($talla->cantidad * $producto->precio_venta_nuevo);
                }
            }
        }

        $monto_total_pagar      =   $monto_subtotal + $monto_embalaje + $monto_envio - $monto_anticipo;
        $monto_total            =   $monto_total_pagar / 1.18;
        $monto_igv              =   $monto_total_pagar - $monto_total;
        $porcentaje_descuento   =   ($monto_descuento * 100) / ($monto_total_pagar);

        //========= CALCULAR LOS MONTOS DE SUNAT =========
        if ($monto_anticipo == 0) {
            $mtoOperGravadasSunat   =   (($monto_subtotal + $monto_embalaje + $monto_envio) / 1.18);
            $mtoIgvSunat            =   $mtoOperGravadasSunat * 0.18;
            $totalImpuestosSunat    =   $mtoIgvSunat;
            $valorVentaSunat        =   $mtoOperGravadasSunat;
            $subTotalSunat          =   $mtoOperGravadasSunat + $mtoIgvSunat;
            $mtoImpVentaSunat       =   $subTotalSunat;
        } else {
            $mtoOperGravadasSunat   =   (($monto_subtotal + $monto_embalaje + $monto_envio) / 1.18) - ($monto_anticipo / 1.18);
            $mtoIgvSunat            =   $mtoOperGravadasSunat * 0.18;
            $valorVentaSunat        =   ($monto_subtotal + $monto_embalaje + $monto_envio) / 1.18;
            $totalImpuestosSunat    =   $mtoIgvSunat;
            $subTotalSunat          =   $valorVentaSunat + ($valorVentaSunat * 0.18);
            $mtoImpVentaSunat       =   $subTotalSunat - ($monto_anticipo / 1.18);
        }


        $montos =   (object) [
            'monto_subtotal'        =>  $monto_subtotal,
            'monto_total_pagar'     =>  $monto_total_pagar,
            'monto_total'           =>  $monto_total,
            'monto_igv'             =>  $monto_igv,
            'porcentaje_descuento'  =>  $porcentaje_descuento,
            'monto_descuento'       =>  $monto_descuento,
            'monto_embalaje'        =>  $monto_embalaje,
            'monto_envio'           =>  $monto_envio,

            'mtoOperGravadasSunat'  =>  $mtoOperGravadasSunat,
            'mtoIgvSunat'           =>  $mtoIgvSunat,
            'totalImpuestosSunat'   =>  $totalImpuestosSunat,
            'valorVentaSunat'       =>  $valorVentaSunat,
            'subTotalSunat'         =>  $subTotalSunat,
            'mtoImpVentaSunat'      =>  $mtoImpVentaSunat
        ];

        return $montos;
    }


    /*
{#1844 // app\Http\Controllers\Ventas\RegistroVentaController.php:174
  +"correlativo": 1
  +"serie": "B001"
}
*/
    public static function getCorrelativo($tipo_comprobante, $sede_id)
    {

        $correlativo        =   null;
        $serie              =   null;

        //======= CONTABILIZANDO SI HAY DOCUMENTOS DE VENTA EMITIDOS PARA EL TYPE SALE ======
        $ultima_venta =   DB::select(
            'SELECT cd.correlativo
                        FROM cotizacion_documento AS cd
                        WHERE cd.sede_id = ?
                        AND cd.tipo_venta_id = ?
                        ORDER BY cd.id DESC
                        LIMIT 1',
            [
                $sede_id,
                $tipo_comprobante->id
            ]
        );


        $serializacion     =   DB::select(
            'select
                            enf.*
                            from empresa_numeracion_facturaciones as enf
                            where
                            enf.empresa_id = ?
                            and enf.tipo_comprobante = ?
                            and enf.sede_id = ?',
            [
                Empresa::find(1)->id,
                $tipo_comprobante->id,
                $sede_id
            ]
        )[0];


        //==== NO EXISTE UNA ÚLTIMA VENTA DE ESE TIPO DE COMPROBANTE =====
        if (count($ultima_venta) === 0) {

            //====== INICIAR DESDE EL STARTING NUMBER =======
            $correlativo        =   $serializacion->numero_iniciar;
            $serie              =   $serializacion->serie;
        } else {

            //======= EN CASO YA EXISTAN DOCUMENTOS DE VENTA DEL TYPE SALE ======
            $correlativo        =   $ultima_venta[0]->correlativo  +   1;
            $serie              =   $serializacion->serie;
        }

        return (object)['correlativo' => $correlativo, 'serie' => $serie];
    }

    public static function comprobanteActivo($sede_id, $tipo_comprobante)
    {

        $existe =   DB::select('select
                    enf.*
                    from empresa_numeracion_facturaciones as enf
                    where
                    enf.empresa_id = 1
                    AND enf.sede_id = ?
                    AND enf.tipo_comprobante = ?', [$sede_id, $tipo_comprobante->id]);

        if (count($existe) === 0) {
            throw new Exception($tipo_comprobante->descripcion . ', NO ESTÁ ACTIVO EN LA EMPRESA!!!');
        }
    }

    public static function validacionStore($request)
    {

        //========= VALIDAR LA SEDE ========
        if (!$request->get('sede_id')) {
            throw new Exception("FALTA EL PARÁMETRO SEDE EN LA PETICIÓN!!!");
        }

        $sede   =   DB::select(
            'select
                    es.*
                    from empresa_sedes as es
                    where
                    es.id = ?
                    and es.estado = "ACTIVO"',
            [$request->get('sede_id')]
        );

        if (count($sede) === 0) {
            throw new Exception("NO EXISTE LA SEDE EN LA BD!!!");
        }

        //======== VALIDAR DETALLE VENTA ======
        $lstVenta   =   json_decode($request->get('productos_tabla'));
        if (count($lstVenta) === 0) {
            throw new Exception("EL DETALLE DE LA VENTA ESTÁ VACÍO!!!");
        }

        //========= VALIDANDO SI EL USUARIO ESTÁ EN UNA CAJA ABIERTA =======
        $caja_movimiento           =   movimientoUser();

        if (count($caja_movimiento) == 0) {
            throw new Exception("DEBES FORMAR PARTE DE UNA CAJA ABIERTA!!!");
        }

        //========= VALIDANDO TIPO COMPROBANTE ========
        $cliente            =   Cliente::find($request->get('cliente_id'));
        $tipo_comprobante   =   DB::select('select
                                td.*
                                from tabladetalles as td
                                where td.id = ?', [$request->get('tipo_venta')])[0];

        if ($cliente->tipo_documento !== 'RUC' && $tipo_comprobante->simbolo === '01') {
            throw new Exception("SE REQUIERE RUC PARA GENERAR FACTURA ELECTRÓNICA!!!");
        }
        if ($cliente->tipo_documento !== 'DNI' && $tipo_comprobante->simbolo === '03') {
            throw new Exception("SE REQUIERE DNI PARA GENERAR BOLETA ELECTRÓNICA!!!");
        }

        //====== CONDICIÓN PAGO =======
        $condicion_id   =   explode('-', $request->get('condicion_id'), 2)[0];
        $condicion      =   Condicion::find($condicion_id);


        $almacen        =   Almacen::find($request->get('almacenSeleccionado'));

        $datos_validados    =   (object)[
            'sede_id'                   =>  $request->get('sede_id'),
            'tipo_venta'                =>  $tipo_comprobante,
            'condicion'                 =>  $condicion,
            'cliente'                   =>  $cliente,
            'porcentaje_igv'            =>  Empresa::find(1)->igv,
            'almacen'                   =>  $almacen,
            'lstVenta'                  =>  $lstVenta,
            'monto_embalaje'            =>  $request->get('monto_embalaje'),
            'monto_envio'               =>  $request->get('monto_envio'),
            'empresa'                   =>  Empresa::find(1),
            'observacion'               =>  $request->get('observacion'),
            'usuario'                   =>  Auth::user(),
            'caja_movimiento'           =>  $caja_movimiento[0],

            'anticipo_consumido_id'     =>  $request->get('anticipo_consumido_id') ?? null,
            'anticipo_monto_consumido'  =>  $request->get('anticipo_monto_consumido') ?? 0
        ];

        return  $datos_validados;
    }


    /*
array:27 [
  "fecha_documento_campo"   => "2025-02-10"
  "fecha_atencion_campo"    => "2025-02-10"
  "fecha_vencimiento_campo" => "2025-02-10"
  "tipo_venta"              => 129
  "condicion_id"            => "1-CONTADO"
  "cliente_id"              => 1
  "tipo_pago_id"            => null
  "efectivo"                => 0
  "importe"                 => 0
  "empresa_id"              => 1
  "observacion"             => null
  "igv"                     => 18
  "igv_check"               => true
  "cotizacion_id"           => null
  "productos_tabla"         => "[{"producto_id":"1","color_id":"2","producto_nombre":"PRODUCTO TEST","color_nombre":"AZUL","precio_venta":"2.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"1"}],"subtotal":2},{"producto_id":"1","color_id":"3","producto_nombre":"PRODUCTO TEST","color_nombre":"CELESTE","precio_venta":"2.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0,"tallas":[{"talla_id":"1","talla_nombre":"34","cantidad":"2"}],"subtotal":4}]"
  "envio_sunat"             => false
  "monto_sub_total"         => 6
  "monto_total_igv"         => 0.91525423728813
  "monto_total"             => 5.0847457627119
  "tipo_cliente_documento"  => null
  "moneda"                  => "SOLES"
  "data_envio"              => "{}"
  "monto_embalaje"          => 0
  "monto_envio"             => 0
  "monto_total_pagar"       => 6
  "monto_descuento"         => 0
  "almacenSeleccionado"     => 2
]
*/
    public function store_olddd(DocVentaStoreRequest $request)
    {
        $this->authorize('haveaccess', 'documento_venta.index');
        ini_set("max_execution_time", 60000);
        try {

            DB::beginTransaction();

            $documento                      = new Documento();
            $documento->fecha_documento     = $request->get('fecha_documento_campo');
            $documento->fecha_atencion      = $request->get('fecha_atencion_campo');
            $documento->fecha_vencimiento   = $request->get('fecha_vencimiento_campo');

            //EMPRESA
            $empresa                                = Empresa::findOrFail($request->get('empresa_id'));
            $documento->ruc_empresa                 = $empresa->ruc;
            $documento->empresa                     = $empresa->razon_social;
            $documento->direccion_fiscal_empresa    = $empresa->direccion_fiscal;
            $documento->empresa_id                  = $request->get('empresa_id'); //OBTENER NUMERACION DE LA EMPRESA


            //CLIENTE
            $cliente                            = Cliente::findOrFail($request->get('cliente_id'));
            $documento->tipo_documento_cliente  = $cliente->tipo_documento;
            $documento->documento_cliente       = $cliente->documento;
            $documento->direccion_cliente       = $cliente->direccion;
            $documento->cliente                 = $cliente->nombre;
            $documento->cliente_id              = $request->get('cliente_id'); //OBTENER TIENDA DEL CLIENTE
            $documento->tipo_venta              = $request->get('tipo_venta');   //boleta,factura,nota_venta

            //CONDICION(TIPO DE PAGO: CONTADO O CREDITO)
            $cadena                     = explode('-', $request->get('condicion_id'));
            $condicion                  = Condicion::findOrFail($cadena[0]);
            $documento->condicion_id    = $condicion->id;


            $documento->observacion = $request->get('observacion');
            $documento->user_id     = auth()->user()->id;

            $monto_sub_total    =   str_replace('S/', '', $request->get('monto_sub_total'));
            $monto_embalaje     =   str_replace('S/', '', $request->get('monto_embalaje') ?? 0);
            $monto_envio        =   str_replace('S/', '', $request->get('monto_envio') ?? 0);
            $monto_total        =   str_replace('S/', '', $request->get('monto_total'));
            $monto_total_igv    =   str_replace('S/', '', $request->get('monto_total_igv'));
            $monto_total_pagar  =   str_replace('S/', '', $request->get('monto_total_pagar'));
            $monto_descuento    =   $request->get('monto_descuento') ?? 0;
            $porcentaje_descuento = ($monto_descuento * 100) / ($monto_total_pagar);


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

            if ($request->has('facturar') && $request->has('pedido_id')) {
                $documento->pedido_id   =   $request->get('pedido_id');
            }

            $documento->save();


            //========== VERIFICANDO SI EL USUARIO ESTÁ PARTICIPANDO DE ALGUNA CAJA ACTUALMENTE ==========
            $caja_usuario           =   movimientoUser();

            if (count($caja_usuario) == 1) {
                $detalle                = new DetalleMovimientoVentaCaja();
                $detalle->cdocumento_id = $documento->id;
                $detalle->mcaja_id      = movimientoUser()[0]->movimiento_id;
                if ($request->has('facturado') && $request->get('facturado') === 'SI') {
                    $detalle->cobrar            =   'NO';
                    $documento->estado_pago     =   'PAGADA';
                }
                $detalle->save();
            }

            if (count($caja_usuario) == 0) {
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
                 ========= CREAMOS EL PRODUCTO COLOR TALLA CON STOCKS EN CERO ========*/
                if ($request->has('facturar')) {
                    $item   =   DB::select(
                        'select pct.producto_id from producto_color_tallas as pct
                                where pct.producto_id = ?  and pct.color_id = ? and pct.talla_id = ?',
                        [$producto->producto_id, $producto->color_id, $producto->talla_id]
                    );

                    if (count($item) === 0) {
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
                $precio_unitario        =   $producto->porcentaje_descuento == 0 ? $producto->precio_unitario : $producto->precio_unitario_nuevo;

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
                    'monto_descuento'           =>  floatval($importe) * floatval($producto->porcentaje_descuento) / 100,
                    'importe_nuevo'             =>  floatval($precio_unitario) * floatval($producto->cantidad),
                    'cantidad_sin_cambio'       => (int) $producto->cantidad

                ]);


                //====== RESTAR STOCK SI NO ES CONVERSIÓN NI REGULARIZACIÓN =======
                if (!$request->has('convertir') && !$request->has('regularizar') && !$request->has('facturar')) {

                    //===== ACTUALIZANDO STOCK ===========
                    DB::update(
                        'UPDATE producto_color_tallas
                        SET stock = stock - ?
                        WHERE producto_id = ? AND color_id = ? AND talla_id = ?',
                        [$producto->cantidad, $producto->producto_id, $producto->color_id, $producto->talla_id]
                    );

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

                if ($request->has('convertir')) {

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

                if ($request->get('regularizar') == 'SI') {
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
            if ($request->has('data_envio') && !$request->has('facturar')) {

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
                    $envio_venta->entrega_domicilio     =   $data_envio->entrega_domicilio ? "SI" : "NO";
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
                } else {


                    //======== OBTENER EMPRESA ENVÍO =======
                    $empresa_envio                      =   DB::select('select ee.id,ee.empresa,ee.tipo_envio
                                                            from empresas_envio as ee')[0];

                    $sede_envio                         =   DB::select('select ees.id,ees.direccion
                                                            from empresa_envio_sedes as ees
                                                            where ees.empresa_envio_id=?', [$empresa_envio->id])[0];

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

            $documento = Documento::find($documento->id);
            $documento->nombre_comprobante_archivo = $documento->serie . '-' . $documento->correlativo . '.pdf';
            $documento->update();


            //========= REGISTRO DE ACTIVIDAD =========
            $descripcion = "SE AGREGÓ EL DOCUMENTO DE VENTA CON LA FECHA: " . Carbon::parse($documento->fecha_documento)->format('d/m/y');
            $gestion = "DOCUMENTO DE VENTA";

            if ($request->get('regularizar') == 'SI') {
                $gestion = "DOCUMENTO DE VENTA REGULARIZADO";
            }
            if ($request->has('convertir')) {
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

                return response()->json([
                    'success' => true,
                    'documento_id' => $documento->id,
                ]);
            }
            return response()->json([
                'success' => false,
                'mensaje' => "exito",
            ]);
        } catch (Exception $e) {


            //====== REVERTIR ACCIONES EN LA BD =========
            DB::rollBack();


            //========== DETALLAR ERROR DE STOCK NEGATIVO ========
            if ($e->getCode() == "22003" && strpos($e->getMessage(), "UPDATE producto_color_tallas") !== false && strpos($e->getMessage(), "SET stock") !== false) {

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
        $productos      =   Producto::where('estado', 'ACTIVO')->get();
        $tallas         =   Talla::where('estado', 'ACTIVO')->get();
        $modelos        =   Modelo::where('estado', 'ACTIVO')->get();
        $documento      =   Documento::findOrFail($id);
        $detalles       =   Detalle::where('documento_id', $id)->where('estado', 'ACTIVO')->with(['lote', 'lote.producto'])->get();
        $condiciones    =   Condicion::where('estado', 'ACTIVO')->get();
        $tipos_venta    =   tipos_venta()->whereIn('parametro', ['F', 'B', 'N']);

        $fullaccess     =   false;

        $registrador    =   User::find($documento->user_id);
        $sede           =   Sede::find($documento->sede_id);
        $almacenes      =   Almacen::where('estado', 'ACTIVO')
            ->where('tipo_almacen', 'PRINCIPAL')
            ->get();

        $cliente        =   DB::table('clientes as c')
            ->select(
                'c.id',
                DB::raw('CONCAT(c.tipo_documento,":",c.documento,"-",c.nombre) as descripcion'),
            )
            ->where('c.id', $documento->cliente_id)
            ->where('c.estado', 'ACTIVO')
            ->get()[0];


        $departamentos  =   Departamento::all();

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



        return view('ventas.documentos.editar.edit', [
            'documento'         =>  $documento,
            'detalles'          =>  $detalles,
            'empresas'          =>  $empresas,
            'productos'         =>  $productos,
            'condiciones'       =>  $condiciones,
            'fullaccess'        =>  $fullaccess,
            'tallas'            =>  $tallas,
            'modelos'           =>  $modelos,
            'departamentos'     =>  $departamentos,
            'registrador'       =>  $registrador,
            'almacenes'         =>  $almacenes,
            'sede'              =>  $sede,
            'cliente'           =>  $cliente,
            'tipos_documento'   =>  tipos_documento(),
            'tipo_clientes'     =>  tipo_clientes(),
            'tipos_venta'       =>  $tipos_venta
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

    public function voucher($id, $size)
    {

        try {

            $documento_id   =   $id;

            if (!$documento_id) {
                throw new Exception("FALTA EL PARÁMETRO DOCUMENTO ID EN LA PETICIÓN!!!");
            }

            $documento  =   Documento::find($documento_id);
            if (!$documento) {
                throw new Exception("NO EXISTE EL DOC VENTA EN LA BD!!!");
            }

            $detalles           =   Detalle::where('documento_id', $documento_id)->where('eliminado', '0')->get();


            $mostrar_cuentas    =   DB::select('select
                                    c.propiedad
                                    from configuracion as c
                                    where c.slug = "MCB"')[0]->propiedad;

            //$qr                 =   self::qr_code($documento_id);

            $empresa            =   Empresa::find(1);
            $sede               =   Sede::find($documento->sede_id);

            $pdf    =   PDF::loadview('ventas.documentos.impresion.comprobante_ticket', [
                'documento'         =>  $documento,
                'detalles'          =>  $detalles,
                'empresa'           =>  $empresa,
                'mostrar_cuentas'   =>  $mostrar_cuentas,
                'sede'              =>  $sede
            ])->setPaper([0, 0, 226.772, 651.95]);

            if ($size == 80) {
                $pdf    =   $pdf->setPaper([0, 0, 226.772, 651.95]);
            }
            if ($size == 100) {
                $pdf    =   $pdf->setPaper('a4')->setWarnings(false);
            }


            return $pdf->stream($documento->serie . '-' . $documento->correlativo . '.pdf');
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function voucher_old2($value)
    {
        try {
            $cadena = explode('-', $value);
            $id     = $cadena[0];
            $size   = (int) $cadena[1];

            $qr     = self::qr_code($id);

            $mostrar_cuentas    =   DB::select('select
                                    c.propiedad
                                    from configuracion as c
                                    where c.slug = "MCB"')[0]->propiedad;

            $documento = Documento::findOrFail($id);
            $detalles = Detalle::where('documento_id', $id)->where('eliminado', '0')->get();
            if ((int) $documento->tipo_venta_id == 127 || (int) $documento->tipo_venta_id == 128) {
                if ($documento->sunat == '0' || $documento->sunat == '2') {


                    $name = $documento->id . '.pdf';
                    $pathToFile = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantes' . DIRECTORY_SEPARATOR . $name);
                    if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantes'))) {
                        mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'comprobantes'));
                    }

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

                // if (empty($documento->correlativo)) {
                //     event(new DocumentoNumeracion($documento));
                // }
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

    public function voucher_old($value)
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

    public function xml($id)
    {
        try {
            $documento      =   Documento::find($id);
            $nombreArchivo  =   basename($documento->ruta_xml);

            if (!file_exists($documento->ruta_xml)) {
                throw new \Exception("El archivo XML del DOCUMENTO: " . $documento->serie . '-' . $documento->correlativo . " ,no existe en la ruta especificada");
            }

            $headers = [
                'Content-Type' => 'text/xml',
            ];

            return Response::download($documento->ruta_xml, $nombreArchivo, $headers);
        } catch (\Throwable $th) {
            Session::flash('doc_error_get_xml', $th->getMessage());
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

            //======= NOTA DE VENTA =====
            if ($documento->tipo_venta_id == 129) {
                $data_qr = $documento->ruc_empresa . '|' .        // RUC
                    '04' . '|' .                           // Tipo de Documento (04 para Nota de Venta)
                    ($documento->contingencia == '0' ? $documento->serie : $documento->serie_contingencia) . '|' . // SERIE
                    $documento->correlativo . '|' .        // NUMERO
                    (float) $documento->total_pagar . '|' .      // MTO TOTAL DEL COMPROBANTE
                    $documento->created_at; // FECHA DE EMISION
            } else {

                //========= BOLETA O FACTURA =======
                $data_qr =  $documento->ruc_empresa . '|' .                // RUC
                    $documento->tipoDocumento() . '|' .            // TIPO DE DOCUMENTO
                    ($documento->contingencia == '0' ? $documento->serie : $documento->serie_contingencia) . '|' . // SERIE
                    $documento->correlativo . '|' .                // NUMERO
                    (float) $documento->total_igv . '|' .                                     // MTO TOTAL IGV
                    (float) $documento->total_pagar . '|' .              // MTO TOTAL DEL COMPROBANTE
                    $documento->created_at . '|' .  // FECHA DE EMISION
                    $documento->tipoDocumentoCliente() . '|' .     // TIPO DE DOCUMENTO ADQUIRENTE
                    $documento->documento_cliente;                 // NUMERO DE DOCUMENTO ADQUIRENTE

            }


            $miQr = QrCode::format('svg')
                ->size(130)
                ->backgroundColor(0, 0, 0)
                ->color(255, 255, 255)
                ->margin(1)
                ->generate($data_qr);

            $pathToFile_qr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR . $name_qr);

            // Crea el directorio si no existe
            if (!file_exists(dirname($pathToFile_qr))) {
                mkdir(dirname($pathToFile_qr), 0755, true);
            }

            // Guarda el QR en el archivo
            file_put_contents($pathToFile_qr, $miQr);

            // Actualiza la ruta del QR en la base de datos
            $documento->ruta_qr = 'public/qrs/' . $name_qr;
            $documento->update();

            return array('success' => true, 'mensaje' => 'QR creado exitosamente');
        } catch (Exception $e) {
            return array('success' => false, 'mensaje' => $e->getMessage());
        }
    }

    public function qr_code_old($id)
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

        $detalles = Detalle::where('documento_id', $id)->where('eliminado', '0')->where('estado', 'ACTIVO')->get();
        $documento = Documento::findOrFail($id);

        $arrayProductos = array();
        for ($i = 0; $i < count($detalles); $i++) {

            $arrayProductos[] = array(
                "codProducto" => $detalles[$i]->codigo_producto,
                "unidad" => $detalles[$i]->unidad,
                // "descripcion"=> $detalles[$i]->nombre_producto.' - '.$detalles[$i]->codigo_lote,                "descripcion"=> $detalles[$i]->nombre_producto.' - '.$detalles[$i]->codigo_lote,
                "descripcion" => $detalles[$i]->nombre_producto . ' - ' . $detalles[$i]->nombre_color . ' - ' . $detalles[$i]->nombre_talla,
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
        if ($documento->monto_embalaje != 0) {
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

        if ($documento->monto_envio != 0) {
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
    private function ObtenerCorrelativoVentas(Documento $documento)
    {
        try {
            $numeracion_factura = Numeracion::where("empresa_id", $documento->empresa_id)
                ->where("estado", "ACTIVO")
                ->where("tipo_comprobante", $documento->tipo_venta)
                ->first();

            if (!$numeracion_factura)
                throw new \Exception("Tipo de Comprobante no registrado en la empresa.");

            DB::select("CALL sp_updateserializacion(?,?)", [$documento->id, $numeracion_factura->id]);

            return array(
                "success" => true,
                "mensaje" => "Documento validado"
            );
        } catch (\Exception $ex) {
            return array(
                "success" => false,
                "mensaje" => $ex->getMessage()
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
                    return array(
                        'success' => true,
                        'mensaje' => 'Documento validado.',
                        'serie_correlativo' => $existe[0]->get('correlativo_datos')['serie'] . '-' . $existe[0]->get('correlativo_datos')['correlativo']
                    );
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

        if ($condicion == '1' && $modo == 'nuevo') {
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

        if ($modo == 'editar' && $condicion == '1') {
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
        if ($condicion == '0' && $modo = 'eliminar') {
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

        return $mensaje;
    }

    //====== DEVOLVER CANTIDAD LÓGICA AL SALIR DE VENTA CREATE ========
    public function devolverCantidades(Request $request)
    {

        $mensaje        =   false;

        if ($request->has('carrito')) {

            $carrito        =   $request->get('carrito');
            $productosJSON  =   json_decode($carrito);

            foreach ($productosJSON as $producto) {
                $mensaje = true;
                foreach ($producto->tallas as $talla) {
                    DB::table('producto_color_tallas')
                        ->where('almacen_id', $producto->almacen_id)
                        ->where('producto_id', $producto->producto_id)
                        ->where('color_id', $producto->color_id)
                        ->where('talla_id', $talla->talla_id)
                        ->increment('stock_logico', $talla->cantidad);
                }
            }
        }

        return $mensaje;
    }


    //====== REGULARIZAR VENTAS ======
    //====== BOLETAS O FACTURAS =======
    public function regularizarVenta(Request $request)
    {
        try {
            DB::beginTransaction();

            $documento_id   =   $request->get('documento_id');

            //======= OBTENIENDO DOCUMENTO DE VENTA ANTIGUO ====
            $documento_venta_antiguo    =   Documento::find($documento_id);

            if ($documento_venta_antiguo->tipo_venta == "129") {
                return response()->json(['success' => false, 'message' => 'SOLO SE PERMITE REGULARIZAR FACTURAS O BOLETAS']);
            }
            if ($documento_venta_antiguo->estado == "ANULADO") {
                return response()->json(['success' => false, 'message' => 'EL DOCUMENTO DE VENTA YA FUE ANULADO PREVIAMENTE']);
            }

            //====== BUSCANDO DETALLES DEL DOC VENTA ANTIGUO ======
            $detalles_documento_venta_antiguo   =   UtilidadesController::formatearArrayDetalleObjetos(Detalle::where('documento_id', $documento_id)->get());

            //===== CREANDO REQUEST =====
            $request_doc_nuevo = new DocVentaStoreRequest();

            $additionalData = [
                'empresa'                   =>  $documento_venta_antiguo->empresa_id,
                'tipo_venta'                =>  $documento_venta_antiguo->tipo_venta_id,
                'condicion_id'              =>  $documento_venta_antiguo->condicion_id,
                'fecha_vencimiento_campo'   =>  Carbon::now(),
                'cliente_id'                =>  $documento_venta_antiguo->cliente_id,
                'igv'                       =>  "18",
                "igv_check"                 =>  "on",
                "efectivo"                  =>  "0",
                "importe"                   =>  "0",
                "empresa_id"                =>  $documento_venta_antiguo->empresa_id,
                "monto_sub_total"           =>  $documento_venta_antiguo->sub_total,
                "monto_embalaje"            =>  $documento_venta_antiguo->monto_embalaje,
                "monto_envio"               =>  $documento_venta_antiguo->monto_envio,
                "monto_total_igv"           =>  $documento_venta_antiguo->total_igv,
                "monto_descuento"           =>  $documento_venta_antiguo->monto_descuento,
                "monto_total"               =>  $documento_venta_antiguo->total,
                "monto_total_pagar"         =>  $documento_venta_antiguo->total_pagar,
                "data_envio"                =>  null,
                "productos_tabla"           =>  json_encode($detalles_documento_venta_antiguo),
                "sede_id"                   =>  $documento_venta_antiguo->sede_id,
                "almacenSeleccionado"       =>  $documento_venta_antiguo->almacen_id,
                "regularizar"               =>  'SI',
                'doc_regularizar_id'        =>  $documento_id
            ];

            $request_doc_nuevo->merge($additionalData);

            //====== GENERANDO NUEVO DOC DE VENTA ======
            $res_storeJson  =   $this->store($request_doc_nuevo);

            $res_store      =   $res_storeJson->getData();

            //====== MANEJANDO RESPUESTA ======
            //====== CASO I - DOC VENTA NUEVO GENERADO CORRECTAMENTE =======
            if ($res_store->success) {

                //===== BUSCAMOS EL NUEVO DOC PARA EXTRAER SU CORRELATIVO Y SERIE =====
                $documento_venta_nuevo           =   Documento::find($res_store->documento_id);
                $serie_correlativo_doc_nuevo     =   $documento_venta_nuevo->serie . '-' . $documento_venta_nuevo->correlativo;

                $serie_correlativo_doc_antiguo   =   $documento_venta_antiguo->serie . '-' . $documento_venta_antiguo->correlativo;
                //==== PREPARAMOS EL MENSAJE ======
                $message    =   "SE CREO EL NUEVO DOCUMENTO DE VENTA: " . $serie_correlativo_doc_nuevo . ",
                                Y SE ANULÓ EL DOCUMENTO DE VENTA: " . $serie_correlativo_doc_antiguo;

                $documento_venta_antiguo->estado                =   'ANULADO';
                $documento_venta_antiguo->regularizado_en_id    =   $documento_venta_nuevo->id;
                $documento_venta_antiguo->regularizado_en_serie =   $documento_venta_nuevo->serie . '-' . $documento_venta_nuevo->correlativo;
                $documento_venta_antiguo->update();
                //===== COMMITEAMOS =====
                DB::commit();

                return response()->json(['success'   => true, 'message' => $message, 'documento_id' => $res_store->documento_id]);
            } else {

                //======= EN CASO NO SE GENERE EL DOC VENTA, SOLO EMITIMOS LA RESPUESTA
                //===== EL ROLLBACK YA SE HIZO EN EL store()
                return $res_storeJson;
            }
        } catch (\Throwable $th) {
            DB::rollback();

            //===== ELIMINANDO DOC DE VENTA NUEVO GENERADO ====
            /*DB::table('cotizacion_documento')
              ->where('id', $res_store->documento_id)
              ->delete();*/

            return response()->json(['message' => $th->getMessage(), 'success' => false, 'line' => $th->getLine()]);
        }
    }

    public function getRecibosCaja($cliente_id)
    {
        try {
            $recibos_caja   =   DB::select('select rc.*,u.usuario as user_nombre
                                from recibos_caja as rc
                                inner join users as u on u.id=rc.user_id
                                where rc.cliente_id=? and rc.saldo > 0 and rc.estado="ACTIVO" and rc.estado_servicio <> "CANJEADO"
                                order by rc.created_at desc', [$cliente_id]);

            return response()->json(['success' => true, 'recibos_caja' => $recibos_caja]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'ERROR AL OBTENER RECIBOS DE CAJA DEL CLIENTE',
                'exception' => $th->getMessage()
            ]);
        }
    }

    //=============== CAMBIO DE TALLAS ==============
    public function cambiarTallasCreate($documento_id)
    {
        try {
            $documento      =   Documento::find($documento_id);
            $cambios_tallas =   DB::select('select * from cambios_tallas as ct
                                where ct.estado = "ACTIVO" and ct.documento_id = ?', [$documento_id]);
            $detalles       =   $documento->detalles;

            return view('ventas.documentos.cambios_tallas.index', compact('documento', 'detalles', 'cambios_tallas'));
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }


    public function getTallas($almacen_id, $producto_id, $color_id)
    {
        try {
            $tallas     =   DB::select(
                'select
                            pct.almacen_id,
                            pct.producto_id,
                            pct.color_id,
                            pct.talla_id,
                            t.descripcion as talla_nombre,
                            p.nombre as producto_nombre,
                            c.descripcion as color_nombre,
                            pct.stock,
                            pct.stock_logico
                            from producto_color_tallas as pct
                            inner join productos as p on p.id = pct.producto_id
                            inner join colores as c on c.id = pct.color_id
                            inner join tallas as t on t.id = pct.talla_id
                            where
                            pct.almacen_id = ?
                            AND pct.producto_id = ?
                            AND pct.color_id = ?
                            AND pct.estado = "ACTIVO"
                            AND pct.stock_logico > 0
                            AND pct.stock > 0',
                [$almacen_id, $producto_id, $color_id]
            );

            return response()->json(['success' => true, 'tallas' => $tallas]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "ERROR AL OBTENER LAS TALLAS",
                'exception' => $th->getMessage()
            ]);
        }
    }

    public function getStock($almacen_id, $producto_id, $color_id, $talla_id)
    {
        try {

            $stock  =   DB::select(
                'select
                        pct.stock
                        from producto_color_tallas as pct
                        where
                        pct.almacen_id = ?
                        AND pct.producto_id = ?
                        AND pct.color_id = ?
                        AND pct.talla_id = ?
                        AND pct.estado = "ACTIVO"',
                [$almacen_id, $producto_id, $color_id, $talla_id]
            );

            return response()->json(['success' => true, 'stock' => $stock, 'message' => "STOCK OBTENIDO"]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => "ERROR AL OBTENER STOCK ACTUAL DE LA TALLA",
                'exception' => $th->getMessage()
            ]);
        }
    }

    public function validarCantCambiar($documento_id, $detalle_id, $cantidad)
    {
        try {
            //====== OBTENER LA CANTIDAD DEL DETALLE ==========
            $cantidad_item     =   DB::select(
                'select
                                    cdd.cantidad
                                    from cotizacion_documento_detalles as cdd
                                    where
                                    cdd.documento_id = ?
                                    and cdd.id = ?
                                    and cdd.estado = "ACTIVO"',
                [$documento_id, $detalle_id]
            );


            if (count($cantidad_item) === 0) {
                throw new Exception('NO SE ENCONTRÓ EL DETALLE #' . $detalle_id . ' EN LA BASE DE DATOS');
            }


            //====== SI LA CANTIDAD DEL ITEM ES MAYOR O IGUAL A LA CANTIDAD QUE SE QUIERE CAMBIAR ========
            if ($cantidad_item[0]->cantidad >= $cantidad) {
                return response()->json(["success" => true, "message" => "CANTIDAD A CAMBIAR VÁLIDA"]);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "CANTIDAD A CAMBIAR DEBE SER MENOR O IGUAL A LA CANTIDAD DEL DETALLE"
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => "ERROR EN EL SERVIDOR AL VALIDAR LA CANTIDAD A CAMBIAR",
                "exception" => $th->getMessage()
            ]);
        }
    }

    public function validarStock(Request $request)
    {

        try {
            $nuevo_cambio       =   json_decode($request->get('nuevo_cambio'));

            //======= OBTENIENDO CONTENIDO =====
            $producto_cambiado      =   $nuevo_cambio->producto_cambiado;
            $producto_reemplazante  =   $nuevo_cambio->producto_reemplazante;
            $documento_id           =   $nuevo_cambio->documento_id;
            $cantidad_cambiar       =   $nuevo_cambio->cantidad;

            if (!$cantidad_cambiar) {
                throw new Exception('EL PRODUCTO ' . $producto_cambiado->producto_nombre . '-' . $producto_cambiado->color_nombre . '-' .
                    $producto_cambiado->talla_nombre . ' NO TIENE UNA CANTIDAD ASIGNADA PARA CAMBIO');
            }

            $documento  =   Documento::find($documento_id);

            //======= OBTENIENDO STOCKS DEL PRODUCTO REEMPLAZANTE ========
            $stocks_producto_reemplazante   =   DB::select(
                'select
                                                pct.stock,
                                                pct.stock_logico
                                                from producto_color_tallas as pct
                                                inner join productos as p on p.id = pct.producto_id
                                                where
                                                p.estado = "ACTIVO"
                                                AND pct.estado = "ACTIVO"
                                                AND pct.almacen_id = ?
                                                AND pct.producto_id = ?
                                                AND pct.color_id = ?
                                                AND pct.talla_id = ?',
                [
                    $documento->almacen_id,
                    $producto_reemplazante->producto_id,
                    $producto_reemplazante->color_id,
                    $producto_reemplazante->talla_id
                ]
            );

            if (count($stocks_producto_reemplazante) === 0) {
                throw new Exception('EL PRODUCTO ' . $producto_reemplazante->producto_nombre . '-' . $producto_reemplazante->color_nombre . '-' .
                    $producto_reemplazante->talla_nombre . ' REEMPLAZANTE  NO FUE ENCONTRADO EN LA BASE DE DATOS');
            }

            $stock_logico_producto_reemplazante =   $stocks_producto_reemplazante[0]->stock_logico;
            if ($stock_logico_producto_reemplazante >= $cantidad_cambiar) {

                //========== SEPARAMOS STOCK LÓGICO DEL PRODUCTO REEMPLAZANTE ======
                DB::update(
                    'UPDATE producto_color_tallas
                SET stock_logico = stock_logico - ?
                WHERE
                almacen_id = ?
                AND producto_id = ?
                AND color_id = ?
                AND talla_id = ?',
                    [
                        $cantidad_cambiar,
                        $documento->almacen_id,
                        $producto_reemplazante->producto_id,
                        $producto_reemplazante->color_id,
                        $producto_reemplazante->talla_id
                    ]
                );

                return response()->json(['success' => true, 'message'  =>  'STOCK LÓGICO SEPARADO PRODUCTO: ' . $producto_reemplazante->producto_nombre .
                    '-' . $producto_reemplazante->color_nombre . '-' . $producto_reemplazante->talla_nombre]);
            } else {
                throw new Exception('STOCK LÓGICO INSUFICIENTE PRODUCTO: ' . $producto_reemplazante->producto_nombre .
                    '-' . $producto_reemplazante->color_nombre . '-' . $producto_reemplazante->talla_nombre);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message'  =>  'ERROR EN EL SERVIDOR AL VALIDAR STOCKS',
                'exception' => $th->getMessage()
            ]);
        }
    }

    /*
array:2 [
  "cambios_devolver" => "[{"documento_id":8151,"producto_reemplazante":{"producto_id":"610","color_id":"5","talla_id":"2","producto_nombre":"SKYLA","color_nombre":"NEGRO CHAROL","talla_nombre":"36"},"producto_cambiado":{"detalle_id":"20823","producto_id":"610","color_id":"5","talla_id":"5","producto_nombre":"SKYLA","color_nombre":"NEGRO CHAROL","talla_nombre":"39","cantidad_detalle":"1.0000"},"cantidad":1}]"
  "almacen_id" => 1
]
*/
    public function devolverStockLogico(Request $request)
    {
        DB::beginTransaction();

        try {
            $cambios_devolver   =  json_decode($request->get('cambios_devolver'));
            $almacen_id         =   $request->get('almacen_id');

            $productos_message  =   '';

            foreach ($cambios_devolver as $cambio) {

                //======= OBTENIENDO PARAMETROS ========
                $producto_reemplazante  =   $cambio->producto_reemplazante;
                $cantidad               =   (int)$cambio->cantidad;

                DB::update(
                    'UPDATE producto_color_tallas
                SET stock_logico = stock_logico + ?
                WHERE
                almacen_id = ?
                AND producto_id = ?
                AND color_id = ?
                AND talla_id = ?',
                    [
                        $cantidad,
                        $almacen_id,
                        $producto_reemplazante->producto_id,
                        $producto_reemplazante->color_id,
                        $producto_reemplazante->talla_id
                    ]
                );

                $productos_message .= ' ' . $producto_reemplazante->producto_nombre . '-' .
                    $producto_reemplazante->color_nombre . '-' . $producto_reemplazante->talla_nombre;
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'CANTIDAD DEVUELTA PRODUCTOS: ' . $productos_message]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'ERROR AL DEVOLVER STOCKS LÓGICOS',
                    'exception' => $th->getMessage(),
                    'line' => $th->getLine()
                ],
            );
        }
    }

    /*
array:2 [
  "cambios"         => "[{"documento_id":8151,"producto_reemplazante":{"producto_id":"610","color_id":"5","talla_id":"1","producto_nombre":"SKYLA","color_nombre":"NEGRO CHAROL","talla_nombre":"35"},"producto_cambiado":{"detalle_id":"20823","producto_id":"610","color_id":"5","talla_id":"5","producto_nombre":"SKYLA","color_nombre":"NEGRO CHAROL","talla_nombre":"39","cantidad_detalle":"1.0000"},"cantidad":1}]"
  "documento_id"    => 8151
]
*/
    public function cambiarTallasStore(Request $request)
    {

        DB::beginTransaction();
        try {
            $documento_id   =   $request->get('documento_id');
            $documento      =   Documento::find($documento_id);

            if (!$documento) {
                throw new Exception('NO SE ENCONTRÓ EL DOCUMENTO DE VENTA EN LA BASE DE DATOS');
            }

            //======= OBTENIENDO CAMBIOS ======
            $cambios                                =   json_decode($request->get('cambios'));

            //======= CREANDO NOTA DE INGRESO PARA PRODUCTOS CAMBIADOS EN SEDE DEL ALMACEN =======
            $almacen_destino                        =   Almacen::find($documento->almacen_id);

            $notaingreso                            =   new NotaIngreso();
            $notaingreso->almacen_destino_id        =   $documento->almacen_id;
            $notaingreso->almacen_destino_nombre    =   $documento->almacen_nombre;
            $notaingreso->sede_id                   =   $almacen_destino->sede_id;
            $notaingreso->registrador_nombre        =   Auth::user()->usuario;
            $notaingreso->registrador_id            =   Auth::user()->id;
            $notaingreso->observacion               =   'CAMBIO DE TALLAS EN EL DOCUMENTO ' . $documento->serie . '-' . $documento->correlativo;
            $notaingreso->save();


            //======== CREANDO NOTA DE SALIDA PARA PRODUCTOS REEMPLAZANTES =======
            //====== VERIFICAR SI EXISTE EL ALMACÉN CAMBIOS ======
            $almacen_cambios    =   Almacen::where('descripcion', 'CAMBIOS')
                ->where('sede_id', $almacen_destino->sede_id)
                ->get();

            if (count($almacen_cambios) === 0) {
                $nuevo_almacen                  =   new Almacen();
                $nuevo_almacen->descripcion     =   'CAMBIOS';
                $nuevo_almacen->descripcion     =   'CAMBIOS';
                $nuevo_almacen->tipo_almacen    =   'SECUNDARIO';
                $nuevo_almacen->sede_id         =   $almacen_destino->sede_id;
                $nuevo_almacen->ubicacion       =   'CAMBIOS';
                $nuevo_almacen->save();

                $almacen_cambios =   $nuevo_almacen;
            } else {
                $almacen_cambios =   $almacen_cambios[0];
            }

            $nota_salida                            =   new NotaSalidad();
            $nota_salida->sede_id                   =   $notaingreso->sede_id;
            $nota_salida->almacen_origen_id         =   $documento->almacen_id;
            $nota_salida->almacen_destino_id        =   $almacen_cambios->id;
            $nota_salida->registrador_nombre        =   Auth::user()->usuario;
            $nota_salida->registrador_id            =   Auth::user()->id;
            $nota_salida->almacen_origen_nombre     =   $documento->almacen_nombre;
            $nota_salida->almacen_destino_nombre    =   $almacen_cambios->descripcion;
            $nota_salida->observacion               =   "CAMBIO DE TALLAS EN EL DOCUMENTO " . $documento->serie . '-' . $documento->correlativo;
            $nota_salida->save();

            //====== CREAR NOTA DE INGRESO AL ALMACÉN CAMBIOS DE LA SEDE DEL ALMACÉN DEL DOCUMENTO ====
            $notaingreso_cambios                            =   new NotaIngreso();
            $notaingreso_cambios->almacen_destino_id        =   $almacen_cambios->id;
            $notaingreso_cambios->almacen_destino_nombre    =   $almacen_cambios->descripcion;
            $notaingreso_cambios->sede_id                   =   $almacen_cambios->sede_id;
            $notaingreso_cambios->registrador_nombre        =   Auth::user()->usuario;
            $notaingreso_cambios->registrador_id            =   Auth::user()->id;
            $notaingreso_cambios->observacion               =   'CAMBIO DE TALLAS EN EL DOCUMENTO ' . $documento->serie . '-' . $documento->correlativo;
            $notaingreso_cambios->nota_salida_id            =   $nota_salida->id;
            $notaingreso_cambios->save();


            foreach ($cambios as $cambio) {

                $producto_cambiado      =   $cambio->producto_cambiado;
                $producto_reemplazante  =   $cambio->producto_reemplazante;
                $cantidad               =   $cambio->cantidad;

                //====== COMPROBAR SI EXISTE EL PRODUCTO REEMPLAZANTE EN EL ALMACÉN DEL DOC VENTA =====
                $existe =   DB::select(
                    'select
                            pct.stock,
                            pct.stock_logico,
                            p.nombre as producto_nombre,
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
                    [
                        $documento->almacen_id,
                        $producto_reemplazante->producto_id,
                        $producto_reemplazante->color_id,
                        $producto_reemplazante->talla_id
                    ]
                );

                if (count($existe) === 0) {
                    throw new Exception("NO EXISTE EL PRODUCTO: " .
                        $producto_reemplazante->producto_nombre . '-' . $producto_reemplazante->color_nombre . '-' . $producto_reemplazante->talla_nombre .
                        ', EN EL ALMACÉN: ' . $documento->almacen_nombre);
                }

                //======= VALIDAR CANTIDAD =====
                if ($cantidad > $existe[0]->stock) {
                    throw new Exception("STOCK INSUFICIENTE (" . $existe[0]->stock . "), " .
                        $producto_reemplazante->producto_nombre . '-' . $producto_reemplazante->color_nombre . '-' . $producto_reemplazante->talla_nombre .
                        ', EN EL ALMACÉN: ' . $documento->almacen_nombre);
                }

                //======== GENERANDO DETALLE DE LA NOTA DE INGRESO PARA EL PRODUCTO CAMBIADO ========
                $detalle_ni                     =   new   DetalleNotaIngreso();
                $detalle_ni->nota_ingreso_id    =   $notaingreso->id;
                $detalle_ni->almacen_id         =   $almacen_destino->id;
                $detalle_ni->producto_id        =   $producto_cambiado->producto_id;
                $detalle_ni->color_id           =   $producto_cambiado->color_id;
                $detalle_ni->talla_id           =   $producto_cambiado->talla_id;
                $detalle_ni->cantidad           =   $cantidad;
                $detalle_ni->almacen_nombre     =   $almacen_destino->descripcion;
                $detalle_ni->producto_nombre    =   $producto_cambiado->producto_nombre;
                $detalle_ni->color_nombre       =   $producto_cambiado->color_nombre;
                $detalle_ni->talla_nombre       =   $producto_cambiado->talla_nombre;
                $detalle_ni->save();

                //========= GENERANDO DETALLE DE LA NOTA DE SALIDA PARA EL PRODUCTO REEMPLAZANTE =======
                $detalle_ns                        =   new   DetalleNotaSalidad();
                $detalle_ns->nota_salida_id        =   $nota_salida->id;
                $detalle_ns->almacen_id            =   $documento->almacen_id;
                $detalle_ns->producto_id           =   $producto_reemplazante->producto_id;
                $detalle_ns->color_id              =   $producto_reemplazante->color_id;
                $detalle_ns->talla_id              =   $producto_reemplazante->talla_id;
                $detalle_ns->cantidad              =   $cantidad;
                $detalle_ns->almacen_nombre        =   $documento->almacen_nombre;
                $detalle_ns->producto_nombre       =   $producto_reemplazante->producto_nombre;
                $detalle_ns->color_nombre          =   $producto_reemplazante->color_nombre;
                $detalle_ns->talla_nombre          =   $producto_reemplazante->talla_nombre;
                $detalle_ns->save();

                //======== INGRESAR STOCK DEL PRODUCTO CAMBIADO EN EL ALMCÉN DEL DOCUMENTO ===
                ProductoColorTalla::where('producto_id', $producto_cambiado->producto_id)
                    ->where('color_id', $producto_cambiado->color_id)
                    ->where('talla_id', $producto_cambiado->talla_id)
                    ->where('almacen_id', $documento->almacen_id)
                    ->update([
                        'stock'         =>  DB::raw("stock + $cantidad"),
                        'stock_logico'  =>  DB::raw("stock_logico + $cantidad"),
                        'updated_at'    =>  now()
                    ]);

                //========= RESTAR STOCK DEL PRODUCTO REEMPLAZANTE EN EL ALMACÉN DEL DOCUMENTO ======
                ProductoColorTalla::where('producto_id', $producto_reemplazante->producto_id)
                    ->where('color_id', $producto_reemplazante->color_id)
                    ->where('talla_id', $producto_reemplazante->talla_id)
                    ->where('almacen_id', $documento->almacen_id)
                    ->update([
                        'stock'         =>  DB::raw("stock - $cantidad"),
                        'updated_at'    =>  now()
                    ]);

                //========== INGRESAR STOCK DEL PRODUCTO REEMPLAZANTE AL ALMACÉN CAMBIOS ======
                $detalle_nic                     =   new   DetalleNotaIngreso();
                $detalle_nic->nota_ingreso_id    =   $notaingreso_cambios->id;
                $detalle_nic->almacen_id         =   $almacen_cambios->id;
                $detalle_nic->producto_id        =   $producto_reemplazante->producto_id;
                $detalle_nic->color_id           =   $producto_reemplazante->color_id;
                $detalle_nic->talla_id           =   $producto_reemplazante->talla_id;
                $detalle_nic->cantidad           =   $cantidad;
                $detalle_nic->almacen_nombre     =   $almacen_cambios->descripcion;
                $detalle_nic->producto_nombre    =   $producto_reemplazante->producto_nombre;
                $detalle_nic->color_nombre       =   $producto_reemplazante->color_nombre;
                $detalle_nic->talla_nombre       =   $producto_reemplazante->talla_nombre;
                $detalle_nic->save();

                //=>COMPROBANDO SI EXISTE EL PRODUCTO COLOR TALLA
                $producto_destino   =   DB::select(
                    'select
                                        pct.stock
                                        from producto_color_tallas as pct
                                        where
                                        pct.almacen_id  = ?
                                        and pct.producto_id = ?
                                        and pct.color_id = ?
                                        and pct.talla_id = ?',
                    [
                        $almacen_cambios->id,
                        $producto_reemplazante->producto_id,
                        $producto_reemplazante->color_id,
                        $producto_reemplazante->talla_id
                    ]
                );

                //======== TALLA EXISTE, INCREMENTAR STOCK =========
                if (count($producto_destino) > 0) {

                    ProductoColorTalla::where('producto_id', $producto_reemplazante->producto_id)
                        ->where('color_id', $producto_reemplazante->color_id)
                        ->where('talla_id', $producto_reemplazante->talla_id)
                        ->where('almacen_id', $almacen_cambios->id)
                        ->update([
                            'stock'         =>  DB::raw("stock + $cantidad"),
                            'stock_logico'  =>  DB::raw("stock_logico + $cantidad"),
                            'updated_at'    =>  now()
                        ]);
                } else {

                    //========= TALLA NO EXISTE =============
                    //======= VERIFICANDO EXISTENCIA DEL COLOR ======
                    $existeColor    =   ProductoColor::where('producto_id', $producto_reemplazante->producto_id)
                        ->where('color_id', $producto_reemplazante->color_id)
                        ->where('almacen_id', $almacen_cambios->id)
                        ->exists();

                    //======== COLOR NO EXISTE, REGISTRAR COLOR =======
                    if (!$existeColor) {
                        $producto_color                 =   new ProductoColor();
                        $producto_color->producto_id    =   $producto_reemplazante->producto_id;
                        $producto_color->color_id       =   $producto_reemplazante->color_id;
                        $producto_color->almacen_id     =   $almacen_cambios->id;
                        $producto_color->save();
                    }

                    //====== REGISTRAR TALLA ============
                    $producto                   =   new ProductoColorTalla();
                    $producto->producto_id      =   $producto_reemplazante->producto_id;
                    $producto->color_id         =   $producto_reemplazante->color_id;
                    $producto->talla_id         =   $producto_reemplazante->talla_id;
                    $producto->stock            =   $cantidad;
                    $producto->stock_logico     =   $cantidad;
                    $producto->almacen_id       =   $almacen_cambios->id;
                    $producto->save();
                }


                //===== GRABANDO CAMBIO ======
                $cambio_talla                               =   new CambioTalla();
                $cambio_talla->documento_id                 =   $documento_id;
                $cambio_talla->detalle_id                   =   $producto_cambiado->detalle_id;
                $cambio_talla->producto_reemplazado_id      =   $producto_cambiado->producto_id;
                $cambio_talla->color_reemplazado_id         =   $producto_cambiado->color_id;
                $cambio_talla->talla_reemplazado_id         =   $producto_cambiado->talla_id;
                $cambio_talla->producto_reemplazado_nombre  =   $producto_cambiado->producto_nombre;
                $cambio_talla->color_reemplazado_nombre     =   $producto_cambiado->color_nombre;
                $cambio_talla->talla_reemplazado_nombre     =   $producto_cambiado->talla_nombre;
                $cambio_talla->cantidad_detalle             =   $producto_cambiado->cantidad_detalle;
                $cambio_talla->producto_reemplazante_id     =   $producto_reemplazante->producto_id;
                $cambio_talla->color_reemplazante_id        =   $producto_reemplazante->color_id;
                $cambio_talla->talla_reemplazante_id        =   $producto_reemplazante->talla_id;
                $cambio_talla->producto_reemplazante_nombre     =   $producto_reemplazante->producto_nombre;
                $cambio_talla->color_reemplazante_nombre        =   $producto_reemplazante->color_nombre;
                $cambio_talla->talla_reemplazante_nombre        =   $producto_reemplazante->talla_nombre;
                $cambio_talla->cantidad_cambiada            =   $cantidad;
                $cambio_talla->cantidad_sin_cambio          =   $producto_cambiado->cantidad_detalle - $cantidad;
                $cambio_talla->user_id                      =   Auth::user()->id;
                $cambio_talla->user_nombre                  =   Auth::user()->usuario;
                $cambio_talla->sede_id                      =   Auth::user()->sede_id;
                $cambio_talla->almacen_id                   =   $documento->almacen_id;
                $cambio_talla->almacen_nombre               =   $documento->almacen_nombre;
                $cambio_talla->save();

                //====== ACTUALIZANDO ESTADO DEL DETALLE DEL DOCUMENTO =======
                DB::table('cotizacion_documento_detalles')
                    ->where('documento_id', $documento->id)
                    ->where('id', $producto_cambiado->detalle_id)
                    ->where('almacen_id', $documento->almacen_id)
                    ->where('producto_id', $producto_cambiado->producto_id)
                    ->where('color_id', $producto_cambiado->color_id)
                    ->where('talla_id', $producto_cambiado->talla_id)
                    ->update([
                        'estado_cambio_talla'   => "CON CAMBIOS",
                        'cantidad_cambiada'     => $cantidad,
                        'cantidad_sin_cambio'   => $producto_cambiado->cantidad_detalle - $cantidad
                    ]);
            }

            //======== MARCANDO DOC VENTA CON CAMBIO DE TALLA ========
            $documento->cambio_talla = '1';
            $documento->update();


            DB::commit();
            return response()->json(['success' => true, 'message' => 'ÉXITO, SE CAMBIARON LAS TALLAS DEL DOC N° ' . $documento->serie . '-' . $documento->correlativo]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'ERROR AL CAMBIAR TALLAS DEL DOC N° ' . $documento->serie . '-' . $documento->correlativo,
                'exception' => $th->getMessage()
            ]);
        }
    }

    public function getHistorialCambiosTallas($detalle_id, $documento_id)
    {
        try {
            $cambios_tallas =   DB::select(
                'select * from cambios_tallas as ct
                                where ct.documento_id = ? and ct.detalle_id = ?',
                [$documento_id, $detalle_id]
            );

            return response()->json(['success' => true, 'cambios_tallas' => $cambios_tallas]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getProductoBarCode($barcode)
    {
        try {

            $barcode    =   mb_strtoupper($barcode, 'UTF-8');

            $producto   =   DB::select('select
                            c.descripcion as color_nombre,
                            t.descripcion as talla_nombre,
                            cb.producto_id,
                            cb.color_id,
                            cb.talla_id,
                            p.nombre as producto_nombre,
                            p.categoria_id,
                            p.marca_id,
                            p.modelo_id,
                            p.precio_venta_1 as precio_venta
                            from codigos_barra as cb
                            inner join colores as c on c.id = cb.color_id
                            inner join tallas as t on t.id = cb.talla_id
                            inner join productos as p on p.id = cb.producto_id
                            where UPPER(cb.codigo_barras) = ?', [$barcode]);

            if (count($producto) === 0) {
                throw new Exception("NO SE ENCONTRÓ NINGÚN PRODUCTO CON ESTE CÓDIGO DE BARRAS!!!");
            }

            return response()->json(['success' => true, 'producto' => $producto[0]]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    /*
    array:1 [
        "almacenId"         =>  "almacenId:1"
        "lstInputProducts"  =>  "[{"producto_id":"2","producto_nombre":"KAREN","color_id":"1","color_nombre":"BLANCO CUERO","talla_id":"1","talla_nombre":"35","cantidad":"2","precio_venta":"45.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0},{"producto_id":"2","producto_nombre":"KAREN","color_id":"3","color_nombre":"NEGRO CUERO","talla_id":"1","talla_nombre":"35","cantidad":"3","precio_venta":"45.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0},{"producto_id":"2","producto_nombre":"KAREN","color_id":"6","color_nombre":"NUDE CUERO","talla_id":"1","talla_nombre":"35","cantidad":"4","precio_venta":"45.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0},{"producto_id":"2","producto_nombre":"KAREN","color_id":"6","color_nombre":"NUDE CUERO","talla_id":"3","talla_nombre":"37","cantidad":"1","precio_venta":"45.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0},{"producto_id":"2","producto_nombre":"KAREN","color_id":"15","color_nombre":"AMARILLO CUERO","talla_id":"3","talla_nombre":"37","cantidad":"2","precio_venta":"45.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0}]"
    ]
*/
    public function validarStockVentas(Request $request)
    {
        try {

            $lstInputProducts   =   json_decode($request->get('lstInputProducts'));
            $almacen_id         =   $request->get('almacenId');

            if (!$almacen_id) {
                throw new Exception("FALTA EL PARÁMETRO ALMACÉN ID!!!");
            }

            $almacen    =   DB::select('select a.*
                            from almacenes as a
                            where a.estado = "ACTIVO"
                            AND a.id = ?', [$almacen_id]);

            if (count($almacen) === 0) {
                throw new Exception("NO EXISTE EL ALMACÉN EN LA BD!!!");
            }

            if (count($lstInputProducts) === 0) {
                throw new Exception("DEBE INGRESAR UNA CANTIDAD PARA AGREGAR PRODUCTOS!!!");
            }

            foreach ($lstInputProducts as  $inputProduct) {

                $producto   =   DB::select(
                    'select
                                pct.stock_logico
                                from producto_color_tallas as pct
                                where
                                pct.almacen_id = ?
                                and pct.producto_id = ?
                                and pct.color_id = ?
                                and pct.talla_id = ?',
                    [
                        $almacen_id,
                        $inputProduct->producto_id,
                        $inputProduct->color_id,
                        $inputProduct->talla_id
                    ]
                );

                if (count($producto) === 0) {
                    throw new Exception("NO EXISTE EL PRODUCTO " . $inputProduct->producto_nombre . '-' . $inputProduct->color_nombre . '-' . $inputProduct->talla_nombre);
                }

                if ($producto[0]->stock_logico < $inputProduct->cantidad) {
                    throw new Exception("STOCK (" . $producto[0]->stock_logico . ") " . "INSUFICIENTE PARA EL PRODUCTO " . $inputProduct->producto_nombre . '-' . $inputProduct->color_nombre . '-' . $inputProduct->talla_nombre);
                }
            }

            return response()->json(['success' => true, 'message' => 'STOCKS VÁLIDOS']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    /*
array:1 [
    "lstInputProducts" => "[{"producto_id":"2","producto_nombre":"KAREN","color_id":"1","color_nombre":"BLANCO CUERO","talla_id":"1","talla_nombre":"35","cantidad":"2","precio_venta":"45.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0},{"producto_id":"2","producto_nombre":"KAREN","color_id":"3","color_nombre":"NEGRO CUERO","talla_id":"1","talla_nombre":"35","cantidad":"3","precio_venta":"45.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0},{"producto_id":"2","producto_nombre":"KAREN","color_id":"6","color_nombre":"NUDE CUERO","talla_id":"1","talla_nombre":"35","cantidad":"4","precio_venta":"45.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0},{"producto_id":"2","producto_nombre":"KAREN","color_id":"6","color_nombre":"NUDE CUERO","talla_id":"3","talla_nombre":"37","cantidad":"1","precio_venta":"45.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0},{"producto_id":"2","producto_nombre":"KAREN","color_id":"15","color_nombre":"AMARILLO CUERO","talla_id":"3","talla_nombre":"37","cantidad":"2","precio_venta":"45.00","monto_descuento":0,"porcentaje_descuento":0,"precio_venta_nuevo":0,"subtotal_nuevo":0}]"
]
*/
    public function actualizarStockAdd(Request $request)
    {
        DB::beginTransaction();

        try {
            $lstInputProducts   =   json_decode($request->get('lstInputProducts'));
            $almacen_id         =   $request->get('almacenId');


            if (!$almacen_id) {
                throw new Exception("FALTA EL PARÁMETRO ALMACÉN ID!!!");
            }

            $almacen    =   DB::select('select a.*
                            from almacenes as a
                            where a.estado = "ACTIVO"
                            AND a.id = ?', [$almacen_id]);

            if (count($almacen) === 0) {
                throw new Exception("NO EXISTE EL ALMACÉN EN LA BD!!!");
            }

            if (count($lstInputProducts) === 0) {
                throw new Exception("DEBE INGRESAR UNA CANTIDAD PARA AGREGAR PRODUCTOS!!!");
            }

            foreach ($lstInputProducts as  $inputProduct) {

                $producto   =   DB::select(
                    'select
                                    pct.stock_logico
                                    from producto_color_tallas as pct
                                    where
                                    pct.almacen_id = ?
                                    AND pct.producto_id = ?
                                    and pct.color_id = ?
                                    and pct.talla_id = ?',
                    [
                        $almacen_id,
                        $inputProduct->producto_id,
                        $inputProduct->color_id,
                        $inputProduct->talla_id
                    ]
                );

                if (count($producto) === 0) {
                    throw new Exception("NO EXISTE EL PRODUCTO " . $inputProduct->producto_nombre . '-' . $inputProduct->color_nombre . '-' . $inputProduct->talla_nombre);
                }

                if ($producto[0]->stock_logico < $inputProduct->cantidad) {
                    throw new Exception("STOCK (" . $producto[0]->stock_logico . ") " . "INSUFICIENTE PARA EL PRODUCTO " . $inputProduct->producto_nombre . '-' . $inputProduct->color_nombre . '-' . $inputProduct->talla_nombre);
                }

                DB::update(
                    'UPDATE producto_color_tallas
                SET stock_logico = stock_logico - ?,updated_at = ?
                WHERE
                almacen_id = ?
                and producto_id = ?
                and color_id = ?
                and talla_id = ?
                and estado = "ACTIVO"',
                    [
                        $inputProduct->cantidad,
                        Carbon::now(),
                        $almacen_id,
                        $inputProduct->producto_id,
                        $inputProduct->color_id,
                        $inputProduct->talla_id
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'STOCK LÓGICO ACTUALIZADO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    /*
array:3 [
  "almacen_id"  => "1"
  "producto_id" => "2"
  "color_id"    => "1"
  "tallas"      => "[{"talla_id":"1","talla_nombre":"35","cantidad":"1"},{"talla_id":"2","talla_nombre":"36","cantidad":"1"},{"talla_id":"3","talla_nombre":"37","cantidad":"1"},{"talla_id":"4","talla_nombre":"38","cantidad":"1"},{"talla_id":"5","talla_nombre":"39","cantidad":"1"}]"
]
*/
    public function actualizarStockDelete(Request $request)
    {
        DB::beginTransaction();
        try {

            $almacen_id     =   $request->get('almacen_id');
            $producto_id    =   $request->get('producto_id');
            $color_id       =   $request->get('color_id');
            $tallas         =   json_decode($request->get('tallas'));

            foreach ($tallas as $talla) {

                $producto   =   DB::select(
                    'select
                                pct.stock_logico
                                from producto_color_tallas as pct
                                where
                                pct.almacen_id = ?
                                and pct.producto_id = ?
                                and pct.color_id = ?
                                and pct.talla_id = ?',
                    [
                        $almacen_id,
                        $producto_id,
                        $color_id,
                        $talla->talla_id
                    ]
                );

                if (count($producto) === 0) {
                    throw new Exception("NO EXISTE ESTE PRODUCTO EN LA BD!!!");
                }

                DB::update(
                    'UPDATE producto_color_tallas
                SET stock_logico = stock_logico + ?,updated_at = ?
                WHERE
                almacen_id = ?
                and producto_id = ?
                and color_id = ?
                and talla_id = ?
                and estado = "ACTIVO"',
                    [
                        $talla->cantidad,
                        Carbon::now(),
                        $almacen_id,
                        $producto_id,
                        $color_id,
                        $talla->talla_id
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => "STOCK LÓGICO DEVUELTO!!!"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    /*
array:1 [
  "lstProductos" => "[{"producto_id":"2","color_id":"1","talla_id":1,"cantidad_actual":"4","cantidad_anterior":"3"},
                    {"producto_id":"2","color_id":"1","talla_id":2,"cantidad_actual":"1","cantidad_anterior":"1"}]"
]
*/
    public function actualizarStockEdit(Request $request)
    {
        DB::beginTransaction();

        try {
            $lstProductos   =   json_decode($request->get('lstProductos'));
            $almacen_id     =   $request->get('almacenId');

            if (!$almacen_id) {
                throw new Exception("FALTA EL PARÁMETRO ALMACÉN ID!!!");
            }

            $almacen    =   DB::select('select a.*
                            from almacenes as a
                            where a.estado = "ACTIVO"
                            AND a.id = ?', [$almacen_id]);

            if (count($almacen) === 0) {
                throw new Exception("NO EXISTE EL ALMACÉN EN LA BD!!!");
            }


            if (count($lstProductos) === 0) {
                throw new Exception("EL LISTADO DE PRODUCTOS ESTÁ VACÍO!!!");
            }

            foreach ($lstProductos as  $item) {

                $item->cantidad_anterior    =   $item->cantidad_anterior == '' ? 0 : $item->cantidad_anterior;
                $item->cantidad_actual      =   $item->cantidad_actual == '' ? 0 : $item->cantidad_actual;

                $producto   =   DB::select(
                    'select
                                pct.stock_logico,
                                p.nombre as producto_nombre,
                                c.descripcion as color_nombre,
                                t.descripcion as talla_nombre
                                from producto_color_tallas as pct
                                inner join productos as p on p.id = pct.producto_id
                                inner join colores as c on c.id = pct.color_id
                                inner join tallas as t on t.id = pct.talla_id
                                where
                                pct.almacen_id = ?
                                and pct.producto_id = ?
                                and pct.color_id = ?
                                and pct.talla_id = ?',
                    [
                        $almacen_id,
                        $item->producto_id,
                        $item->color_id,
                        $item->talla_id
                    ]
                );

                if (count($producto) === 0) {
                    throw new Exception("NO EXISTE EL PRODUCTO EN LA BD!!!");
                }

                $stock_logico   =   $producto[0]->stock_logico;

                if ($stock_logico + $item->cantidad_anterior < $item->cantidad_actual) {
                    throw new Exception("STOCK (" . $producto[0]->stock_logico . ") " . "INSUFICIENTE PARA EL PRODUCTO " . $producto[0]->producto_nombre . '-' . $producto[0]->color_nombre . '-' . $producto[0]->talla_nombre);
                }

                DB::update(
                    'UPDATE producto_color_tallas
                SET stock_logico = stock_logico + ?,updated_at = ?
                WHERE
                almacen_id = ?
                and producto_id = ?
                and color_id = ?
                and talla_id = ?
                and estado = "ACTIVO"',
                    [
                        $item->cantidad_anterior - $item->cantidad_actual,
                        Carbon::now(),
                        $almacen_id,
                        $item->producto_id,
                        $item->color_id,
                        $item->talla_id
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'STOCK LÓGICO ACTUALIZADO']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    /*
array:5 [
  "search"      => "aaa"
  "page"        => "1"
  "perPage"     => "10"
  "almacen_id"  => "1"
]
*/
    public function getProductosVenta(Request $request)
    {


        try {

            $search         = $request->query('search'); // Palabra clave para la búsqueda
            $almacenId      = $request->query('almacen_id'); // ID del almacén
            $page           = $request->query('page', 1); // Página actual (default es 1)
            $perPage        = $request->query('perPage', 10); // Número de elementos por página (default es 10)

            if (!$almacenId) {
                throw new Exception("FALTA SELECCIONAR UN ALMACÉN!!!");
            }

            $productos  =   DB::table('productos as p')
                ->join('categorias as c', 'c.id', 'p.categoria_id')
                ->join('marcas as ma', 'ma.id', 'p.marca_id')
                ->join('modelos as mo', 'mo.id', 'p.modelo_id')
                ->leftJoin('producto_color_tallas as pct', 'p.id', 'pct.producto_id')
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
                ->where('pct.almacen_id', $almacenId)
                ->where('pct.stock', '>', 0)
                ->where('p.estado', 'ACTIVO')
                ->groupBy('pct.almacen_id', 'p.id', 'c.descripcion', 'ma.marca', 'mo.descripcion', 'p.nombre')
                ->paginate($perPage, ['*'], 'page', $page);


            return response()->json(['success' => true, 'message' => 'PRODUCTOS OBTENIDOS', 'data' => $productos]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function convertirCreate($id)
    {

        $this->authorize('haveaccess', 'documento_venta.index');

        $productos          =   Producto::where('estado', 'ACTIVO')->get();
        $documento          =   Documento::findOrFail($id);
        $detalles           =   Detalle::where('documento_id', $id)->where('estado', 'ACTIVO')->get();
        $tallas             =   Talla::where('estado', 'ACTIVO')->get();
        $registrador        =   User::find($documento->user_id);
        $almacen            =   Almacen::find($documento->almacen_id);
        $cliente            =   Cliente::find($documento->cliente_id);
        $sede               =   Sede::find($documento->sede_id);

        $tipo_comprobante   =   DB::table('tabladetalles as td')
            ->select('td.*')
            ->where('td.tabla_id', 21);

        if ($cliente->tipo_documento === 'DNI') {
            $tipo_comprobante   =    $tipo_comprobante->where('td.parametro', "B")->get();
        }
        if ($cliente->tipo_documento === 'RUC') {
            $tipo_comprobante   =    $tipo_comprobante->where('td.parametro', "F")->get();
        }


        return view('ventas.documentos.convertir.index', [
            'documento'         =>  $documento,
            'detalles'          =>  $detalles,
            'productos'         =>  $productos,
            'tallas'            =>  $tallas,
            'registrador'       =>  $registrador,
            'almacen'           =>  $almacen,
            'cliente'           =>  $cliente,
            'tipo_comprobante'  =>  $tipo_comprobante[0],
            'sede'              =>  $sede
        ]);
    }

    /*
array:2 [
  "documento_id"    => "8150"
  "lstVenta"        => "[{"producto_id":565,"color_id":6,"producto_nombre":"KUSY","color_nombre":"NUDE CUERO","modelo_nombre":"KUSY","precio_venta":"45.00","porcentaje_descuento":"0.00","precio_venta_nuevo":"45.00","tallas":[{"talla_id":3,"cantidad":1,"talla_nombre":"37"}],"subtotal":45,"monto_descuento":0,"subtotal_nuevo":"45.00"},{"producto_id":610,"color_id":1,"producto_nombre":"SKYLA","color_nombre":"BLANCO CUERO","modelo_nombre":"SKYLA","precio_venta":"55.00","porcentaje_descuento":"0.00","precio_venta_nuevo":"55.00","tallas":[{"talla_id":3,"cantidad":1,"talla_nombre":"37"}],"subtotal":55,"monto_descuento":0,"subtotal_nuevo":"55.00"},{"producto_id":610,"color_id":6,"producto_nombre":"SKYLA","color_nombre":"NUDE CUERO","modelo_nombre":"SKYLA","precio_venta":"55.00","porcentaje_descuento":"0.00","precio_venta_nuevo":"55.00","tallas":[{"talla_id":3,"cantidad":1,"talla_nombre":"37"}],"subtotal":55,"monto_descuento":0,"subtotal_nuevo":"55.00"},{"producto_id":610,"color_id":45,"producto_nombre":"SKYLA","color_nombre":"BLANCO REPTIL","modelo_nombre":"SKYLA","precio_venta":"55.00","porcentaje_descuento":"0.00","precio_venta_nuevo":"55.00","tallas":[{"talla_id":1,"cantidad":1,"talla_nombre":"35"}],"subtotal":55,"monto_descuento":0,"subtotal_nuevo":"55.00"}]"
]
*/
    public function convertirStore(Request $request)
    {

        try {

            $documento_id   =   $request->get('documento_id');
            if (!$documento_id) {
                throw new Exception("FALTA EL PARÁMETRO DOCUMENTO ID EN LA PETICIÓN");
            }

            $documento  =   Documento::find($documento_id);
            if (!$documento) {
                throw new Exception("NO EXISTE EL DOCUMENTO DE VENTA EN LA BD");
            }

            if ($documento->convert_en_id) {
                throw new Exception("ESTE DOCUMENTO YA FUE CONVERTIDO");
            }

            $cliente    =   Cliente::find($documento->cliente_id);
            if ($cliente->tipo_documento !== 'DNI' && $cliente->tipo_documento !== 'RUC') {
                throw new Exception("EL CLIENTE DEBE TENER DNI O RUC PARA GENERAR UN COMPROBANTE DE VENTA");
            }

            $tipo_venta =   null;
            if ($cliente->tipo_documento === 'DNI') {
                $tipo_venta =   128;
            }
            if ($cliente->tipo_documento === 'RUC') {
                $tipo_venta =   127;
            }

            $request->merge(
                [
                    'almacenSeleccionado'   =>  $documento->almacen_id,
                    'sede_id'               =>  $documento->sede_id,
                    'cliente_id'            =>  $documento->cliente_id,
                    'condicion_id'          =>  $documento->condicion_id,
                    'productos_tabla'       =>  $request->get('lstVenta'),
                    'tipo_venta'            =>  $tipo_venta,
                    'documento_convertido'  =>  $documento->id
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }

        $docVentaRequest        =   DocVentaStoreRequest::createFrom($request);
        $documentoController    =   new DocumentoController();
        $res                    =   $documentoController->store($docVentaRequest);
        $jsonResponse           =   $res->getData();

        return $res;
    }

    /*
array:12 [
  "_token"              => "JkC2sg9Zt5Isu8EDaiym3KG9zjpCHMeGw2NmMA0P"
  "data_envio"          => "{"departamento":{"nombre":"LA LIBERTAD"},"provincia":{"text":"TRUJILLO"},"distrito":{"text":"TRUJILLO"},"empresa_envio":{"id":"1","empresa":"EMTRAFESA"},"sede_envio":{"id":"2","direccion":"Av. Nicolás de Piérola 1404 – 1406 Urb. Mochica."},"tipo_envio":{"descripcion":"AGENCIA"},"destinatario":{"nro_documento":"48259735","nombres":"KAREN JAZMIN ROJAS MELGAREJO","tipo_documento":"DNI"},"tipo_pago_envio":{"descripcion":"PAGAR ENVÍO"},"entrega_domicilio":false,"direccion_entrega":"","fecha_envio_propuesta":"2025-03-04","origen_venta":{"descripcion":"WATHSAPP"},"obs_rotulo":"","obs_despacho":""}"
  "data_envio"          =>  null
  "registrador"         => "MERCEDES ALFARO"
  "fecha_registro"      => "2025-02-28 16:31:05"
  "fecha_vencimiento"   => "2025-02-28"
  "documento"           => "N001-3999"
  "sede"                => "SEDE CENTRAL"
  "almacen"             => "1"
  "condicion_id"        => "1"
  "cliente"             => "4851"
  "tipo_venta"          => "129"
  "observacion"         => "test"
  "amounts"             => "{"subtotal":"38.00","embalaje":"0.00","envio":"0.00","total":"32.20","igv":"5.80","totalPagar":"38.00","monto_descuento":"0.00"}"
  "lstVenta"            => "[{"producto_id":581,"producto_nombre":"2MC792 SAND MICKEY","color_id":55,"color_nombre":"NEGRO","precio_venta":"38.00","subtotal":38,"subtotal_nuevo":0,"porcentaje_descuento":0,"monto_descuento":0,"precio_venta_nuevo":0,"tallas":[{"talla_id":15,"talla_nombre":"28","cantidad":1}]}]"
]
*/
    public function update(DocVentaUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        try {

            $datos_validados        =   DocumentoController::validacionUpdate($request, $id);

            //========== CALCULAR MONTOS ======
            $montos                 =   DocumentoController::calcularMontos($datos_validados->lstVenta, $datos_validados);

            //======== OBTENIENDO LEYENDA ======
            $legenda                =   UtilidadesController::convertNumeroLetras($montos->monto_total_pagar);

            //====== GRABAR MAESTRO VENTA =====
            $documento                      = $datos_validados->documento;

            //========= FECHAS ========
            //$documento->fecha_documento     = $documento->fecha_documento;
            //$documento->fecha_atencion      = $documento->fecha_documento;

            if ($datos_validados->condicion->id != 1) {
                $nro_dias                       = $datos_validados->condicion->dias;
                $documento->fecha_vencimiento   = Carbon::parse($documento->fecha_documento)->addDays($nro_dias)->toDateString();
            } else {
                $documento->fecha_vencimiento   = $documento->fecha_documento;
            }

            //========= CLIENTE =======
            $documento->tipo_documento_cliente  = $datos_validados->cliente->tipo_documento;
            $documento->documento_cliente       = $datos_validados->cliente->documento;
            $documento->direccion_cliente       = $datos_validados->cliente->direccion;
            $documento->cliente                 = $datos_validados->cliente->nombre;
            $documento->cliente_id              = $datos_validados->cliente->id;

            //======== TIPO VENTA ======
            $documento->tipo_venta_id           = $datos_validados->tipo_venta->id;   //boleta,factura,nota_venta
            $documento->tipo_venta_nombre       = $datos_validados->tipo_venta->descripcion;

            //========= CONDICIÓN PAGO ======
            $documento->condicion_id            = $datos_validados->condicion->id;

            $documento->observacion             = $request->get('observacion');

            //========= MONTOS Y MONEDA ========
            $documento->sub_total               =   $montos->monto_subtotal;
            $documento->monto_embalaje          =   $montos->monto_embalaje;
            $documento->monto_envio             =   $montos->monto_envio;
            $documento->total                   =   $montos->monto_total;
            $documento->total_igv               =   $montos->monto_igv;
            $documento->total_pagar             =   $montos->monto_total_pagar;
            //$documento->igv                   =   $datos_validados->empresa->igv;
            $documento->monto_descuento         =   $montos->monto_descuento;
            $documento->porcentaje_descuento    =   $montos->porcentaje_descuento;
            //$documento->moneda                =   1;

            //======== SUNAT ========
            $documento->mto_oper_gravadas_sunat =   $montos->mtoOperGravadasSunat;
            $documento->mto_igv_sunat           =   $montos->mtoIgvSunat;
            $documento->total_impuestos_sunat   =   $montos->totalImpuestosSunat;
            $documento->valor_venta_sunat       =   $montos->valorVentaSunat;
            $documento->sub_total_sunat         =   $montos->subTotalSunat;
            $documento->mto_imp_venta_sunat     =   $montos->mtoImpVentaSunat;

            //======= LEGENDA ======
            $documento->legenda     =   $legenda;

            //$documento->sede_id         =   $datos_validados->sede_id;
            //======= ALMACÉN ======
            $documento->almacen_id      =   $datos_validados->almacen->id;
            $documento->almacen_nombre  =   $datos_validados->almacen->descripcion;
            $documento->update();

            //======== OBTENER DETALLE ANTERIOR ========
            $detalle_anterior   =   DB::select('SELECT
                                    cdd.*
                                    FROM cotizacion_documento_detalles AS cdd
                                    WHERE cdd.documento_id = ?', [$id]);

            //======== DEVOLVER STOCKS =======
            foreach ($detalle_anterior as $da) {

                //===== ACTUALIZANDO STOCK ===========
                DB::update(
                    'UPDATE producto_color_tallas
               SET stock = stock + ?, stock_logico = stock_logico + ?
               WHERE
               almacen_id = ?
               AND producto_id = ?
               AND color_id = ?
               AND talla_id = ?',
                    [
                        $da->cantidad,
                        $da->cantidad,
                        $da->almacen_id,
                        $da->producto_id,
                        $da->color_id,
                        $da->talla_id,
                    ]
                );
            }

            //========= ELIMINAR DETALLE ANTERIOR =========
            DB::delete(
                "DELETE FROM cotizacion_documento_detalles WHERE documento_id = ?",
                [$documento->id]
            );

            //========= GRABAR NUEVO DETALLE ======
            foreach ($datos_validados->lstVenta as $item) {
                foreach ($item->tallas as $talla) {

                    //====== COMPROBAR SI EXISTE EL PRODUCTO COLOR TALLA EN EL ALMACÉN =====
                    $existe =   DB::select(
                        'SELECT
                                pct.stock,
                                pct.stock_logico,
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
                        [
                            $datos_validados->almacen->id,
                            $item->producto_id,
                            $item->color_id,
                            $talla->talla_id
                        ]
                    );

                    if (count($existe) === 0) {
                        throw new Exception($item->producto_nombre . '-' . $item->color_nombre . '-' . $talla->talla_nombre . ', NO EXISTE EN EL ALMACÉN!!!');
                    }

                    //========= VALIDACIÓN STOCK ========
                    if (
                        floatval($talla->cantidad) > floatval($existe[0]->stock)
                        || floatval($talla->cantidad) > floatval($existe[0]->stock_logico)
                    ) {
                        throw new Exception($item->producto_nombre . '-' . $item->color_nombre . '-' . $talla->talla_nombre . ', STOCK INSUFICIENTE' . ' (' . $existe[0]->stock . ')');
                    }

                    //========== GRABAR NUEVO DETALLE VENTA =====
                    $importe                =   floatval($talla->cantidad) * floatval($item->precio_venta);
                    $precio_unitario        =   $item->porcentaje_descuento == 0 ? $item->precio_venta : $item->precio_venta_nuevo;

                    if (floatval($precio_unitario === 0)) {
                        throw new Exception($item->producto_nombre . '-' . $item->color_nombre . '-' . $talla->talla_nombre . ', PRECIO NO VÁLIDO');
                    }
                    if (floatval($talla->cantidad === 0)) {
                        throw new Exception($item->producto_nombre . '-' . $item->color_nombre . '-' . $talla->talla_nombre . ', CANTIDAD NO VÁLIDA');
                    }

                    $detalle                            =   new Detalle();
                    $detalle->documento_id              =   $documento->id;
                    $detalle->almacen_id                =   $datos_validados->almacen->id;
                    $detalle->producto_id               =   $item->producto_id;
                    $detalle->color_id                  =   $item->color_id;
                    $detalle->talla_id                  =   $talla->talla_id;
                    $detalle->almacen_nombre            =   $datos_validados->almacen->descripcion;
                    $detalle->codigo_producto           =   $existe[0]->producto_codigo;
                    $detalle->nombre_producto           =   $existe[0]->producto_nombre;
                    $detalle->nombre_color              =   $existe[0]->color_nombre;
                    $detalle->nombre_talla              =   $existe[0]->talla_nombre;
                    $detalle->nombre_modelo             =   $existe[0]->modelo_nombre;
                    $detalle->cantidad                  =   floatval($talla->cantidad);
                    $detalle->precio_unitario           =   floatval($item->precio_venta);
                    $detalle->importe                   =   $importe;
                    $detalle->precio_unitario_nuevo     =   floatval($precio_unitario);
                    $detalle->porcentaje_descuento      =   floatval($item->porcentaje_descuento);
                    $detalle->monto_descuento           =   floatval($importe) * floatval($item->porcentaje_descuento) / 100;
                    $detalle->importe_nuevo             =   floatval($precio_unitario) * floatval($talla->cantidad);
                    $detalle->cantidad_sin_cambio       =   (int) $talla->cantidad;
                    $detalle->save();


                    //===== ACTUALIZANDO STOCK ===========
                    DB::update(
                        'UPDATE producto_color_tallas
                    SET stock = stock - ?, stock_logico = stock_logico - ?
                    WHERE
                    almacen_id = ?
                    AND producto_id = ?
                    AND color_id = ?
                    AND talla_id = ?',
                        [
                            $talla->cantidad,
                            $talla->cantidad,
                            $datos_validados->almacen->id,
                            $item->producto_id,
                            $item->color_id,
                            $talla->talla_id
                        ]
                    );
                }
            }

            //===== SI ESTÁ PRESENTE EL PARÁMETRO DATA ENVIO ========
            if ($request->has('data_envio')) {

                $data_envio     =  json_decode($request->get('data_envio'));

                //========== SI HAY DATA DE ENVÍO LLENA =========
                if (!empty((array)$data_envio)) {

                    //====== REVIZAR SI TIENE ENVÍO CREADO ANTES DE LA EDICIÓN ========
                    $envio_venta                                =   EnvioVenta::where('documento_id', $id)->first();

                    if ($envio_venta) {

                        $envio_venta->departamento              =   $data_envio->departamento->nombre;
                        $envio_venta->provincia                 =   $data_envio->provincia->text;
                        $envio_venta->distrito                  =   $data_envio->distrito->text;
                        $envio_venta->empresa_envio_id          =   $data_envio->empresa_envio->id;
                        $envio_venta->empresa_envio_nombre      =   $data_envio->empresa_envio->empresa;
                        $envio_venta->sede_envio_id             =   $data_envio->sede_envio->id;
                        $envio_venta->sede_envio_nombre         =   $data_envio->sede_envio->direccion;
                        $envio_venta->tipo_envio                =   $data_envio->tipo_envio->descripcion;
                        $envio_venta->destinatario_tipo_doc     =   $data_envio->destinatario->tipo_documento;
                        $envio_venta->destinatario_nro_doc      =   $data_envio->destinatario->nro_documento;
                        $envio_venta->destinatario_nombre       =   $data_envio->destinatario->nombres;
                        $envio_venta->tipo_pago_envio           =   $data_envio->tipo_pago_envio->descripcion;
                        $envio_venta->entrega_domicilio         =   $data_envio->entrega_domicilio ? "SI" : "NO";
                        $envio_venta->direccion_entrega         =   $data_envio->direccion_entrega;
                        $envio_venta->fecha_envio_propuesta     =   $data_envio->fecha_envio_propuesta;
                        $envio_venta->origen_venta              =   $data_envio->origen_venta->descripcion;
                        $envio_venta->obs_rotulo                =   $data_envio->obs_rotulo;
                        $envio_venta->obs_despacho              =   $data_envio->obs_despacho;
                        $envio_venta->cliente_celular           =   $documento->clienteEntidad->telefono_movil;
                        $envio_venta->estado                    =   "PENDIENTE";
                        $envio_venta->almacen_id                =   $documento->almacen_id;
                        $envio_venta->almacen_nombre            =   $documento->almacen_nombre;
                        $envio_venta->update();
                    } else {

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
                        $envio_venta->entrega_domicilio     =   $data_envio->entrega_domicilio ? "SI" : "NO";
                        $envio_venta->direccion_entrega     =   $data_envio->direccion_entrega;
                        $envio_venta->documento_nro         =   $documento->serie . '-' . $documento->correlativo;
                        $envio_venta->fecha_envio_propuesta =   $data_envio->fecha_envio_propuesta;
                        $envio_venta->origen_venta          =   $data_envio->origen_venta->descripcion;
                        $envio_venta->obs_rotulo            =   $data_envio->obs_rotulo;
                        $envio_venta->obs_despacho          =   $data_envio->obs_despacho;
                        $envio_venta->cliente_celular       =   $documento->clienteEntidad->telefono_movil;
                        $envio_venta->user_vendedor_id      =   $documento->user_id;
                        $envio_venta->user_vendedor_nombre  =   $documento->user->usuario;
                        $envio_venta->almacen_id            =   $documento->almacen_id;
                        $envio_venta->almacen_nombre        =   $documento->almacen_nombre;
                        $envio_venta->sede_id               =   $documento->sede_id;
                        $envio_venta->sede_despachadora_id  =   $datos_validados->almacen->sede_id;
                        $envio_venta->save();
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "DOCUMENTO VENTA ACTUALIZADO CON ÉXITO",
                'documento_id' => $documento->id
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    public static function validacionUpdate($request, $id)
    {

        $documento          =   Documento::find($id);
        if (!$documento) {
            throw new Exception("NO EXISTE EL DOCUMENTO DE VENTA EN LA BD");
        }
        if ($documento->estado_pago !== 'PENDIENTE') {
            throw new Exception("LOS DOCUMENTOS CON ESTADO DE PAGO : " . $documento->estado_pago . ", NO PUEDEN EDITARSE");
        }
        if ($documento->sunat == '1') {
            throw new Exception("LOS DOCUMENTOS ENVIADOS A SUNAT NO PUEDEN EDITARSE!!!");
        }
        if ($documento->sunat == '2') {
            throw new Exception("LOS DOCUMENTOS ANULADOS NO PUEDEN EDITARSE!!!");
        }
        if ($documento->cambio_talla == '1') {
            throw new Exception("LOS DOCUMENTOS CON CAMBIO DE TALLA NO PUEDEN EDITARSE!!!");
        }
        if ($documento->convert_en_id) {
            throw new Exception("LOS DOCUMENTOS CONVERTIDOS NO PUEDEN EDITARSE");
        }
        if ($documento->convert_de_id) {
            throw new Exception("LOS DOCUMENTOS RESULTANTES DE UNA CONVERSIÓN NO PUEDEN EDITARSE");
        }

        //========= ALMACÉN =======
        $almacen    =   Almacen::find($request->get('almacen'));

        //========== CLIENTE =======
        $cliente            =   Cliente::find($request->get('cliente'));
        $tipo_comprobante   =   DB::select('select
                                td.*
                                from tabladetalles as td
                                where td.id = ?', [$request->get('tipo_venta')])[0];

        if ($cliente->tipo_documento !== 'RUC' && $tipo_comprobante->simbolo === '01') {
            throw new Exception("SE REQUIERE RUC PARA GENERAR FACTURA ELECTRÓNICA!!!");
        }
        if ($cliente->tipo_documento !== 'DNI' && $tipo_comprobante->simbolo === '03') {
            throw new Exception("SE REQUIERE DNI PARA GENERAR BOLETA ELECTRÓNICA!!!");
        }

        //======== CONDICIÓN =======
        $condicion      =   Condicion::find($request->get('condicion_id'));

        //========= VALIDAR DETALLE VENTA ======
        $lstVenta   =   json_decode($request->get('lstVenta'));

        if (count($lstVenta) === 0) {
            throw new Exception("EL DETALLE DE LA VENTA ESTÁ VACÍO!!!");
        }

        //===== VALIDAR MONTOS =====
        $amounts    =   json_decode($request->get('amounts'));

        return (object)[
            'documento'                 =>  $documento,
            'lstVenta'                  =>  $lstVenta,
            'monto_embalaje'            =>  $amounts->embalaje,
            'monto_envio'               =>  $amounts->envio,
            'condicion'                 =>  $condicion,
            'cliente'                   =>  $cliente,
            'tipo_venta'                =>  $tipo_comprobante,
            'almacen'                   =>  $almacen,
            'anticipo_consumido_id'     =>  $request->get('anticipo_consumido_id') ?? null,
            'anticipo_monto_consumido'  =>  $request->get('anticipo_monto_consumido') ?? 0
        ];
    }


    public function guiaCreate($venta_id)
    {

        $venta              =   Documento::find($venta_id);
        $almacen_origen     =   Almacen::find($venta->almacen_id);
        $venta_detalle      =   UtilidadesController::formatearArrayDetalle(Detalle::where('documento_id', $venta_id)->get());

        $sede_id            =   Auth::user()->sede_id;

        $sede_origen        =   Sede::find($almacen_origen->sede_id);
        $sede_documento     =   Sede::find($venta->sede_id);

        $cliente            =   DB::select('select
                                c.direccion,
                                c.tipo_documento,
                                c.documento,
                                c.nombre,
                                d.nombre as departamento_nombre,
                                pr.nombre as provincia_nombre,
                                di.nombre as distrito_nombre,
                                c.distrito_id
                                from clientes as c
                                inner join departamentos as d on d.id = c.departamento_id
                                inner join provincias as pr on pr.id = c.provincia_id
                                inner join distritos as di on di.id = c.distrito_id
                                where c.id = ?', [$venta->cliente_id])[0];

        $almacenes          =   Almacen::where('estado', 'ACTIVO')
            ->get();


        $registrador        =   User::find(Auth::user()->id);
        $tallas             =   Talla::where('estado', 'ACTIVO')->get();
        $empresas           =   Empresa::where('estado', 'ACTIVO')->get();
        $conductores        =   Conductor::where('estado', 'ACTIVO')->get();
        $vehiculos          =   Vehiculo::where('estado', 'ACTIVO')->get();


        $tipos_documento    =   DB::select('select
                                td.*
                                from tabladetalles as td
                                where td.tabla_id = 3');

        $sedes              =   Sede::where('estado', 'ACTIVO')
            ->where('id', '<>', $sede_id)
            ->get();

        $motivos_traslado   =   DB::select('select
                                td.*
                                from tabladetalles as td
                                where
                                td.tabla_id = 34
                                AND td.simbolo IN ("01","04")');

        return view('ventas.guia_venta.create', [

            'sede_origen'       =>  $sede_origen,
            'motivos_traslado'  =>  $motivos_traslado,
            'sede_id'           =>  $sede_id,
            'almacenes'         =>  $almacenes,
            'registrador'       =>  $registrador,
            'tallas'            =>  $tallas,
            'empresas'          =>  $empresas,
            'conductores'       =>  $conductores,
            'sedes'             =>  $sedes,
            'vehiculos'         =>  $vehiculos,
            'tipos_documento'   =>  $tipos_documento,
            'venta'             =>  $venta,
            'almacen_origen'    =>  $almacen_origen,
            'venta_detalle'     =>  $venta_detalle,
            'cliente'           =>  $cliente
        ]);
    }

    /*
array:18 [
  "registrador" => "ADMINISTRADOR"
  "fecha_emision" => "2025-03-06"
  "modalidad_traslado" => "01"
  "fecha_traslado" => "2025-03-06"
  "peso" => "0.1"
  "unidad" => "KGM"
  "vehiculo" => "1"
  "conductor" => "2"
  "sede_origen" => "SEDE CENTRAL"
  "cliente" => "DNI:63035047-YOSELYN HILARION ROQUE"
  "cliente_ubigeo" => "LIMA-LIMA-LIMA"
  "cliente_codigo_ubigeo" => "150101"
  "cliente_direccion" => "CAMPOy"
  "sede_id" => "1"
  "registrador_id" => "1"
  "venta_id" => "8151"
  "almacen" => "1"
  "motivo_traslado" => "01"
]
*/
    public function guiaStore(GuiaStoreRequest $request)
    {

        $lstGuia    =   UtilidadesController::formatearArrayDetalle(Detalle::where('documento_id', $request->get('venta_id'))->get());
        $venta      =   Documento::find($request->get('venta_id'));

        $almacen_origen =   Almacen::find($venta->almacen_id);

        $request->merge([
            'lstGuia'           =>  json_encode($lstGuia),
            'sede_genera_guia'  =>  $request->get('sede_id'),
            'sede_usa_guia'     =>  $almacen_origen->sede_id,
            'cliente_destino'   =>  $venta->cliente_id,
            'venta'             =>  $venta->id
        ]);


        $guia_controller        =   new GuiaController();
        $res                    =   $guia_controller->store($request);
        $jsonResponse           =   $res->getData();

        if (!$jsonResponse->success) {
            return $res;
        }

        DB::beginTransaction();
        try {

            $venta->guia_id  =   $jsonResponse->id;
            $venta->update();

            DB::commit();

            return $res;
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
