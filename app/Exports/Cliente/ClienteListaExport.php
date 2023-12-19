<?php

namespace App\Exports\Cliente;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;

class ClienteListaExport implements WithEvents,ShouldAutoSize,WithTitle,HasReferencesToOtherSheets
{
    function title():String{
        return "listCombobox";
    }
    function  registerEvents(): array
    {
        return [
            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function (AfterSheet $event) {
                $tipos= tipo_clientes();
                $event->sheet->setCellValue('A1','TiposCliente');
                $i=2;
                foreach ($tipos as $key => $tipo) {
                    $event->sheet->setCellValue('A' . $i, $tipo->descripcion);
                    $i++;
                }
                //------------------------

                $depatamentos= departamentos();
                $event->sheet->setCellValue('B1','Departamentos');
                $i=2;
                foreach ($depatamentos as $key => $departamento) {
                    $event->sheet->setCellValue('B' . $i, $departamento->nombre);
                    $i++;
                }
                //------------------------

                $provincias= provincias();
                $event->sheet->setCellValue('C1','Provincias');
                $i=2;
                foreach ($provincias as $key => $provincia) {
                    $event->sheet->setCellValue('C' . $i, $provincia->nombre);
                    $i++;
                }
                //------------------------

                $distritos= distritos();
                $event->sheet->setCellValue('D1','Distritos');
                $i=2;
                foreach ($distritos as $key => $distrito) {
                    $event->sheet->setCellValue('D' . $i, $distrito->nombre);
                    $i++;
                }

                //------------------------
                $tiposDocumentos= tipos_documento();
                $event->sheet->setCellValue('E1','TipoDocumentos');
                $i=2;
                foreach ($tiposDocumentos as $key => $tipo) {
                    $event->sheet->setCellValue('E' . $i, $tipo->simbolo);
                    $i++;
                }

            }
        ];
    }
}
