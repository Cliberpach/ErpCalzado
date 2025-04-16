<?php

use App\Almacenes\LoteProducto;
use App\Almacenes\Producto;
use App\Almacenes\TipoCliente;
use App\Compras\CuentaProveedor;
use App\Mantenimiento\Tabla\General;
use App\Mantenimiento\Ubigeo\Departamento;
use App\Mantenimiento\Ubigeo\Distrito;
use App\Mantenimiento\Ubigeo\Provincia;
// use App\Parametro;
use Carbon\Carbon;
//Orden de compra
use App\Compras\Documento\Detalle as Detalle_Documento;
use App\Compras\Detalle;
use App\Compras\Orden;
use App\Compras\Documento\Documento;
use App\Compras\Proveedor;
use App\Configuracion\Configuracion;
use App\Mantenimiento\Colaborador\Colaborador;
use App\Mantenimiento\Empresa\Empresa;
//Bitacora de actividades
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\Auth;

//Facturacion Electronica
use Illuminate\Support\Facades\Http;
use App\Mantenimiento\Parametro\Parametro;
use GuzzleHttp\Client;
use App\Mantenimiento\Empresa\Facturacion;
use App\Mantenimiento\Empresa\Numeracion;
use App\Mantenimiento\Vendedor\Vendedor;
use App\Pos\Caja;
use App\Pos\MovimientoCaja;
use App\Ventas\Cliente;
use App\Ventas\TipoPago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mantenimiento\Tabla\Detalle as TablaDetalle;
use App\Movimientos\MovimientoAlmacen;
use App\Notifications\FacturacionNotification;
use App\Notifications\GuiaNotification;
use App\Notifications\NotaNotification;
use App\Notifications\RegularizeNotification;
use App\Notifications\RetencionNotification;
use App\Ventas\CuentaCliente;
use App\Ventas\Documento\Detalle as DocumentoDetalle;
use App\Ventas\Documento\Documento as DocumentoDocumento;
use Luecano\NumeroALetras\NumeroALetras;
use App\Ventas\Nota;
use App\Ventas\NotaDetalle;

// TABLAS-DETALLES

if (!function_exists('tipos_moneda')) {
    function tipos_moneda()
    {
        return General::find(1)->detalles;
    }
}

if (!function_exists('bancos')) {
    function bancos()
    {
        return General::find(2)->detalles;
    }
}

if (!function_exists('tipos_documento')) {
    function tipos_documento()
    {
        return General::find(3)->detalles;
    }
}

if (!function_exists('tipos_sexo')) {
    function tipos_sexo()
    {
        return General::find(4)->detalles;
    }
}

if (!function_exists('estados_civiles')) {
    function estados_civiles()
    {
        return General::find(5)->detalles;
    }
}

if (!function_exists('zonas')) {
    function zonas()
    {
        return General::find(6)->detalles;
    }
}

if (!function_exists('areas')) {
    function areas()
    {
        return General::find(7)->detalles;
    }
}

if (!function_exists('cargos')) {
    function cargos()
    {
        return General::find(8)->detalles;
    }
}

if (!function_exists('profesiones')) {
    function profesiones()
    {
        return General::find(9)->detalles;
    }
}

if (!function_exists('presentaciones')) {
    function presentaciones()
    {
        return General::find(10)->detalles;
    }
}

if (!function_exists('personas')) {
    function personas()
    {
        return General::find(11)->detalles;
    }
}

if (!function_exists('grupos_sanguineos')) {
    function grupos_sanguineos()
    {
        return General::find(12)->detalles;
    }
}

if (!function_exists('modo_compra')) {
    function modo_compra()
    {
        return General::find(14)->detalles;
    }
}

if (!function_exists('unidad_medida')) {
    function unidad_medida()
    {
        return General::find(13)->detalles;
    }
}

if (!function_exists('tipo_compra')) {
    function tipo_compra()
    {
        return General::find(16)->detalles;
    }
}

if (!function_exists('tipo_clientes')) {
    function tipo_clientes()
    {
        return General::find(17)->detalles;
    }
}

if (!function_exists('condicion_reparto')) {
    function condicion_reparto()
    {
        return General::find(18)->detalles;
    }
}

if (!function_exists('tipos_venta')) {
    function tipos_venta()
    {
        return General::find(21)->detalles;
    }
}

if (!function_exists('cod_motivos')) {
    function cod_motivos()
    {
        return TablaDetalle::where('tabla_id',33)->wherein('simbolo',['01','07'])->get();
    }
}

if (!function_exists('forma_pago')) {
    function forma_pago()
    {
        return General::find(30)->detalles;
    }
}

if (!function_exists('motivo_traslado')) {
    function motivo_traslado()
    {
        return General::find(34)->detalles;
    }
}

if (!function_exists('modos_pago')) {
    function modos_pago()
    {
        return TipoPago::where('estado', 'ACTIVO')->get();
    }
}

if (!function_exists('vendedores')) {
    function vendedores()
    {
        return Colaborador::cursor()->filter(function ($vendedor) {
            return $vendedor->persona->estado == "ACTIVO" ? true : false;
        });
    }
}

// DOCUMENTO VALIDO PARA NOTA DE CRÉDITO
if (!function_exists('docValido')) {
    function docValido($id)
    {
        $documento = DocumentoDocumento::find($id);
        $detalles = DocumentoDetalle::where('documento_id', $id)->get();
        $cont = 0 ;

        if($documento->sunat == '2')
        {
            return false;
        }

        foreach($detalles as $detalle)
        {
            if($detalle->cantidad == $detalle->detalles->sum('cantidad'))
            {
                $cont = $cont + 1;
            }
        }

        if(count($detalles) == $cont)
        {
            $documento->sunat = '2';
            $documento->update();
            return false;
        }
        else
        {
            return true;
        }
    }
}

// DOCUMENTO DE COMPRA VALIDO PARA NOTA DE CRÉDITO
if (!function_exists('docCompraValido')) {
    function docCompraValido($id)
    {
        $documento = Documento::find($id);
        $detalles = Detalle_Documento::where('documento_id', $id)->get();
        $cont = 0;

        foreach ($detalles as $detalle) {
            $cant_nota_electronica = 0;
            foreach($detalle->loteProducto->detalles_venta as $item) {
                $cant_nota_electronica = $cant_nota_electronica + $item->detalles->sum("cantidad");
            }
            $cantidad = $detalle->loteProducto->cantidad_inicial + $cant_nota_electronica - $detalle->loteProducto->detalles_venta->sum("cantidad") - $detalle->loteProducto->detalles_salida->sum("cantidad") - $detalle->detalles->sum('cantidad');
            if ($cantidad == 0) {
                $cont = $cont + 1;
            }
        }

        if (count($detalles) == $cont) {
            return false;
        } else {
            return true;
        }
    }
}

// UBIGEO
if (!function_exists('departamentos')) {
    function departamentos($id = null)
    {
        if (is_null($id)) {
            return Departamento::all();
        } else {
            $departamento_id = str_pad($id, 2, "0", STR_PAD_LEFT);
            return Departamento::where('id', $id)->get();
        }
    }
}

if (!function_exists('provincias')) {
    function provincias($id = null)
    {
        if (is_null($id)) {
            return Provincia::all();
        } else {
            $provincia_id = str_pad($id, 4, "0", STR_PAD_LEFT);
            return Provincia::where('id', $provincia_id)->get();
        }
    }
}

if (!function_exists('getProvinciasByDepartamento')) {
    function getProvinciasByDepartamento($departamento_id)
    {
        if (is_null($departamento_id)) {
            return collect([]);
        } else {
            $departamento_id = str_pad($departamento_id, 2, "0", STR_PAD_LEFT);
            return Provincia::where('departamento_id', $departamento_id)->get();
        }
    }
}

if (!function_exists('distritos')) {
    function distritos($id = null)
    {
        if (is_null($id)) {
            return Distrito::all();
        } else {
            $distrito_id = str_pad($id, 6, "0", STR_PAD_LEFT);
            return Distrito::where('id', $distrito_id)->get();
        }
    }
}

if (!function_exists('getDistritosByProvincia')) {
    function getDistritosByProvincia($provincia_id)
    {
        if (is_null($provincia_id)) {
            return collect([]);
        } else {
            $provincia_id = str_pad($provincia_id, 4, "0", STR_PAD_LEFT);
            return Distrito::where('provincia_id', $provincia_id)->get();
        }
    }
}

//Consultas a la Api
if (!function_exists('consultaRuc')) {
    function consultaRuc()
    {
        return Parametro::findOrFail(1);
    }
}

if (!function_exists('consultaDni')) {
    function consultaDni()
    {
        return Parametro::findOrFail(2);
    }
}

if (!function_exists('consultaCrd')) {
    function consultaCrd($comprobante)
    {
        $curl = curl_init();
        $data = [
            "ruc_emisor" => $comprobante->ruc,
            "codigo_tipo_documento" => $comprobante->tipo,
            "serie_documento" => $comprobante->serie,
            "numero_documento" => $comprobante->correlativo,
            "fecha_de_emision" => $comprobante->fecha_emision,
            "total" => $comprobante->total
        ];

        $post_data = http_build_query($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://apiperu.dev/api/cpe?api_token=c36358c49922c564f035d4dc2ff3492fbcfd31ee561866960f75b79f7d645d7d",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_SSL_VERIFYPEER => false
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $resultado = json_encode(array('success' => false, 'error' => $err));
            return $resultado;
        } else {
            return $response;
        }
    }
}

