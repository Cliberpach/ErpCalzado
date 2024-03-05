<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered table-hover" style="text-transform:uppercase" id="table-detalle-conv">
            <thead>
                <tr>

                    {{-- <th class="text-center"></th> --}}
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
                    <td colspan="{{count($tallas) + 2}}" style="font-weight: bold;text-align:end;">MONTO SUBTOTAL:</td>
                    <td class="subtotal" colspan="{{count($tallas) + 3}}" style="font-weight: bold;text-align:end;"></td>
                </tr>
                <tr>
                    <td colspan="{{count($tallas) + 2}}" style="font-weight: bold;text-align:end;">IGV:</td>
                    <td class="igv" colspan="{{count($tallas) + 3}}" style="font-weight: bold;text-align:end;"></td>
                </tr>
                <tr>
                    <td colspan="{{count($tallas) + 2}}" style="font-weight: bold;text-align:end;">MONTO TOTAL:</td>
                    <td  class="total" colspan="{{count($tallas) + 3}}" style="font-weight: bold;text-align:end;"></td>
                </tr> 
            </tfoot>
    </table> 
</div>
