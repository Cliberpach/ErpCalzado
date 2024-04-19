<div class="modal inmodal" id="modal_detalle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">

                <span class="text-uppercase font-weight-bold"> Detalle de Cuenta Proveedor</span>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="cuenta_proveedor_id" id="cuenta_proveedor_id">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="" class="required">Proveedor</label>
                                        <input type="text" name="proveedor" id="proveedor"
                                            class="form-control form-control-sm" disabled>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="" class="required">Numero</label>
                                        <input type="text" name="numero" id="numero"
                                            class="form-control form-control-sm" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="" class="required">Monto</label>
                                        <input type="text" name="monto" id="monto" class="form-control form-control-sm"
                                            disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="" class="required">Saldo</label>
                                        <input type="text" name="saldo" id="saldo" class="form-control form-control-sm"
                                            disabled>
                                    </div>
                                </div>
                                <div class="row align-items-end">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="required">Estado</label>
                                            <input type="text" name="estado" id="estado"
                                                class="form-control form-control-sm" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <a class="btn btn-danger" style="color:white" id="btn-detalle"
                                                target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table dataTables-detalle table-striped table-bordered table-hover"
                                    style="text-transform:uppercase">
                                    <thead>
                                        <tr>
                                            <th class="text-center pletra">Fecha</th>
                                            <th class="text-center pletra">Observacion</th>
                                            <th class="text-center pletra">Monto</th>
                                            <th class="text-center pletra">Im&aacute;gen</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="" class="col-form-label required">Pago</label>
                                            <select name="pago" id="pago" class="form-control select2_form" required onchange="tipoPago(this)">
                                                <option value="A CUENTA">A CUENTA</option>
                                                <option value="TODO">TODO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="" class="col-form-label required">Observacion</label>
                                            <textarea name="observacion" id="observacion" cols="30" rows="2"
                                                class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="" class="col-form-label required">Fecha</label>
                                            <input type="date" name="fecha" id="fecha" value="{{ $fecha_hoy }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label id="imagen_label">Imagen:</label>

                                            <div class="custom-file">
                                                <input id="imagen" type="file" name="imagen" class="custom-file-input"
                                                    accept="image/*">

                                                <label for="imagen" id="imagen_txt"
                                                    class="custom-file-label selected">Seleccionar</label>

                                                <div class="invalid-feedback"><b><span id="error-imagen"></span></b>
                                                </div>

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
                                                        <img class="imagen"
                                                            src="{{ asset('img/default.png') }}" alt="">
                                                        <input id="url_imagen" name="url_imagen" type="hidden" value="">
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="col-form-label required">Efectivo</label>
                                    <input type="text" value="0.00" class="form-control" id="efectivo_venta"
                                        {{-- onkeypress="return filterFloat(event, this);" onkeyup="changeEfectivo(this)" --}} name="efectivo_venta">
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label required">Modo de pago</label>
                                    <select name="modo_pago" id="modo_pago" class="select2_form form-control"
                                        onchange="changeModoPago(this)">
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
                                        {{-- onkeypress="return filterFloat(event, this);" onkeyup="changeImporte(this)" --}} name="importe_venta">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-primary btn-sm" id="btn_guardar_detalle"><i
                            class="fa fa-save"></i> Guardar</button>
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

        .pletra {
            font-size: 0.9em;
        }

    </style>
@endpush
@push('scripts')
    <script>
        var fileImagen = null;
        $("#btn_guardar_detalle").click(function(e) {
            e.preventDefault();
            var pago = $("#modal_detalle #pago").val();
            var fecha = $("#modal_detalle #fecha").val();
            var efectivo_venta = $("#efectivo_venta").val();
            var importe_venta = $("#importe_venta").val();
            var cantidad = parseFloat(efectivo_venta) + parseFloat(importe_venta);
            var observacion = $("#modal_detalle #observacion").val();
            var saldo = parseFloat($("#modal_detalle #saldo").val());
            var modo_pago = $("#modo_pago").val();
            var id_cuenta_proveedor = $("#modal_detalle #cuenta_proveedor_id").val()
            if (pago.length == 0 || fecha.length == 0 || fecha.length == 0 || cantidad.length == 0 || observacion
                .length == 0) {
                toastr.error('Ingrese todo los datos');
            } else {
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
                            toastr.error('El tipo de pago se puede hacer a varios, seleccione de nuevo')
                            enviar = false
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
                                const config = {
                                    headers: {
                                        "content-type": "multipart/form-data"
                                    }
                                };
                                let data = new FormData();
                                data.append("id", id_cuenta_proveedor);
                                data.append("pago", pago);
                                data.append("fecha", fecha);
                                data.append("cantidad", cantidad);
                                data.append("observacion", observacion);
                                data.append("efectivo_venta", efectivo_venta)
                                data.append("importe_venta", importe_venta)
                                data.append("modo_pago", modo_pago)
                                data.append("file", fileImagen);
                               
                                axios.post("{{ route('cuentaProveedor.detallePago')}}", data)
                                    .then((value) => {
                                     window.location.href = "{{ route('cuentaProveedor.index') }}"
                                    }).catch((value) => {

                                    })
                            }
                        }
                    })

                }
            }

        });

        $('#limpiar_imagen').click(function() {
            $('.imagen').attr("src", "{{ asset('img/default.png') }}")
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
                fileImagen = event.target.files[0];
                var data = userFile.src;
                $imagenPrevisualizacion.src = data
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            } else {
                toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)', 'Error');
                $('.imagen').attr("src", "{{ asset('img/default.png') }}")
            }
        });

        function changeModoPago(b)
        {
            if(b.value==1) {
                    $("#efectivo_venta").attr('readonly',false)
                    $("#importe_venta").attr('readonly',true)
            }
            else{
                $("#efectivo_venta").attr('readonly',false)
                $("#importe_venta").attr('readonly',false)
            }

            const tipo_pago = document.querySelector('#pago').value;
            if(tipo_pago == "TODO"){
                const saldo =   document.querySelector('#saldo').value;
                document.querySelector('#efectivo_venta').value         =   saldo;
                document.querySelector('#efectivo_venta').readOnly      =   true;
            }
        }

        function tipoPago(tipoPago){
            const tipo_pago =   tipoPago.value;
            if(tipo_pago == "TODO"){
                const saldo =   document.querySelector('#saldo').value;
                document.querySelector('#efectivo_venta').value         =   saldo;
                document.querySelector('#efectivo_venta').readOnly      =   true;
            }
            if(tipo_pago == "A CUENTA"){
                document.querySelector('#efectivo_venta').value         =   "0.00";
                document.querySelector('#efectivo_venta').readOnly      = false;
            }
        }
    </script>
@endpush
