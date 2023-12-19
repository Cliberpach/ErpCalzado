<div class="modal inmodal" id="modal_agregar_entidad" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" onclick="limpiar()" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Entidad Financiera</h4>
                <small class="font-bold">Registrar entidad financiera asociada a la empresa.</small>
            </div>
            <div class="modal-body">
                <form role="form" id="">
                    <input type="hidden" id="editar_id_articulo">
                    <input type="hidden" id="indice">
                    <div class="form-group">
                        <label class="required">Descripción</label>
                        <select class="select2_form form-control" style="text-transform: uppercase; width:100%"
                            name="entidad_descripcion" id="entidad_descripcion">
                            <option></option>
                            @foreach ($bancos as $banco)
                            <option value="{{$banco->descripcion}}">{{$banco->descripcion}}
                            </option>
                            @endforeach

                        </select>
                        <div class="invalid-feedback"><b><span id="error-entidad"></span></b></div>

                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="required">Moneda</label>
                            <select class="select2_form form-control" style="text-transform: uppercase; width:100%"
                                name="entidad_moneda" id="entidad_moneda">
                                <option></option>
                                @foreach ($monedas as $moneda)
                                <option value="{{$moneda->descripcion}}">{{$moneda->descripcion.' '.$moneda->simbolo}}
                                </option>
                                @endforeach

                            </select>
                            <div class="invalid-feedback"><b><span id="error-entidad-moneda"></span></b></div>
                        </div>

                        <div class="col-md-6">
                            <label class="required" for="amount"># Cuenta</label>
                            <input type="text" id="entidad_cuenta" class="form-control">
                            <div class="invalid-feedback"><b><span id="error-entidad-cuenta"></span></b></div>
                        </div>

                    </div>

                    <div class="form-group">

                        <label class="required" for="amount"># CCI</label>
                        <input type="text" id="entidad_cci" class="form-control">
                        <div class="invalid-feedback"><b><span id="error-entidad-cci"></span></b></div>


                    </div>

                    <div class="form-group">

                        <label># ITF</label>
                        <input type="text" id="entidad_itf" class="form-control">
                        <div class="invalid-feedback"><b><span id="error-entidad-itf"></span></b></div>


                    </div>

            </div>


            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-primary btn-sm agregarEntidad" style="color:white"><i class="fa fa-save"></i>
                        Guardar</a>
                    <button type="button" onclick="limpiar()" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i>
                        Cancelar</button>
                </div>
            </div>

            </form>
        </div>
    </div>
</div>

<div class="modal inmodal" id="modal_editar_entidad" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" onclick="limpiarModificar()" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Entidad Financiera</h4>
                <small class="font-bold">Modificar entidad financiera asociada a la empresa.</small>
            </div>
            <div class="modal-body">
                <form role="form" id="">
                    <input type="hidden" id="modificar_id_banco">
                    <div class="form-group">
                        <label class="required">Descripción</label>
                        <select class="select2_form form-control" style="text-transform: uppercase; width:100%"
                            name="entidad_descripcion" id="modificar_entidad_descripcion">
                            <option></option>
                            @foreach ($bancos as $banco)
                            <option value="{{$banco->descripcion}}">{{$banco->descripcion}}
                            </option>
                            @endforeach

                        </select>
                        <div class="invalid-feedback"><b><span id="error-modificar-entidad"></span></b></div>

                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="required">Moneda</label>
                            <select class="select2_form form-control" style="text-transform: uppercase; width:100%"
                                name="entidad_moneda" id="modificar_entidad_moneda">
                                <option></option>
                                @foreach ($monedas as $moneda)
                                <option value="{{$moneda->descripcion}}">{{$moneda->descripcion.' '.$moneda->simbolo}}
                                </option>
                                @endforeach

                            </select>
                            <div class="invalid-feedback"><b><span id="error-modificar-entidad-moneda"></span></b></div>
                        </div>

                        <div class="col-md-6">
                            <label class="required" for="amount"># Cuenta</label>
                            <input type="text" id="modificar_entidad_cuenta" class="form-control">
                            <div class="invalid-feedback"><b><span id="error-modificar-entidad-cuenta"></span></b></div>
                        </div>

                    </div>

                    <div class="form-group">

                        <label class="required" for="amount"># CCI</label>
                        <input type="text" id="modificar_entidad_cci" class="form-control">
                        <div class="invalid-feedback"><b><span id="error-modificar-entidad-cci"></span></b></div>


                    </div>

                    <div class="form-group">

                        <label># ITF</label>
                        <input type="text" id="modificar_entidad_itf" class="form-control">
                        <div class="invalid-feedback"><b><span id="error-modificar-entidad-itf"></span></b></div>


                    </div>

            </div>


            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label
                            class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-primary btn-sm modificarEntidad" style="color:white"><i class="fa fa-save"></i>
                        Guardar</a>
                    <button type="button" onclick="limpiarModificar()" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i>
                        Cancelar</button>
                </div>
            </div>

            </form>
        </div>
    </div>
