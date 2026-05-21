<?php

namespace App\Http\Services\Mantenimiento\TipoPago;

use App\Mantenimiento\TipoPago\TipoPago;

class TipoPagoManager
{
    private TipoPagoService $s_tipo_pago;

    public function __construct()
    {
        $this->s_tipo_pago = new TipoPagoService();
    }

    public function store(array $data): TipoPago
    {
        return $this->s_tipo_pago->store($data);
    }

    public function update(array $data, int $id): TipoPago
    {
        return $this->s_tipo_pago->update($data, $id);
    }

    public function destroy(int $id): void
    {
        $this->s_tipo_pago->destroy($id);
    }

    public function asignarCuentasStore(int $tipo_pago_id, array $cuenta_ids): void
    {
        $this->s_tipo_pago->asignarCuentasStore($tipo_pago_id, $cuenta_ids);
    }
}
