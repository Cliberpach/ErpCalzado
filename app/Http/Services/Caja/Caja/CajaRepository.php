<?php

namespace App\Http\Services\Caja\Caja;

use App\Pos\Caja;
use App\Pos\MovimientoCaja;

class CajaRepository
{
    public function store(string $nombre, int $sedeId): Caja
    {
        $caja = new Caja();
        $caja->nombre  = strtoupper($nombre);
        $caja->sede_id = $sedeId;
        $caja->save();
        return $caja;
    }

    public function update(int $id, string $nombre): Caja
    {
        $caja = Caja::findOrFail($id);
        $caja->nombre = strtoupper($nombre);
        $caja->save();
        return $caja;
    }

    public function destroy(int $id): void
    {
        $caja = Caja::findOrFail($id);
        $caja->estado = 'ANULADO';
        $caja->save();
    }

    public function estaAbierta(int $id): bool
    {
        return MovimientoCaja::where('caja_id', $id)
            ->where('estado_movimiento', 'APERTURA')
            ->exists();
    }
}
