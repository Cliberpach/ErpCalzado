<?php

namespace App\Http\Services\Mantenimiento\TipoPago;

use App\Mantenimiento\TipoPago\TipoPago;
use App\Mantenimiento\TipoPago\TipoPagoCuenta;
use Illuminate\Support\Facades\DB;

class TipoPagoRepository
{
    public function store(array $dto): TipoPago
    {
        return TipoPago::create($dto);
    }

    public function update(array $dto, int $id): TipoPago
    {
        $tipo_pago = TipoPago::findOrFail($id);
        $tipo_pago->update($dto);
        return $tipo_pago;
    }

    public function destroy(int $id): void
    {
        $tipo_pago         = TipoPago::findOrFail($id);
        $tipo_pago->estado = 'ANULADO';
        $tipo_pago->save();
    }

    public function asignarCuentasStore(int $tipo_pago_id, array $cuenta_ids): void
    {
        DB::delete('DELETE FROM tipo_pago_cuentas WHERE tipo_pago_id = ?', [$tipo_pago_id]);

        foreach ($cuenta_ids as $cuenta_id) {
            $registro               = new TipoPagoCuenta();
            $registro->tipo_pago_id = $tipo_pago_id;
            $registro->cuenta_id    = $cuenta_id;
            $registro->save();
        }
    }
}
