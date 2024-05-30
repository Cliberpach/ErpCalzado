<?php

namespace App\Http\Controllers\Ventas\Electronico;

use App\Events\DocumentoNumeracion;
use App\Events\DocumentoNumeracionContingencia;
use App\Events\NotifySunatEvent;
use App\Http\Controllers\Controller;
use App\Mantenimiento\Condicion;
use App\Mantenimiento\Empresa\Empresa;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Luecano\NumeroALetras\NumeroALetras;
use stdClass;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

use Greenter\Model\Response\BillResult;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;

use Greenter\Ws\Services\SunatEndpoints;

use DateTime;
use App\Greenter\Utils\Util;

class ComprobanteController extends Controller
{
    public function index()
    {
        return view('ventas.comprobantes.index');
    }

    public function getVouchers(){

        $documentos = Documento::where('sunat',"1")->orderBy('id','DESC')->get();

        $coleccion = collect([]);
        foreach($documentos as $documento){

            $coleccion->push([
                'id' => $documento->id,
                'numero' => $documento->serie.'-'.$documento->correlativo,
                'tipo_venta' => $documento->descripcionTipo(),
                'cliente' => $documento->tipo_documento_cliente.': '.$documento->documento_cliente.' - '.$documento->cliente,
                'empresa' => $documento->empresa,
                'fecha_documento' =>  Carbon::parse($documento->fecha_documento)->format( 'd/m/Y'),
                'total' => 'S/. '.number_format($documento->total, 2, '.', ''),
                'ruta_comprobante_archivo' => $documento->ruta_comprobante_archivo,
                'nombre_comprobante_archivo' => $documento->nombre_comprobante_archivo,
                'sunat' => $documento->sunat,
            ]);
        }
        return DataTables::of($coleccion)->toJson();
    }