</div>

<div class="modal inmodal" id="modal_certificado" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" onclick="limpiarmodalCertificado()" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Certificado PEM</h4>
                <small class="font-bold">Generar Certificado PEM.</small>
            </div>

            <div class="modal-body">
                <form role="form" id="fileinfo" enctype="multipart/form-data" method="post" name="fileinfo">
                    {{ csrf_field() }} {{method_field('POST')}}
                    <div class="form-group">
                        <label class="required">Certificado (.pfx):</label>
                        <div class="custom-file">
                            <input id="certificado" type="file" name="certificado" class="custom-file-input {{ $errors->has('certificado') ? ' is-invalid' : '' }}" accept="*">

                            <label for="logo" id="certificado_txt"
                                class="custom-file-label selected {{ $errors->has('ruta_certificado') ? ' is-invalid' : '' }}">Seleccionar</label>

                            @if ($errors->has('certificado'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('certificado') }}</strong>
                            </span>
                            @endif
                            <div class="invalid-feedback"><b><span id="error-archivo_certificado"></span></b></div>

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="required">Contraseña:</label>
                        <input type="text" class="form-control {{ $errors->has('contra_certificado') ? ' is-invalid' : '' }}" name="contra_certificado" id="contra_certificado" value="{{old('contra_certificado')}}" >
                        <div class="invalid-feedback"><b><span id="error-contra_certificado"></span></b></div>
                    </div>

                </form>


            </div>



            <div class="modal-footer">
                <div class="col-md-6 text-left" style="color:#fcbc6c">
                    <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-primary btn-sm consultarCertificado" style="color:white"><i class="fa fa-save"></i>
                        Guardar</a>
                    <button type="button" onclick="limpiarmodalCertificado()" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                            class="fa fa-times"></i>
                        Cancelar</button>
                </div>
            </div>


        </div>
    </div>
</div>

<div class="modal inmodal" id="modal_numeracion_facturacion" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" onclick="limpiar()" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-cogs modal-icon"></i>
                <h4 class="modal-title">Numeración de facturación</h4>
                <small class="font-bold">Registrar Numeración de facturación de la Empresa.</small>
            </div>
            <form role="form" id="formulario_comprobante_numero" method="post" name="formulario_comprobante_numero">
                {{ csrf_field() }} {{method_field('POST')}}
                <div class="modal-body">


                        <div class="form-group">
                            <label class="required">Tipo de Comprobante:</label>
                            <select class="select2_form form-control" style="text-transform: uppercase; width:100%" name="tipo_comprobante" id="tipo_comprobante" required>
                                <option></option>
                                @foreach (tipos_venta() as $venta)
                                    <option value="{{$venta->id}}">{{$venta->descripcion}}</option>
                                @endforeach

                            </select>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-6 col-xs-12">
                                <label class="required">Serie:</label>
                                <input type="text" name="serie_comprobante" id="serie_comprobante" class="form-control" readonly required>
                            </div>
                            <div class="col-lg-6 col-xs-12">
                                <label class="required">Número a Iniciar :</label>
                                <input type="number" name="numero_iniciar" id="numero_iniciar" class="form-control" required>
                            </div>

                        </div>
                </div>

                <div class="modal-footer">
                    <div class="col-md-6 text-left" style="color:#fcbc6c">
                        <i class="fa fa-exclamation-circle"></i> <small>Los campos marcados con asterisco (<label class="required"></label>) son obligatorios.</small>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary btn-sm" type="submit" ><i class="fa fa-save"></i>
                            Guardar</button>
                        <button type="button" onclick="" class="btn btn-danger btn-sm" data-dismiss="modal"><i
                                class="fa fa-times"></i>
                            Cancelar</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>


@push('scripts')

<script>


