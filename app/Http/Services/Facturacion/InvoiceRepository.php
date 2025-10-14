<?php

namespace App\Http\Services\Facturacion;

use App\Greenter\Utils\Util;
use App\Ventas\Documento\Documento;
use Greenter\Model\Sale\Invoice;
use Greenter\See;

class InvoiceRepository
{

    public function guardarXml(Invoice $invoice, Util $util, See $see, Documento $documento)
    {
        $util->writeXml($invoice, $see->getFactory()->getLastXml(), $documento->tipo_venta_id, null);

        if ($documento->tipo_venta_id   ==  127) {
            $documento->ruta_xml      =   'storage/greenter/facturas/xml/' . $invoice->getName() . '.xml';
        }
        if ($documento->tipo_venta_id   ==  128) {
            $documento->ruta_xml      =   'storage/greenter/boletas/xml/' . $invoice->getName() . '.xml';
        }

        $documento->update();
    }

    public function guardarCdr(Invoice $invoice, Documento $documento, $cdr, Util $util, $res): string
    {
        $documento->cdr_response_description    =   $cdr->getDescription();
        $documento->cdr_response_id             =   $cdr->getId();
        $documento->cdr_response_code           =   $cdr->getCode();
        $documento->cdr_response_reference      =   $cdr->getReference();
        $documento->cdr_response_notes          =   '|' . implode('|', $cdr->getNotes()) . '|';

        if ($documento->tipo_venta_id   ==  127) {
            $documento->ruta_cdr      =   'storage/greenter/facturas/cdr/' . $invoice->getName() . '.zip';
        }
        if ($documento->tipo_venta_id   ==  128) {
            $documento->ruta_cdr      =   'storage/greenter/boletas/cdr/' . $invoice->getName() . '.zip';
        }

        $documento->sunat                       =   "1";

        if ($cdr->getCode() != '0') {
            $documento->regularize              =   '1';
        }

        $documento->update();

        $util->writeCdr($invoice, $res->getCdrZip(), $documento->tipo_venta_id, null);

        $message    =   $documento->cdr_response_code . ' | ' . $documento->cdr_response_description;
        return $message;
    }

    public function guardarResNotSuccess( Documento $documento, $res): string
    {
        $documento->response_error_message  =   $res->getError()->getMessage();
        $documento->response_error_code     =   $res->getError()->getCode();
        $documento->update();

        /*
                    ================================================================
                        ERROR 1033
                        El comprobante fue registrado previamente con otros datos
                        - Detalle: xxx.xxx.xxx value='ticket: 202413738761966
                        error: El comprobante B001-1704 fue informado anteriormente'

                        ERROR 2223
                        El documento ya fue informado
                    ================================================================
                    */

        if ($res->getError()->getCode() == 1033 || $res->getError()->getCode() == 2223) {
            $documento->response_error_message  =   $res->getError()->getMessage();
            $documento->response_error_code     =   $res->getError()->getCode();
            $documento->regularize              =   '0';
            $documento->sunat                   =   '1';
            $documento->update();
        }

        $message    =   $documento->response_error_code.' | '.$documento->response_error_message;
        return $message;
    }
}
