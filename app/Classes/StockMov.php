<?php

namespace App\Classes;


class StockMov 
{
    protected $producto_id;
    protected $color_id;
    protected $talla_id;
    protected $cantidad;


    public function __construct($producto_id, $color_id, $talla_id, $cantidad)
    {
        $this->producto_id = $producto_id;
        $this->color_id = $color_id;
        $this->talla_id = $talla_id;
        $this->cantidad = $cantidad;
    }

    public function saludar($nombre)
    {
        return $this->saludo . ', ' . $nombre;
    }
}
