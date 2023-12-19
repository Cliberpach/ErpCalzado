<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;

class TipoCliente extends Model
{
    protected $table = 'productos_clientes';
    protected $fillable = [
        'cliente',
        'monto',
        'porcentaje',
        'producto_id',
        'moneda',
        'estado'
    ];

    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto');
    }

    public function tipocliente(): string
    {
        $cliente = tipo_clientes()->where('id', $this->cliente)->first();
        if (is_null($cliente))
            return "-";
        else
            return $cliente->descripcion;
    }

    protected static function booted()
    {
        static::created(function(TipoCliente $tipo){
            if($tipo->cliente == '121')
            {
                $producto = Producto::find($tipo->producto_id);
                $producto->porcentaje_normal = $tipo->porcentaje;
                $producto->update();
            }
            else
            {
                $producto = Producto::find($tipo->producto_id);
                $producto->porcentaje_distribuidor = $tipo->porcentaje;
                $producto->update();
            }
        });

        static::updated(function(TipoCliente $tipo){

        });
    }

}
