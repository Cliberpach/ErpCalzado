<?php

namespace App\Exports\Reportes\Pos;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class EgresoExport implements FromCollection, WithHeadings, WithEvents
{
    public $cuenta, $fecha_ini, $fecha_fin;
    use Exportable;

    public function headings(): array
    {
        return [
            [
                "ID",
                "DESCRIPCION",
                "TIPO DOCUMENTO",
                "DOCUMENTO",
                "MONTO",
                "USUARIO",
                "FECHA",
            ]
        ];
    }

    function title(): String
    {
        return "EGRESO " . $this->fecha_ini . "-" . $this->fecha_fin;
    }

    public function __construct($cuenta, $fecha_ini, $fecha_fin)
    {
        $this->cuenta = $cuenta;
        $this->fecha_ini = $fecha_ini;
        $this->fecha_fin = $fecha_fin;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        $consulta =  DB::table('egreso')
            //->join('tabladetalles', 'egreso.tipodocumento_id', '=', 'tabladetalles.id')
            ->join('tabladetalles', 'egreso.cuenta_id', '=', 'tabladetalles.id')
            ->select(
                'egreso.id',
                'egreso.descripcion',
                DB::raw('(select descripcion from tabladetalles where id = egreso.tipodocumento_id) as tipoDocumento'),
                'egreso.documento',
                'egreso.monto',
                'egreso.usuario',
                'egreso.created_at',
                //'tabladetalles.descripcion as cuenta',
            )->where('egreso.estado','ACTIVO');

        if ($this->cuenta) {
            $consulta = $consulta->where('tabladetalles.id', $this->cuenta);
        }
        if ($this->fecha_ini && $this->fecha_fin) {
            $consulta = $consulta->whereBetween(DB::raw('DATE_FORMAT(egreso.created_at, "%Y-%m-%d")'), [$this->fecha_ini, $this->fecha_fin]);
        }
        return $consulta->get();
    }
    public function registerEvents(): array
    {
        return [

            BeforeWriting::class => [self::class, 'beforeWriting'],
            AfterSheet::class    => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:G1')->applyFromArray(
                    [

                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                            'rotation' => 90,
                            'startColor' => [
                                'argb' => '00bbd4',
                            ],
                            'endColor' => [
                                'argb' => '00bbd4',
                            ],
                        ],


                    ]

                );
                $event->sheet->getStyle('A1:H1')->applyFromArray(
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



                $event->sheet->getColumnDimension('A')->setWidth(20);
                $event->sheet->getColumnDimension('B')->setWidth(20);
                $event->sheet->getColumnDimension('C')->setWidth(15);
                //    $event->sheet->getColumnDimension('D')->setWidth(20);
                //    $event->sheet->getColumnDimension('E')->setWidth(20);
                //    $event->sheet->getColumnDimension('F')->setWidth(20);
                //    $event->sheet->getColumnDimension('G')->setWidth(20);
                //    $event->sheet->getColumnDimension(''H)->setWidth(20);

            },
        ];
    }
}
