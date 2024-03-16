<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered table-hover" style="text-transform:uppercase" id="table-detalle-docno">
            <thead>
                <tr>

                    <th class="text-center"></th>
                    <th class="text-center">PRODUCTO</th>
                    @foreach ($tallas as $talla)
                        <th class="text-center">{{$talla->descripcion}}</th>
                    @endforeach
                    <th class="text-center">P. VENTA</th>
                    <th class="text-center">SUBTOTAL</th>

                </tr>
            </thead>
            <tbody>

            </tbody>

            <tfoot>
                <tr>
                    <td colspan="{{count($tallas) + 3 }}" style="font-weight: bold;text-align:end;">SUBTOTAL:</td>
                    <td class="subtotal" colspan="1" style="font-weight: bold;text-align:end;"> S/{{number_format($documento->sub_total, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td  colspan="{{count($tallas) + 3}}" style="font-weight: bold;text-align:end;vertical-align:middle;">EMBALAJE:</td>
                    <td class="td-embalaje" colspan="1" style="font-weight: bold;text-align:end;">
                        <div class="input-group">
                            <span class="input-group-text" id="monto-embalaje">
                                <i class="fas fa-box-open"></i>
                            </span>
                            <input  style="width: 10px;" type="text" class="form-control embalaje" value="{{$documento->monto_embalaje}}" aria-label="Username" aria-describedby="monto_embalaje">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td  colspan="{{count($tallas) + 3 }}" style="font-weight: bold;text-align:end;">ENVÍO:</td>
                    <td  class="td-envio" colspan="1" style="font-weight: bold;text-align:end;">
                        <div class="input-group">
                            <span class="input-group-text" id="monto-envio">
                                <i class="fas fa-truck"></i>
                            </span>
                            <input style="width: 10px;"  type="text" class="form-control envio" value="{{$documento->monto_envio }}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="{{count($tallas) + 3 }}" style="font-weight: bold;text-align:end;">MONTO TOTAL:</td>
                    <td class="total" colspan="1" style="font-weight: bold;text-align:end;">S/{{ number_format($documento->total, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="{{count($tallas) + 3 }}" style="font-weight: bold;text-align:end;">IGV:</td>
                    <td class="igv" colspan="1" style="font-weight: bold;text-align:end;">S/{{ number_format($documento->total_igv, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="{{count($tallas) + 3 }}" style="font-weight: bold;text-align:end;">MONTO TOTAL A PAGAR:</td>
                    <td class="total-pagar" colspan="1" style="font-weight: bold;text-align:end;">S/{{ number_format($documento->total_pagar, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
    </table> 
</div>
