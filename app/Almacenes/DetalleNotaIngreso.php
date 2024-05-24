<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Almacenes\ProductoColorTalla;

class DetalleNotaIngreso extends Model
{
    protected $table = 'detalle_nota_ingreso';
    protected $fillable = [
        'id',
        'nota_ingreso_id',
        // 'lote',
        // 'lote_id',
        'producto_id',
        'color_id',
        'talla_id',
        'cantidad',
        // 'fecha_vencimiento',
        // 'costo',
        // 'costo_soles',
        // 'costo_dolares',
        // 'valor_ingreso',
    ];
    public $timestamps = true;

    public function nota_ingreso()
    {
        return $this->belongsTo(NotaIngreso::class, 'nota_ingreso_id', 'id');
    }

    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto');
    }


    public function loteProducto()
    {
        return $this->belongsTo('App\Almacenes\LoteProducto', 'lote_id');
    } 

     protected static function booted()
     {
        //=========== actualizando stock producto ===============
        static::created(function(DetalleNotaIngreso $detalleNotaIngreso){

            $cantidadProductos  = $detalleNotaIngreso->cantidad;

            //comprobando si existe el producto_color_talla
            $producto   =   DB::select('select * from producto_color_tallas as pct
                                        where pct.producto_id = ? and
                                        pct.color_id = ? and pct.talla_id = ?',[$detalleNotaIngreso->producto_id,
                                        $detalleNotaIngreso->color_id,$detalleNotaIngreso->talla_id]);
                 
            //======== el producto existe =========
            //========= incrementar sus stocks ===========
            if (count($producto) > 0) {

                ProductoColorTalla::where('producto_id', $detalleNotaIngreso->producto_id)
                ->where('color_id', $detalleNotaIngreso->color_id)
                ->where('talla_id', $detalleNotaIngreso->talla_id)
                ->update([
                    'stock' => DB::raw("stock + $cantidadProductos"),
                    'stock_logico'  =>  DB::raw("stock_logico + $cantidadProductos"),
                    'estado'        =>  '1',  
                ]);

            } else {
            //========= el producto no existe =============
            //================== crearlo =========================

                //========= verificando si ya existe registro en la tabla producto_color =========
                $existeColor = ProductoColor::where('producto_id', $detalleNotaIngreso->producto_id)
                                ->where('color_id', $detalleNotaIngreso->color_id)
                                ->exists();
                
                //======== registrar en caso no exista el color para el producto =======
                //===== cuando se reciba varias tallas por color esto evitará color_id duplicado =====
                if(!$existeColor){
                    $producto_color                 =   new ProductoColor();
                    $producto_color->producto_id    =   $detalleNotaIngreso->producto_id;
                    $producto_color->color_id       =   $detalleNotaIngreso->color_id;
                    $producto_color->save(); 
                }  

                //====== registrar el stock de producto_color_talla ============
                $producto                   =    new ProductoColorTalla();
                $producto->producto_id      =   $detalleNotaIngreso->producto_id;
                $producto->color_id         =   $detalleNotaIngreso->color_id;
                $producto->talla_id         =   $detalleNotaIngreso->talla_id;
                $producto->stock            =   $cantidadProductos;
                $producto->stock_logico     =   $cantidadProductos;
                $producto->save();

            }  


            
            
            //=========== registrando movimiento ===============
                MovimientoNota::create([
                 'cantidad' => $detalleNotaIngreso->cantidad,
                 'observacion' => $detalleNotaIngreso->producto->modelo->descripcion.' - '.$detalleNotaIngreso->producto->nombre,
                 'movimiento' => "INGRESO",
                //  'lote_id' => $lote->id,
                 'usuario_id' => Auth()->user()->id,
                 'nota_id' => $detalleNotaIngreso->nota_ingreso->id,
                 'producto_id' => $detalleNotaIngreso->producto_id,
                 'color_id' => $detalleNotaIngreso->color_id,
                 'talla_id' => $detalleNotaIngreso->talla_id,
             ]);

            //=========== OBTENIENDO PRODUCTO CON STOCK NUEVO ===========
            $producto   =   DB::select('select * from producto_color_tallas as pct
                                        where pct.producto_id = ? and
                                        pct.color_id = ? and pct.talla_id = ?',[$detalleNotaIngreso->producto_id,
                                        $detalleNotaIngreso->color_id,$detalleNotaIngreso->talla_id]);
                 

             //==================== KARDEX ==================
            $kardex                    =    new Kardex();
            $kardex->origen            =    'INGRESO';
            $kardex->numero_doc        =    $detalleNotaIngreso->nota_ingreso->numero;
            $kardex->fecha             =    $detalleNotaIngreso->nota_ingreso->fecha;
            $kardex->cantidad          =    $detalleNotaIngreso->cantidad;
            $kardex->producto_id       =    $detalleNotaIngreso->producto_id;
            $kardex->color_id          =    $detalleNotaIngreso->color_id;
            $kardex->talla_id          =    $detalleNotaIngreso->talla_id;
            $kardex->descripcion       =    $detalleNotaIngreso->nota_ingreso->usuario;
            //$kardex->descripcion       =    $detalleNotaIngreso->nota_ingreso->origen;
            //$kardex->precio          = $detalle->costo_soles;
            //$kardex->importe         = $detalle->costo_soles * $detalle->cantidad;
            //$kardex->stock           = $detalle->producto->stock;
            count($producto)>0? $kardex->stock = $producto[0]->stock: 0;
            $kardex->save();
           
        });
        
    //     static::created(function (DetalleNotaIngreso $detalle) {

    //         $lote = new LoteProducto();
    //         $lote->nota_ingreso_id = $detalle->nota_ingreso->id;
    //         $lote->codigo_lote = $detalle->lote;
    //         $lote->producto_id = $detalle->producto_id;
    //         $lote->cantidad = $detalle->cantidad;
    //         $lote->cantidad_logica = $detalle->cantidad;
    //         $lote->cantidad_inicial = $detalle->cantidad;
    //         $lote->fecha_vencimiento = $detalle->fecha_vencimiento;
    //         $lote->fecha_entrega = $detalle->nota_ingreso->fecha;
    //         $lote->observacion = 'NOTA DE INGRESO';
    //         $lote->estado = '1';
    //         $lote->save();

    //         $producto = Producto::findOrFail($detalle->producto_id);
    //         $producto->precio_compra = $detalle->costo_soles;
    //         $producto->update();

    //         $detalle->lote_id = $lote->id;
    //         $detalle->update();


    //         MovimientoNota::create([
    //             'cantidad' => $detalle->cantidad,
    //             'observacion' => $detalle->producto->nombre,
    //             'movimiento' => "INGRESO",
    //             'lote_id' => $lote->id,
    //             'usuario_id' => Auth()->user()->id,
    //             'nota_id' => $detalle->nota_ingreso->id,
    //             'producto_id' => $detalle->producto_id,
    //         ]);

    //         //KARDEX
    //         $kardex = new Kardex();
    //         $kardex->origen = 'INGRESO';
    //         $kardex->numero_doc = $detalle->nota_ingreso->numero;
    //         $kardex->fecha = $detalle->nota_ingreso->fecha;
    //         $kardex->cantidad = $detalle->cantidad;
    //         $kardex->producto_id = $detalle->producto_id;
    //         $kardex->descripcion = $detalle->nota_ingreso->origen;
    //         $kardex->precio = $detalle->costo_soles;
    //         $kardex->importe = $detalle->costo_soles * $detalle->cantidad;
    //         $kardex->stock = $detalle->producto->stock;
    //         $kardex->save();
    //     });
    }


}
