<?php

namespace App\Almacenes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetalleNotaSalidad extends Model
{
    protected $table = 'detalle_nota_salidad';
    protected $fillable = [
        'id',
        'nota_salidad_id',
        'color_id',
        'talla_id',
        'cantidad',
        'producto_id'
    ];

    public $timestamps                  =   true;
    public $decrementarStockLogico      =   true;
    public $almacen_origen_id           =   null;
    public $almacen_destino_id          =   null;
    public $sede_id                     =   null;


    public function setAlmacenDestinoId($valor)
    {
        $this->almacen_destino_id = $valor;
    }

    public function setAlmacenOrigenId($valor)
    {
        $this->almacen_origen_id = $valor;
    }

    public function setSedeId($valor)
    {
        $this->sede_id = $valor;
    }

    public function getAlmacenDestinoId(){
        return $this->almacen_destino_id;
    }

    public function getAlmacenOrigenId(){
        return $this->almacen_origen_id;
    }

    public function getSedeId(){
        return $this->sede_id;
    }

    public function nota_salidad(){
        return $this->belongsTo(NotaSalidad::class,'nota_salidad_id','id');
    }

    public function producto()
    {
        return $this->belongsTo('App\Almacenes\Producto');
    }

    public function lote()
    {
        return $this->belongsTo('App\Almacenes\LoteProducto','lote_id');
    }

     // Método para desactivar el decremento de stock lógico
     public function disableDecrementarStockLogico()
     {
         $this->decrementarStockLogico = false;
     }
    
    // Método para activar el decremento de stock lógico
    public function enableDecrementarStockLogico()
    {
        $this->decrementarStockLogico = true;
    }
 
