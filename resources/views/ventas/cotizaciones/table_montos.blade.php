<table style="margin:0 0 0 auto;">
    <tfoot>
        <tr>
          <td colspan="{{count($tallas) + 5 }}" style="font-weight: bold;text-align:end;">SUBTOTAL:</td>
          <td class="subtotal" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
        </tr>
        <tr>
          <td  colspan="{{count($tallas) + 5}}" style="font-weight: bold;text-align:end;vertical-align:middle;">EMBALAJE:</td>
          <td class="td-embalaje" colspan="1" style="font-weight: bold;text-align:end;">
            <div class="input-group">
              <span class="input-group-text" id="monto-embalaje">
                <i class="fas fa-box-open"></i>
              </span>
              <input  style="width: 10px;" type="text" class="form-control embalaje" value="0" aria-label="Username" aria-describedby="monto_embalaje">
            </div>
          </td>
        </tr>
        <tr>
          <td  colspan="{{count($tallas) + 5 }}" style="font-weight: bold;text-align:end;">ENVÍO:</td>
          <td  class="td-envio" colspan="1" style="font-weight: bold;text-align:end;">
            <div class="input-group">
              <span class="input-group-text" id="monto-envio">
                <i class="fas fa-truck"></i>             
              </span>
              <input style="width: 70px;"  type="text" class="form-control envio" value="0" aria-label="Username" aria-describedby="basic-addon1">
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="{{count($tallas) + 5 }}" style="font-weight: bold;text-align:end;">DESCUENTO:</td>
          <td class="descuento" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
        </tr>
        <tr>
          <td colspan="{{count($tallas) + 5 }}" style="font-weight: bold;text-align:end;">MONTO TOTAL:</td>
          <td class="total" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
        </tr>
        <tr>
          <td colspan="{{count($tallas) + 5 }}" style="font-weight: bold;text-align:end;">IGV({{$porcentaje_igv}}%):</td>
          <td class="igv" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
        </tr>
        <tr>
          <td colspan="{{count($tallas) + 5 }}" style="font-weight: bold;text-align:end;">MONTO TOTAL A PAGAR:</td>
          <td class="total-pagar" colspan="1" style="font-weight: bold;text-align:end;">S/. 00.00</td>
        </tr>
      </tfoot>
</table>