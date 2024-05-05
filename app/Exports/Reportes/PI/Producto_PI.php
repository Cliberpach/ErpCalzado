<?php

namespace App\Exports\Reportes\PI;

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
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class Producto_PI implements fromArray, WithHeadings, ShouldAutoSize,WithEvents
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
        $productos  =   DB::select('select p.nombre as producto,c.descripcion as color,
            t.descripcion as talla,m.descripcion as modelo,pct.stock,ca.descripcion as categoria
            from producto_color_tallas as pct
            inner join productos as p on p.id=pct.producto_id
            inner join colores as c on c.id=pct.color_id
            inner join tallas as t on t.id=pct.talla_id
            inner join modelos as m on m.id=p.modelo_id
            inner join categorias as ca on ca.id=p.categoria_id');

        foreach ($productos as $producto) {
            $data[] = [
                'producto'  => $producto->producto,
                'color'     => $producto->color,
                'talla'     => $producto->talla,
                'modelo'    => $producto->modelo,
                'categoria' => $producto->categoria,
                'stock'     => $producto->stock==0?"0":$producto->stock,
            ];
        }

        return $data;
    }
    public function headings(): array
    {
        return [
            'PRODUCTO',
            'COLOR',
            'TALLA',
            'MODELO',
            'CATEGORÍA',
            'STOCK'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Aplicar negrita a los encabezados
                $event->sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                // Aplicar un fondo azul suave a los encabezados
                $event->sheet->getStyle('A1:F1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9EDF7'], // Azul claro
                    ],
                ]);
            },
        ];
    }

}
