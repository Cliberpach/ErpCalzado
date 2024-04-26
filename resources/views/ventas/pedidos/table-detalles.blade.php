<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered table-hover"  id="table-detalle-pedido">
      <thead>
        <tr>
         
            <th></th>
          
            <th  scope="col">PRODUCTO</th>
            @foreach ($tallas as $talla)
                <th style="background-color: rgb(210, 242, 242);"  scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
            @endforeach
          
            <th style="text-align: right;">PRECIO VENTA</th>
            <th style="text-align: right;">SUBTOTAL</th>
            <th style="text-align: center;">DSCTO %</th>
          
        </tr>
      </thead>
      <tbody>
        {{-- <tr>
          <td colspan="{{count($tallas) + 4 }}" style="font-weight: bold;text-align:center;">NO HAY DETALLES</td>
        </tr> --}}
      </tbody>
     
        <tfoot>
          <tr>
            <td colspan="{{count($tallas) + 4 }}" style="font-weight: bold;text-align:end;">SUBTOTAL:</td>
            <td class="subtotal" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
          <tr>
            <td colspan="{{count($tallas) + 4 }}" style="font-weight: bold;text-align:end;">DESCUENTO:</td>
            <td class="descuento" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
          <tr>
            <td colspan="{{count($tallas) + 4 }}" style="font-weight: bold;text-align:end;">MONTO TOTAL:</td>
            <td class="total" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
          <tr>
            <td colspan="{{count($tallas) + 4 }}" style="font-weight: bold;text-align:end;">IGV:</td>
            <td class="igv" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
          <tr>
            <td colspan="{{count($tallas) + 4 }}" style="font-weight: bold;text-align:end;">MONTO TOTAL A PAGAR:</td>
            <td class="total-pagar" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
          </tr>
        </tfoot>
      
    </table>
  </div>
  