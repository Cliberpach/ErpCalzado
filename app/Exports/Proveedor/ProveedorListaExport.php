<?php

namespace App\Exports\Proveedor;

use App\Almacenes\Almacen;
use App\Almacenes\Categoria;
use App\Almacenes\Marca;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;

class ProveedorListaExport implements WithEvents,WithColumnWidths,WithTitle,HasReferencesToOtherSheets
{
    function title():String{
        return "detalles";
    }
    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 25
        ];
    }
    function  registerEvents(): array
    {
        return [
            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function (AfterSheet $event) {
                $tipos = personas();
                $event->sheet->setCellValue('A1','Tipos');
                $i=2;
                foreach ($tipos as $key => $tipo) {
                    $event->sheet->setCellValue('A' . $i, $tipo->descripcion );
                    $i++;
                }
                //------------------------

                $zonas = zonas();
                $event->sheet->setCellValue('B1','Zona');
                $i=2;
                foreach ($zonas as $key => $zona) {
                    $event->sheet->setCellValue('B' . $i, $zona->descripcion);
                    $i++;
                }


            }
        ];
    }
}
