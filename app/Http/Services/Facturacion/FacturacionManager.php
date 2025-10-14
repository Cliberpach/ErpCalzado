<?php

namespace App\Http\Services\Facturacion;

use App\Ventas\Documento\Documento;

class FacturacionManager
{
    private InvoiceService $s_invoice;

    public function __construct()
    {
        $this->s_invoice    =   new InvoiceService();
    }

    public function sunat_invoice(int $id):array{
        return $this->s_invoice->sunat($id);
    }
}