    public function getDecrementarStockLogico(){
         return $this->decrementarStockLogico;
    }

    
    protected static function booted()
    {
       //=========== ACTUALIZANDO STOCK PRODUCTOS ===============
       static::created(function(DetalleNotaSalidad $detalleNotaSalida){

           $cantidadProductos  = $detalleNotaSalida->cantidad;

           //======= COMPROBANDO SI EXISTE EL PRODUCTO COLOR TALLA =========
           $producto    =   DB::select('select
                            pct.* 
                            from producto_color_tallas as pct
                            where pct.producto_id = ? 
                            and pct.color_id = ? 
                            and pct.talla_id = ?',
                            [$detalleNotaSalida->producto_id,
                            $detalleNotaSalida->color_id,
                            $detalleNotaSalida->talla_id]);
                
           //======== el producto existe =========
           //========= DECREMENTAR STOCK EN ALMACEN ORIGEN ===========
           if (count($producto) > 0) {

                if($detalleNotaSalida->nota_salidad->observacion == '/guiasremision/create_new'){
                    ProductoColorTalla::where('producto_id', $detalleNotaSalida->producto_id)
                    ->where('color_id', $detalleNotaSalida->color_id)
                    ->where('talla_id', $detalleNotaSalida->talla_id)
                    ->where('almacen_id', $detalleNotaSalida->getAlmacenOrigenId())
                    ->update([
                        'stock'     =>  DB::raw("stock - $cantidadProductos"),
                        'estado'    =>  '1',  
                    ]);
                }
                
                if($detalleNotaSalida->nota_salidad->observacion !== '/guiasremision/create_new'){
                    if($detalleNotaSalida->getDecrementarStockLogico()){

                        //===== RESTAR STOCK EN ALMACÉN ORIGEN ======
                        ProductoColorTalla::where('producto_id', $detalleNotaSalida->producto_id)
                        ->where('color_id', $detalleNotaSalida->color_id)
                        ->where('talla_id', $detalleNotaSalida->talla_id)
                        ->where('almacen_id', $detalleNotaSalida->getAlmacenOrigenId())
                        ->update([
                            'stock'         =>  DB::raw("stock - $cantidadProductos"),
                            'stock_logico'  =>  DB::raw("stock_logico - $cantidadProductos"),
                            'estado'        =>  '1',  
                        ]);

                        //===== AUMENTAR STOCK EN ALMACÉN DESTINO =======
                        //=> COMPROBANDO SI EXISTE EL PRODUCTO COLOR TALLA EN EL DESTINO <=
                        $pct_en_destino =   DB::select('select 
                                            pct.* 
                                            from producto_color_tallas as pct
                                            where pct.producto_id = ? 
                                            and pct.color_id = ? 
                                            and pct.talla_id = ?
                                            and pct.almacen_id = ?',
                                            [$detalleNotaSalida->producto_id,
                                            $detalleNotaSalida->color_id,
                                            $detalleNotaSalida->talla_id,
                                            $detalleNotaSalida->getAlmacenDestinoId()]);

                        if(count($pct_en_destino) > 0){

                            //======= INCREMENTAMOS STOCK ======
                            ProductoColorTalla::where('producto_id', $detalleNotaSalida->producto_id)
                            ->where('color_id', $detalleNotaSalida->color_id)
                            ->where('talla_id', $detalleNotaSalida->talla_id)
                            ->where('almacen_id', $detalleNotaSalida->getAlmacenDestinoId())
                            ->update([
                                'stock'         =>  DB::raw("stock + $cantidadProductos"),
                                'stock_logico'  =>  DB::raw("stock_logico + $cantidadProductos"),
                                'estado'        =>  '1',  
                            ]);

                        }else{

                            //======= EN CASO NO EXISTE =====
                            //===== PREGUNTAMOS SI EXISTE EL PRODUCTO COLOR EN DESTINO =====
                            $existeColor    =   ProductoColor::where('producto_id', $detalleNotaSalida->producto_id)
                                                ->where('color_id', $detalleNotaSalida->color_id)
                                                ->where('almacen_id',$detalleNotaSalida->getAlmacenDestinoId())
                                                ->exists();

                            //======== EN CASO NO EXISTE EL COLOR ======
                            //======= CREARLO EN EL DESTINO ======
                            if(!$existeColor){
                                $producto_color                 =   new ProductoColor();
                                $producto_color->producto_id    =   $detalleNotaSalida->producto_id;
                                $producto_color->color_id       =   $detalleNotaSalida->color_id;
                                $producto_color->almacen_id     =   $detalleNotaSalida->getAlmacenDestinoId();
                                $producto_color->save(); 
                            }  

                            //=========== REGISTRAR EL PRODUCTO COLOR TALLA ============
                            $pct_destino                   =   new ProductoColorTalla();
                            $pct_destino->producto_id      =   $detalleNotaSalida->producto_id;
                            $pct_destino->color_id         =   $detalleNotaSalida->color_id;
                            $pct_destino->talla_id         =   $detalleNotaSalida->talla_id;
                            $pct_destino->stock            =   $cantidadProductos;
                            $pct_destino->stock_logico     =   $cantidadProductos;
                            $pct_destino->almacen_id       =   $detalleNotaSalida->getAlmacenDestinoId();
                            $pct_destino->save();
                            
                        }

                    }else{

                        ProductoColorTalla::where('producto_id', $detalleNotaSalida->producto_id)
                        ->where('color_id', $detalleNotaSalida->color_id)
                        ->where('talla_id', $detalleNotaSalida->talla_id)
                        ->where('almacen_id', $detalleNotaSalida->getAlmacenOrigenId())
                        ->update([
                            'stock'         =>  DB::raw("stock - $cantidadProductos"),
                            'estado'        =>  '1',  
                        ]);

                    } 
                }
           } 
           
            //=========== REGISTRANDO MOVIMIENTO ===============
            MovimientoNota::create([
                'cantidad'      => $detalleNotaSalida->cantidad,
                'observacion'   => $detalleNotaSalida->producto->modelo->descripcion.' - '.$detalleNotaSalida->producto->nombre,
                'movimiento'    => "SALIDA",
                'usuario_id'    => Auth()->user()->id,
                'nota_id'       => $detalleNotaSalida->id,
                'producto_id'   => $detalleNotaSalida->producto_id,
                'color_id'      => $detalleNotaSalida->color_id,
                'talla_id'      => $detalleNotaSalida->talla_id,
                'almacen_origen_id'     =>   $detalleNotaSalida->getAlmacenOrigenId(),
                'almacen_destino_id'    =>   $detalleNotaSalida->getAlmacenDestinoId(),
                'sede_id'               =>   $detalleNotaSalida->getSedeId()
            ]);

            //=========== OBTENIENDO PRODUCTO CON STOCK NUEVO ===========
            $producto_origen   =    DB::select('select 
                                    pct.* 
                                    from producto_color_tallas as pct
                                    where pct.producto_id = ? 
                                    and pct.color_id = ? 
                                    and pct.talla_id = ?
                                    and pct.almacen_id = ?',
                                    [$detalleNotaSalida->producto_id,
                                    $detalleNotaSalida->color_id,
                                    $detalleNotaSalida->talla_id,
                                    $detalleNotaSalida->getAlmacenOrigenId()]);

            $producto_destino   =    DB::select('select 
                                    pct.* 
                                    from producto_color_tallas as pct
                                    where pct.producto_id = ? 
                                    and pct.color_id = ? 
                                    and pct.talla_id = ?
                                    and pct.almacen_id = ?',
                                    [$detalleNotaSalida->producto_id,
                                    $detalleNotaSalida->color_id,
                                    $detalleNotaSalida->talla_id,
                                    $detalleNotaSalida->getAlmacenDestinoId()]);
                

            //==================== KARDEX ==================
            $kardex                    =     new Kardex();
            $kardex->origen            =     'SALIDA';
            $kardex->numero_doc        =     $detalleNotaSalida->nota_salidad->numero;
            $kardex->fecha             =     $detalleNotaSalida->nota_salidad->fecha;
            $kardex->cantidad          =     $detalleNotaSalida->cantidad;
            $kardex->producto_id       =     $detalleNotaSalida->producto_id;
            $kardex->color_id          =     $detalleNotaSalida->color_id;
            $kardex->talla_id          =     $detalleNotaSalida->talla_id;
            $kardex->descripcion       =     $detalleNotaSalida->nota_salidad->observacion;
            $kardex->almacen_id        =     $detalleNotaSalida->getAlmacenOrigenId();
            count($producto_origen)>0? $kardex->stock = $producto_origen[0]->stock: 0;
            $kardex->save();

            $kardex                    =     new Kardex();
            $kardex->origen            =     'INGRESO';
            $kardex->numero_doc        =     $detalleNotaSalida->nota_salidad->numero;
            $kardex->fecha             =     $detalleNotaSalida->nota_salidad->fecha;
            $kardex->cantidad          =     $detalleNotaSalida->cantidad;
            $kardex->producto_id       =     $detalleNotaSalida->producto_id;
            $kardex->color_id          =     $detalleNotaSalida->color_id;
            $kardex->talla_id          =     $detalleNotaSalida->talla_id;
            $kardex->descripcion       =     $detalleNotaSalida->nota_salidad->observacion;
            $kardex->almacen_id        =     $detalleNotaSalida->getAlmacenDestinoId();
            count($producto_destino)>0? $kardex->stock = $producto_destino[0]->stock: 0;
            $kardex->save();
          
       });
       
   //     static::created(function (detalleNotaSalida $detalle) {

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



    // protected static function booted()
    // {
    //     static::created(function(DetalleNotaSalidad $detalle){

    //         MovimientoNota::create([
    //             'cantidad'=> $detalle->cantidad,
    //             'observacion'=> $detalle->producto->nombre,
    //             'movimiento'=> "SALIDA",
    //             'color_id'=> $detalle->color_id,
    //             'talla_id'=> $detalle->talla_id,
    //             'usuario_id'=> Auth()->user()->id,
    //             'nota_id'=> $detalle->nota_salidad->id,
    //             'producto_id'=> $detalle->producto_id,
    //         ]);

    //         $lote_producto = LoteProducto::findOrFail($detalle->lote_id);
    //         $lote_producto->cantidad = $lote_producto->cantidad - $detalle->cantidad;

    //         if($lote_producto->cantidad == 0)
    //         {
    //             $lote_producto->estado = '0';
    //         }
    //         $lote_producto->update();

    //         //KARDEX
    //         $kardex = new Kardex();
    //         $kardex->origen = 'SALIDA';
    //         $kardex->numero_doc = $detalle->nota_salidad->numero;
    //         $kardex->fecha = $detalle->nota_salidad->fecha;
    //         $kardex->cantidad = $detalle->cantidad;
    //         $kardex->producto_id = $detalle->producto_id;
    //         $kardex->descripcion = $detalle->nota_salidad->destino;
    //         $kardex->precio = $detalle->lote->detalle_compra ? $detalle->lote->detalle_compra->precio : $detalle->lote->detalle_nota->costo_soles;
    //         $kardex->importe = ($detalle->lote->detalle_compra ? $detalle->lote->detalle_compra->precio : $detalle->lote->detalle_nota->costo_soles) * $detalle->cantidad;
    //         $kardex->stock = $detalle->producto->stock;
    //         $kardex->save();

    //         //DB::update('update lote_productos set cantidad= ?,cantidad_logica = ? where id = ?', [$lote_productocantidad,$lote_productocantidad_logica,$detalle->lote_id]);

    //          //RECORRER DETALLE NOTAS
    //          //$cantidadProductos = LoteProducto::where('producto_id',$detalle->producto_id)->where('estado','1')->sum('cantidad');
    //          //ACTUALIZAR EL STOCK DEL PRODUCTO
    //          //$producto = Producto::findOrFail($detalle->producto_id);
    //          //$producto->stock = $cantidadProductos;
    //          //$producto->update();
    //     });
    // }
}
