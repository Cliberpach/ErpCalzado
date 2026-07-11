<?php

namespace App\Classes;

/**
 * Única fuente de verdad de los valores de `promociones.tipo_promocion`.
 * Antes estaban repetidos como strings sueltos en PromocionStoreRequest,
 * PromocionUpdateRequest y en el JS de ecommerceMerris — un mismatch de
 * mayúsculas entre store/update rompía el cálculo de descuento en la tienda
 * sin error visible. Cualquier código nuevo que valide o compare este campo
 * debe usar esta clase, nunca escribir el string de nuevo.
 */
class TipoPromocion
{
    const DESCUENTO_FIJO = 'descuento_fijo';
    const DESCUENTO_PORCENTAJE = 'descuento_porcentaje';
    const PRECIO_TOTAL = 'precio_total';

    public static function values(): array
    {
        return [
            self::DESCUENTO_FIJO,
            self::DESCUENTO_PORCENTAJE,
            self::PRECIO_TOTAL,
        ];
    }

    public static function reglaValidacion(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
