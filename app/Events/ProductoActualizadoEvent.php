<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fase 4.2 (ecommerceMerris): se dispara cuando un producto se crea,
 * actualiza o se marca ANULADO (destroy() en ProductoService es un soft
 * delete vía estado, no un DELETE real). Lo captura
 * NotificarEcommerceMerrisListener y hace un POST firmado al webhook del
 * storefront.
 */
class ProductoActualizadoEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var int */
    public $productoId;

    /** @var string 'creado' | 'actualizado' | 'eliminado' */
    public $accion;

    public function __construct(int $productoId, string $accion)
    {
        $this->productoId = $productoId;
        $this->accion = $accion;
    }
}
