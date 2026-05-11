<?php

namespace App\Models\Mantenimiento\Promocion;

use App\Almacenes\Producto;
use Illuminate\Database\Eloquent\Model;

class PromocionProducto extends Model
{
    protected $table = 'promociones_productos';
    protected $fillable = [
        'promocion_id',
        'producto_id',
        'estado',
    ];
    public $timestamps = true;

    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'promocion_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
