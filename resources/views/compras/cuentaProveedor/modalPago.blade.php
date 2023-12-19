<div class="modal inmodal" id="modal_pago" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Venta - Pago</h4>
                <small class="font-bold">Forma de cobranza</small>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-form-label required">Monto</label>
                    <input type="text" class="form-control" id="monto_venta" name="monto_venta" onkeypress="return filterFloat(event, this);" readonly>
                </div>
                <div class="form-group">
                    <label class="col-form-label required">Efectivo</label>
                    <input type="text" value="0.00" class="form-control" id="efectivo_venta" onkeypress="return filterFloat(event, this);" onkeyup="changeEfectivo(this)" name="efectivo_venta">
                </div>
                <div class="form-group">
                    <label class="col-form-label required">Modo de pago</label>
                    <select name="modo_pago" id="modo_pago" class="select2_form form-control" onchange="changeModoPago(this)">
                        <option></option>
                        @foreach (modos_pago() as $modo)
                            <option value="{{$modo->id}}-{{$modo->descripcion}}">{{$modo->descripcion}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label  class="col-form-label required">Importe</label>
                    <input type="text" class="form-control" id="importe_venta" onkeypress="return filterFloat(event, this);" onkeyup="changeImporte(this)" name="importe_venta">
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" id="btn_grabar_pago" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function changeModoPago(b)
        {
            let monto = $('#monto_venta').val();
            let importe = $('#importe_venta').val();
            let efectivo = $('#efectivo_venta').val();
            let suma = convertFloat(importe) + convertFloat(efectivo);
            document.getElementById("efectivo_venta").disabled = false;
            document.getElementById("importe_venta").disabled = false;
            if(b.value != '')
            {
                let cadena = b.value.split('-');
                if(cadena[1] == 'EFECTIVO')
                {
                    document.getElementById("efectivo_venta").disabled = true;
                    document.getElementById("importe_venta").disabled = true;
                    $('#efectivo_venta').val('0.00');
                    $('#importe_venta').val(monto);
                }
                $('#tipo_pago_id').val(cadena[0]);
            }
        }

        function changeEfectivo(b)
        {
            let modo = $('#modo_pago').val();
            let monto = convertFloat($('#monto_venta').val());
            let efectivo = convertFloat(b.value);
            let importe = $('#importe_venta').val();
            let cadena = modo.split('-');
            if(cadena[1] != 'EFECTIVO')
            {
                let diferencia = monto - efectivo;
                $('#importe_venta').val(diferencia.toFixed(2));
            }
        }

        function changeImporte(b)
        {
            let modo = $('#modo_pago').val();
            let monto = convertFloat($('#monto_venta').val());
            let importe = convertFloat(b.value);
            let efectivo = $('#efectivo_venta').val();
            let cadena = modo.split('-');
            if(cadena[1] != 'EFECTIVO')
            {
                let diferencia = monto - importe;
                $('#efectivo_venta').val(diferencia.toFixed(2));
            }
        }
    </script>
@endpush