    public function obtenerLeyenda($documento)
    {
        $formatter = new NumeroALetras();
        $convertir = $formatter->toInvoice($documento->total_pagar, 2, 'SOLES');

        //CREAR LEYENDA DEL COMPROBANTE
        $arrayLeyenda = Array();
        $arrayLeyenda[] = array(
            "code" => "1000",
            "value" => $convertir
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

    public function obtenerCuotas($id)
    {
        $documento = Documento::find($id);
        $arrayCuotas = Array();
        $condicion = Condicion::find($documento->condicion_id);
        if(strtoupper($condicion->descripcion) == 'CREDITO' || strtoupper($condicion->descripcion) == 'CRÉDITO')
        {
            $arrayCuotas[] = array(
                "moneda" => "PEN",
                "monto" => (float)$documento->total,
                "fechaPago" => self::obtenerFechaVencimiento($documento)

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
        $fecha = $fecha_emision.'T'.$hora_emision.'-05:00';

        return $fecha;
    }

    public function obtenerFechaEmision($documento)
    {
        $date = strtotime($documento->fecha_documento);
        $fecha_emision = date('Y-m-d', $date);
        $hora_emision = date('H:i:s', $date);
        $fecha = $fecha_emision.'T'.$hora_emision.'-05:00';

        return $fecha;
    }

    public function obtenerFechaVencimiento($documento)
    {
        $date = strtotime($documento->fecha_vencimiento);
        $fecha_emision = date('Y-m-d', $date);
        $hora_emision = date('H:i:s', $date);
        $fecha = $fecha_emision.'T'.$hora_emision.'-05:00';

        return $fecha;
    }

    public function sunat_antiguo($id){
 // try
        // {
        //     $documento = Documento::findOrFail($id);
        //     //OBTENER CORRELATIVO DEL COMPROBANTE ELECTRONICO
        //     $existe = event(new DocumentoNumeracion($documento));
        //     if($existe[0]){
        //         if ($existe[0]->get('existe') == true) {
        //             if ($documento->sunat != '1') {
        //                 //ARREGLO COMPROBANTE
        //                 $arreglo_comprobante = array(
        //                     "tipoOperacion" => $documento->tipoOperacion(),
        //                     "tipoDoc"=> $documento->tipoDocumento(),
        //                     "serie" => $existe[0]->get('numeracion')->serie,
        //                     "correlativo" => $documento->correlativo,
        //                     "fechaEmision" => self::obtenerFechaEmision($documento),
        //                     "fecVencimiento" => self::obtenerFechaVencimiento($documento),
        //                     "observacion" => $documento->observacion,
        //                     "formaPago" => array(
        //                         "moneda" =>  $documento->simboloMoneda(),
        //                         "tipo" =>  $documento->forma_pago(),
        //                         "monto" => (float)$documento->total,
        //                     ),
        //                     "cuotas" => self::obtenerCuotas($documento->id),
        //                     "tipoMoneda" => $documento->simboloMoneda(),
        //                     "client" => array(
        //                         "tipoDoc" => $documento->tipoDocumentoCliente(),
        //                         "numDoc" => $documento->documento_cliente,
        //                         "rznSocial" => $documento->cliente,
        //                         "address" => array(
        //                             "direccion" => $documento->direccion_cliente,
        //                         )
        //                     ),
        //                     "company" => array(
        //                         "ruc" =>  $documento->ruc_empresa,
        //                         "razonSocial" => $documento->empresa,
        //                         "address" => array(
        //                             "direccion" => $documento->direccion_fiscal_empresa,
        //                             "provincia" =>  "TRUJILLO",
        //                             "departamento"=> "LA LIBERTAD",
        //                             "distrito"=> "TRUJILLO",
        //                             "ubigueo"=> "130101"
        //                         )),
    
        //                     //"mtoOperGravadas" => (float)$documento->sub_total,
        //                     "mtoOperGravadas" => (float)$documento->total, //=== nuestro subtotal ===
        //                     "mtoOperExoneradas" => 0,
        //                     "mtoIGV" => (float)$documento->total_igv,
        //                     // "valorVenta" => (float)$documento->sub_total,
        //                     "valorVenta" => (float)$documento->total, //=== nuestro subtotal ===
        //                     "totalImpuestos" => (float)$documento->total_igv,
        //                     // "subTotal" => (float)$documento->total + ($documento->retencion ? $documento->retencion->impRetenido : 0),
        //                     // "mtoImpVenta" => (float)$documento->total + ($documento->retencion ? $documento->retencion->impRetenido : 0),
        //                     "subTotal" => (float)$documento->total_pagar + ($documento->retencion ? $documento->retencion->impRetenido : 0),
        //                     "mtoImpVenta" => (float)$documento->total_pagar + ($documento->retencion ? $documento->retencion->impRetenido : 0),
                    
        //                     "ublVersion" => "2.1",
        //                     "details" => self::obtenerProductos($documento->id),
        //                     "legends" =>  self::obtenerLeyenda($documento),
        //                 );

        //                 // return $arreglo_comprobante;
        //                 //OBTENER JSON DEL COMPROBANTE EL CUAL SE ENVIARA A SUNAT
        //                 $data = enviarComprobanteapi(json_encode($arreglo_comprobante), $documento->empresa_id);

        //                 //RESPUESTA DE LA SUNAT EN JSON
        //                 $json_sunat = json_decode($data);

        //                 if ($json_sunat->sunatResponse->success == true) {

        //                     if($json_sunat->sunatResponse->cdrResponse->code == "0")
        //                     {
        //                         $documento->sunat = '1';
        //                         $respuesta_cdr = json_encode($json_sunat->sunatResponse->cdrResponse, true);
        //                         $respuesta_cdr = json_decode($respuesta_cdr, true);
        //                         $documento->getCdrResponse = $respuesta_cdr;

        //                         $data_comprobante = generarComprobanteapi(json_encode($arreglo_comprobante), $documento->empresa_id);
        //                         $name = $documento->serie."-".$documento->correlativo.'.pdf';

        //                         $data_cdr = base64_decode($json_sunat->sunatResponse->cdrZip);
        //                         $name_cdr = 'R-'.$documento->serie."-".$documento->correlativo.'.zip';

        //                         if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'sunat'))) {
        //                             mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'sunat'));
        //                         }

        //                         if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'cdr'))) {
        //                             mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'cdr'));
        //                         }

        //                         $pathToFile = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'sunat'.DIRECTORY_SEPARATOR.$name);
        //                         $pathToFile_cdr = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'cdr'.DIRECTORY_SEPARATOR.$name_cdr);

        //                         file_put_contents($pathToFile, $data_comprobante);
        //                         file_put_contents($pathToFile_cdr, $data_cdr);

        //                         $arreglo_qr = array(
        //                             "ruc" => $documento->ruc_empresa,
        //                             "tipo" => $documento->tipoDocumento(),
        //                             "serie" => $documento->serie,
        //                             "numero" => $documento->correlativo,
        //                             "emision" => self::obtenerFechaEmision($documento),
        //                             "igv" => 18,
        //                             "total" => (float)$documento->total,
        //                             "clienteTipo" => $documento->tipoDocumentoCliente(),
        //                             "clienteNumero" => $documento->documento_cliente
        //                         );

        //                         /********************************/
        //                         $data_qr = generarQrApi(json_encode($arreglo_qr), $documento->empresa_id);

        //                         $name_qr = $documento->serie."-".$documento->correlativo.'.svg';

        //                         if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'qrs'))) {
        //                             mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'qrs'));
        //                         }

        //                         $pathToFile_qr = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'qrs'.DIRECTORY_SEPARATOR.$name_qr);

        //                         file_put_contents($pathToFile_qr, $data_qr);

        //                         /********************************/

        //                         $data_xml = generarXmlapi(json_encode($arreglo_comprobante), $documento->empresa_id);
        //                         $name_xml = $documento->serie.'-'.$documento->correlativo.'.xml';
        //                         $pathToFile_xml = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.$name_xml);
        //                         if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'xml'))) {
        //                             mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'xml'));
        //                         }
        //                         file_put_contents($pathToFile_xml, $data_xml);

        //                         /********************************* */

        //                         $documento->nombre_comprobante_archivo = $name;
        //                         $documento->hash = $json_sunat->hash;
        //                         $documento->xml = $name_xml;
        //                         $documento->ruta_comprobante_archivo = 'public/sunat/'.$name;
        //                         $documento->ruta_qr = 'public/qrs/'.$name_qr;
        //                         $documento->update();


        //                         //Registro de actividad
        //                         $descripcion = "SE AGREGÓ EL COMPROBANTE ELECTRONICO: ". $documento->serie."-".$documento->correlativo;
        //                         $gestion = "COMPROBANTES ELECTRONICOS";
        //                         crearRegistro($documento , $descripcion , $gestion);

        //                         Session::flash('success', 'Documento de Venta enviada a Sunat con exito.');
        //                         Session::flash('sunat_exito', '1');
        //                         Session::flash('id_sunat', $json_sunat->sunatResponse->cdrResponse->id);
        //                         Session::flash('descripcion_sunat', $json_sunat->sunatResponse->cdrResponse->description,);
        //                         return redirect()->route('ventas.documento.index')->with('sunat_exito', 'success');
        //                     }
        //                     else
        //                     {
        //                         $documento->sunat = '0';
        //                         $id_sunat = $json_sunat->sunatResponse->cdrResponse->code;
        //                         $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;

        //                         $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
        //                         $respuesta_error = json_decode($respuesta_error, true);
        //                         $documento->getCdrResponse = $respuesta_error;

        //                         $documento->update();
        //                         Session::flash('error', 'Documento de Venta sin exito en el envio a sunat.');
        //                         Session::flash('sunat_error', '1');
        //                         Session::flash('id_sunat', $id_sunat);
        //                         Session::flash('descripcion_sunat', $descripcion_sunat);
        //                         return redirect()->route('ventas.documento.index')->with('sunat_error', 'error');
        //                     }

        //                 }else{

        //                     //COMO SUNAT NO LO ADMITE VUELVE A SER 0
        //                     $documento->sunat = '0';
        //                     $documento->regularize = '1';

        //                     if ($json_sunat->sunatResponse->error) {
        //                         $id_sunat = $json_sunat->sunatResponse->error->code;
        //                         $descripcion_sunat = $json_sunat->sunatResponse->error->message;

        //                         $obj_erro = new stdClass();
        //                         $obj_erro->code = $json_sunat->sunatResponse->error->code;
        //                         $obj_erro->description = $json_sunat->sunatResponse->error->message;
        //                         $respuesta_error = json_encode($obj_erro, true);
        //                         $respuesta_error = json_decode($respuesta_error, true);
        //                         $documento->getRegularizeResponse = $respuesta_error;


        //                     }else {
        //                         $id_sunat = $json_sunat->sunatResponse->cdrResponse->id;
        //                         $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;

        //                         $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
        //                         $respuesta_error = json_decode($respuesta_error, true);
        //                         $documento->getCdrResponse = $respuesta_error;
        //                     };

        //                     $documento->update();
        //                     Session::flash('error', 'Documento de Venta sin exito en el envio a sunat.');
        //                     Session::flash('sunat_error', '1');
        //                     Session::flash('id_sunat', $id_sunat);
        //                     Session::flash('descripcion_sunat', $descripcion_sunat);
        //                     return redirect()->route('ventas.documento.index')->with('sunat_error', 'error');
        //                 }
        //             }else{
        //                 $documento->sunat = '1';
        //                 $documento->update();
        //                 Session::flash('error','Documento de venta fue enviado a Sunat.');
        //                 return redirect()->route('ventas.documento.index')->with('sunat_existe', 'error');
        //             }
        //         }else{
        //             Session::flash('error','Tipo de Comprobante no registrado en la empresa.');
        //             return redirect()->route('ventas.documento.index')->with('sunat_existe', 'error');
        //         }
        //     }else{
        //         Session::flash('error','Empresa sin parametros para emitir comprobantes electronicos');
        //         return redirect()->route('ventas.documento.index');
        //     }
        // }
        // catch(Exception $e)
        // {
        //     $documento = Documento::findOrFail($id);
        //     $documento->regularize = '1';
        //     $documento->sunat = '0';
        //     $obj_erro = new stdClass();
        //     $obj_erro->code = 6;
        //     $obj_erro->description = $e->getMessage();
        //     $respuesta_error = json_encode($obj_erro, true);
        //     $respuesta_error = json_decode($respuesta_error, true);
        //     $documento->getRegularizeResponse = $respuesta_error;
        //     $documento->update();
        //     Session::flash('error', 'No se puede conectar con el servidor, porfavor intentar nuevamente.'); //$e->getMessage()
        //     return redirect()->route('ventas.documento.index');
        // }
    }

    public function sunat($id)
    {
        
        try {
            //====== VERIFICAR SI EL COMPROBANTE ESTÁ ACTIVO EN LA EMPRESA =========
            $documento  =   Documento::findOrFail($id);
            $existe     =   DB::select('select * from empresa_numeracion_facturaciones as enf
                            where enf.tipo_comprobante=?
                            and enf.estado="ACTIVO"',[$documento->tipo_venta]);

            if(count($existe) === 1){

                $tipo_documento_cliente =   null;
                $tipo_doc_facturacion   =   null;

                if($documento->tipo_venta   ==  127){   //====== FACTURA ====
                    $tipo_documento_cliente =   '6';    //======= RUC ====
                    $tipo_doc_facturacion   =   '01';
                }
                if($documento->tipo_venta   ==  128){   //======== BOLETA =====
                    $tipo_documento_cliente =   '1';    //====== DNI ======
                    $tipo_doc_facturacion   =   '03';
                }

                //======= INSTANCIAMOS LA CLASE UTIL ========
                $util = Util::getInstance();

                //======== INSTANCIAR OBJETO FACTURA O BOLETA ========
                $invoice = new Invoice();
            
                //====== CONSTRUIR CLIENTE =========
                $client = new Client();
                $client->setTipoDoc($tipo_documento_cliente)
                    ->setNumDoc($documento->documento_cliente)
                    ->setRznSocial($documento->cliente)
                    ->setAddress((new Address())
                        ->setDireccion($documento->direccion_cliente))
                    ->setEmail($documento->clienteEntidad->correo_electronico)
                    ->setTelephone($documento->clienteEntidad->telefono_movil);
                    
                //======= CONSTRUIR FACTURA ENCABEZADO ======
                $invoice
                    ->setUblVersion('2.1')
                    ->setFecVencimiento(new DateTime($documento->fecha_vencimiento))
                    ->setTipoOperacion('0101')
                    ->setTipoDoc($tipo_doc_facturacion)
                    ->setSerie($documento->serie)
                    ->setCorrelativo($documento->correlativo)
                    ->setFechaEmision(new DateTime($documento->created_at))
                    ->setFormaPago(new FormaPagoContado())
                    ->setTipoMoneda('PEN')
                    ->setCompany($util->shared->getCompany())
                    ->setClient($client)
                    ->setMtoOperGravadas($documento->total)
                    ->setMtoIGV($documento->total_igv)
                    ->setTotalImpuestos($documento->total_igv)
                    ->setValorVenta($documento->total)
                    ->setSubTotal($documento->total_pagar)
                    ->setMtoImpVenta($documento->total_pagar);

                //======== CONSTRUIR DETALLE FACTURA ========
                $detalles   =   $documento->detalles;
                $items      =   [];

                foreach ($detalles as $detalle) {
                    $items[] = (new SaleDetail())
                                ->setCodProducto($detalle->codigo_producto)
                                ->setUnidad($detalle->unidad)
                                ->setDescripcion($detalle->nombre_modelo.'-'.$detalle->nombre_producto.'-'.$detalle->nombre_color.'-'.$detalle->nombre_talla)
                                ->setCantidad($detalle->cantidad)
                                ->setMtoValorUnitario( (float)$detalle->precio_unitario_nuevo / 1.18)
                                ->setMtoValorVenta(((float)$detalle->precio_unitario_nuevo / 1.18)*(float)$detalle->cantidad)
                                ->setMtoBaseIgv(((float)$detalle->precio_unitario_nuevo / 1.18)*(float)$detalle->cantidad)
                                ->setPorcentajeIgv(18)
                                ->setIgv( (float)$detalle->cantidad * ( (float)$detalle->precio_unitario_nuevo - (float)$detalle->precio_unitario_nuevo / 1.18 ) )
                                ->setTipAfeIgv('10') // Catalog: 07
                                ->setTotalImpuestos((float)$detalle->cantidad * ( (float)$detalle->precio_unitario_nuevo - (float)$detalle->precio_unitario_nuevo / 1.18 ))
                                ->setMtoPrecioUnitario($detalle->precio_unitario_nuevo);
             
                }

                $formatter  = new NumeroALetras();
                $legenda    = $formatter->toInvoice($documento->total_pagar, 2, 'SOLES');

                $invoice->setDetails($items)
                ->setLegends([
                    (new Legend())
                        ->setCode('1000')
                        ->setValue($legenda)
                ]);

                $see = $this->controlConfiguracionGreenter($util);

                $res = $see->send($invoice);
                $util->writeXml($invoice, $see->getFactory()->getLastXml(),$documento->tipo_venta,null);


                //======== ENVÍO CORRECTO Y ACEPTADO ==========
                if($res->isSuccess()){
                    //====== GUARDANDO RESPONSE ======
                    $cdr                                    =   $res->getCdrResponse();
                    $documento->cdr_response_description    =   $cdr->getDescription();
                    $util->writeCdr($invoice, $res->getCdrZip(),$documento->tipo_venta,null);
                    $documento->sunat                        =   "1";
                    $documento->update(); 

                    return response()->json(["success"   =>  true,"message"=>$cdr->getDescription()]);
                }else{
                    $documento->response_error_message  =   $res->getError()->getMessage();
                    $documento->response_error_code     =   $res->getError()->getCode();
                    $documento->regularize              =   '1';
                    $documento->update(); 

                    if($res->getError()->getCode() == 2223){
                        dd($res);
                         return response()->json(["success"   =>  true,"message"=>$cdr->getDescription()]);
                    }

                    throw new Exception("ERROR AL ENVIAR FACTURA A SUNAT. "."CÓDIGO: ".$res->getError()->getCode()
                    .",DESCRIPCIÓN: ".$res->getError()->getMessage());
                }


            }else{
                throw new Exception("NO SE ENCUENTRA ACTIVA LA EMISIÓN DE FACTURAS EN LA EMPRSA");
            }


            
        } catch (\Throwable $th) {
            
            return response()->json(['success'=>false,
            "message"   =>  "ERROR EN EL SERVIDOR",
            "exception"=>$th->getMessage(),
            'line' => $th->getLine(),
            'file' => $th->getFile()
            ]);

        }
        
    }

  
    public function controlConfiguracionGreenter($util){
        //==== OBTENIENDO CONFIGURACIÓN DE GREENTER ======
        $greenter_config    =   DB::select('select gc.ruta_certificado,gc.id_api_guia_remision,gc.modo,
          gc.clave_api_guia_remision,e.ruc,e.razon_social,e.direccion_fiscal,e.ubigeo,
          e.direccion_llegada,gc.sol_user,gc.sol_pass
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
        if(!$greenter_config[0]->sol_pass){
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_PASS');
        }
        if ($greenter_config[0]->modo !== "BETA" && $greenter_config[0]->modo !== "PRODUCCION") {
            throw new Exception('NO SE HA CONFIGURADO EL AMBIENTE BETA O PRODUCCIÓN PARA GREENTER');
        }

        $see    =   null;
        if($greenter_config[0]->modo === "BETA"){
            //===== MODO BETA ======
            $see = $util->getSee(SunatEndpoints::FE_BETA,$greenter_config[0]);
        }

        if($greenter_config[0]->modo === "PRODUCCION"){
            //===== MODO PRODUCCION ======
            $see = $util->getSee(SunatEndpoints::FE_PRODUCCION,$greenter_config[0]);
        }

        if(!$see){
            throw new Exception('ERROR EN LA CONFIGURACIÓN DE GREENTER, SEE ES NULO');
        }

        return $see;
    }


    public function convertirContingencia($id)
    {
        $documento = Documento::findOrFail($id);
        $documento->contingencia = '1';
        $documento->update();
        event(new DocumentoNumeracionContingencia($documento));
        Session::flash('success', 'Documento de venta fue cambiado comprobante de contingencia.');
        return redirect()->route('ventas.documento.index');
    }

    public function sunatContingencia($id)
    {
        try {
            $documento = Documento::findOrFail($id);
            //OBTENER CORRELATIVO DEL COMPROBANTE ELECTRONICO
            $existe = event(new DocumentoNumeracionContingencia($documento));

            if ($existe[0]) {
                if ($existe[0]->get('existe') == true) {
                    if ($documento->sunat_contingencia != '1') {
                        //ARREGLO COMPROBANTE
                        $arreglo_comprobante = array(
                            "tipoOperacion" => $documento->tipoOperacion(),
                            "tipoDoc" => $documento->tipoDocumento(),
                            "serie" => $documento->serie_contingencia,
                            "correlativo" => $documento->correlativo,
                            "fechaEmision" => self::obtenerFechaEmision($documento),
                            "observacion" => $documento->observacion,
                            "formaPago" => array(
                                "moneda" =>  $documento->simboloMoneda(),
                                "tipo" =>  $documento->forma_pago(),
                                "monto" => (float)$documento->total,
                            ),
                            "cuotas" => self::obtenerCuotas($documento->id),
                            "tipoMoneda" => $documento->simboloMoneda(),
                            "client" => array(
                                "tipoDoc" => $documento->tipoDocumentoCliente(),
                                "numDoc" => $documento->documento_cliente,
                                "rznSocial" => $documento->cliente,
                                "address" => array(
                                    "direccion" => $documento->direccion_cliente,
                                )
                            ),
                            "company" => array(
                                "ruc" =>  $documento->ruc_empresa,
                                "razonSocial" => $documento->empresa,
                                "address" => array(
                                    "direccion" => $documento->direccion_fiscal_empresa,
                                )
                            ),
                            "mtoOperGravadas" => (float)$documento->sub_total,
                            "mtoOperExoneradas" => 0,
                            "mtoIGV" => (float)$documento->total_igv,

                            "valorVenta" => (float)$documento->sub_total,
                            "totalImpuestos" => (float)$documento->total_igv,
                            "subTotal" => (float)$documento->total,
                            "mtoImpVenta" => (float)$documento->total,
                            "ublVersion" => "2.1",
                            "details" => self::obtenerProductos($documento->id),
                            "legends" =>  self::obtenerLeyenda($documento),
                        );

                        //return $arreglo_comprobante;
                        //OBTENER JSON DEL COMPROBANTE EL CUAL SE ENVIARA A SUNAT
                        $data = enviarComprobanteapi(json_encode($arreglo_comprobante), $documento->empresa_id);

                        //RESPUESTA DE LA SUNAT EN JSON
                        $json_sunat = json_decode($data);

                        if ($json_sunat->sunatResponse->success == true) {

                            if ($json_sunat->sunatResponse->cdrResponse->code == "0") {
                                $documento->sunat_contingencia = '1';
                                $respuesta_cdr = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                                $respuesta_cdr = json_decode($respuesta_cdr, true);
                                $documento->getCdrResponse_contingencia = $respuesta_cdr;

                                $data_comprobante = generarComprobanteapi(json_encode($arreglo_comprobante), $documento->empresa_id);
                                $name = $documento->serie_contingencia . "-" . $documento->correlativo . '.pdf';

                                $data_cdr = base64_decode($json_sunat->sunatResponse->cdrZip);
                                $name_cdr = 'R-' . $documento->serie_contingencia . "-" . $documento->correlativo . '.zip';

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
                                    "serie" => $documento->serie_contingencia,
                                    "numero" => $documento->correlativo,
                                    "emision" => self::obtenerFechaEmision($documento),
                                    "igv" => 18,
                                    "total" => (float)$documento->total,
                                    "clienteTipo" => $documento->tipoDocumentoCliente(),
                                    "clienteNumero" => $documento->documento_cliente
                                );

                                /********************************/
                                $data_qr = generarQrApi(json_encode($arreglo_qr), $documento->empresa_id);

                                $name_qr = $documento->serie_contingencia . "-" . $documento->correlativo . '.svg';

                                if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'))) {
                                    mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs'));
                                }

                                $pathToFile_qr = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'qrs' . DIRECTORY_SEPARATOR . $name_qr);

                                file_put_contents($pathToFile_qr, $data_qr);

                                /********************************/

                                $data_xml = generarXmlapi(json_encode($arreglo_comprobante), $documento->empresa_id);
                                $name_xml = $documento->serie_contingencia . '-' . $documento->correlativo . '.xml';
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
                                $descripcion = "SE AGREGÓ EL COMPROBANTE ELECTRONICO: " . $documento->serie_contingencia . "-" . $documento->correlativo;
                                $gestion = "COMPROBANTES ELECTRONICOS";
                                crearRegistro($documento, $descripcion, $gestion);

                                Session::flash('success', 'Documento de Venta enviada a Sunat con exito.');
                                return view('ventas.documentos.index', [

                                    'id_sunat' => $json_sunat->sunatResponse->cdrResponse->id,
                                    'descripcion_sunat' => $json_sunat->sunatResponse->cdrResponse->description,
                                    'notas_sunat' => $json_sunat->sunatResponse->cdrResponse->notes,
                                    'sunat_exito' => true

                                ])->with('sunat_exito', 'success');
                            } else {
                                $documento->sunat_contingencia = '0';
                                $id_sunat = $json_sunat->sunatResponse->cdrResponse->code;
                                $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;

                                $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                                $respuesta_error = json_decode($respuesta_error, true);
                                $documento->getCdrResponse_contingencia = $respuesta_error;

                                $documento->update();
                                Session::flash('error', 'Documento de Venta sin exito en el envio a sunat.');
                                $dato = "Message";
                                broadcast(new NotifySunatEvent($dato));
                                return view('ventas.documentos.index', [
                                    'id_sunat' =>  $id_sunat,
                                    'descripcion_sunat' =>  (string)$descripcion_sunat,
                                    'sunat_error' => true,

                                ])->with('sunat_error', 'error');
                            }
                        } else {

                            //COMO SUNAT NO LO ADMITE VUELVE A SER 0
                            $documento->sunat_contingencia = '0';

                            if ($json_sunat->sunatResponse->error) {
                                $id_sunat = $json_sunat->sunatResponse->error->code;
                                $descripcion_sunat = $json_sunat->sunatResponse->error->message;

                                $obj_erro = new stdClass();
                                $obj_erro->code = $json_sunat->sunatResponse->error->code;
                                $obj_erro->description = $json_sunat->sunatResponse->error->message;
                                $respuesta_error = json_encode($obj_erro, true);
                                $respuesta_error = json_decode($respuesta_error, true);
                                $documento->getRegularizeResponse_contingencia = $respuesta_error;
                            } else {
                                $id_sunat = $json_sunat->sunatResponse->cdrResponse->id;
                                $descripcion_sunat = $json_sunat->sunatResponse->cdrResponse->description;

                                $respuesta_error = json_encode($json_sunat->sunatResponse->cdrResponse, true);
                                $respuesta_error = json_decode($respuesta_error, true);
                                $documento->getCdrResponse_contingencia = $respuesta_error;
                            };

                            $documento->update();
                            Session::flash('error', 'Documento de Venta sin exito en el envio a sunat.');
                            $dato = "Message";
                            broadcast(new NotifySunatEvent($dato));
                            return view('ventas.documentos.index', [
                                'id_sunat' =>  $id_sunat,
                                'descripcion_sunat' =>  $descripcion_sunat,
                                'sunat_error' => true,

                            ])->with('sunat_error', 'error');
                        }
                    } else {
                        $documento->sunat_contingencia = '1';
                        $documento->update();
                        Session::flash('error', 'Documento de venta fue enviado a Sunat.');
                        return redirect()->route('ventas.documento.index')->with('sunat_existe', 'error');
                    }
                } else {
                    Session::flash('error', 'Tipo de Comprobante no registrado en la empresa.');
                    return redirect()->route('ventas.documento.index')->with('sunat_existe', 'error');
                }
            } else {
                Session::flash('error', 'Empresa sin parametros para emitir comprobantes electronicos');
                return redirect()->route('ventas.documento.index');
            }
        } catch (Exception $e) {
            $documento = Documento::findOrFail($id);
            $documento->sunat_contingencia = '0';
            $obj_erro = new stdClass();
            $obj_erro->code = 6;
            $obj_erro->description = $e->getMessage();
            $respuesta_error = json_encode($obj_erro, true);
            $respuesta_error = json_decode($respuesta_error, true);
            $documento->getRegularizeResponse_contingencia = $respuesta_error;
            $documento->update();
            Session::flash('error', 'No se puede conectar con el servidor, porfavor intentar nuevamente.'); //$e->getMessage()
            return redirect()->route('ventas.documento.index');
        }
    }

    public function cdr($id)
    {

        try
        {
            $documento = Documento::findOrFail($id);
            $json_data = json_decode($documento->getRegularizeResponse, false);
            if($documento->regularize == '1' && $json_data->code == '1033')
            {
                $documento->regularize = '0';
                $documento->sunat = '1';
                $documento->update();
                Session::flash('success','Documento de Venta regularizado con exito.');
                return view('ventas.documentos.index',[

                    'id_sunat' => $documento->serie.'-'.$documento->correlativo,
                    'descripcion_sunat' => 'CDR regularizado.',
                    'notas_sunat' => '',
                    'sunat_exito' => true

                ])->with('sunat_exito', 'success');
            }
            else
            {
                Session::flash('error','Este documento tiene un error diferente al CDR, intentar enviar a sunat.');
                return redirect()->route('ventas.documento.index')->with('sunat_existe', 'error');
            }
        }
        catch(Exception $e)
        {
            Session::flash('error', 'No se puede conectar con el servidor, porfavor intentar nuevamente.'); //$e->getMessage()
            return redirect()->route('ventas.documento.index');
        }

    }

    public function email(Request $request)
    {
        try
        {
            $id = $request->id;
            $correo = $request->correo;
            $documento = Documento::findOrFail($id);
            $detalles = Detalle::where('documento_id',$id)->where('eliminado','0')->get();
            $empresa = Empresa::first();
            $legends = self::obtenerLeyenda($documento);
            $legends = json_encode($legends,true);
            $legends = json_decode($legends,true);

            $pdf = PDF::loadview('ventas.documentos.impresion.comprobante_normal',[
                'documento' => $documento,
                'detalles' => $detalles,
                'moneda' => $documento->simboloMoneda(),
                'empresa' => $empresa,
                "legends" =>  $legends,
                ])->setPaper('a4')->setWarnings(false);
                
            Mail::send('ventas.documentos.mail.cliente_mail',compact("documento"), function ($mail) use ($pdf,$documento,$correo) {
                $mail->to($correo);
                $mail->subject($documento->nombreTipo());
                $mail->attachdata($pdf->output(), $documento->serie.'-'.$documento->correlativo.'.pdf');
                if($documento->tipo_venta != '129' && $documento->sunat == '1')
                {
                    $mail->attach(base_path().'/storage/app/public/cdr/R-'.$documento->serie.'-'.$documento->correlativo.'.zip');
                }
                $mail->from('facturacion@siscomfac.com','SiScOmFaC');
            });

            return response()->json([
                'success' => true,
                'message' => 'El correo se envio con exito.'
            ]);
        }
        catch(Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'No se puede conectar con el servidor, porfavor intentar nuevamente.'
            ]);
        }
    }
}
