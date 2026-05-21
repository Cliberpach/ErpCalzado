<?php

namespace App\Exports\Pedido\Detalles;

use App\Http\Services\Pedidos\Detalle\DetalleManager;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PedidoDetalleExport implements FromView, ShouldAutoSize
{
    private array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $manager  = new DetalleManager();
        $detalles = $manager->getDetalles($this->filters);

        return view('pedidos.detalles.reports.excel_detalle', [
            'detalles' => $detalles,
        ]);
    }
}
