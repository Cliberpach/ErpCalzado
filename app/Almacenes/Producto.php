<?php

namespace App\Almacenes;

use App\Compras\Documento\Detalle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Ventas\Documento\Detalle as DocumentoDetalleVenta;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'marca_id',
        'modelo_id',
        'almacen_id',
        'codigo',
        'nombre',
        'descripcion',
        'medida',
        'codigo_barra',
        'stock_minimo',
        'precio_compra',
        'precio_venta_1',
        'precio_venta_2',
        'precio_venta_3',
        'precio_venta_4',
        'igv',
        'facturacion',
        'estado',
        'costo',
        'tipo',

        'img1_ruta',
        'img1_nombre',
        'img2_ruta',
        'img2_nombre',
        'img3_ruta',
        'img3_nombre',
        'img4_ruta',
        'img4_nombre',
        'img5_ruta',
        'img5_nombre',

        'mostrar_en_web',

        'is_featured',
        'is_sale',
        'is_outlet',
    ];

    protected $guarded = [];

    protected $casts = [
        'igv' => 'boolean'
    ];
    public function almacen()
    {
        return $this->belongsTo('App\Almacenes\Almacen');
    }
    public function marca()
    {
        return $this->belongsTo('App\Almacenes\Marca');
    }
    public function categoria()
    {
        return $this->belongsTo('App\Almacenes\Categoria');
    }
    public function detalles()
    {
        return $this->hasMany('App\Almacenes\ProductoDetalle');
    }
    public function tipoCliente()
    {
        return $this->hasMany('App\Almacenes\TipoCliente', 'producto_id', 'id');
    }
    public function getDescripcionCompleta()
    {
        return $this->codigo . ' - ' . $this->nombre;
    }
    public function tabladetalle()
    {
        return $this->belongsTo('App\Mantenimiento\Tabla\Detalle', 'medida');
    }
    public function getMedida(): string
    {
        $medida = unidad_medida()->where('id', $this->medida)->first();
        if (is_null($medida))
            return "-";
        else
            return $medida->simbolo;
    }
    public function medidaCompleta(): string
    {
        $medida = unidad_medida()->where('id', $this->medida)->first();
        if (is_null($medida))
            return "-";
        else
            return $medida->simbolo . ' - ' . $medida->descripcion;
    }

    public function compraDetalles(): HasMany
    {
        return $this->hasMany(Detalle::class, 'producto_id', 'id');
    }

    public function DetalleNI(): HasMany
    {
        return $this->hasMany(DetalleNotaIngreso::class, 'producto_id', 'id');
    }

    public function DetalleVenta(): HasMany
    {
        return $this->hasMany(DocumentoDetalleVenta::class, 'codigo_producto', 'codigo');
    }

    public function DetalleVenta1(): HasMany
    {
        return $this->hasMany(DocumentoDetalleVenta::class, 'codigo_producto', 'codigo');
    }

    public function DetalleNS(): HasMany
    {
        return $this->hasMany(DetalleNotaSalidad::class, 'producto_id', 'id');
    }

    public function DetalleNE(): HasMany
    {
        return $this->hasMany(DocumentoDetalleVenta::class, 'codigo_producto', 'codigo');
    }
    /**
     * Get all of the DetalleCompras for the Producto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function compraDetalles1(): HasMany
    {
        return $this->hasMany(Detalle::class, 'producto_id', 'id');
    }

    public function productos()
    {
        return $this->hasMany('App\Almacenes\ProductoColorTalla');
    }

    public function modelo()
    {
        return $this->belongsTo('App\Almacenes\Modelo');
    }
}
