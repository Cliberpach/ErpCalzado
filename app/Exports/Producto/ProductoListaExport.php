<?php

namespace App\Exports\Producto;

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

class ProductoListaExport implements WithEvents,WithColumnWidths,WithTitle,HasReferencesToOtherSheets
{
    function title():String{
        return "detalles";
    }
    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 25,
            'C' => 30,
            'D' => 40,
        ];
    }
    function  registerEvents(): array
    {
        return [
            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function (AfterSheet $event) {
                $unidades= unidad_medida();
                $event->sheet->setCellValue('A1','Unidades');
                $i=2;
                foreach ($unidades as $key => $unidad) {
                    $event->sheet->setCellValue('A' . $i,  $unidad->simbolo.'-'.$unidad->descripcion );
                    $i++;
                }
                //------------------------
                $categorias= Categoria::where('estado','ACTIVO')->get();
                $event->sheet->setCellValue('B1','Categorias');
                $i=2;
                foreach ($categorias as $key => $categoria) {
                    $event->sheet->setCellValue('B' . $i, $categoria->descripcion);
                    $i++;
                }
                //------------------------
                $marcas= Marca::where('estado', 'ACTIVO')->get();
                $event->sheet->setCellValue('C1','Marcas');
                $i=2;
                foreach ($marcas as $key => $marca) {
                    $event->sheet->setCellValue('C' . $i, $marca->marca);
                    $i++;
                }
                //------------------------
                $almacenes= Almacen::where('estado', 'ACTIVO')->get();
                $event->sheet->setCellValue('D1','Almacenes');
                $i=2;
                foreach ($almacenes as $key => $almacen) {
                    $event->sheet->setCellValue('D' . $i, $almacen->descripcion);
                    $i++;
                }

            }
        ];
    }
}
