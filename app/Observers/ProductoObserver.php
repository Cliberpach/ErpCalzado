<?php

namespace App\Observers;

use App\Almacenes\Producto;
use App\Events\ProductoActualizadoEvent;

/**
 * Fase 4.2 (ecommerceMerris): observa App\Almacenes\Producto — el modelo
 * REAL usado por el CRUD de productos (ProductoService/ProductoRepository)
 * y por la API pública. Existe un segundo modelo duplicado
 * (App\Models\Almacenes\Producto\Producto) que apunta a la misma tabla
 * pero no lo usa nada del flujo real: NO observarlo, el evento nunca
 * dispararía y no habría ningún error visible.
 *
 * ProductoService::destroy() no borra la fila, solo pone
 * estado = 'ANULADO' y llama a $producto->update() — por eso "eliminado"
 * se detecta acá comparando el estado, no con el evento `deleted`.
 */
class ProductoObserver
{
    public function created(Producto $producto): void
    {
        ProductoActualizadoEvent::dispatch($producto->id, 'creado');
    }

    public function updated(Producto $producto): void
    {
        $accion = ($producto->wasChanged('estado') && $producto->estado === 'ANULADO')
            ? 'eliminado'
            : 'actualizado';

        ProductoActualizadoEvent::dispatch($producto->id, $accion);
    }
}