if (!function_exists('getFechaFormato')) {
    function getFechaFormato($fecha, $formato)
    {
        if (is_null($fecha) || empty($fecha))
            return "-";

        $fecha_formato = Carbon::parse($fecha)->format($formato);
        return ($fecha_formato) ? $fecha_formato : $fecha;
    }
}

// Documento tributarios
if (!function_exists('tipos_documentos_tributarios')) {
    function tipos_documentos_tributarios()
    {
        return General::findOrFail(15)->detalles;
    }
}

// Tipos de pago caja chica
if (!function_exists('tipos_pago_caja')) {
    function tipos_pago_caja()
    {
        return General::findOrFail(19)->detalles;
    }
}

// Tipo: Maquina o equipo
if (!function_exists('tipos_maq_eq')) {
    function tipos_maq_eq()
    {
        return General::find(20)->detalles;
    }
}

// Tipo: tienda
if (!function_exists('tipos_tienda')) {
    function tipos_tienda()
    {
        return General::find(22)->detalles;
    }
}

// Tipo: tienda
if (!function_exists('tipos_negocio')) {
    function tipos_negocio()
    {
        return General::find(23)->detalles;
    }
}

// Modo: Responsable
if (!function_exists('modo_responsables')) {
    function modo_responsables()
    {
        return General::find(24)->detalles;
    }
}
//Linea Comercial
if (!function_exists('lineas_comerciales')) {
    function lineas_comerciales()
    {
        return General::find(25)->detalles;
    }
}


// Monto a Pagar Orden de compra
if (!function_exists('calcularMonto')) {
    function calcularMonto($id)
    {

        $detalles = Detalle::where('orden_id', $id)->get();
        $orden = Orden::findOrFail($id);
        $subtotal = 0;
        $igv = '';
        $tipo_moneda = '';
        foreach ($detalles as $detalle) {
            $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
        }

        if (!$orden->igv) {
            $igv = $subtotal * 0.18;
            $total = $subtotal + $igv;
            $decimal_subtotal = number_format($subtotal, 2, '.', '');
            $decimal_total = number_format($total, 2, '.', '');
            $decimal_igv = number_format($igv, 2, '.', '');
        } else {
            $calcularIgv = $orden->igv / 100;
            $base = $subtotal / (1 + $calcularIgv);
            $nuevo_igv = $subtotal - $base;
            $decimal_subtotal = number_format($base, 2, '.', '');
            $decimal_total = number_format($subtotal, 2, '.', '');
            $decimal_igv = number_format($nuevo_igv, 2, '.', '');
        }
        return $decimal_total;
    }
}

//Obtner Simbbolo de la moneda
if (!function_exists('simbolo_monedas')) {
    function simbolo_monedas($moneda_descripcion)
    {
        $simbolo = '';
        foreach (tipos_moneda() as $moneda) {
            if ($moneda->descripcion == $moneda_descripcion) {
                $simbolo = $moneda->simbolo;
            }
        }
        return $simbolo;
    }
}

// Monto a Pagar Documento de compra
if (!function_exists('calcularMontoDocumento')) {
    function calcularMontoDocumento($id)
    {

        $detalles = Detalle_Documento::where('documento_id', $id)->get();
        $documento = Documento::findOrFail($id);
        $subtotal = 0;
        $igv = '';
        $tipo_moneda = '';
        foreach ($detalles as $detalle) {
            $subtotal = ($detalle->cantidad * $detalle->precio) + $subtotal;
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
        return $decimal_total;
    }
}


// Monto a Pagar Documento de compra
if (!function_exists('calcularMontosAcuentaDocumentos')) {
    function calcularMontosAcuentaDocumentos($id)
    {

        $suma_detalle_pago = DB::table('documento_pago_detalle')
            ->join('compra_documento_pagos', 'compra_documento_pagos.id', '=', 'documento_pago_detalle.pago_id')
            ->join('compra_documento_pago_detalle', 'compra_documento_pago_detalle.id', '=', 'documento_pago_detalle.detalle_id')
            ->join('compra_documentos', 'compra_documentos.id', '=', 'compra_documento_pagos.documento_id')
            ->select('compra_documento_pagos.*', 'compra_documentos.*')
            ->where('compra_documentos.id', '=', $id)
            ->where('compra_documento_pagos.estado', 'ACTIVO')
            ->sum('compra_documento_pago_detalle.monto');

        return $suma_detalle_pago;
    }
}


// Calcular Montos Acuentas
if (!function_exists('calcularMontosAcuentaVentas')) {
    function calcularMontosAcuentaVentas($id)
    {

        $montos = DB::table('cotizacion_documento_pago_detalle_cajas')
            ->join('cotizacion_documento_pagos', 'cotizacion_documento_pagos.id', '=', 'cotizacion_documento_pago_detalle_cajas.pago_id')
            ->join('cotizacion_documento', 'cotizacion_documento.id', '=', 'cotizacion_documento_pagos.documento_id')
            ->join('cotizacion_documento_pago_cajas', 'cotizacion_documento_pago_cajas.id', '=', 'cotizacion_documento_pago_detalle_cajas.caja_id')
            ->join('pos_caja_chica', 'pos_caja_chica.id', '=', 'cotizacion_documento_pago_cajas.caja_id')

            ->select('cotizacion_documento.id as id_documento', 'cotizacion_documento_pago_detalle_cajas.id', 'cotizacion_documento_pago_cajas.monto as caja_monto', 'cotizacion_documento_pagos.tipo_pago', 'cotizacion_documento_pago_detalle_cajas.created_at')
            ->where('cotizacion_documento.id', '=', $id)
            //ANULAR
            ->where('cotizacion_documento_pago_cajas.estado', '!=', 'ANULADO')
            ->sum('cotizacion_documento_pago_cajas.monto');

        return $montos;
    }
}


// Monto a Pagar Documento de venta
if (!function_exists('calcularMontosAcuentaDocumentosVentas')) {
    function calcularMontosAcuentaDocumentosVentas($id)
    {

        $suma_detalle_pago = DB::table('cotizacion_documento_pago_detalles')
            ->join('cotizacion_documento_pagos', 'cotizacion_documento_pagos.id', '=', 'cotizacion_documento_pago_detalles.pago_id')

            ->join('cotizacion_documento', 'cotizacion_documento.id', '=', 'cotizacion_documento_pagos.documento_id')

            ->select('compra_documento_pagos.*', 'compra_documentos.*')
            ->where('cotizacion_documento.id', '=', $id)
            ->where('cotizacion_documento_pago_detalles.estado', 'ACTIVO')
            ->sum('cotizacion_documento_pago_detalles.monto');

        return $suma_detalle_pago;
    }
}


// Calcular monto restante caja chica
if (!function_exists('calcularMontoRestanteCaja')) {
    function calcularMontoRestanteCaja($id)
    {

        $restante = DB::table('compra_documento_pago_detalle')
            ->join('documento_pago_detalle', 'documento_pago_detalle.detalle_id', '=', 'compra_documento_pago_detalle.id')
            ->join('compra_documento_pagos', 'compra_documento_pagos.id', '=', 'documento_pago_detalle.pago_id')
            ->select('compra_documento_pago_detalle.*', 'compra_documentos.*')
            ->where('compra_documento_pagos.estado', '!=', 'ANULADO')
            ->where('compra_documento_pago_detalle.caja_id', $id)
            ->sum('compra_documento_pago_detalle.monto');


        return $restante;
    }
}

// Calcular monto restante caja chica
if (!function_exists('calcularSumaMontosPagosVentas')) {
    function calcularSumaMontosPagosVentas($id)
    {

        $restante = DB::table('cotizacion_documento_pago_cajas')
            ->select('cotizacion_documento_pago_cajas.*')
            ->where('cotizacion_documento_pago_cajas.estado', '!=', 'ANULADO')
            ->where('cotizacion_documento_pago_cajas.caja_id', $id)
            ->sum('cotizacion_documento_pago_cajas.monto');


        return $restante;
    }
}


// Calcular monto restante caja chica
if (!function_exists('calcularMontosAcuentaVentasTransferencia')) {
    function calcularMontosAcuentaVentasTransferencia($id)
    {

        $restante = DB::table('cotizacion_documento_pago_transferencias')
            ->select('cotizacion_documento_pago_transferencias.*')
            ->where('cotizacion_documento_pago_transferencias.estado', '!=', 'ANULADO')
            ->where('cotizacion_documento_pago_transferencias.documento_id', $id)
            ->sum('cotizacion_documento_pago_transferencias.monto');


        return $restante;
    }
}

/////////////////////////////////////////////////////////////////////////////
// REGISTRO DE ACTIVIDADES

if (!function_exists('crearRegistro')) {
    function crearRegistro($modelo, $descripcion, $gestion)
    {
        activity()
            ->causedBy(Auth::user())
            ->performedOn($modelo)
            ->withProperties(
                [
                    'operacion' => 'AGREGAR',
                    'gestion' => $gestion
                ]
            )
            ->log($descripcion);
    }
}

if (!function_exists('modificarRegistro')) {
    function modificarRegistro($modelo, $descripcion, $gestion)
    {
        activity()
            ->causedBy(Auth::user())
            ->performedOn($modelo)
            ->withProperties(
                [
                    'operacion' => 'MODIFICAR',
                    'gestion' => $gestion
                ]
            )
            ->log($descripcion);
    }
}

if (!function_exists('eliminarRegistro')) {
    function eliminarRegistro($modelo, $descripcion, $gestion)
    {
        activity()
            ->causedBy(Auth::user())
            ->performedOn($modelo)
            ->withProperties(
                [
                    'operacion' => 'ELIMINAR',
                    'gestion' => $gestion
                ]
            )
            ->log($descripcion);
    }
}



/////////////////////////////////////////////////////////////////////////////
// FACTURACION ELECTRONICA
//LOGUEO
// GENERAR TOKEN
if (!function_exists('obtenerTokenapi')) {
    
    function obtenerTokenapi()
    {

        // $client = new \GuzzleHttp\Client(['verify'=>false]);

        // $client_id = "9e8eaf55-cf1d-4bf0-9837-0c3d897c08d5"; // Reemplaza esto con tu client_id
        // $client_secret = "3xSHGqcy5mglRIJzxx6eZw=="; // Reemplaza esto con tu client_secret
        // $numero_ruc = "20611904020"; // Reemplaza esto con tu número de RUC
        // $usuario_sol = "SISCOMFA"; // Reemplaza esto con tu usuario SOL
        // $contraseña_sol = "Merry321"; // Reemplaza esto con tu contraseña SOL

        
        // $body = [
        //       'grant_type' => 'password',
        //       'scope' => 'https://api-cpe.sunat.gob.pe',
        //       'client_id' => $client_id,
        //       'client_secret' => $client_secret,
        //       'username' => $numero_ruc . $usuario_sol,
        //       'password' => $contraseña_sol
        // ];

        // try {
           
        // // Realizar la solicitud POST
        //      $response = $client->post("https://api-seguridad.sunat.gob.pe/v1/clientessol/{$client_id}/oauth2/token/", [
        //          'form_params' => $body
        //      ]);
        
        //      // Manejar la respuesta aquí
        //      $statusCode = $response->getStatusCode();
        //      $body = json_decode($response->getBody(), true);
        //      $token_sunat    = $body['access_token'] ;
        //      dd($token_sunat);
        //      return $token_sunat;
             
        // } catch (Exception $e) {
        //      echo $e->getMessage();
        // }

        $parametro = Parametro::findOrFail(3);
        
        
        $response = Http::post('https://facturacion.apisperu.com/api/v1/auth/login', [
              'username' => "$parametro->usuario_proveedor",
              'password' => $parametro->contra_proveedor,
         ]);

         $estado = $response->getStatusCode();
        
         if ($estado == '200') {

              $resultado = $response->getBody()->getContents();
              $resultado = json_decode($resultado);
              return $resultado->token;
         }
    }
}

////////////////////////////////////////////////

//EMPRESA
// AGREGAR EMPRESA
if (!function_exists('agregarEmpresaapi')) {
    function agregarEmpresaapi($empresa)
    {
        $url = "https://facturacion.apisperu.com/api/v1/companies";
        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = obtenerTokenapi();
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ],
            'body'    => $empresa
        ]);

        $estado = $response->getStatusCode();

        if ($estado == '200') {

            $resultado = $response->getBody()->getContents();
            json_decode($resultado);
            return $resultado;
        }
    }
}
// BORRAR EMPRESA
if (!function_exists('borrarEmpresaapi')) {
    function borrarEmpresaapi($id)
    {
        $url = "https://facturacion.apisperu.com/api/v1/companies/{$id}";
        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = obtenerTokenapi();
        $response = $client->delete($url, [
            'headers' => [
                'Authorization' => "Bearer {$token}"
            ],
        ]);

        $estado = $response->getStatusCode();

        return $estado;
    }
}
// MODIFICAR EMPRESA
if (!function_exists('modificarEmpresaapi')) {
    function modificarEmpresaapi($empresa, $id)
    {
        $url = "https://facturacion.apisperu.com/api/v1/companies/{$id}";
        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = obtenerTokenapi();
        $response = $client->put($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ],
            'body'    => $empresa
        ]);

        $estado = $response->getStatusCode();

        if ($estado == '200') {

            $resultado = $response->getBody()->getContents();
            // json_decode($resultado);
           // dd($resultado);
            return $resultado;
        }
    }
}

