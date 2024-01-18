<div class="table-responsive">
    <table class="table table-hover" @if (!isset($carrito))  id="table-stocks" @else id="table-detalle" @endif>
      <thead>
        <tr>
          @if (isset($carrito))
              <th></th>
          @endif
          <th scope="col">PRODUCTO</th>
          @foreach ($tallas as $talla)
              <th scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
              @if (!isset($carrito))
                <th>CANT</th>
              @endif
          @endforeach
          
            <th>PRECIO VENTA</th>  
            @if (isset($carrito))
                <th>SUBTOTAL</th>
             @endif
        </tr>
      </thead>
      <tbody>
          
      </tbody>
      @if (isset($carrito))
        <tfoot>
          <tr>
            <td colspan="7" style="font-weight: bold;text-align:end;">MONTO SUBTOTAL:</td>
            <td class="subtotal" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
          <tr>
            <td colspan="7" style="font-weight: bold;text-align:end;">IGV:</td>
            <td class="igv" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
          <tr>
            <td colspan="7" style="font-weight: bold;text-align:end;">MONTO TOTAL:</td>
            <td  class="total" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
        </tfoot>
      @endif
    </table>
  </div>
  