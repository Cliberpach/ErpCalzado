<?php

namespace App\Http\Services\Facturacion;

use App\Greenter\Utils\Util;
use App\Ventas\Documento\Detalle;
use App\Ventas\Documento\Documento;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\Invoice;
use DateTime;
use Exception;
use Greenter\Model\Sale\Charge;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Prepayment;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    private InvoiceDto $s_invoice_dto;
    private InvoiceRepository $s_invoice_repository;

    public function __construct()
    {
        $this->s_invoice_dto    =   new InvoiceDto();
        $this->s_invoice_repository =   new InvoiceRepository();
    }

    public function sunat(int $id): array
    {
        $dto    =   $this->s_invoice_dto->getDtoSunat($id);

        //======= INSTANCIAMOS LA CLASE UTIL ========
        $util = Util::getInstance();

        //========= CONSTRUIR INVOICE =========
        $invoice    =   $this->build_invoice($util, $dto);
      
        //========= ENVIAR SUNAT ==========
        $see = $this->s_invoice_dto->controlConfiguracionGreenter($util);
        $res = $see->send($invoice);

        //========== GUARDAR XML ==========
        $this->s_invoice_repository->guardarXml($invoice, $util, $see, $dto['documento']);

        $res_operar    =   $this->operarRespuesta($res, $dto['documento'], $invoice, $util);
        return $res_operar;
    }

    public function getCliente(array $dto): Client
    {
        $clienteBD              =   $dto['cliente_bd'];
        $tipo_documento_cliente =   $dto['codigos']['tipo_documento_cliente'];
        $documento              =   $dto['documento'];

        //======= INSTANCIAR CLIENTE =========
        $client = new Client();
        $client->setTipoDoc($tipo_documento_cliente)
            ->setNumDoc($clienteBD->documento)
            ->setRznSocial($documento->cliente)
            ->setAddress((new Address())
                ->setDireccion($documento->direccion_cliente))
            ->setEmail($documento->clienteEntidad->correo_electronico)
            ->setTelephone($documento->clienteEntidad->telefono_movil);

        return $client;
    }

    public function build_invoice(Util $util, array $dto): Invoice
    {
        $documento      =   $dto['documento'];

        //========== INSTANCIAR INVOICE ===========
        $invoice    =   new Invoice();
        $invoice    =   $this->setEncabezado($invoice, $dto, $util);

        //======== SET DESCUENTOS GLOBALES ========
        if (floatval($documento->descuento_global_sunat) > 0) {
            $invoice    =   $this->setDescuentosGlobales($invoice, $documento);
        }

        //======== SET MONTOS ========
        $invoice    =   $this->setMontos($invoice, $documento);

        //========= SET ANTICIPOS ========
        if (floatval($documento->total_anticipos_sunat) > 0) {
            $invoice    =   $this->setAnticipos($invoice, $dto);
        }

        //======== SET DETALLES ========
        $invoice    =   $this->setDetalles($invoice, $dto);

        //========= SET LEGENDA ===========
        $invoice    =   $this->setLegenda($invoice, $documento);

        return $invoice;
    }

    public function setEncabezado(Invoice $invoice, array $dto, Util $util)
    {
        $documento              =   $dto['documento'];
        $tipo_doc_facturacion   =   $dto['codigos']['tipo_doc_facturacion'];
        $client                 =   $this->getCliente($dto);

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
            ->setCompany($util->shared->getCompany($documento->sede_id))
            ->setClient($client);

        return $invoice;
    }

    public function setDescuentosGlobales(Invoice $invoice, Documento $documento): Invoice
    {
        $invoice->setDescuentos([
            (
                new Charge())
                ->setCodTipo('04')
                ->setFactor(1)
                ->setMonto(floatval($documento->descuento_global_sunat)) // anticipo
                ->setMontoBase(floatval($documento->descuento_global_sunat))
        ]);

        return $invoice;
    }

    public function setMontos(Invoice $invoice, Documento $documento): Invoice
    {
        $invoice->setMtoOperGravadas(floatval($documento->mto_oper_gravadas_sunat)) // Sumatoria de Valor Venta (detalles) menos descuentos globales (anticipo): 200 - 100
            ->setMtoIGV(floatval($documento->mto_igv_sunat))
            ->setValorVenta(floatval($documento->valor_venta_sunat)) // sumatoria de valor venta (detalle)
            ->setTotalImpuestos(floatval($documento->total_impuestos_sunat))
            ->setSubTotal(floatval($documento->sub_total_sunat)) // ValorVenta + (sumatoria de valor venta detalle) * 18% (IGV)
            ->setMtoImpVenta(floatval($documento->mto_imp_venta_sunat)); // subTotal - Anticipos: 236 - 100

        return $invoice;
    }

    public function setAnticipos(Invoice $invoice, array $dto): Invoice
    {
        $lst_anticipos          =   $dto['lst_anticipos'];
        $documento              =   $dto['documento'];
        $lst_anticipos_greenter =   [];

        foreach ($lst_anticipos as $anticipo) {

            $doc_anticipo   =   Documento::findOrFail($anticipo->anticipo_id);
            $tipo_doc_rel   =   null;

            if ($doc_anticipo->tipo_venta_id == 127) {
                $tipo_doc_rel = '02';
            }
            if ($doc_anticipo->tipo_venta_id == 128) {
                $tipo_doc_rel = '03';
            }

            $anticipo_greenter      =    (new Prepayment())
                ->setTipoDocRel($tipo_doc_rel) // catalog. 12
                ->setNroDocRel($anticipo->anticipo_serie)
                ->setTotal($doc_anticipo->total_pagar);

            $lst_anticipos_greenter[]   =   $anticipo_greenter;

        }

        $invoice
            ->setAnticipos($lst_anticipos_greenter);
        $invoice->setTotalAnticipos($documento->total_anticipos_sunat);
        return $invoice;
    }

    public function setDetalles(Invoice $invoice, array $dto): Invoice
    {
        $lst_items  =   $dto['lst_items'];
        $details    =   [];

        foreach ($lst_items as $item) {
            $detail = new SaleDetail();
            $detail->setCodProducto($item->codigo_producto)
                ->setUnidad($item->unidad)
                ->setDescripcion($item->nombre_producto)
                ->setCantidad(floatval($item->cantidad))
                ->setMtoValorUnitario((float)$item->precio_unitario_nuevo / 1.18)
                ->setMtoValorVenta(((float)$item->precio_unitario_nuevo / 1.18) * (float)$item->cantidad)
                ->setMtoBaseIgv(((float)$item->precio_unitario_nuevo / 1.18) * (float)$item->cantidad)
                ->setPorcentajeIgv(18)
                ->setIgv((float)$item->cantidad * ((float)$item->precio_unitario_nuevo - (float)$item->precio_unitario_nuevo / 1.18))
                ->setTipAfeIgv('10') // Catalog: 07
                ->setTotalImpuestos((float)$item->cantidad * ((float)$item->precio_unitario_nuevo - (float)$item->precio_unitario_nuevo / 1.18))
                ->setMtoPrecioUnitario($item->precio_unitario_nuevo);

            $details[]  =   $detail;
        }

        $invoice->setDetails($details);

        return $invoice;
    }

    public function setLegenda(Invoice $invoice, Documento $documento): Invoice
    {
        $invoice->setLegends([
            (new Legend())
                ->setCode('1000')
                ->setValue($documento->legenda)
        ]);

        return $invoice;
    }

    public function operarRespuesta($res, Documento $documento, Invoice $invoice, Util $util): array
    {
        $message    =   null;
        $success    =   false;

        if ($res->isSuccess()) {

            //====== GUARDANDO RESPONSE ======
            $cdr        =   $res->getCdrResponse();

            if ($cdr) {
                $message    =   $this->s_invoice_repository->guardarCdr($invoice, $documento, $cdr, $util, $res);
                $success    =   $cdr->getCode() == 0 ? true : false;
            } else {
                $message    =   'ENVIADO, CDR NO RECIBIDO';
            }
        }

        if (!$res->isSuccess()) {
            $message =   $this->s_invoice_repository->guardarResNotSuccess($documento, $res);
        }

        return ['success' => $success, 'message' => $message];
    }
}