const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger',
    },
    buttonsStyling: false
})

//Editar Registro
$(document).on('click', '#editar_entidad', function(event) {
    var table = $('.dataTables-bancos').DataTable();
    var data = table.row($(this).parents('tr')).data();
    var tr = $(this).closest("tr");
    var rowindex = tr.index();
    $('#modificar_id_banco').val(rowindex);
    $("#modificar_entidad_descripcion option:contains("+data[1]+")").attr('selected', true).change();
    $("#modificar_entidad_moneda option:contains("+data[2]+")").attr('selected', true).change();
    $('#modificar_entidad_cuenta').val(data[3]);
    $('#modificar_entidad_cci').val(data[4]);
    $('#modificar_entidad_itf').val(data[5]);
    $('#modal_editar_entidad').modal('show');
})
//Validacion al ingresar tablas
$(".modificarEntidad").click(function() {
    limpiarErroresmodificar()
    var enviar = false;
    if ($('#modificar_entidad_descripcion').val() == '') {

        toastr.error('Seleccione la entidad financiera.', 'Error');
        enviar = true;

        $("#modificar_entidad_descripcion").addClass("is-invalid");
        $('#error-modificar-entidad').text('El campo Descripcion es obligatorio.')
    }

    if ($('#modificar_entidad_moneda').val() == '') {

        toastr.error('Seleccione el tipo de moneda.', 'Error');
        enviar = true;

        $("#modificar_entidad_moneda").addClass("is-invalid");
        $('#error-modificar-entidad-moneda').text('El campo Moneda es obligatorio.')
    }

    if ($('#modificar_entidad_cuenta').val() == '') {
        toastr.error('Ingrese el N° de cuenta.', 'Error');
        enviar = true;

        $("#modificar_entidad_cuenta").addClass("is-invalid");
        $('#error-modificar-entidad-cuenta').text('El campo N° Cuenta es obligatorio.')
    }

    if ($('#modificar_entidad_cci').val() == '') {
        toastr.error('Ingrese el N° de CCI.', 'Error');
        enviar = true;

        $("#modificar_entidad_cci").addClass("is-invalid");
        $('#error-modificar-entidad-cci').text('El campo N° CCI es obligatorio.')
    }

    /*if ($('#modificar_entidad_itf').val() == '') {
        toastr.error('Ingrese el N° de ITF.', 'Error');
        enviar = true;

        $("#modificar_entidad_itf").addClass("is-invalid");
        $('#error-modificar-entidad-itf').text('El campo N° ITF es obligatorio.')
    }*/

    if (enviar != true) {
        Swal.fire({
            customClass: {
                container: 'my-swal'
            },
            title: 'Opción Modificar',
            text: "¿Seguro que desea modificar Entidad?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                var entidad = {
                    descripcion: $('#modificar_entidad_descripcion').val() ,
                    moneda: $('#modificar_entidad_moneda').val(),
                    cuenta: $('#modificar_entidad_cuenta').val(),
                    cci: $('#modificar_entidad_cci').val(),
                    itf: $('#modificar_entidad_itf').val(),
                }
                var existe = buscarRegistromodificar(entidad)
                if (existe == false) {
                    actualizarTabla($('#modificar_id_banco').val())
                    $('#modal_editar_entidad').modal('hide');
                }
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La Solicitud se ha cancelado.',
                    'error'
                )
            }
        })

    }
})

function actualizarTabla(i) {
    var table = $('.dataTables-bancos').DataTable();
    table.row(i).remove().draw();

    var entidad = {
        descripcion: $('#modificar_entidad_descripcion').val(),
        moneda: $('#modificar_entidad_moneda').val(),
        cuenta: $('#modificar_entidad_cuenta').val(),
        cci: $('#modificar_entidad_cci').val(),
        itf: $('#modificar_entidad_itf').val(),
    }
    agregarTabla(entidad);

}

$('#entidad_cci , #modificar_entidad_cci , #entidad_cuenta , #modificar_entidad_cuenta').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});


$('#entidad_itf , #modificar_entidad_itf').keyup(function(){
    var val = $(this).val();
    if(isNaN(val)){
        val = val.replace(/[^0-9\.]/g,'');
        if(val.split('.').length>2)
            val =val.replace(/\.+$/,"");
    }
    $(this).val(val);
});

