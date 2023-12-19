<?php

namespace App\Exports\Marca;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class MarcaExport  implements fromArray, WithHeadings,WithColumnWidths, WithEvents,WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data = array();
        array_push($data,[
            'MARCA'=>'KAZO',
            'PROCEDENCIA'=>'CHINO',
        ]);
        return $data;
    }
    function title():String{
        return "Marcas";
    }
    public function headings(): array
    {
        return [
                'MARCA',
                'PROCEDENCIA'
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
        ];
    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:B1')->applyFromArray(
                    [
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'startColor' => [
                                'argb' => '1ab394',
                            ],
                            'endColor' => [
                                'argb' => '1ab394',
                            ],
                        ],


                    ]

                );

            },
        ];
    }
}
