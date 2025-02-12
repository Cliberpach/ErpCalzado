<?php

namespace App\Classes;

class ValidatedDetail 
{
    protected $almacen_id;
    protected $producto_id;
    protected $color_id;
    protected $talla_id;
    protected $producto_nombre;
    protected $color_nombre;
    protected $talla_nombre;
    protected $stock_logico;
    protected $cantidad_solicitada;
    protected $precio_unitario;
    protected $porcentaje_descuento;
    protected $precio_unitario_nuevo;
    protected $tipo;

    public function __construct($almacen_id = null,$producto_id = null, $color_id = null, $talla_id = null, $producto_nombre = null, $color_nombre = null, $talla_nombre = null, $stock_logico = null, $cantidad_solicitada = null, $precio_unitario = null,$precio_unitario_nuevo = null,$porcentaje_descuento = null, $tipo = null)
    {
        $this->almacen_id           =   $almacen_id;
        $this->producto_id          =   $producto_id;
        $this->color_id             =   $color_id;
        $this->talla_id             =   $talla_id;
        $this->producto_nombre      =   $producto_nombre;
        $this->color_nombre         =   $color_nombre;
        $this->talla_nombre         =   $talla_nombre;
        $this->stock_logico         =   $stock_logico;
        $this->cantidad_solicitada  =   $cantidad_solicitada;
        $this->precio_unitario      =   $precio_unitario;
        $this->precio_unitario_nuevo=   $precio_unitario_nuevo;
        $this->porcentaje_descuento =   $porcentaje_descuento;
        $this->tipo = $tipo;
    }

    public function setAlmacenId($almacen_id)
    {
        $this->almacen_id = $almacen_id;
    }

    public function setProductoId($producto_id)
    {
        $this->producto_id = $producto_id;
    }

    // Setter para color_id
    public function setColorId($color_id)
    {
        $this->color_id = $color_id;
    }

    // Setter para talla_id
    public function setTallaId($talla_id)
    {
        $this->talla_id = $talla_id;
    }

    // Setter para producto_nombre
    public function setProductoNombre($producto_nombre)
    {
        $this->producto_nombre = $producto_nombre;
    }

    // Setter para color_nombre
    public function setColorNombre($color_nombre)
    {
        $this->color_nombre = $color_nombre;
    }

    // Setter para talla_nombre
    public function setTallaNombre($talla_nombre)
    {
        $this->talla_nombre = $talla_nombre;
    }

    // Setter para stock_logico
    public function setStockLogico($stock_logico)
    {
        $this->stock_logico = $stock_logico;
    }

    // Setter para cantidad_solicitada
    public function setCantidadSolicitada($cantidad_solicitada)
    {
        $this->cantidad_solicitada = $cantidad_solicitada;
    }

    public function setPrecioUnitario($precio_unitario)
    {
        $this->precio_unitario = $precio_unitario;
    }

    public function setPrecioUnitarioNuevo($precio_unitario_nuevo)
    {
        $this->precio_unitario_nuevo = $precio_unitario_nuevo;
    }

    public function setPorcentajeDescuento($porcentaje_descuento)
    {
        $this->porcentaje_descuento = $porcentaje_descuento;
    }

    // Setter para tipo
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }



     // Getters
     public function getAlmacenId()
     {
         return $this->almacen_id;
     }

     public function getProductoId()
     {
         return $this->producto_id;
     }
 
     public function getColorId()
     {
         return $this->color_id;
     }
 
     public function getTallaId()
     {
         return $this->talla_id;
     }
 
     public function getProductoNombre()
     {
         return $this->producto_nombre;
     }
 
     public function getColorNombre()
     {
         return $this->color_nombre;
     }
 
     public function getTallaNombre()
     {
         return $this->talla_nombre;
     }
 
     public function getStockLogico()
     {
         return $this->stock_logico;
     }
 
     public function getCantidadSolicitada()
     {
         return $this->cantidad_solicitada;
     }

     public function getPrecioUnitario()
     {
         return $this->precio_unitario;
     }

     public function getPrecioUnitarioNuevo()
     {
         return $this->precio_unitario_nuevo;
     }

     public function getPorcentajeDescuento()
     {
         return $this->porcentaje_descuento;
     }
 
     public function getTipo()
     {
         return $this->tipo;
     }
}

