<?php

namespace App\Exports\Producto;

use App\Almacenes\Almacen;
use App\Almacenes\Categoria;
use App\Almacenes\Marca;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ProductoExport implements fromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    function title(): String
    {
        return "productos";
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data = array();
        array_push($data, [
            'UnidadMedida' => 'NIU-UNIDAD (BIENES)',
            'Peso' => '0.00',
            'Nombre' => 'GATILLO',
            'Categorias' => 'MOTORES',
            'Marcas' => 'JET AGRO',
            'Almacenes' => 'CENTRAL',
            'StockMinimo' => '0.00',
            'PorcentajeDistribuidor' => '0.00',
            'PorcentajeNormal' => '0.00',
            'Igv' => 'SI',
            'CodigoBarra' => '115464856'
        ]);
        return $data;
    }
    public function headings(): array
    {
        return [
            'UnidadMedida',
            'Peso',
            'Nombre',
            'Categorias',
            'Marcas',
            'Almacenes',
            'StockMinimo',
            'PorcentajeDistribuidor',
            'PorcentajeNormal',
            'Igv',
            'CodigoBarra'
        ];
    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:M1')->applyFromArray(
                    [
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'startColor' => [
                                'argb' => 'ffffff',
                            ],
                            'endColor' => [
                                'argb' => 'ffffff',
                            ],
                        ],


                    ]

                );
                for ($j = 2; $j < 1000; $j++) {
                    $validation = $event->sheet->getCell('A' . $j)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(false);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error en escritura');
                    $validation->setError('El valor no esta en la lista');
                    $validation->setFormula1('detalles!$A$2:$A$' . (unidad_medida()->count() + 1));

                    $validation = $event->sheet->getCell('D' . $j)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(false);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error en escritura');
                    $validation->setError('El valor no esta en la lista');
                    $validation->setFormula1('detalles!$B$2:$B$' . (Categoria::where('estado', 'ACTIVO')->count() + 1));

                    $validation = $event->sheet->getCell('E' . $j)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(false);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error en escritura');
                    $validation->setError('El valor no esta en la lista');
                    $validation->setFormula1('detalles!$C$2:$C$' . (Marca::where('estado', 'ACTIVO')->count() + 1));

                    $validation = $event->sheet->getCell('F' . $j)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(false);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error en escritura');
                    $validation->setError('El valor no esta en la lista');
                    $validation->setFormula1('detalles!$D$2:$D$' . (Almacen::where('estado', 'ACTIVO')->count() + 1));
                }
            },
        ];
    }
}
