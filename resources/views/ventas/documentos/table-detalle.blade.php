<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered table-hover" @if (!isset($carrito))  id="table-stocks" @else id="table-detalle" @endif>
      <thead>
        <tr>
          {{-- @if (isset($carrito))
              <th></th>
          @endif --}}
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
            <td colspan="{{count($tallas) + 2 }}" style="font-weight: bold;text-align:end;">SUBTOTAL:</td>
            <td class="subtotal" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
          <tr>
            <td  colspan="{{count($tallas) + 2}}" style="font-weight: bold;text-align:end;vertical-align:middle;">EMBALAJE:</td>
            <td class="td-embalaje" colspan="1" style="font-weight: bold;text-align:end;">
              <div class="input-group">
                <span class="input-group-text" id="monto-embalaje">
                  <i class="fas fa-box-open"></i>
                </span>
                <input disabled  style="width: 10px;" type="text" class="form-control embalaje" value="0" aria-label="Username" aria-describedby="monto_embalaje">
              </div>
            </td>
          </tr>
          <tr>
            <td  colspan="{{count($tallas) + 2 }}" style="font-weight: bold;text-align:end;">ENVÍO:</td>
            <td  class="td-envio" colspan="1" style="font-weight: bold;text-align:end;">
              <div class="input-group">
                <span class="input-group-text" id="monto-envio">
                  <i class="fas fa-truck"></i>
                </span>
                <input disabled style="width: 10px;"  type="text" class="form-control envio" value="0" aria-label="Username" aria-describedby="basic-addon1">
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="{{count($tallas) + 2 }}" style="font-weight: bold;text-align:end;">MONTO TOTAL:</td>
            <td class="total" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
          <tr>
            <td colspan="{{count($tallas) + 2 }}" style="font-weight: bold;text-align:end;">IGV:</td>
            <td class="igv" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
          <tr>
            <td colspan="{{count($tallas) + 2 }}" style="font-weight: bold;text-align:end;">MONTO TOTAL A PAGAR:</td>
            <td class="total-pagar" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
        </tfoot>
      @endif
    </table>
  </div>
  