// OBTENER TOKEN DE LA EMPRESA TOKEN-CODE
if (!function_exists('tokenEmpresa')) {
    function tokenEmpresa($id)
    {
        $facturacion = Facturacion::findOrFail($id);
        return $facturacion->token_code;
    }
}

////////////////////////////////////////////////
//ENVIAR FACTURA O BOLETA
//GENERAR PDF
if (!function_exists('generarComprobanteapi')) {
    function generarComprobanteapi($comprobante, $empresa)
    {
        $url = "https://facturacion.apisperu.com/api/v1/invoice/pdf";
        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = tokenEmpresa($empresa);
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ],
            'body'    => $comprobante
        ]);

        $estado = $response->getStatusCode();

        return $response->getBody()->getContents();

        //dd($response->getBody()->getContents());
        if ($estado == '200') {

            $resultado = $response->getBody()->getContents();
            json_decode($resultado);
            return $resultado;
        }
    }
}
//GENERAR XML
if (!function_exists('generarXmlapi')) {
    function generarXmlapi($comprobante, $empresa)
    {
        $url = "https://facturacion.apisperu.com/api/v1/invoice/xml";
        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = tokenEmpresa($empresa);
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ],
            'body'    => $comprobante
        ]);

        $estado = $response->getStatusCode();

        return $response->getBody();



        // dd( $response->getBody()->getContents());
        if ($estado == '200') {

            $resultado = $response->getBody()->getContents();
            json_decode($resultado);
            return $resultado;
        }
    }
}

//GENERAR XML
if (!function_exists('generarQrApi')) {
    function generarQrApi($comprobante, $empresa)
    {
        $url = "https://facturacion.apisperu.com/api/v1/sale/qr";
        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = tokenEmpresa($empresa);
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ],
            'body'    => $comprobante
        ]);

        $estado = $response->getStatusCode();

        return $response->getBody();

        // dd( $response->getBody()->getContents());
        if ($estado == '200') {

            $resultado = $response->getBody()->getContents();
            json_decode($resultado);
            return $resultado;
        }
    }
}

//ENVIAR A  SUNAT
if (!function_exists('enviarComprobanteapi')) {
    function enviarComprobanteapi($comprobante, $empresa)
    {
        $url = "https://facturacion.apisperu.com/api/v1/invoice/send";
        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = tokenEmpresa($empresa);
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ],
            'body'    => $comprobante
        ]);

        $estado = $response->getStatusCode();

        if ($estado == '200') {

            $resultado = $response->getBody()->getContents();
            json_decode($resultado);
            return $resultado;
        }
    }
}

////////////////////////////////////////////////
//ENVIAR COMPROBANTE DE RETENCION
//ENVIAR A  SUNAT
if (!function_exists('enviarComprobanteRetencion')) {
    function enviarComprobanteRetencion($comprobante, $empresa)
    {
        $url = "https://facturacion.apisperu.com/api/v1/retention/send";
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $token = tokenEmpresa($empresa);
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ],
            'body'    => $comprobante
        ]);

        $estado = $response->getStatusCode();

        if ($estado == '200') {

            $resultado = $response->getBody()->getContents();
            json_decode($resultado);
            return $resultado;
        }
    }
}
//GENERAR PDF
if (!function_exists('generarComprobanteRetencion')) {
    function generarComprobanteRetencion($comprobante, $empresa)
    {
        $url = "https://facturacion.apisperu.com/api/v1/retention/pdf";
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $token = tokenEmpresa($empresa);
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ],
            'body'    => $comprobante
        ]);

        $estado = $response->getStatusCode();

        return $response->getBody()->getContents();

        dd($response->getBody()->getContents());
        if ($estado == '200') {

            $resultado = $response->getBody()->getContents();
            json_decode($resultado);
            return $resultado;
        }
    }
}
//GENERAR XML
if (!function_exists('generarXmlRetencion')) {
    function generarXmlRetencion($comprobante, $empresa)
    {
        $url = "https://facturacion.apisperu.com/api/v1/retention/xml";
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $token = tokenEmpresa($empresa);
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ],
            'body'    => $comprobante
        ]);

        $estado = $response->getStatusCode();

        return $response->getBody();



        // dd( $response->getBody()->getContents());
        if ($estado == '200') {

            $resultado = $response->getBody()->getContents();
            json_decode($resultado);
            return $resultado;
        }
    }
}

