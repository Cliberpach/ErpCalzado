<div class="modal inmodal" id="modal_editar_egreso" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon d-none"></i>
                <h4 class="modal-title">Egreso</h4>
                <small class="font-bold">Editar Egreso</small>
            </div>
            <div class="modal-body">
                <form role="form" action="" method="POST" id="frm_editar_egreso">
                    {{ csrf_field() }} {{ method_field('POST') }}
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="" class="required">Cuentas</label>
                                <select name="cuenta_editar" id="cuenta_editar" class="select2_form form-control" required>
                                    <option value=""></option>
                                    @foreach (cuentas() as $cuenta)
                                        <option value="{{ $cuenta->id }}">{{ $cuenta->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="required">Tipo Documento:</label>
                                <input type="text" name="tipo_documento_editar" id="tipo_documento_editar" class="form-control" value="RECIBO" readonly>
                                {{-- <select name="tipo_documento_editar" id="tipo_documento_editar" class="form-control select2_form" required>
                                    <option value=""></option>
                                    @foreach (tipo_compra() as $documento)
                                            <option value="{{$documento->id}}">{{$documento->descripcion}}</option>
                                    @endforeach
                                </select> --}}
                            </div>
                            <div class="form-group">
                                <label class="required">Monto</label>
                                <input type="text" class="form-control" id="monto_editar" name="monto_editar" required readonly>
                            </div>
                            <div class="form-group">
                                <label class="">Documento</label>
                                <input type="text" name="documento_editar" id="documento_editar" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="required">Descripcion:</label>
                                <textarea name="descripcion_editar" id="descripcion_editar" cols="30" rows="2"
                                    class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="col-form-label required">Efectivo</label>
                                <input type="text" value="0.00" class="form-control" id="efectivo_editar"
                                    onkeypress="return filterFloat(event, this);" onkeyup="changeEfectivoEditar()"
                                    name="efectivo_editar" required>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label required">Modo de pago</label>
                                <select name="modo_pago_editar" id="modo_pago_editar" class="select2_form form-control"
                                    onchange="changeModoPagoEditar(this)" required>
                                    <option></option>
                                    @foreach (modos_pago() as $modo)
                                        <option value="{{ $modo->id }}">
                                            {{ $modo->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label required">Importe</label>
                                <input type="text" class="form-control" id="importe_editar" value="0.00"
                                    onkeypress="return filterFloat(event, this);" onkeyup="changeImporteEditar()"
                                    name="importe_editar" required>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm btn-submit-edit"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
@push('styles')
@endpush
@push('scripts')
    <script>
        $("#tipo_documento_editar").on('change', function (e) {
            var tipoDocumento=$("#tipo_documento_editar option:selected").text()
           if(tipoDocumento=="RECIBO")
           {
            $("#documento_editar").attr('disabled',true)
           }
           else
           {
            $("#documento_editar").attr('disabled',false)
           }
        });

        $("#frm_editar_egreso").on('submit', function(e) {
            e.preventDefault();

            var efectivo_editar = $("#efectivo_editar").val();
            var importe_editar = $("#importe_editar").val();
            var cuenta_editar = $("#cuenta_editar").val();
            var cantidad_editar = convertFloat(efectivo_editar) + convertFloat(importe_editar);
            var modo_pago_editar = $("#modo_pago_editar").val();

            let correcto = true;
            if (cantidad_editar.length == 0 || cuenta_editar.length == 0  || modo_pago_editar.length == 0) {
                correcto = false;
                toastr.error('Ingrese todos los datos');
            }

            if(cantidad_editar == 0 || cantidad_editar < 0)
            {
                correcto = false;
                toastr.error('El monto de egreso debe ser mayor a 0.');
            }

            if(correcto)
            {
                $('.btn-submit-edit').attr('disabled',true);
                $('.btn-submit-edit').html('Cargando <span class="loading bullet"></span> ');
                this.submit();
            }
        })

        function changeModoPagoEditar(b)
        {
            if(b.value == 1) {
                $("#efectivo_editar").attr('readonly',false)
                $("#importe_editar").attr('readonly',true)
                $("#importe_editar").val(0.00)
                changeEfectivoEditar()
            }
            else{
                $("#efectivo_editar").attr('readonly',false)
                $("#importe_editar").attr('readonly',false)
            }
        }

        function changeEfectivoEditar()
        {
            let efectivo = convertFloat($('#efectivo_editar').val());
            let importe = convertFloat($('#importe_editar').val());
            let suma = efectivo + importe;
            $('#monto_editar').val(suma.toFixed(2))
        }

        function changeImporteEditar()
        {
            let efectivo = convertFloat($('#efectivo_editar').val());
            let importe = convertFloat($('#importe_editar').val());
            let suma = efectivo + importe;
            $('#monto_editar').val(suma.toFixed(2));
        }
    </script>
@endpush
