<?php

namespace App\Exports\Reportes\PI;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class Producto_PI implements FromView
{
    protected $data;
    protected $filters;
    protected $empresa;

    public function __construct($data,$filters,$empresa)
    {
        $this->data     =   $data;
        $this->filters  =   $filters;
        $this->empresa  =   $empresa;
    }

    public function view(): View
    {
        return view('reportes.almacenes.producto.excel.excel', [
            'productos' =>  $this->data,
            'filters'   =>  $this->filters,
            'empresa'   =>  $this->empresa
        ]);
    }

}
