<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered table-hover" id="table-detalle">
      <thead>
        <tr>
          <th scope="col">PRODUCTO</th>
          @foreach ($tallas as $talla)
              <th scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
          @endforeach
          
          <th>PRECIO VENTA</th>  
          <th>SUBTOTAL</th>
          <th>DESCUENTO %</th>

        </tr>
      </thead>
      <tbody>
          
      </tbody>
      
      <tfoot>
          <tr>
            <td colspan="{{count($tallas) + 3 }}" style="font-weight: bold;text-align:end;">SUBTOTAL:</td>
            <td class="subtotal" colspan="1" style="font-weight: bold;text-align:end;">S/. {{ number_format($cotizacion->sub_total, 2, '.', ',') }}</td>
          </tr>
          <tr>
            <td  colspan="{{count($tallas) + 3}}" style="font-weight: bold;text-align:end;vertical-align:middle;">EMBALAJE:</td>
            <td class="td-embalaje" colspan="1" style="font-weight: bold;text-align:end;">
              <div class="input-group">
                <span class="input-group-text" id="monto-embalaje">
                  <i class="fas fa-box-open"></i>
                </span>
                <input disabled  style="width: 10px;" type="text" class="form-control embalaje" value="{{$cotizacion->monto_embalaje}}" aria-label="Username" aria-describedby="monto_embalaje">
              </div>
            </td>
          </tr>
          <tr>
            <td  colspan="{{count($tallas) + 3 }}" style="font-weight: bold;text-align:end;">ENVÍO:</td>
            <td  class="td-envio" colspan="1" style="font-weight: bold;text-align:end;">
              <div class="input-group">
                <span class="input-group-text" id="btn-envio">
                  <i class="fas fa-truck btn btn-light"></i>
                </span>
                <input disabled style="width: 10px;"  type="text" class="form-control envio" value="{{$cotizacion->monto_envio}}" aria-label="Username" aria-describedby="basic-addon1">
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="{{count($tallas) + 3 }}" style="font-weight: bold;text-align:end;">DESCUENTO:</td>
            <td class="total" colspan="1" style="font-weight: bold;text-align:end;">S/. {{ number_format($cotizacion->monto_descuento, 2, '.', ',') }}</td>
          </tr>
          <tr>
            <td colspan="{{count($tallas) + 3 }}" style="font-weight: bold;text-align:end;">MONTO TOTAL:</td>
            <td class="total" colspan="1" style="font-weight: bold;text-align:end;">S/. {{ number_format($cotizacion->total, 2, '.', ',') }}</td>
          </tr>
          <tr>
            <td colspan="{{count($tallas) + 3 }}" style="font-weight: bold;text-align:end;">IGV:</td>
            <td class="igv" colspan="1" style="font-weight: bold;text-align:end;">S/. {{ number_format($cotizacion->total_igv, 2, '.', ',') }}</td>
          </tr>
          <tr>
            <td colspan="{{count($tallas) + 3 }}" style="font-weight: bold;text-align:end;">MONTO TOTAL A PAGAR:</td>
            <td class="total-pagar" colspan="1" style="font-weight: bold;text-align:end;">S/. {{ number_format($cotizacion->total_pagar, 2, '.', ',') }}</td>
          </tr>
      </tfoot>
    </table>
  </div>
  