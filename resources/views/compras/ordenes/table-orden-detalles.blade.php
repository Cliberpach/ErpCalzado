<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered table-hover"   id="table-orden-detalles">
      <thead>
        <tr>
            <th></th>
            <th scope="col">PRODUCTO</th>
            <th scope="col">COLOR</th>
          @foreach ($tallas as $talla)
            <th style="background-color: rgb(210, 242, 242);"  scope="col" data-talla={{$talla->id}}>{{$talla->descripcion}}</th>
          @endforeach
          
          <th style="text-align: right;">P. UNIT</th>
          <th style="text-align: right;">TOTAL</th>

        </tr>
      </thead>
      <tbody>
       
      </tbody>
      <tfoot>
        <tr>
          <td colspan="{{count($tallas) + 4 }}" style="font-weight: bold;text-align:end;">SUBTOTAL:</td>
          <td class="tfoot-subtotal" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
        </tr>
        <tr>
          <td colspan="{{count($tallas) + 4 }}" style="font-weight: bold;text-align:end;">IGV:</td>
          <td class="tfoot-igv" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
        </tr>
        <tr>
          <td colspan="{{count($tallas) + 4 }}" style="font-weight: bold;text-align:end;">MONTO TOTAL:</td>
          <td class="tfoot-total" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
        </tr>
      </tfoot>
    </table>
  </div>
  