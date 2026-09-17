<?php

namespace App\Http\Services\Ventas;

/**
 * Reglas de cálculo de una venta, en un único sitio.
 *
 * Nacieron privadas dentro de CajaMovimientoRepository, para el Reporte
 * Cantidades. Al aparecer un segundo consumidor (el ranking de vendedores del
 * dashboard) se extraen aquí: si el respaldo de importe o el criterio de
 * documento vendible se corrigen algún día, deben corregirse una sola vez, o el
 * sistema acaba con dos cifras distintas de "cuánto se vendió" y nadie sabe
 * cuál creer.
 *
 * Los métodos devuelven fragmentos de SQL parametrizados por alias de tabla,
 * para poder usarlos desde consultas con alias distintos. No incluyen nada
 * específico de un informe: la caja, el periodo o la condición de pago se
 * quedan en el repositorio de cada uno.
 */
class ReglasVenta
{
    /**
     * Importe real de una línea de detalle de venta.
     *
     * Normalmente es importe_nuevo, que ya trae el descuento aplicado. Hay
     * líneas antiguas con importe_nuevo = 0 sin ningún descuento registrado:
     * ahí el dato bueno es importe. Se distingue un caso del otro por el
     * descuento, porque un 100% de descuento sí deja importe_nuevo = 0 de forma
     * legítima y no debe recuperarse el importe original.
     *
     * @param string $d Alias de cotizacion_documento_detalles.
     */
    public static function importeLinea(string $d = 'd'): string
    {
        return "CASE WHEN IFNULL({$d}.importe_nuevo, 0)         = 0"
            . " AND IFNULL({$d}.monto_descuento, 0)       = 0"
            . " AND IFNULL({$d}.porcentaje_descuento, 0)  = 0"
            . " AND IFNULL({$d}.precio_unitario_nuevo, 0) = 0"
            . " THEN IFNULL({$d}.importe, 0)"
            . " ELSE IFNULL({$d}.importe_nuevo, 0)"
            . " END";
    }

    /**
     * Documento que cuenta como venta.
     *
     *   convert_de_id IS NULL -> el destino de una conversión NO se cuenta; la
     *                            venta se imputa al documento de origen. Sin
     *                            esto se contarían dos veces los mismos pares.
     *   estado <> 'ANULADO'   -> las anuladas no suman.
     *
     * NO incluye la condición de pago: el Reporte Cantidades se limita a
     * contado porque es un arqueo de caja, pero un informe de ventas normal
     * debe contar también el crédito. Quien lo necesite lo añade aparte.
     *
     * @param string $cd Alias de cotizacion_documento.
     */
    public static function documentoVendible(string $cd = 'cd'): string
    {
        return "{$cd}.convert_de_id IS NULL AND {$cd}.estado <> 'ANULADO'";
    }

    /**
     * Línea de detalle viva: ni dada de baja ni marcada como eliminada.
     *
     * @param string $d Alias de cotizacion_documento_detalles.
     */
    public static function lineaActiva(string $d = 'd'): string
    {
        return "{$d}.estado = 'ACTIVO' AND {$d}.eliminado = '0'";
    }
}
