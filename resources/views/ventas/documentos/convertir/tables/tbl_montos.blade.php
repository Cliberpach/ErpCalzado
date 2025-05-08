<table style="margin:0 0 0 auto;">
    <tfoot>
        <tr>
          <td colspan="{{ count($tallas) + 5 }}" style="font-weight: bold; text-align:end;">SUBTOTAL:</td>
          <td class="subtotal" colspan="1" style="font-weight: bold; text-align:end;">
            S/ {{ number_format($documento->sub_total, 2, '.', ',') }}
          </td>
        </tr>
        <tr>
          <td colspan="{{ count($tallas) + 5 }}" style="font-weight: bold; text-align:end; vertical-align:middle;">EMBALAJE:</td>
          <td class="td-embalaje" colspan="1" style="font-weight: bold; text-align:end;">
            <div class="input-group">
              <span class="input-group-text" id="monto-embalaje">
                <i class="fas fa-box-open"></i>
              </span>
              <input readonly value="{{ number_format($documento->monto_embalaje, 2, '.', ',') }}" style="width: 10px;" type="text" class="form-control embalaje">
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="{{ count($tallas) + 5 }}" style="font-weight: bold; text-align:end;">ENVÍO:</td>
          <td class="td-envio" colspan="1" style="font-weight: bold; text-align:end;">
            <div class="input-group">
              <span class="input-group-text" id="monto-envio">
                <i class="fas fa-truck"></i>
              </span>
              <input readonly value="{{ number_format($documento->monto_envio, 2, '.', ',') }}" style="width: 70px;" type="text" class="form-control envio">
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="{{ count($tallas) + 5 }}" style="font-weight: bold; text-align:end;">DESCUENTO:</td>
          <td class="descuento" colspan="1" style="font-weight: bold; text-align:end;">
            S/ {{ number_format($documento->monto_descuento, 2, '.', ',') }}
          </td>
        </tr>
        <tr>
          <td colspan="{{ count($tallas) + 5 }}" style="font-weight: bold; text-align:end;">MONTO TOTAL:</td>
          <td class="total" colspan="1" style="font-weight: bold; text-align:end;">
            S/ {{ number_format($documento->total, 2, '.', ',') }}
          </td>
        </tr>
        <tr>
          <td colspan="{{ count($tallas) + 5 }}" style="font-weight: bold; text-align:end;">
            IGV ({{ number_format($documento->igv, 2, '.', ',') }}%):
          </td>
          <td class="igv" colspan="1" style="font-weight: bold; text-align:end;">
            S/ {{ number_format($documento->total_igv, 2, '.', ',') }}
          </td>
        </tr>
        <tr>
          <td colspan="{{ count($tallas) + 5 }}" style="font-weight: bold; text-align:end;">MONTO TOTAL A PAGAR:</td>
          <td class="total-pagar" colspan="1" style="font-weight: bold; text-align:end;">
            S/ {{ number_format($documento->total_pagar, 2, '.', ',') }}
          </td>
        </tr>
      </tfoot>
</table>
