<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        "almacenes/nota_ingreso/uploadnotaingreso",
        "almacenes/nota_ingreso_articulo/uploadnotaingreso",
        "api/posiciciones/clientes",
        'compras/articulos/uploadexcel',
        'produccion/composicion/uploadexcel',
        'importExcel/*'
    ];
}
