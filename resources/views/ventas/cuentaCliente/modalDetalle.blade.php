<div class="modal inmodal" id="modal_detalle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">

                <span class="text-uppercase font-weight-bold"> Detalle de Cuenta Cliente</span>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="cuenta_cliente_id" id="cuenta_cliente_id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row align-items-end">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="" class="required">Cliente</label>
                                    <input type="text" name="cliente" id="cliente"
                                        class="form-control form-control-sm" disabled>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="" class="required">Numero</label>
                                    <input type="text" name="numero" id="numero"
                                        class="form-control form-control-sm" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="required">Monto</label>
                                    <input type="text" name="monto" id="monto" class="form-control form-control-sm"
                                        disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="required">Saldo</label>
                                    <input type="text" name="saldo" id="saldo" class="form-control form-control-sm"
                                        disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                     <label for="" class="required">Estado</label>
                                     <input type="text" name="estado" id="estado"
                                         class="form-control form-control-sm" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="btn btn-danger" id="btn-detalle" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12" style="zoom: 85%;">
                                <div class="table-responsive">
                                    <table class="table dataTables-detalle table-striped table-bordered table-hover"
                                        style="text-transform:uppercase">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Fecha</th>
                                                <th class="text-center">Observacion</th>
                                                <th class="text-center">Monto</th>
                                                <th class="text-center">Im&aacute;gen</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <form id="frmDetalle" method="POST" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="" class="required">Pago</label>
                                        <select name="pago" id="pago" class="form-control select2_form" required onchange="tipoPago(this)">
                                            <option value="A CUENTA">A CUENTA</option>
                                            <option value="TODO">TODO</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="required">Fecha</label>
                                        <input type="date" name="fecha" id="fecha" class="form-control" value="{{$fecha_hoy}}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="required">Cantidad</label>
                                        <input type="number" min="1" name="cantidad" id="cantidad" value="0.00" class="form-control" onkeypress="return filterFloat(event, this);" readonly required>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Observacion</label>
                                        <textarea name="observacion" id="observacion" cols="30" rows="3"
                                        class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label required">Efectivo</label>
                                        <input type="text" value="0.00" class="form-control" id="efectivo_venta"
                                            onkeypress="return filterFloat(event, this);" onkeyup="changeEfectivo()"
                                            name="efectivo_venta" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-form-label required">Modo de pago</label>
                                        <select name="modo_pago" id="modo_pago" class="select2_form form-control"
                                            onchange="changeModoPago(this)" required>
                                            <option></option>
                                            @foreach (modos_pago() as $modo)
                                                <option value="{{ $modo->id }}">
                                                    {{ $modo->descripcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-form-label required">Importe</label>
                                        <input type="text" class="form-control" id="importe_venta" value="0.00"
                                            onkeypress="return filterFloat(event, this);" onkeyup="changeImporte()"
                                            name="importe_venta" required>
                                    </div>
                                </div>
                                <div class="col-12">
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
                </div>

            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm" id="btn_guardar_detalle" form="frmDetalle">
                        Guardar
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i> Cancelar</button>
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
        $("#frmDetalle").submit(function(e) {
            e.preventDefault();
            var pago                = $("#modal_detalle #pago").val();
            var fecha               = $("#modal_detalle #fecha").val();
            var cantidad            = parseFloat($("#modal_detalle #cantidad").val());
            var observacion         = $("#modal_detalle #observacion").val();
            var saldo               = parseFloat($("#modal_detalle #saldo").val());
            var id_cuenta_cliente   = $("#modal_detalle #cuenta_cliente_id").val();

            var efectivo_venta = $("#efectivo_venta").val();
            var importe_venta = $("#importe_venta").val();
            var cantidad = parseFloat(efectivo_venta) + parseFloat(importe_venta);
            var modo_pago = $("#modo_pago").val();

            let correcto = true;
            if (pago.length == 0 || fecha.length == 0 || fecha.length == 0 || cantidad.length == 0) {
                correcto = false;
                toastr.error('Ingrese todo los datos');
            }

            if(cantidad == 0 || cantidad < 0)
            {
                correcto = false;
                toastr.error('El monto de pago debe ser mayor a 0.');
            }

            if(correcto) {
                if (saldo == 0) {
                    toastr.error("Ya esta cancelado");
                } else {
                    var enviar = true;
                    if (pago == "TODO") {

                        /*if (cantidad < saldo || cantidad == saldo) {
                            toastr.error("El monto a pagar, no cumple para el pago a varias cuentas")
                            enviar = false
                        }*/
                        if (cantidad != saldo) {
                            toastr.error("El monto a pagar, no cumple para el pago de todo el saldo: " + saldo)
                            enviar = false
                        }
                    } else {
                        /*if (cantidad > saldo) {
                            toastr.error('El tipo de pago se puede hacer a varios, seleccione de nuevo');
                            enviar = false;
                        }*/
                        if (cantidad > saldo) {
                            toastr.error('La cantidad a pagar excede el saldo: ' + saldo);
                            enviar = false;
                        }
                    }

                    axios.get("{{ route('Caja.movimiento.verificarestado') }}").then((value) => {
                        let data = value.data
                        if (!data.success) {
                             toastr.error(data.mensaje);
                        }  else {
                            if (enviar) {
                                $('#btn_guardar_detalle').attr('disabled',true);
                                $('#btn_guardar_detalle').html('Cargando <span class="loading bullet"></span> ');
                                this.submit();
                            }
                        }
                    })
                }
            }

        });

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
                $imagenPrevisualizacion.src = data
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            } else {
                toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                $('.imagen').attr("src", "{{asset('img/default.png')}}")
            }
        });

        function changeModoPago(b)
        {
            if(b.value == 1) {
                    $("#efectivo_venta").attr('readonly',false)
                    $("#importe_venta").attr('readonly',true)
                    $("#importe_venta").val(0.00)
                    changeEfectivo()
            }
            else{
                $("#efectivo_venta").attr('readonly',false)
                $("#importe_venta").attr('readonly',false)
            }

            if(document.querySelector('#pago').value = "TODO"){
                document.querySelector('#efectivo_venta').readOnly      =   true;
            }
        }

        function changeEfectivo()
        {
            let efectivo = convertFloat($('#efectivo_venta').val());
            let importe = convertFloat($('#importe_venta').val());
            let suma = efectivo + importe;
            $('#cantidad').val(suma.toFixed(2))
        }

        function changeImporte()
        {
            let efectivo = convertFloat($('#efectivo_venta').val());
            let importe = convertFloat($('#importe_venta').val());
            let suma = efectivo + importe;
            $('#cantidad').val(suma.toFixed(2));
        }

        function tipoPago(tipoPago){
            const tipo_pago =   tipoPago.value;
            if(tipo_pago == "TODO"){
                const saldo =   document.querySelector('#saldo').value;
                document.querySelector('#efectivo_venta').value         =   saldo;
                document.querySelector('#efectivo_venta').readOnly      =   true;
                document.querySelector('#cantidad').value               =   saldo;      
            }
            if(tipo_pago == "A CUENTA"){
                document.querySelector('#efectivo_venta').value         =   "0.00";
                document.querySelector('#efectivo_venta').readOnly      = false;
            }
        }
    </script>
@endpush