////////////////////////////////////////////////
//GUIA DE REMISION
//GENERAR PDF DE GUIA DE REMISION
if (!function_exists('pdfGuiaapi')) {
    function pdfGuiaapi($guia)
    {

        
        $url = "https://facturacion.apisperu.com/api/v1/despatch/pdf";

        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = obtenerTokenapi();
        $response = $client->post($url, [
             'headers' => [
                 'Content-Type' => 'application/json',
                 'Accept' => 'application/json',
                 'Authorization' => "Bearer {$token}"
             ],
             'body'    => $guia
        ]);

        $estado = $response->getStatusCode();

        //dd( json_decode($response->getBody()->getContents()));

        return $response->getBody()->getContents();

        if ($estado == '200') {

             $resultado = $response->getBody()->getContents();
             json_decode($resultado);
             return $resultado;
        }
    }
}
//ENVIAR A  SUNAT
if (!function_exists('enviarGuiaapi')) {
    function enviarGuiaapi($guia)
    {
        
        $url = "https://facturacion.apisperu.com/api/v1/despatch/send";
        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = obtenerTokenapi();
        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$token}"
                ],
                'body'    => $guia
            ]);
        
            // Manejar la respuesta aquí
            dd($response->getBody()->getContents());
        } catch (RequestException $e) {
            dd($e->getMessage(), $e->getTraceAsString());
        }

        $estado = $response->getStatusCode();
        
        if ($estado == '200') {
            $resultado = $response->getBody()->getContents();
            json_decode($resultado);
            return $resultado;
        }
    }
}

////////////////////////////////////////////////
//NOTA DE CREDITO Y DEBITO
//GENERAR PDF DE NOTA
if (!function_exists('pdfNotaapi')) {
    function pdfNotaapi($nota)
    {
        $url = "https://facturacion.apisperu.com/api/v1/note/pdf";
        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = obtenerTokenapi();
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ],
            'body'    => $nota
        ]);

        $estado = $response->getStatusCode();

        if ($estado == '200') {
            $resultado = $response->getBody()->getContents();
            json_decode($resultado);
            return $resultado;
        }
    }
}

//ENVIAR NOTA A SUNAT
if (!function_exists('enviarNotaapi')) {
    function enviarNotaapi($nota)
    {
        $url = "https://facturacion.apisperu.com/api/v1/note/send";
        $client = new \GuzzleHttp\Client(['verify'=>false]);
        $token = obtenerTokenapi();
        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ],
            'body'    => $nota
        ]);

        $estado = $response->getStatusCode();

        if ($estado == '200') {
            $resultado = $response->getBody()->getContents();
            json_decode($resultado);
            return $resultado;
        }
    }
}

//MODIFICAR LOTES CANTIDADES

if (!function_exists('actualizarStockLotes')) {
    function actualizarStockLotes()
    {
        DB::update('update lote_productos set cantidad_logica = cantidad');
    }
}

if (!function_exists('actualizarStockProductos')) {
    function actualizarStockProductos()
    {
        $productos = Producto::all();
        foreach($productos as $producto)
        {
            $cantidadProductos = LoteProducto::where('producto_id',$producto->id)->where('estado','1')->sum('cantidad');
            //ACTUALIZAR EL STOCK DEL PRODUCTO
            $producto->stock = $cantidadProductos ? $cantidadProductos : 0.00;
            $producto->update();
        }
    }
}

if (!function_exists('actualizarPorcentajes')) {
    function actualizarPorcentajes()
    {
        $productos = Producto::all();
        foreach($productos as $producto)
        {
            $tipo_normal = TipoCliente::where('producto_id',$producto->id)->where('estado','ACTIVO')->where('cliente','121')->first();
            $tipo_distribuidor = TipoCliente::where('producto_id',$producto->id)->where('estado','ACTIVO')->where('cliente','122')->first();

            $producto->porcentaje_normal = $tipo_normal ? $tipo_normal->porcentaje : 0;
            $producto->porcentaje_distribuidor = $tipo_distribuidor ? $tipo_distribuidor->porcentaje : 0;
            $producto->update();
        }
    }
}

if (!function_exists('actualizarStockProductosxCompras')) {
    function actualizarStockProductosxCompras()
    {
        $compras = Documento::where('estado','ANULADO')->get();
        foreach($compras as $compra)
        {
            foreach($compra->detalles as $item)
            {
                $item->estado = 'ANULADO';
                $item->update();
            }
        }
    }
}

if (!function_exists('turnos')) {
    function turnos()
    {
        return General::find(31)->detalles;
    }
}
if (!function_exists('cajas')) {
    function cajas()
    {
        return Caja::where('estado_caja', 'CERRADA')->where('estado', 'ACTIVO')->get();
    }
}
if (!function_exists('colaboradoresDisponibles')) {
    function colaboradoresDisponibles()
    {
        $colaboradores = Colaborador::join('personas as p', 'p.id', '=', 'colaboradores.id')
            //->join('movimiento_caja as mc','mc.colaborador_id','!=','colaboradores.id')
            // ->select('colaboradores.id','p.nombres','p.apellido_paterno','p.apellido_materno')
            ->where('p.estado', 'ACTIVO')
            // ->where('mc.estado_movimiento','CIERRE')
            ->get();
        // $datos=array();
        // foreach ($colaboradores as $key => $value) {
        //
        //     if($consulta->count()==0)
        //     {
        //         array_push($datos,$value);
        //     }
        // }
        //   return $datos;
        $colaboradores = Colaborador::cursor()->filter(function ($colaborador) {
            $consulta = MovimientoCaja::where('colaborador_id', $colaborador->id)->where('estado_movimiento', 'APERTURA')->get();
            return ($consulta->count() == 0 && $colaborador->estado == 'ACTIVO') ? true : false;
        });
        return $colaboradores;
    }
}
if (!function_exists('cuentas')) {
    function cuentas()
    {
        return General::find(32)->detalles;
    }
}

if (!function_exists('proveedores')) {
    function proveedores()
    {
        return Proveedor::where('estado','!=','ANULADO')->get();
    }
}

if (!function_exists('clientes')) {
    function clientes()
    {
        return Cliente::where('estado','!=','ANULADO')->get();
    }
}
if (!function_exists('movimientoUser')) {
    function movimientoUser()
    {
        try{
            /*========= BUSCAR EL MOVIMIENTO CAJA A LA CUAL PERTENECE EL QUE REALIZÓ LA VENTA,
            DE ESTA ENLAZAREMOS EL DOCUMENTO DE VENTA CON EL MOVIMIENTO CAJA RESPECTIVO =========
            */

            /*===== 
                OJ0: 
                MOVIMIENTO_CAJA: CONTIENE ID CAJA, CAJERO Y ESTADO (APERTURA Y CIERRE)
                DETALLE_MOVIMIENTO_CAJA: CONTIENE  CAJERO Y SUS COLABORADORES(VENDEDORES)
            ======
            */

            return  DB::select('select 
                    dmc.movimiento_id 
                    from detalles_movimiento_caja as dmc
                    where 
                    dmc.colaborador_id = ? 
                    and dmc.fecha_cierre is null
                    and dmc.estado_movimiento = "APERTURA"',
                    [Auth::user()->colaborador_id]);


            // if (FullAccess() || PuntoVenta()) {

            //     $consulta = MovimientoCaja::where('caja_id', 1)->where('estado_movimiento', 'APERTURA');
            //     if ($consulta->count() != 0) {
            //         return $consulta->first();
            //     } else {
            //         dd(MovimientoCaja::where('estado_movimiento', 'APERTURA')->first());
            //         return MovimientoCaja::where('estado_movimiento', 'APERTURA')->first();
            //     }
            // } else {
            //     return MovimientoCaja::where('colaborador_id', Auth::user()->user->persona->colaborador->id)->where('estado_movimiento', 'APERTURA')->first();
            // }

        }catch(\Exception $ex){
            Log::info($ex);
        }
    }
}

/**
 * CUADRE CAJA
 */


//ingresos
if (!function_exists('cuadreMovimientoCajaIngresosCuadreEfectivo')) {
    function cuadreMovimientoCajaIngresosCuadreEfectivo(MovimientoCaja $movimiento)
    {
        $totalIngresos = 0;
        foreach ($movimiento->detalleMovimientoVentas as $item) {
            if ($item->documento->condicion_id == 1 && ifNoConvertido($item->documento->id) && $item->documento->estado_pago == 'PAGADA') { // && $item->documento->sunat != '2'
                if ($item->documento->tipo_pago_id == 1) {
                    $totalIngresos = $totalIngresos + $item->documento->importe;
                }
                else{
                    $totalIngresos = $totalIngresos + $item->documento->efectivo;
                }
            }
        }
        foreach ($movimiento->detalleCuentaCliente as $item) {
            if($item->tipo_pago_id == 1)
            {
                $totalIngresos = $totalIngresos  + $item->efectivo;
            }
        }
        return $totalIngresos;
    }
}

//egresos
if (!function_exists('cuadreMovimientoCajaEgresosCuadreEfectivo')) {
    function cuadreMovimientoCajaEgresosCuadreEfectivo($movimiento)
    {
        $totalEgresos = 0;
        foreach ($movimiento->detalleCuentaProveedor as $key => $item) {
            if($item->tipo_pago_id == 1)
            {
                $totalEgresos = $totalEgresos + $item->efectivo;
            }
        }
        foreach ($movimiento->detalleMoviemientoEgresos as $key => $item) {
            if ($item->egreso->estado == "ACTIVO" && $item->egreso->tipo_pago_id == 1) {
                $totalEgresos = $totalEgresos + $item->egreso->efectivo;
            }
        }
        return $totalEgresos;
    }
}

