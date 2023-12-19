<div class="modal inmodal" id="modal_pago" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title pago-title"></h4>
                <small class="font-bold pago-subtitle"></small>
            </div>
            <div class="modal-body">
                <form action="{{ route('ventas.caja.storePago') }}" id="pago_venta" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-6 br">
                            <div class="form-group d-none">
                                <label class="col-form-label required">Venta</label>
                                <input type="text" class="form-control" id="venta_id" name="venta_id" readonly>
                            </div>
                            <div class="form-group d-none">
                                <label class="col-form-label required">Tipo Pago</label>
                                <input type="text" class="form-control" id="tipo_pago_id" name="tipo_pago_id" readonly>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label required">Monto</label>
                                <input type="text" class="form-control" id="monto_venta" name="monto_venta" onkeypress="return filterFloat(event, this);" readonly>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label required">Efectivo</label>
                                <input type="text" value="0.00" class="form-control" id="efectivo" name="efectivo" onkeypress="return filterFloat(event, this);" onkeyup="changeEfectivo(this)">
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
                                <input type="text" class="form-control" id="importe" name="importe" onkeypress="return filterFloat(event, this);" onkeyup="changeImporte(this)">
                            </div>
                            <div class="form-group d-none" id="div_cuentas">
                                <label class="col-form-label">Cuentas</label>
                                <select name="cuenta_id" id="cuenta_id" class="select2_form form-control">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label id="imagen_label">Imagen:</label>

                                <div class="custom-file">
                                    <input id="imagen" type="file" name="imagen" class="custom-file-input"   accept="image/*">

                                    <label for="imagen" id="imagen_txt"
                                        class="custom-file-label selected">Seleccionar</label>

                                    <div class="invalid-feedback"><b><span id="error-imagen"></span></b></div>

                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <div class="col-6 align-content-center">
                                    <div class="row justify-content-end">
                                        <a href="javascript:void(0);" id="limpiar_imagen">
                                            <span class="badge badge-danger">x</span>
                                        </a>
                                    </div>
                                    <div class="row justify-content-center">
                                        <p>
                                            <img class="imagen" src="{{asset('img/default.png')}}"
                                                alt="">
                                            <input id="url_imagen" name="url_imagen" type="hidden" value="">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm" form="pago_venta"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <style>
        .imagen {
            width: 200px;
            height: 200px;
            border-radius: 10%;
        }

    </style>
@endpush
@push('scripts')
    <script>
        /* Limpiar imagen */
        $('#limpiar_imagen').click(function() {
            $('.imagen').attr("src", "{{asset('img/default.png')}}")
            var fileName = "Seleccionar"
            $('.custom-file-label').addClass("selected").html(fileName);
            $('#imagen').val('')
        })

        $('#imagen').on('change', function() {
            var fileInput = document.getElementById('imagen');
            var filePath = fileInput.value;
            var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
            $imagenPrevisualizacion = document.querySelector(".imagen");

            if (allowedExtensions.exec(filePath)) {
                var userFile = document.getElementById('imagen');
                userFile.src = URL.createObjectURL(event.target.files[0]);
                var data = userFile.src;
                $imagenPrevisualizacion.src = data;
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            } else {
                toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                $('.imagen').attr("src", "{{asset('img/default.png')}}")
            }
        });

        function changeModoPago(b)
        {
            let monto = $('#monto_venta').val();
            let importe = $('#importe').val();
            let efectivo = $('#efectivo').val();
            let suma = convertFloat(importe) + convertFloat(efectivo);
            $('#cuenta_id').val('').trigger('change.select2');
            // document.getElementById("efectivo").disabled = false;
            // document.getElementById("importe").disabled = false;
            $('#efectivo').attr('readonly', false);
            $('#importe').attr('readonly', false);
            if(b.value != '')
            {
                let cadena = b.value.split('-');
                if(cadena[1] == 'EFECTIVO')
                {
                    // document.getElementById("efectivo").disabled = true;
                    // document.getElementById("importe").disabled = true;
                    $('#efectivo').attr('readonly', true);
                    $('#importe').attr('readonly', true);
                    $('#efectivo').val('0.00');
                    $('#importe').val(monto);
                }
                $('#tipo_pago_id').val(cadena[0]);

                if(cadena[1] == 'TRANSFERENCIA')
                {
                    $('#div_cuentas').removeClass('d-none');
                }else{
                    $('#div_cuentas').addClass('d-none');
                }
            }else{
                $('#tipo_pago_id').val('');
            }

        }

        function changeEfectivo(b)
        {
            let modo = $('#modo_pago').val();
            let monto = convertFloat($('#monto_venta').val());
            let efectivo = convertFloat(b.value);
            let importe = $('#importe').val();
            let cadena = modo.split('-');
            if(cadena[1] != 'EFECTIVO')
            {
                let diferencia = monto - efectivo;
                $('#importe').val(diferencia.toFixed(2));
            }
        }

        function changeImporte(b)
        {
            let modo = $('#modo_pago').val();
            let monto = convertFloat($('#monto_venta').val());
            let importe = convertFloat(b.value);
            let efectivo = $('#efectivo').val();
            let cadena = modo.split('-');
            if(cadena[1] != 'EFECTIVO')
            {
                let diferencia = monto - importe;
                $('#efectivo').val(diferencia.toFixed(2));
            }
        }
    </script>
@endpush