function limpiarErrores() {
    $('#entidad_descripcion').removeClass("is-invalid")
    $('#error-entidad').text('')

    $('#entidad_moneda').removeClass("is-invalid")
    $('#error-entidad-moneda').text('')

    $('#entidad_cuenta').removeClass("is-invalid")
    $('#error-entidad-cuenta').text('')

    $('#entidad_cci').removeClass("is-invalid")
    $('#error-entidad-cci').text('')

    $('#entidad_itf').removeClass("is-invalid")
    $('#error-entidad-itf').text('')

}

function limpiarErroresmodificar() {
    $('#modificar_entidad_descripcion').removeClass("is-invalid")
    $('#error-modificar-entidad').text('')

    $('#modificar_entidad_moneda').removeClass("is-invalid")
    $('#error-modificar-entidad-moneda').text('')

    $('#modificar_entidad_cuenta').removeClass("is-invalid")
    $('#error-modificar-entidad-cuenta').text('')

    $('#modificar_entidad_cci').removeClass("is-invalid")
    $('#error-modificar-entidad-cci').text('')

    $('#modificar_entidad_itf').removeClass("is-invalid")
    $('#error-modificar-entidad-itf').text('')

}

$(".agregarEntidad").click(function() {
    limpiarErrores()
    var enviar = false;
    if ($('#entidad_descripcion').val() == '') {

        toastr.error('Seleccione la entidad financiera.', 'Error');
        enviar = true;

        $("#entidad_descripcion").addClass("is-invalid");
        $('#error-entidad').text('El campo Descripcion es obligatorio.')
    }

    if ($('#entidad_moneda').val() == '') {

        toastr.error('Seleccione el tipo de moneda.', 'Error');
        enviar = true;

        $("#entidad_moneda").addClass("is-invalid");
        $('#error-entidad-moneda').text('El campo Moneda es obligatorio.')
    }

    if ($('#entidad_cuenta').val() == '') {
        toastr.error('Ingrese el N° de cuenta.', 'Error');
        enviar = true;

        $("#entidad_cuenta").addClass("is-invalid");
        $('#error-entidad-cuenta').text('El campo N° Cuenta es obligatorio.')
    }

    if ($('#entidad_cci').val() == '') {
        toastr.error('Ingrese el N° de CCI.', 'Error');
        enviar = true;

        $("#entidad_cci").addClass("is-invalid");
        $('#error-entidad-cci').text('El campo N° CCI es obligatorio.')
    }


    /*if ($('#entidad_itf').val() == '') {
        toastr.error('Ingrese el N° de ITF.', 'Error');
        enviar = true;

        $("#entidad_itf").addClass("is-invalid");
        $('#error-entidad-itf').text('El campo N° ITF es obligatorio.')
    }*/

    if (enviar != true) {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger',
                container: 'my-swal',
            },
            buttonsStyling: false
        })

        Swal.fire({
            customClass: {
                container: 'my-swal'
            },
            title: 'Opción Agregar',
            text: "¿Seguro que desea agregar Entidad Financiera?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                var entidad = {
                    descripcion: $('#entidad_descripcion').val() ,
                    moneda: $('#entidad_moneda').val(),
                    cuenta: $('#entidad_cuenta').val(),
                    cci: $('#entidad_cci').val(),
                    itf: $('#entidad_itf').val(),
                }

                var existe = buscarRegistro(entidad)
                if (existe == false) {
                    limpiar()
                    agregarTabla(entidad)
                    $('#modal_agregar_entidad').modal('hide');
                }
                cargarEntidades()

            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La Solicitud se ha cancelado.',
                    'error'
                )
            }
        })

    }
})

function buscarRegistro(entidad) {
    var existe = false;
    var t = $('.dataTables-bancos').DataTable();
    t.rows().data().each(function(el, index) {
        if (el[1] == entidad.descripcion && el[2] == entidad.moneda && el[3] == entidad.cuenta && el[4] == entidad.cci && el[5] == entidad.itf) {
            existe = true
            toastr.error('Entidad Bancaria ya se encuentra registrada.', 'Error');
        }
        /*if (el[3] == entidad.cuenta) {
            existe = true
            toastr.error('N° de Cuenta ya se encuentra registrada.', 'Error');
        }
        if (el[4] == entidad.cci) {
            existe = true
            toastr.error('N° de CCI ya se encuentra registrada.', 'Error');
        }
        if (el[5] == entidad.itf) {
            existe = true
            toastr.error('N° de ITF ya se encuentra registrada.', 'Error');
        }*/

    });
    return existe
}

