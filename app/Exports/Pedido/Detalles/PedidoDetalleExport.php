<?php

namespace App\Exports\Pedido\Detalles;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Ventas\PedidoDetalle;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PedidoDetalleExport implements FromCollection,WithHeadings,WithStyles
{

    protected $estado;
    protected $cliente_id;
    protected $modelo_id;
    protected $producto_id;

    public function __construct($estado, $cliente_id, $modelo_id, $producto_id)
    {
        $this->estado       = $estado;
        $this->cliente_id   = $cliente_id;
        $this->modelo_id    = $modelo_id;
        $this->producto_id  = $producto_id;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $pedidos_detalles = PedidoDetalle::from('pedidos_detalles as pd')
                            ->select(
                                \DB::raw('concat("PE-", p.id) as `N° PEDIDO`'),
                                'p.created_at as as fecha',
                                'p.cliente_nombre as CLIENTE',
                                'p.user_nombre as VENDEDOR',
                                'pd.producto_nombre as PRODUCTO',
                                'pd.color_nombre as COLOR',
                                'pd.talla_nombre as TALLA',
                                'pd.cantidad as CANTIDAD_SOLICITADA',
                                'pd.precio_unitario_nuevo as PRECIO',
                                'pd.importe_nuevo as TOTAL',
                                'pd.cantidad_atendida as CANTIDAD_ATENDIDA',
                                'pd.cantidad_pendiente as CANTIDAD_PENDIENTE',
                                'pd.cantidad_enviada as CANTIDAD_ENVIADA',
                                \DB::raw('"CANTIDAD_FABRICACION" as CANTIDAD_FABRICACION'),
                                \DB::raw('"CANTIDAD_CAMBIO" as CANTIDAD_CAMBIO'),
                                \DB::raw('"CANTIDAD_DEVOLUCIÓN" as CANTIDAD_DEVOLUCIÓN')
                            )
                            ->join('pedidos as p', 'pd.pedido_id', '=', 'p.id')
                            ->join('productos as prod', 'prod.id', '=', 'pd.producto_id')
                            ->join('modelos as m', 'm.id', '=', 'prod.modelo_id')
                            ->where('p.estado','!=','FINALIZADO');

        // Aplicar el filtro de estado si se proporciona
        if ($this->estado !== '-') {
            if($this->estado === "PENDIENTE"){
                $pedidos_detalles->where('pd.cantidad_pendiente', '>', 0);
            }
            if($this->estado === "ATENDIDO"){
                $pedidos_detalles->where('pd.cantidad_pendiente', '=', 0);
            }
        }

        if ($this->cliente_id !== '-') {
            $pedidos_detalles->where('p.cliente_id', $this->cliente_id);
        }

        if ($this->modelo_id !== '-') {
            $pedidos_detalles->where('prod.modelo_id', $this->modelo_id);
        }

        if ($this->producto_id !== '-') {
            $pedidos_detalles->where('pd.producto_id', $this->producto_id);
        }

        // Ordenar y obtener los resultados
        return $pedidos_detalles->orderBy('p.id', 'desc')->get();
    }

     /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'N°_PEDIDO',
            'FECHA',
            'CLIENTE',
            'VENDEDOR',
            'PRODUCTO',
            'COLOR',
            'TALLA',
            'CANTIDAD_SOLICITADA',
            'PRECIO',
            'TOTAL',
            'CANTIDAD_ATENDIDA',
            'CANTIDAD_PENDIENTE',
            'CANTIDAD_ENVIADA',
            'CANTIDAD_FABRICACIÓN',
            'CANTIDAD_CAMBIO',
            'CANTIDAD_DEVOLUCIÓN',
        ];
    }

      /**
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF00', // Color de fondo amarillo
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getColumnDimension('A')->setWidth(15); // Ajusta el ancho según el contenido
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(20);
    }
 
}
