<?php

namespace App\Exports\Producto;

use App\Almacenes\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CodigoBarra implements FromCollection,WithHeadings,ShouldAutoSize, WithDrawings
{
    public $producto;

    public function headings(): array
    {
        return [
            'PRODUCTO',
            'CODIGO_BARRA',
        ];
    }

    use Exportable;

    function title(): String
    {
        return "CodigoBarraProducto";
    }

    public function __construct($producto)
    {
        $this->producto = $producto;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $productos = Producto::select('nombre','codigo_barra')->where('id',$this->producto->id)->get();
        return $productos;
    }

    public function drawings() {
        return $this->collection()->map(function($producto, $index) {
            $drawing = new Drawing();
            $drawing->setPath(public_path('/storage/productos/'.$producto->codigo_barra.'.png'));
            $drawing->setHeight(90);
            $drawing->setCoordinates('C'.(2));
            return $drawing;
        })->toArray();
    }
}