function buscarRegistromodificar(entidad) {
    var existe = false;
    var t = $('.dataTables-bancos').DataTable();
    var indice = $('#modificar_id_banco').val()
    t.rows().data().each(function(el, index) {
        console.log("indice:"+index)
        if (index!=indice) {
            if (el[1] == entidad.descripcion && el[2] == entidad.moneda && el[3] == entidad.cuenta && el[4] == entidad.cci && el[5] == entidad.itf) {
                existe = true
                toastr.error('Entidad Bancaria ya se encuentra registrada.', 'Error');
            }
            /*if (el[3] == entidad.cuenta) {
                existe = true
                toastr.error('N° de Cuenta ya se encuentra registrada.', 'Error');
            }
            if (el[4] == entidad.cci) {
                existe = true
                toastr.error('N° de CCI ya se encuentra registrada.', 'Error');
            }
            if (el[5] == entidad.itf) {
                existe = true
                toastr.error('N° de ITF ya se encuentra registrada.', 'Error');
            }*/
        }


    });
    return existe
}

function limpiarForm() {
    $('#entidad_descripcion').val($('#entidad_descripcion option:first-child').val()).trigger('change');
    $('#entidad_moneda').val($('#entidad_moneda option:first-child').val()).trigger('change');
    $('#entidad_cuenta').val('')
    $('#entidad_cci').val('')
    $('#entidad_itf').val('')
    limpiarErrores()
}

function limpiar() {
    $('#entidad_descripcion').val($('#entidad_descripcion option:first-child').val()).trigger('change');
    $('#entidad_moneda').val($('#entidad_moneda option:first-child').val()).trigger('change');
    $('#entidad_cuenta').val('')
    $('#entidad_cci').val('')
    $('#entidad_itf').val('')
    limpiarErrores()
    $('#modal_agregar_entidad').modal('hide');
}

function limpiarModificar() {
    limpiarErroresmodificar()
}

$('#modal_agregar_entidad').on('hidden.bs.modal', function(e) {
    limpiar()
});

$('#modal_editar_entidad').on('hidden.bs.modal', function(e) {
    limpiarModificar()
});

//Borrar registro de articulos
$(document).on('click', '#borrar_entidad', function(event) {

    Swal.fire({
        title: 'Opción Eliminar',
        text: "¿Seguro que desea eliminar Entidad Bancaria?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            var table = $('.dataTables-bancos').DataTable();
            table.row($(this).parents('tr')).remove().draw();
            cargarEntidades()

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
                'Cancelado',
                'La Solicitud se ha cancelado.',
                'error'
            )
        }
    })



});

function agregarTabla($entidad) {
    var t = $('.dataTables-bancos').DataTable();
    t.row.add([
        '',
        $entidad.descripcion,
        $entidad.moneda,
        $entidad.cuenta,
        $entidad.cci,
        $entidad.itf,
    ]).draw(false);

    cargarEntidades()

}

function cargarEntidades() {

    var entidades = [];
    var table = $('.dataTables-bancos').DataTable();
    var data = table.rows().data();
    data.each(function(value, index) {
        let fila = {
            entidad: value[1],
            moneda: value[2],
            cuenta: value[3],
            cci: value[4],
            itf: value[5],
        };

        entidades.push(fila);

    });

    $('#entidades_tabla').val(JSON.stringify(entidades));
}

//CERTIFICADO
$(function(){
    $('.consultarCertificado').on('click', function(){

        var fd = new FormData(document.getElementById("fileinfo"));
        var request = $.ajax({
            url: "{{route('mantenimiento.empresas.certificado')}}",
            type: 'POST',
            data: fd,
            cache: false,
            contentType: false,
            processData: false
        });

        request.done(function( msg ) {
            limpiarerrorCertificado()
            if (msg.success == false) {
                errorCertificado()
                toastr.error('Campo Certificado (.pfx) es obligatorio.', 'Error');
            }
            if (msg.success == true) {
                console.log(msg.certificado)
                $('#certificado_base').val(msg.certificado)
                $('#estado_certificado').val('VERIFICADO')
                toastr.success('Certificado correcto.');
                $('#modal_certificado').modal('hide')

            }
        });

        request.fail(function( jqXHR, textStatus ) {
            toastr.error('Contraseña incorrecta', 'Error');
            $('#estado_certificado').val('SIN VERIFICAR')
            $('#certificado_base').val('')
            //MOSTRAR ERROR
            $("#contra_certificado").addClass("is-invalid");
            $('#error-contra_certificado').text('Contraseña Invalida o Nula.')
        });


    });
});

