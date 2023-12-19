<div class="modal inmodal" id="modal_pago_show" tabindex="-1" role="dialog" aria-hidden="true">
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
                <form action="{{ route('ventas.documento.updatePago') }}" id="update_pago_venta" method="POST" enctype="multipart/form-data">
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
                                <input type="text" value="0.00" class="form-control" id="efectivo" name="efectivo" onkeypress="return filterFloat(event, this);" onkeyup="changeEfectivoUpdate(this)">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label required">Modo de pago</label>
                                <select name="modo_pago" id="modo_pago" class="select2_form form-control" onchange="changeModoPagoUpdate(this)">
                                    <option></option>
                                    @foreach (modos_pago() as $modo)
                                        <option value="{{$modo->id}}" data-descripcion="{{ $modo->descripcion }}">{{$modo->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label  class="col-form-label required">Importe</label>
                                <input type="text" class="form-control" id="importe" name="importe" onkeypress="return filterFloat(event, this);" onkeyup="changeImporteUpdate(this)">
                            </div>
                            <div class="form-group d-none" id="div_cuentas">
                                <label class="col-form-label">Cuentas</label>
                                <select name="cuenta_id" id="cuenta_id_show" class="select2_form form-control">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label id="imagen_label">Imagen:</label>
                                <div class="custom-file">
                                    <input id="imagen_update" type="file" name="imagen" class="custom-file-input" accept="image/*">

                                    <label for="imagen" id="imagen_txt"
                                        class="custom-file-label selected">Seleccionar</label>

                                    <div class="invalid-feedback"><b><span id="error-imagen"></span></b></div>
                                    <input type="hidden" name="ruta_pago" id="ruta_pago">
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
                                            <img class="imagen_update" src="{{asset('img/default.png')}}" alt="IMG">
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
                <div class="col-md-6 text-right">
                    @if(PuntoVenta())
                    <button type="submit" class="btn btn-primary btn-sm" form="update_pago_venta"><i class="fa fa-pencil"></i> Editar</button>
                    @endif
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
    <style>
        .imagen_update {
            width: 200px;
            height: 200px;
            border-radius: 10%;
        }

    </style>
@endpush
@push('scripts')
    <script>
        /* Limpiar imagen */
        $('#modal_pago_show #limpiar_imagen').click(function() {
            $('#modal_pago_show .imagen_update').attr("src", "{{asset('img/default.png')}}")
            var fileName = "Seleccionar"
            $('#modal_pago_show .custom-file-label').addClass("selected").html(fileName);
            $('#modal_pago_show #imagen_update').val('')
            $('#modal_pago_show #ruta_pago').val('')
        })

        $('#modal_pago_show #imagen_update').on('change', function() {
            var fileInput = document.getElementById('imagen_update');
            var filePath = fileInput.value;
            var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
            $imagenPrevisualizacion = document.querySelector(".imagen_update");

            if (allowedExtensions.exec(filePath)) {
                var userFile = document.getElementById('imagen_update');
                userFile.src = URL.createObjectURL(event.target.files[0]);
                var data = userFile.src;
                $imagenPrevisualizacion.src = data;
                let fileName = $(this).val().split('\\').pop();
                $(this).next('#modal_pago_show .custom-file-label').addClass("selected").html(fileName);
            } else {
                toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                $('.imagen_update').attr("src", "{{asset('img/default.png')}}")
            }
        });

        function changeModoPagoUpdate(b)
        {
            let monto = $('#modal_pago_show #monto_venta').val();
            let importe = $('#modal_pago_show #importe').val();
            let efectivo = $('#modal_pago_show #efectivo').val();
            let suma = convertFloat(importe) + convertFloat(efectivo);
            $('#modal_pago_show #cuenta_id').val('').trigger('change.select2');
            // document.getElementById("efectivo").disabled = false;
            // document.getElementById("importe").disabled = false;
            $('#modal_pago_show #efectivo').attr('readonly', false);
            $('#modal_pago_show #importe').attr('readonly', false);
            if(b.value != '')
            {
                let id = b.value;
                let descripcion = $('#modal_pago_show #modo_pago option:selected').data('descripcion');
                console.log(descripcion);
                if(descripcion == 'EFECTIVO')
                {
                    // document.getElementById("efectivo").disabled = true;
                    // document.getElementById("importe").disabled = true;
                    $('#modal_pago_show #efectivo').attr('readonly', true);
                    $('#modal_pago_show #importe').attr('readonly', true);
                    $('#modal_pago_show #efectivo').val('0.00');
                    $('#modal_pago_show #importe').val(monto);
                }
                $('#modal_pago_show #tipo_pago_id').val(id);

                if(descripcion == 'TRANSFERENCIA')
                {
                    $('#modal_pago_show #div_cuentas').removeClass('d-none');
                }else{
                    $('#modal_pago_show #div_cuentas').addClass('d-none');
                }
            }else{
                $('#modal_pago_show #tipo_pago_id').val('');
            }

        }

        function changeEfectivoUpdate(b)
        {
            let modo = $('#modal_pago_show #modo_pago').val();
            let monto = convertFloat($('#modal_pago_show #monto_venta').val());
            let efectivo = convertFloat(b.value);
            let importe = $('#modal_pago_show #importe').val();
            let descripcion = $('#modal_pago_show #modo_pago option:selected').data('descripcion');
            if(descripcion != 'EFECTIVO')
            {
                let diferencia = monto - efectivo;
                $('#modal_pago_show #importe').val(diferencia.toFixed(2));
            }
        }

        function changeImporteUpdate(b)
        {
            let modo = $('#modal_pago_show #modo_pago').val();
            let monto = convertFloat($('#modal_pago_show #monto_venta').val());
            let importe = convertFloat(b.value);
            let efectivo = $('#modal_pago_show #efectivo').val();
            let descripcion = $('#modal_pago_show #modo_pago option:selected').data('descripcion');
            if(descripcion != 'EFECTIVO')
            {
                let diferencia = monto - importe;
                $('#modal_pago_show #efectivo').val(diferencia.toFixed(2));
            }
        }
    </script>
@endpush
