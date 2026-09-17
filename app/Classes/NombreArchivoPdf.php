<?php

namespace App\Classes;

/**
 * Única fuente de verdad del nombre con el que se guardan los PDF de
 * documentos: <TIPO>-<SERIE>-<NUMERO>-<DOC_CLIENTE>.
 *
 * El nombre no depende del formato de impresión: una boleta en A4 y la misma
 * boleta en 80 mm se guardan igual. El tamaño no entra en el nombre.
 *
 * Los reportes de caja tienen su propio formato y no pasan por aquí.
 */
class NombreArchivoPdf
{
    /** Cuando el documento no guarda DNI/RUC del cliente. */
    const SIN_DOC = 'SIN-DOC';

    /** Códigos SUNAT de tipo de comprobante -> prefijo del nombre. */
    private static $tiposVenta = [
        '01' => 'FAC',
        '03' => 'BOL',
        '04' => 'NV',
    ];

    /** Códigos SUNAT de tipo de nota -> prefijo del nombre. */
    private static $tiposNota = [
        '07' => 'NC',
        '08' => 'ND',
    ];

    /**
     * Arma el nombre sin extensión. Los tramos vacíos se omiten, así un
     * documento sin serie queda COT-125-12345678 y no COT--125-...
     *
     * @param  string      $tipo   Prefijo del patrón (BOL, FAC, GUIA...).
     * @param  string|null $serie  Serie tal como se imprime, o null si no tiene.
     * @param  string|int  $numero Número tal como se imprime.
     * @param  string|null $docCliente DNI/RUC del cliente.
     */
    public static function armar($tipo, $serie, $numero, $docCliente)
    {
        $tramos = [
            self::limpiar($tipo),
            self::limpiar($serie),
            self::limpiar($numero),
            self::limpiar($docCliente) !== '' ? self::limpiar($docCliente) : self::SIN_DOC,
        ];

        return implode('-', array_filter($tramos, function ($tramo) {
            return $tramo !== '';
        }));
    }

    /** Igual que armar(), con la extensión puesta. */
    public static function archivo($tipo, $serie, $numero, $docCliente)
    {
        return self::armar($tipo, $serie, $numero, $docCliente) . '.pdf';
    }

    /**
     * Boleta, factura o nota de venta (tabla cotizacion_documento).
     * En contingencia se usa la serie de contingencia, que es la que sale
     * impresa en el comprobante.
     */
    public static function documentoVenta($documento)
    {
        $enContingencia = isset($documento->contingencia) && (string) $documento->contingencia !== '0';

        return self::armar(
            self::tipoVenta($documento),
            $enContingencia ? $documento->serie_contingencia : $documento->serie,
            $enContingencia ? $documento->correlativo_contingencia : $documento->correlativo,
            isset($documento->documento_cliente) ? $documento->documento_cliente : null
        );
    }

    /** Nota de crédito o de débito electrónica (tabla nota_electronica). */
    public static function notaElectronica($nota)
    {
        $codigo = isset($nota->tipoDoc) ? (string) $nota->tipoDoc : '';
        $tipo   = isset(self::$tiposNota[$codigo]) ? self::$tiposNota[$codigo] : 'NC';

        return self::armar(
            $tipo,
            $nota->serie,
            $nota->correlativo,
            isset($nota->documento_cliente) ? $nota->documento_cliente : null
        );
    }

    /**
     * Guía de remisión. El documento del patrón es el del destinatario, que
     * es lo que la guía guarda en documento_cliente. Mientras no se envía a
     * SUNAT no hay serie ni correlativo: se cae al id para no dejar el nombre
     * a medias.
     */
    public static function guia($guia)
    {
        $tieneNumeracion = self::limpiar($guia->serie) !== '' && self::limpiar($guia->correlativo) !== '';

        return self::armar(
            'GUIA',
            $tieneNumeracion ? $guia->serie : null,
            $tieneNumeracion ? $guia->correlativo : $guia->id,
            isset($guia->documento_cliente) ? $guia->documento_cliente : null
        );
    }

    /** Cotización: no tiene serie, se numera por id (impreso como CO-<id>). */
    public static function cotizacion($cotizacion, $docCliente)
    {
        return self::armar('COT', null, $cotizacion->id, $docCliente);
    }

    /** Pedido: no tiene serie, se numera por id (impreso como PE-<id>). */
    public static function pedido($pedido, $docCliente)
    {
        return self::armar('PED', null, $pedido->id, $docCliente);
    }

    /** Prefijo del patrón según el código SUNAT del tipo de venta. */
    private static function tipoVenta($documento)
    {
        $codigo = isset($documento->tipo_venta_codigo) ? (string) $documento->tipo_venta_codigo : '';
        $codigo = str_pad($codigo, 2, '0', STR_PAD_LEFT);

        return isset(self::$tiposVenta[$codigo]) ? self::$tiposVenta[$codigo] : 'DOC';
    }

    /** Deja sólo A-Z, 0-9 y guiones; sin espacios, tildes ni signos. */
    private static function limpiar($valor)
    {
        if ($valor === null) {
            return '';
        }

        $valor = strtoupper(trim((string) $valor));
        $valor = strtr($valor, [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'Ñ' => 'N', 'Ü' => 'U',
        ]);
        $valor = preg_replace('/[^A-Z0-9-]+/', '', $valor);

        return trim($valor, '-');
    }
}