/**
 * VENTAS
 **/
if (!function_exists('cuadreMovimientoCajaIngresosVenta')) {
    function cuadreMovimientoCajaIngresosVenta(MovimientoCaja $movimiento)
    {
        $totalIngresos = 0;

       
        //====== BUSCAR LOS DOCUMENTOS VENTA QUE NO SE ENCUENTREN PAGADOS CON RECIBOS =========
        foreach ($movimiento->detalleMovimientoVentas as $item) {
            if ($item->cobrar === 'SI' 
            && $item->documento->condicion_id == 1 
            && ifNoConvertido($item->documento->id) 
            && $item->documento->estado_pago == 'PAGADA' 
            && $item->documento->tipo_pago_id !='4'
            && $item->documento->estado === 'ACTIVO') { // && $item->documento->sunat != '2'
                $totalIngresos = $totalIngresos + ($item->documento->importe + $item->documento->efectivo);
            }
        }

        return $totalIngresos;
    }
}



//========== CALCULAR INGRESOS EN RECIBOS ==============
if (!function_exists('cuadreMovimientoCajaIngresosRecibo')) {
    function cuadreMovimientoCajaIngresosRecibo(MovimientoCaja $movimiento)
    {
        $totalIngresos = 0;
        //======== OBTENER TODOS LOS RECIBOS DE DICHO MOVIMIENTO =========
        $recibos        =   DB::select('select * from recibos_caja as rc 
                            where rc.movimiento_id=? and rc.estado="ACTIVO"',[$movimiento->id]);
    

        foreach ($recibos as $item) {
            $totalIngresos  +=  $item->monto;
        }

        return $totalIngresos;
    }
}


if (!function_exists('cuadreMovimientoCajaIngresosVentaElectronico')) {
    function cuadreMovimientoCajaIngresosVentaElectronico(MovimientoCaja $movimiento)
    {
        $totalIngresos = 0;
        foreach(tipos_pago() as $tipo)
        {
            if($tipo->id > 1)   //====== TIPO PAGO != EFECTIVO =======
            {
                $totalIngresos = $totalIngresos + (cuadreMovimientoCajaIngresosVentaResum($movimiento, $tipo->id) - cuadreMovimientoDevolucionesResum($movimiento, $tipo->id));
            }
        }
        return $totalIngresos;
    }
}

//========== CALCULA EL TOTAL DE INGRESOS EN DOCS VENTAS QUE NO SE PAGARON CON RECIBOS =========
if (!function_exists('cuadreMovimientoCajaIngresosVentaResum')) {
    function cuadreMovimientoCajaIngresosVentaResum(MovimientoCaja $movimiento,$tipo_pago)
    {
        $totalIngresos = 0;

        //====== TIPO PAGO (5) => INGRESOS TOTALES SIN IMPORTAR EL TIPO DE PAGO =======
        if($tipo_pago == 5){

            foreach ($movimiento->detalleMovimientoVentas as $item) {
                if (
                    $item->documento->condicion_id == 1 
                    && ifNoConvertido($item->documento->id) 
                    && $item->documento->estado_pago == 'PAGADA'
                    && $item->documento->estado == 'ACTIVO'
                ) { // && $item->documento->sunat != '2'
                    if ($item->documento->tipo_pago_id == 1 || $item->documento->tipo_pago_id == 2 || $item->documento->tipo_pago_id == 3 || $item->documento->tipo_pago_id !== 4) {
                        $totalIngresos = $totalIngresos + $item->documento->importe + $item->documento->efectivo;
                    }
                }
            }

        }else{

            //======= INGRESO POR TIPO DE PAGO ESPECÍFICO =======
            foreach ($movimiento->detalleMovimientoVentas as $item) {
                if (
                    $item->documento->condicion_id == 1 
                    && ifNoConvertido($item->documento->id) 
                    && $item->documento->estado_pago == 'PAGADA'
                    && $item->documento->estado == 'ACTIVO'
                ) { // && $item->documento->sunat != '2'
                    if ($item->documento->tipo_pago_id == $tipo_pago) {
                        $totalIngresos = $totalIngresos + $item->documento->importe + $item->documento->efectivo;
                    }
                }
            }

        }

        return $totalIngresos;

    //    if($id == 1)
    //    {
    //         $totalIngresos = 0;
    //         foreach ($movimiento->detalleMovimientoVentas as $item) {
    //             if ($item->documento->condicion_id == 1 && ifNoConvertido($item->documento->id) && $item->documento->estado_pago == 'PAGADA') { // && $item->documento->sunat != '2'
    //                 if ($item->documento->tipo_pago_id == $id) {
    //                     $totalIngresos = $totalIngresos + $item->documento->importe;
    //                 }
    //                 else{
    //                     $totalIngresos = $totalIngresos + $item->documento->efectivo;
    //                 }
    //             }
    //         }
    //         return $totalIngresos;
    //    }
    //    else
    //    {
    //         $totalIngresos = 0;
    //         foreach ($movimiento->detalleMovimientoVentas as $item) {
    //             if ($item->documento->condicion_id == 1  && ifNoConvertido($item->documento->id) && $item->documento->estado_pago == 'PAGADA') { // && $item->documento->sunat != '2'
    //                 if ($item->documento->tipo_pago_id == $id) {
    //                     $totalIngresos = $totalIngresos + $item->documento->importe;
    //                 }
    //             }
    //         }
    //         return $totalIngresos;
    //    }
    }
}

if (!function_exists('calcularTotalesRecibosCaja')) {
    function calcularTotalesRecibosCaja(MovimientoCaja $movimiento,$tipo_pago)
    {
        $recibos    =   DB::select('select rc.monto,rc.metodo_pago,rc.estado 
                        from recibos_caja as rc where rc.movimiento_id=?',[$movimiento->id]);
        $total      =   0;
        foreach ($recibos as $recibo) {
            if($recibo->metodo_pago == $tipo_pago && $recibo->estado === "ACTIVO"){
                $total+= $recibo->monto;
            }
        }

        return $total;
    }
}


if (!function_exists('obtenerTotalIngresosPorTipoPago')) {
    function obtenerTotalIngresosPorTipoPago(MovimientoCaja $movimiento)
    {
        //===== OBTENER TIPOS DE PAGO =====
        $tipos_pago =   tipos_pago();
        //======= OBTENER LOS DOCUMENTOS DE VENTA ENLAZADOS AL MOVIMIENTO ======
        $docs_venta     =   $movimiento->detalleMovimientoVentas;
        
        $res    =   [];
        //====== PARA CADA TIPO DE PAGO ======
        foreach ($tipos_pago as $tipo_pago) {
            $resultado  =   ['tipo_pago'                =>  $tipo_pago->descripcion,
                            'monto_total_ingresos'      =>  0];

            //======= CALCULAR TOTAL INGRESOS ======
            foreach ($docs_venta as $doc_venta) {
               
                if($doc_venta->documento->tipo_pago_id == $tipo_pago->id && $doc_venta->documento->estado !== 'ANULADO'){
                    $resultado['monto_total_ingresos']  +=  $doc_venta->documento->importe;
                }
            }

            $res[]  =   (object)$resultado;
        } 
        
        return $res;
    }
}

/**
 * DEVOLUCION
 */

if (!function_exists('cuadreMovimientoDevoluciones')) {
    function cuadreMovimientoDevoluciones(MovimientoCaja $movimiento)
    {
        //$cuenta = TablaDetalle::find(165);
        //$cuenta_id = $cuenta->descripcion == 'DEVOLUCION' ? $cuenta->id : (TablaDetalle::where('descripcion','DEVOLUCION')->where('tabla_id',32)->first() ? TablaDetalle::where('descripcion','DEVOLUCION')->where('tabla_id',32)->first()->id : null);
        

        
        $totalEgresos = 0;
        // foreach ($movimiento->detalleMoviemientoEgresos as $key => $item) {
        //     if ($item->egreso->estado == "ACTIVO" && $item->egreso->cuenta_id == $cuenta_id) {
        //         $totalEgresos = $totalEgresos + ($item->egreso->efectivo + $item->egreso->importe);
        //     }
        // }

        foreach ($movimiento->detalleMoviemientoEgresos as $key => $item) {
            if ( $item->egreso->estado == "ACTIVO" ) {
                $totalEgresos = $totalEgresos + ($item->egreso->efectivo + $item->egreso->importe);
            }
        }

        return $totalEgresos;
    }
}

if (!function_exists('cuadreMovimientoDevolucionesResum')) {
    function cuadreMovimientoDevolucionesResum(MovimientoCaja $movimiento, $id)
    {
        $cuenta = TablaDetalle::find(165);
        $cuenta_id = $cuenta->descripcion == 'DEVOLUCION' ? $cuenta->id : (TablaDetalle::where('descripcion','DEVOLUCION')->where('tabla_id',32)->first() ? TablaDetalle::where('descripcion','DEVOLUCION')->where('tabla_id',32)->first()->id : null);
        if($id == 1)
        {
            $totalEgresos = 0;
            foreach ($movimiento->detalleMoviemientoEgresos as $key => $item) {
                if ($item->egreso->estado == "ACTIVO" && $item->egreso->cuenta_id == $cuenta_id) {
                    $totalEgresos = $totalEgresos + $item->egreso->efectivo;
                }
            }
            return $totalEgresos;
        }
        else
        {
            $totalEgresos = 0;
            foreach ($movimiento->detalleMoviemientoEgresos as $key => $item) {
                if ($item->egreso->estado == "ACTIVO" && $item->egreso->cuenta_id == $cuenta_id) {
                    if($item->egreso->tipo_pago_id == $id)
                    {
                        $totalEgresos = $totalEgresos + $item->egreso->importe;
                    }
                }
            }
            return $totalEgresos;
        }
    }
}

/*COBRANZA */
if (!function_exists('cuadreMovimientoCajaIngresosCobranza')) {
    function cuadreMovimientoCajaIngresosCobranza(MovimientoCaja $movimiento)
    {
        $totalIngresos = 0;

        foreach ($movimiento->detalleCuentaCliente as $item) {
            $totalIngresos = $totalIngresos  + $item->monto;
        }
        return $totalIngresos;
    }
}

if (!function_exists('cuadreMovimientoCajaIngresosCobranzaResum')) {
    function cuadreMovimientoCajaIngresosCobranzaResum(MovimientoCaja $movimiento)
    {
        $totalIngresos = 0;

            foreach ($movimiento->detalleCuentaCliente as $item) {
                 $totalIngresos = $totalIngresos  + $item->monto;
            }

        return $totalIngresos;

        // if($id == 1)
        // {
        //     $totalIngresos = 0;

        //     foreach ($movimiento->detalleCuentaCliente as $item) {
        //         $totalIngresos = $totalIngresos  + $item->efectivo;
        //     }
        //     return $totalIngresos;
        // }
        // else{
        //     $totalIngresos = 0;

        //     foreach ($movimiento->detalleCuentaCliente as $item) {
        //         if($item->tipo_pago_id == $id)
        //         {
        //             $totalIngresos = $totalIngresos  + $item->importe;
        //         }
        //     }
        //     return $totalIngresos;
        // }
    }
}

/**EGRESOS */

if (!function_exists('cuadreMovimientoCajaEgresosEgreso')) {
    function cuadreMovimientoCajaEgresosEgreso($movimiento)
    {

        $totalEgresos = 0;
        foreach ($movimiento->detalleMoviemientoEgresos as $key => $item) {
            if ($item->egreso->estado == "ACTIVO") {
                $totalEgresos = $totalEgresos + ($item->egreso->efectivo + $item->egreso->importe);
            }
        }
        return $totalEgresos;
    }
}

if (!function_exists('cuadreMovimientoCajaEgresosEgresoResum')) {
    function cuadreMovimientoCajaEgresosEgresoResum($movimiento,$tipo_pago)
    {
        $totalEgresos = 0;
        
         //====== TIPO PAGO (4) => INGRESOS TOTALES SIN IMPORTAR EL TIPO DE PAGO =======
        if($tipo_pago == 4){
            foreach ($movimiento->detalleMoviemientoEgresos as $key => $item) {
                if($item->egreso->estado == 'ACTIVO' && $item->egreso->tipo_pago_id >0)
                {
                    $totalEgresos = $totalEgresos + $item->egreso->monto;
                }
            }
            return $totalEgresos;
        }


        foreach ($movimiento->detalleMoviemientoEgresos as $key => $item) {
            if($item->egreso->estado == 'ACTIVO' && $item->egreso->tipo_pago_id == $tipo_pago)
            {
                $totalEgresos = $totalEgresos + $item->egreso->monto;
            }
        }
        return $totalEgresos;


        // if($id == 1)
        // {
        //     $totalEgresos = 0;
        //     foreach ($movimiento->detalleMoviemientoEgresos as $key => $item) {
        //         if($item->egreso->estado == 'ACTIVO')
        //         {
        //             $totalEgresos = $totalEgresos + $item->egreso->efectivo;
        //         }
        //     }
        //     return $totalEgresos;
        // }
        // else
        // {
        //     $totalEgresos = 0;
        //     foreach ($movimiento->detalleMoviemientoEgresos as $key => $item) {
        //         if ($item->egreso->estado == "ACTIVO" && $item->egreso->tipo_pago_id == $id) {
        //             $totalEgresos = $totalEgresos + $item->egreso->importe;
        //         }
        //     }
        //     return $totalEgresos;
        // }
    }
}

/**PAGOS */
if (!function_exists('cuadreMovimientoCajaEgresosPago')) {
    function cuadreMovimientoCajaEgresosPago($movimiento)
    {
        $totalEgresos = 0;
        foreach ($movimiento->detalleCuentaProveedor as $key => $item) {
            $totalEgresos = $totalEgresos + ($item->efectivo + $item->importe);
        }
        return $totalEgresos;
    }
}

if (!function_exists('cuadreMovimientoCajaEgresosPagoResum')) {
    function cuadreMovimientoCajaEgresosPagoResum($movimiento)
    {
        $totalEgresos = 0;
            foreach ($movimiento->detalleCuentaProveedor as $key => $item) {
                $totalEgresos = $totalEgresos + $item->monto;
            }
        return $totalEgresos;

        // if($id == 1)
        // {
        //     $totalEgresos = 0;
        //     foreach ($movimiento->detalleCuentaProveedor as $key => $item) {
        //         $totalEgresos = $totalEgresos + $item->efectivo;
        //     }
        //     return $totalEgresos;
        // }
        // else
        // {
        //     $totalEgresos = 0;
        //     foreach ($movimiento->detalleCuentaProveedor as $key => $item) {
        //         if($item->tipo_pago_id == $id)
        //         {
        //             $totalEgresos = $totalEgresos + $item->importe;
        //         }
        //     }
        //     return $totalEgresos;
        // }
    }
}

//==================================================================================================================================

if (!function_exists('comprobantes_empresa')) {
    function comprobantes_empresa()
    {
        $comprobantes = Numeracion::where('empresa_id',Empresa::first()->id)->where('estado','ACTIVO')->get();
        foreach($comprobantes as $arr)
    {
        $arr['descripcion'] = $arr->comprobanteDescripcion();
    }
        return $comprobantes;
    }
}

if (!function_exists('precio_dolar')) {
    function precio_dolar()
    {
        $fecha =  Carbon::now()->toDateString();
        $ctx = stream_context_create(array('http'=>
            array(
                'timeout' => 1200,  //1200 Seconds is 20 Minutes
            )
        ));
        $data = file_get_contents("https://api.apis.net.pe/v1/tipo-cambio-sunat?fecha=".$fecha,false,$ctx);
        $infodata = json_decode($data,false);
        return response()->json($infodata);
    }
}

if (!function_exists('mes')) {
    function mes()
    {
        date_default_timezone_set("America/Lima");
        $mes = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"][date("n") - 1];
        return $mes;
    }
}
if(!function_exists("")){

}
if (!function_exists('ventas_mensual')) {
    function ventas_mensual()
    {
        $fecha_hoy = Carbon::now();
        $mes = date_format($fecha_hoy,'m');
        $anio = date_format($fecha_hoy,'Y');
        $total = DocumentoDocumento::where('estado','!=','ANULADO')->whereMonth('fecha_documento',$mes)->whereYear('fecha_documento',$anio)->sum('total');
        $total_invalida = DocumentoDocumento::where('estado', '!=', 'ANULADO')->whereMonth('fecha_documento', $mes)->whereYear('fecha_documento', $anio)->where('convertir', '!=', '')->where('tipo_venta_id','!=','129')->sum('total');
        $total_notas = Nota::whereMonth('fechaEmision',$mes)->whereYear('fechaEmision',$anio)->sum('mtoImpVenta');
        return (float)($total - $total_invalida - $total_notas);
    }
}

if (!function_exists('ventas_mensual_random')) {
    function ventas_mensual_random($mes,$anio)
    {
        $total = DocumentoDocumento::where('estado','!=','ANULADO')->whereMonth('fecha_documento',$mes)->whereYear('fecha_documento',$anio)->sum('total');
        $total_invalida = DocumentoDocumento::where('estado','!=','ANULADO')->whereMonth('fecha_documento',$mes)->whereYear('fecha_documento',$anio)->where('convertir','!=','')->where('tipo_venta_id','!=','129')->sum('total');
        $total_notas = Nota::whereMonth('fechaEmision', $mes)->whereYear('fechaEmision', $anio)->sum('mtoImpVenta');
        return (float)($total - $total_invalida - $total_notas);
    }
}

if (!function_exists('utilidad_mensual')) {
    function utilidad_mensual()
    {
        $fecha_hoy = Carbon::now();
        $mes = date_format($fecha_hoy,'m');
        $anio = date_format($fecha_hoy,'Y');
        $ventas = DocumentoDocumento::where('estado','!=','ANULADO')->whereMonth('fecha_documento',$mes)->whereYear('fecha_documento',$anio)->get();
        $coleccion = collect();
        /**foreach ($ventas as $venta) {
            if(ifNoConvertido($venta->id))
            {
                $detalles = DocumentoDetalle::where('estado', 'ACTIVO')->where('documento_id', $venta->id)->get();
                foreach ($detalles as $detalle) {
                    $precom = $detalle->lote->detalle_compra ? ($detalle->lote->detalle_compra->precio_soles + ($detalle->lote->detalle_compra->costo_flete_soles / $detalle->lote->detalle_compra->cantidad)) : $detalle->lote->detalle_nota->costo_soles;
                    $coleccion->push([
                        "fecha_doc" => $venta->fecha_documento,
                        "cantidad" => $detalle->cantidad,
                        "producto" => $detalle->lote->producto->nombre,
                        "precio_venta" => $detalle->precio_nuevo,
                        "precio_compra" => $precom,
                        "utilidad" => $detalle->precio_nuevo - $precom,
                        "importe" => ($detalle->precio_nuevo - $precom) * $detalle->cantidad
                    ]);
                }
            }
        }
        */
        foreach ($ventas as $venta) {
            $detalles = DocumentoDetalle::where('documento_id',$venta->id)->get();
            foreach($detalles as $detalle)
            {
                $precom = $detalle->lote->detalle_compra ? ($detalle->lote->detalle_compra->precio_soles + ($detalle->lote->detalle_compra->costo_flete_soles / $detalle->lote->detalle_compra->cantidad)) : $detalle->lote->detalle_nota->costo_soles;
                $utilidad =  number_format(($detalle->precio_nuevo - $precom),2);

                $coleccion->push([
                    "fecha_doc" => $venta->fecha_documento,
                    "cantidad" => $detalle->cantidad,
                    "producto" => $detalle->lote->producto->nombre,
                    "precio_venta" => number_format($detalle->precio_nuevo,2),
                    "precio_compra" => number_format($precom,2),
                    "utilidad" =>$utilidad,
                    "importe" => number_format(($detalle->cantidad) * $utilidad,2),
                    "valorVenta"=>$detalle->valor_venta
                ]);

                $notaDetalle = NotaDetalle::where("detalle_id",$detalle->id)->get();
                foreach($notaDetalle as $nota){
                    $precom_nota = $nota->detalle->lote->detalle_compra ? ($nota->detalle->lote->detalle_compra->precio_soles + ($nota->detalle->lote->detalle_compra->costo_flete_soles / $nota->detalle->lote->detalle_compra->cantidad)) : $nota->detalle->lote->detalle_nota->costo_soles;
                    $utilidad_nota =  number_format(($nota->mtoPrecioUnitario - $precom_nota),2);

                    $coleccion->push([
                        "fecha_doc" => $nota->nota_dev->fechaEmision,
                        "cantidad" => $nota->cantidad,
                        "producto" => $nota->detalle->lote->producto->nombre,
                        "precio_venta" => number_format($nota->mtoPrecioUnitario,2),
                        "precio_compra" => number_format($precom_nota,2),
                        "utilidad" =>$utilidad_nota,
                        "importe" => "-".number_format(($nota->cantidad) * $utilidad_nota,2),
                        "valorVenta"=>number_format(($nota->cantidad) * $nota->mtoPrecioUnitario,2)
                    ]);
                }
            }
        }
        $utilidad = $coleccion->sum('importe');
        return $utilidad;
    }
}

if (!function_exists('utilidad_mensual_random')) {
    function utilidad_mensual_random($mes,$anio)
    {
        $ventas = DocumentoDocumento::where('estado', '!=', 'ANULADO')->whereMonth('fecha_documento', $mes)->whereYear('fecha_documento', $anio)->get();
        $coleccion = collect();
        /**foreach ($ventas as $venta) {
            if (ifNoConvertido($venta->id)) {
                $detalles = DocumentoDetalle::where('estado', 'ACTIVO')->where('documento_id', $venta->id)->get();
                foreach ($detalles as $detalle) {
                    $precom = $detalle->lote->detalle_compra ? ($detalle->lote->detalle_compra->precio_soles + ($detalle->lote->detalle_compra->costo_flete_soles / $detalle->lote->detalle_compra->cantidad)) : $detalle->lote->detalle_nota->costo_soles;
                    $coleccion->push([
                        "fecha_doc" => $venta->fecha_documento,
                        "cantidad" => $detalle->cantidad,
                        "producto" => $detalle->lote->producto->nombre,
                        "precio_venta" => $detalle->precio_nuevo,
                        "precio_compra" => $precom,
                        "utilidad" => $detalle->precio_nuevo - $precom,
                        "importe" => ($detalle->precio_nuevo - $precom) * $detalle->cantidad
                    ]);
                }
            }
        }
        **/
        foreach ($ventas as $venta) {
            $detalles = DocumentoDetalle::where('documento_id',$venta->id)->get();
            foreach($detalles as $detalle)
            {
                $precom = $detalle->lote->detalle_compra ? ($detalle->lote->detalle_compra->precio_soles + ($detalle->lote->detalle_compra->costo_flete_soles / $detalle->lote->detalle_compra->cantidad)) : $detalle->lote->detalle_nota->costo_soles;
                $utilidad = $detalle->precio_nuevo - $precom;

                $coleccion->push([
                    "fecha_doc" => $venta->fecha_documento,
                    "cantidad" => $detalle->cantidad,
                    "producto" => $detalle->lote->producto->nombre,
                    "precio_venta" => number_format($detalle->precio_nuevo,2),
                    "precio_compra" => number_format($precom,2),
                    "utilidad" =>$utilidad,
                    "importe" => ($detalle->cantidad!="" ? $detalle->cantidad : 0) * $utilidad,
                    "valorVenta"=>$detalle->valor_venta
                ]);

                $notaDetalle = NotaDetalle::where("detalle_id",$detalle->id)->get();
                foreach($notaDetalle as $nota){
                    $precom_nota = $nota->detalle->lote->detalle_compra ? ($nota->detalle->lote->detalle_compra->precio_soles + ($nota->detalle->lote->detalle_compra->costo_flete_soles / $nota->detalle->lote->detalle_compra->cantidad)) : $nota->detalle->lote->detalle_nota->costo_soles;
                    $utilidad_nota =  $nota->mtoPrecioUnitario - $precom_nota;

                    $coleccion->push([
                        "fecha_doc" => $nota->nota_dev->fechaEmision,
                        "cantidad" => $nota->cantidad,
                        "producto" => $nota->detalle->lote->producto->nombre,
                        "precio_venta" => number_format($nota->mtoPrecioUnitario,2),
                        "precio_compra" => number_format($precom_nota,2),
                        "utilidad" =>$utilidad_nota,
                        "importe" => floatval($nota->cantidad!="" ? 0 : $nota->cantidad) * floatval($utilidad_nota!="" ? 0 : $utilidad_nota),
                        "valorVenta"=>floatval($nota->cantidad) * floatval($nota->mtoPrecioUnitario)
                    ]);
                }
            }
        }
        $utilidad = $coleccion->sum('importe');
        return $utilidad;
    }
}



if (!function_exists('inversion_mensual')) {
    function inversion_mensual()
    {
        $fecha_hoy = Carbon::now();
        $mes = date_format($fecha_hoy,'m');
        $anio = date_format($fecha_hoy,'Y');
        $total = Documento::where('estado','!=','ANULADO')->whereMonth('fecha_emision',$mes)->whereYear('fecha_emision',$anio)->sum('total_soles');
        return $total;
    }
}

if (!function_exists('compras_mensual')) {
    function compras_mensual()
    {
        $fecha_hoy = Carbon::now();
        $mes = date_format($fecha_hoy, 'm');
        $anio = date_format($fecha_hoy, 'Y');
        $total = Documento::where('estado', '!=', 'ANULADO')->whereMonth('fecha_emision', $mes)->whereYear('fecha_emision', $anio)->sum('total_soles');
        return $total;
    }
}

if (!function_exists('cuentas_pagar')) {
    function cuentas_pagar()
    {
        $fecha_hoy = Carbon::now();
        $mes = date_format($fecha_hoy,'m');
        $anio = date_format($fecha_hoy,'Y');
        $total = CuentaProveedor::where('estado','!=','ANULADO')->whereMonth('created_at',$mes)->whereYear('created_at',$anio)->sum('saldo');
        return $total;
    }
}

if (!function_exists('cuentas_cobrar')) {
    function cuentas_cobrar()
    {
        $fecha_hoy = Carbon::now();
        $mes = date_format($fecha_hoy,'m');
        $anio = date_format($fecha_hoy,'Y');
        $total = CuentaCliente::where('estado','!=','ANULADO')->whereMonth('created_at',$mes)->whereYear('created_at',$anio)->sum('saldo');
        return $total;
    }
}

if (!function_exists('generarCodigo')) {
    function generarCodigo($longitud)
    {
        $existe     =   true;
        while ($existe) {
            
            $key        = '';
            // $pattern    = '1234567890abcdefghijklmnopqrstuvwxyz0987654321ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $pattern    = '12345678900987654321ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $max        = strlen($pattern)-1;
            for($i=0;$i < $longitud;$i++) $key .= $pattern[mt_rand(0,$max)];
    
            $existe =   DB::table('codigos_barra')
                        ->whereRaw('UPPER(codigo_barras) = ?', [strtoupper($key)])
                        ->first();
        }

        return $key;
    }
}

if (!function_exists('CEC')) {
    function CEC()
    {
        $config = Configuracion::where('slug', 'CEC')->first();
        return $config->propiedad;
    }
}

if (!function_exists('FullAccess')) {
    function FullAccess()
    {
        $user = Auth::user();
        $fullaccess = false;
        if(count($user->roles)>0)
        {
            $cont = 0;
            while($cont < count($user->roles))
            {
                if($user->roles[$cont]['full-access'] == 'SI')
                {
                    $fullaccess = true;
                    $cont = count($user->roles);
                }

                $cont = $cont + 1;
            }
        }
        return $fullaccess;
    }
}

if (!function_exists('PuntoVenta')) {
    function PuntoVenta()
    {
        $user = Auth::user();
        $fullaccess = false;
        if(count($user->roles)>0)
        {
            $cont = 0;
            while($cont < count($user->roles))
            {
                if($user->roles[$cont]['punto-venta'] == 'SI')
                {
                    $fullaccess = true;
                    $cont = count($user->roles);
                }

                $cont = $cont + 1;
            }
        }
        return $fullaccess;
    }
}

if (!function_exists('tipos_pago')) {
    function tipos_pago()
    {
        $tipos = TipoPago::where('estado','ACTIVO')->get();
        return $tipos;
    }
}

if (!function_exists('ifNoConvertido')) {
    function ifNoConvertido($id)
    {
        $doc = DocumentoDocumento::find($id);
        
        if($doc->convert_de_id){
            return false;
        }else{
            return true;
        }

        // if($doc->tipo_venta_id == '129')
        // {
        //     return true;
        // }
        // else
        // {
        //     if($doc->convertir)
        //     {
        //         return false;
        //     }else{
        //         return true;
        //     }
        // }
    }
}

if (!function_exists('addComprasLote')) {
    function addComprasLote()
    {
        $detalles = Detalle_Documento::where('estado','ACTIVO')->get();

        foreach($detalles as $detalle)
        {
            $lote = new LoteProducto();
            $lote->compra_documento_id = $detalle->documento->id;
            $lote->codigo_lote = $detalle->lote;
            $lote->producto_id = $detalle->producto_id;
            $lote->cantidad = $detalle->cantidad;
            $lote->cantidad_logica = $detalle->cantidad;
            $lote->cantidad_inicial = $detalle->cantidad;
            $lote->fecha_vencimiento = $detalle->fecha_vencimiento;
            $lote->fecha_entrega = $detalle->documento->fecha_entrega;
            $lote->observacion = 'DOC. COMPRA';
            $lote->estado = '1';
            $lote->save();

            $detalle->lote_id = $lote->id;
            $detalle->update();

            //MOVIMIENTO
            $producto = Producto::findOrFail($detalle->producto_id);

            $movimiento = new MovimientoAlmacen();
            $movimiento->almacen_final_id = $detalle->producto->almacen->id;
            $movimiento->cantidad = $detalle->cantidad;
            $movimiento->nota = 'COMPRA';
            $movimiento->observacion = $producto->codigo.' - '.$producto->descripcion;
            $movimiento->usuario_id = auth()->user()->id;
            $movimiento->movimiento = 'INGRESO';
            $movimiento->producto_id = $detalle->producto_id;
            $movimiento->lote_id = $lote->id;
            $movimiento->compra_documento_id = $detalle->documento_id; //DOCUMENTO DE COMPRA
            $movimiento->save();
        }

        return 'Exito';
    }
}

if (!function_exists('timeago')) {
    function timeago($date) {
        $timestamp = strtotime($date);

        $strTime = array("segundo", "minuto", "hora", "dia", "mes", "año");
        $length = array("60","60","24","30","12","10");

        $currentTime = time();
        if($currentTime >= $timestamp) {
                    $diff     = time()- $timestamp;
                    for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
                    $diff = $diff / $length[$i];
                    }

                    $diff = round($diff);
                    return "Hace " . $diff . " " . $strTime[$i] . "(s)";
        }
    }
}

if (!function_exists('obtenerLegend')) {
    function obtenerLegend($documento) {
        $formatter = new NumeroALetras();
        $convertir = $formatter->toInvoice($documento->total, 2, 'SOLES');

        //CREAR LEYENDA DEL COMPROBANTE
        $arrayLeyenda = Array();
        $arrayLeyenda[] = array(
            "code" => "1000",
            "value" => $convertir
        );
        return $arrayLeyenda;
    }
}

if (!function_exists('refreshNotifications')) {
    function refreshNotifications() {
        $delete = DB::table('notifications');

        if(!PuntoVenta() && !FullAccess())
        {
            $delete = $delete->where('notifiable_id',Auth::user()->id);
        }

        $delete = $delete->delete();

        $documentos =  DB::table('cotizacion_documento')
        ->select(
            'cotizacion_documento.*',
        )
        ->whereIn('cotizacion_documento.tipo_venta_id',['127','128'])
        ->where('cotizacion_documento.estado', '!=','ANULADO')
        ->where('cotizacion_documento.sunat','0')
        ->where('cotizacion_documento.contingencia','0')
        ->whereRaw('ifnull((json_unquote(json_extract(cotizacion_documento.getRegularizeResponse, "$.code"))),"0000") != "1033"');

        if(!PuntoVenta() && !FullAccess())
        {
            $documentos = $documentos->where('user_id',Auth::user()->id);
        }

        $documentos = $documentos->orderBy('cotizacion_documento.id','ASC')->get();
        foreach($documentos as $documento)
        {
            Auth::user()->notify(new FacturacionNotification($documento));
        }

        // Regularizaciones

        $regularizaciones =  DB::table('cotizacion_documento')
        ->select(
            'cotizacion_documento.*',
        )
        ->orderBy('cotizacion_documento.id','DESC')
        ->whereIn('cotizacion_documento.tipo_venta_id',['127','128'])
        ->where('cotizacion_documento.estado', '!=','ANULADO')
        ->where('cotizacion_documento.sunat', '!=','2')
        ->where('cotizacion_documento.contingencia', '0')
        ->where(DB::raw('JSON_EXTRACT(cotizacion_documento.getRegularizeResponse, "$.code")'),'1033')
        ->where('cotizacion_documento.regularize','1')
        ->orderBy("id","DESC");

        if(!PuntoVenta() && !FullAccess())
        {
            $regularizaciones = $regularizaciones->where('user_id',Auth::user()->id);
        }

        $regularizaciones = $regularizaciones->get();

        foreach($regularizaciones as $doc)
        {
            Auth::user()->notify(new RegularizeNotification($doc));
        }

        // Notas
        $notas =  DB::table('nota_electronica')
        ->select(
            'nota_electronica.*',
        )
        ->whereIn('nota_electronica.tipDocAfectado', ['01', '03'])
        ->where('nota_electronica.estado', '!=', 'ANULADO')
        ->where('nota_electronica.sunat', '0')
        ->orderBy("id","DESC");

        if (!PuntoVenta() && !FullAccess()) {
            $notas = $notas->where('user_id', Auth::user()->id);
        }

        $notas = $notas->orderBy('nota_electronica.id', 'desc')->get();
        foreach ($notas as $nota) {
            Auth::user()->notify(new NotaNotification($nota));
        }

        // Guia
        $guias =  DB::table('guias_remision')
        ->select(
            'guias_remision.*',
        )
        ->where('guias_remision.estado', '!=', 'NULO')
        ->where('guias_remision.sunat', '0')
        ->orderBy("id","DESC");

        if (!PuntoVenta() && !FullAccess()) {
            $guias = $guias->where('user_id', Auth::user()->id);
        }

        $guias = $guias->orderBy('guias_remision.id', 'desc')->get();
        foreach ($guias as $guia) {
            Auth::user()->notify(new GuiaNotification($guia));
        }

        // Retenciones
        $retenciones =  DB::table('retencions')
        ->join('cotizacion_documento', 'retencions.documento_id', '=', 'cotizacion_documento.id')
        ->select(
            'retencions.*',
        )
        ->where('cotizacion_documento.estado', '!=', 'ANULADO')
        ->where('retencions.sunat', '0')
        ->where('cotizacion_documento.sunat', '1')
        ->orderBy("id","DESC");

        if (!PuntoVenta() && !FullAccess()) {
            $retenciones = $retenciones->where('cotizacion_documento.user_id', Auth::user()->id);
        }

        $retenciones = $retenciones->orderBy('cotizacion_documento.id', 'desc')->get();
        foreach ($retenciones as $retencion) {
            Auth::user()->notify(new RetencionNotification($retencion));
        }
    }
}

if (!function_exists('ifComprobanteSeleccionado')) {
    function ifComprobanteSeleccionado($id)
    {
        $facturaciones = Numeracion::where('estado', 'ACTIVO')->get();
        $existe = false;
        foreach ($facturaciones as $fac) {
            if ($fac->tipo_comprobante == $id) {
                $existe = true;
            }
        }

        return $existe;
    }
}

if (!function_exists('codigoPrecioMenor')) {
    function codigoPrecioMenor()
    {
        $empresa = Empresa::first();
        return $empresa;
    }
}


if (!function_exists('FechaActual')) {
    function FechaActual()
    {
        $fecha_hoy = Carbon::now()->toDateString();
        $fecha = Carbon::createFromFormat('Y-m-d', $fecha_hoy);
        $fecha = Carbon::parse($fecha);
        $fecha = $fecha->format("Y-m-d");
        return $fecha;
    }
}




