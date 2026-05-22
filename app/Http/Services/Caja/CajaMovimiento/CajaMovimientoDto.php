<?php

namespace App\Http\Services\Caja\CajaMovimiento;

class CajaMovimientoDto
{
    public string $caja;
    public string $colaborador;
    public float  $montoInicial;
    public array  $resumenMetodos;
    public array  $resumenEfectivo;
    public float  $totalVentaDia;     // ventas contado bruto (todos los métodos)
    public float  $ingresos;
    public float  $egresos;
    public float  $saldo;
    public float  $saldoConsolidado;  // montoInicial + ingresos(todos) − egresos(todos)

    public function toArray(): array
    {
        return [
            'caja'             => $this->caja,
            'colaborador'      => $this->colaborador,
            'monto_inicial'    => $this->montoInicial,
            'resumenMetodos'   => $this->resumenMetodos,
            'resumenEfectivo'  => $this->resumenEfectivo,
            'total_venta_dia'   => $this->totalVentaDia,
            'ingresos'          => $this->ingresos,
            'egresos'           => $this->egresos,
            'saldo'             => $this->saldo,
            'saldo_consolidado' => $this->saldoConsolidado,
        ];
    }
}