function errorCertificado() {
    $("#certificado").addClass("is-invalid");
    $('#error-archivo_certificado').text('El campo Certificado es obligatorio.')
}

function limpiarerrorCertificado() {
    $("#certificado").removeClass("is-invalid");
    $('#error-archivo_certificado').text('')

    $("#contra_certificado").removeClass("is-invalid");
    $('#error-contra_certificado').text('')
}

function limpiarmodalCertificado() {
    limpiarerrorCertificado()
    // LIMPIAR CAMPOS
    $('#contra_certificado').val('')
    var fileName = "Seleccionar"
    $('#certificado_txt').addClass("selected").html(fileName);
    $('#certificado').val('')

}

$('#modal_certificado').on('hidden.bs.modal', function(e) {
    limpiarmodalCertificado()
});

//NUMERACION
$(document).ready(function(){
    $("#tipo_comprobante").on('change', function () {
        $("#tipo_comprobante option:selected").each(function () {
            seleccionado = $(this).val();
            if(seleccionado != '')
            {
                var url = '{{ route("serie.empresa.facturacion", ":id")}}';
                url = url.replace(':id',seleccionado);
                $.ajax({
                    url: url,
                    type:'get',
                    success:  function (response) {
                        console.log(response)
                        $('#serie_comprobante').val($.trim(response));
                    },
                });
            }
        });
   });
});


$('#formulario_comprobante_numero').submit(function(e) {
    e.preventDefault();
    if (buscarNumeracion() == false) {
        Swal.fire({
            customClass: {
                container: 'my-swal'
            },
            title: 'Opción Agregar',
            text: "¿Seguro que desea Agregar Numeración de facturación?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                agregarNumeracion();
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La Solicitud se ha cancelado.',
                    'error'
                )
            }
        })
    }


})

function agregarNumeracion(){
    var t = $('.dataTables-numeracion').DataTable();
        t.row.add(
                {
                emision: "0",
                tipo_comprobante: $('#tipo_comprobante option:selected').text(),
                serie: $('#serie_comprobante').val(),
                numero_iniciar: $('#numero_iniciar').val(),
                emision: '0',
                tipo_id: $('#tipo_comprobante').val() ,
        }).draw();
        limpiar();
}

function buscarNumeracion() {
    var existe = false;
    var t = $('.dataTables-numeracion').DataTable();
    t.rows().data().each(function(el, index) {
        if (el.tipo_id == $('#tipo_comprobante').val()) {
            existe = true
            toastr.error('Numeración de facturación ya ingresado.', 'Error');
        }
    });
    return existe
}

function limpiar() {
    $("#modal_numeracion_facturacion select").val("").trigger("change");
    $("#modal_numeracion_facturacion input[type=text] , #modal_numeracion_facturacion input[type=number]").each(function() { this.value = '' });
    $('#modal_numeracion_facturacion ').modal('hide');
}

$('#modal_numeracion_facturacion').on('hidden.bs.modal', function(e) {
    limpiar()
});

function cargarNumeracion() {

    var entidades = [];
    var table = $('.dataTables-numeracion').DataTable();
    var data = table.rows().data();
    data.each(function(value, index) {
      console.log(value);
        let fila = {
            tipo_nombre: value.tipo_comprobante,
            serie: value.serie,
            numero_iniciar: value.numero_iniciar,
            emision: value.emision,
            tipo_id: value.tipo_id,
        };

        entidades.push(fila);

    });


    $('#numeracion_tabla').val(JSON.stringify(entidades));
}

$(document).on('click', '#borrar_numeracion', function(event) {
    Swal.fire({
        title: 'Opción Eliminar',
        text: "¿Seguro que desea eliminar Entidad Bancaria?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: "#1ab394",
        confirmButtonText: 'Si, Confirmar',
        cancelButtonText: "No, Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            var table = $('.dataTables-numeracion').DataTable();
            table.row($(this).parents('tr')).remove().draw();
            cargarNumeracion()

        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
                'Cancelado',
                'La Solicitud se ha cancelado.',
                'error'
            )
        }
    })



});



</script>
@endpush
