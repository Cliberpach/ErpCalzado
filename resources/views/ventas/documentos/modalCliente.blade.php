<div class="modal inmodal" id="modal_cliente" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <i class="fa fa-user-plus modal-icon"></i>
                <h4 class="modal-title">NUEVO CLIENTE</h4>
                <small class="font-bold">Registrar</small>
            </div>
            <div class="modal-body">
                <form id="frmCliente">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="tipo_documento">Tipo de documento</label>
                                        <select id="tipo_documento" name="tipo_documento"
                                            class="select2_form form-control" required>
                                            <option></option>
                                            @foreach (tipos_documento() as $tipo_documento)
                                                <option value="{{ $tipo_documento->simbolo }}">{{ $tipo_documento->simbolo }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="documento">Nro. Documento</label>

                                        <div class="input-group">
                                            <input type="text" id="documento" name="documento"
                                                class="form-control"
                                                maxlength="8" onkeypress="return isNumber(event)" required>
                                            <span class="input-group-append"><a style="color:white" class="btn btn-primary" onclick="consultarDocumento()"><i class="fa fa-search"></i> <span
                                                        id="entidad">Entidad</span></a></span>


                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="tipo_cliente">Tipo Cliente</label>
                                        <select id="tipo_cliente_id" name="tipo_cliente_id" class="select2_form form-control" style="width: 100%" required>
                                            <option></option>
                                            @foreach (tipo_clientes() as $tipo_cliente)
                                                <option value="{{ $tipo_cliente->id }}">{{ $tipo_cliente->descripcion }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <input type="hidden" id="codigo_verificacion" name="codigo_verificacion">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="" for="activo">Estado</label>
                                        <input type="text" id="activo" name="activo" class="form-control text-center" value="SIN VERIFICAR" readonly>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="required" id="lblNombre" for="nombre">Nombre</label>
                                        <input type="text" id="nombre" name="nombre" class="form-control" maxlength="191" onkeyup="return mayus(this)" required>

                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="direccion" class="required">Dirección Fiscal</label>
                                        <input type="text" id="direccion" name="direccion" class="form-control" value="Direccion Trujillo" maxlength="191" onkeyup="return mayus(this)" required>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="departamento">Departamento</label>
                                        <select id="departamento" name="departamento"
                                            class="select2_form form-control"
                                            style="width: 100%" onchange="zonaDepartamento(this)">
                                            <option></option>
                                            @foreach (departamentos() as $departamento)
                                                <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="provincia">Provincia</label>
                                        <select id="provincia" name="provincia"
                                            class="select2_form form-control"
                                            style="width: 100%">
                                            <option></option>
                                            @foreach (provincias() as $provincia)
                                                <option value="{{ $provincia->id }}">{{ $provincia->nombre }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="distrito">Distrito</label>
                                        <select id="distrito" name="distrito"
                                            class="select2_form form-control {{ $errors->has('distrito') ? ' is-invalid' : '' }}"
                                            style="width: 100%">
                                            <option></option>
                                            @foreach (distritos() as $distrito)
                                                <option value="{{ $distrito->id }}">{{ $distrito->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="zona">Zona</label>
                                        <input type="text" id="zona" name="zona"
                                            class=" text-center form-control {{ $errors->has('zona') ? ' is-invalid' : '' }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="telefono_movil" class="required">Teléfono móvil</label>
                                        <input type="text" id="telefono_movil" name="telefono_movil" class="form-control" onkeypress="return isNroPhone(event)" maxlength="9" value="999999999" required>

                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="telefono_fijo">Teléfono fijo</label>
                                        <input type="text" id="telefono_fijo" name="telefono_fijo" class="form-control" onkeypress="return isNroPhone(event)" maxlength="9">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="correo_electronico">Correo electr&oacute;nico</label>
                                        <input type="text" id="correo_electronico" name="correo_electronico" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="col-md-6 text-left">
                    <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos
                        marcados con asterisco (*) son obligatorios.</small>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-sm" form="frmCliente" style="color:white;"><i class="fa fa-save"></i> Guardar</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </div>

        </div>
    </div>
</div>
@push('scripts')
    <script>
        // $(document).ready(function() {
        //     $("#frmCliente").validate({
        //         rules: {
        //             tipo_documento : {
        //                 required: true
        //             },
        //         },
        //         messages : {
        //             tipo_documento: {
        //                 required: "Completar tipo de documento.",
        //             }
        //         }
        //     });
        // });

        var departamento_api = '';
        var provincia_api = '';
        var distrito_api = '';

        Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger',
                    container: 'my-swal',
                },
                buttonsStyling: false
        })

        $("#tipo_documento").on("change", cambiarTipoDocumento);

        $("#departamento").on("change", cargarProvincias);

        $('#provincia').on("change", cargarDistritos);
        function cambiarTipoDocumento() {
            var tipo_documento = $("#tipo_documento").val();

            setLongitudDocumento();
        }

        function setLongitudDocumento() {
            var tipo_documento = $('#tipo_documento').val();
            if (tipo_documento !== undefined && tipo_documento !== null && tipo_documento !== "" && tipo_documento.length >
                0) {

                switch (tipo_documento) {
                    case 'DNI':
                        $('#entidad').text('Reniec')
                        $('#lblNombre').text('Nombre')
                        $("#documento").attr('maxlength', 8);
                        $("#activo").val("SIN VERIFICAR");
                        break;

                    case 'RUC':
                        $('#entidad').text('Sunat')
                        $('#lblNombre').text('Razón social')
                        $("#documento").attr('maxlength', 11);
                        $("#activo").val("SIN VERIFICAR");
                        break;

                    case 'CARNET EXT.':
                        $("#documento").attr('maxlength', 20);
                        $('#lblNombre').text('Nombre')
                        $("#activo").val("SIN VERIFICAR");
                        break;

                    case 'PASAPORTE':
                        $("#documento").attr('maxlength', 20);
                        $('#lblNombre').text('Nombre')
                        $("#activo").val("SIN VERIFICAR");
                        break;

                    case 'P. NAC.':
                        $("#documento").attr('maxlength', 25);
                        $('#lblNombre').text('Nombre')
                        $("#activo").val("SIN VERIFICAR");
                        break;
                }
            }
        }

        function clearDatosPersona(limpiarDocumento) {
            if (limpiarDocumento)
                $('#documento').val("");

            $('#nombre').val("");
            $('#direccion').val("");
            // $('#departamento').val("").trigger("change");
            // $('#provincia').val("").trigger("change");
            // $('#distrito').val("").trigger("change");
            // $("#provincia").empty();
            // $("#distrito").empty();
            $('#correo_electronico').val("");
            $('#telefono_movil').val("");
            $('#telefono_fijo').val("");

            departamento_api = '';
            provincia_api = '';
            distrito_api = '';
        }

        function consultarDocumento() {

            var tipo_documento = $('#tipo_documento').val();
            var documento = $('#documento').val();

            // Consultamos nuestra BBDD
            $.ajax({
                dataType: 'json',
                type: 'post',
                url: '{{ route('ventas.cliente.getDocumento') }}',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'tipo_documento': tipo_documento,
                    'documento': documento,
                    'id': null
                }
            }).done(function(result) {
                if (result.existe) {
                    toastr.error('El ' + tipo_documento + ' ingresado ya se encuentra registrado para un cliente',
                        'Error');
                    clearDatosPersona(true);
                } else {
                    if (tipo_documento === "DNI") {
                        if (documento.length === 8) {
                            consultarAPI(tipo_documento, documento);
                        } else {
                            toastr.error('El DNI debe de contar con 8 dígitos', 'Error');
                            clearDatosPersona(false);
                        }
                    } else if (tipo_documento === "RUC") {
                        if (documento.length === 11) {
                            consultarAPI(tipo_documento, documento);
                        } else {
                            toastr.error('El RUC debe de contar con 11 dígitos', 'Error');
                            clearDatosPersona(false);
                        }
                    }
                }
            });
        }

        function consultarAPI(tipo_documento, documento) {

            if (tipo_documento === 'DNI' || tipo_documento === 'RUC') {
                var url = (tipo_documento === 'DNI') ? '{{ route('getApidni', ':documento') }}' :
                    '{{ route('getApiruc', ':documento') }}';
                url = url.replace(':documento', documento);
                var textAlert = (tipo_documento === 'DNI') ? "¿Desea consultar DNI a RENIEC?" :
                    "¿Desea consultar RUC a SUNAT?";
                Swal.fire({
                    title: 'Consultar',
                    text: textAlert,
                    icon: 'question',
                    customClass: {
                        container: 'my-swal'
                    },
                    showCancelButton: true,
                    confirmButtonColor: "#1ab394",
                    confirmButtonText: 'Si, Confirmar',
                    cancelButtonText: "No, Cancelar",
                    showLoaderOnConfirm: true,
                    preConfirm: (login) => {
                        return fetch(url)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText)
                                }

                                return response.json();
                            })
                            .catch(error => {
                                Swal.showValidationMessage(
                                    `El documento ingresado es incorrecto`
                                );
                                clearDatosPersona(true);
                            })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.value !== undefined && result.isConfirmed) {
                        $('#documento').removeClass('is-invalid')
                        if (tipo_documento === 'DNI')
                            camposDNI(result);
                        else
                            camposRUC(result);

                        consultaExitosa();
                    }
                });
            }
        }

        function camposDNI(objeto) {
            console.log(objeto);
            if (objeto.value === undefined)
                return;

            if(objeto.value.success)
            {
                var nombres = objeto.value.data.nombres;
                var apellido_paterno = objeto.value.data.apellido_paterno;
                var apellido_materno = objeto.value.data.apellido_materno;
                var codigo_verificacion = objeto.value.data.codigo_verificacion;

                var direccion = objeto.value.data.direccion_completa;
                var departamento = objeto.value.data.ubigeo[0];
                var provincia = objeto.value.data.ubigeo[1];
                var distrito = objeto.value.data.ubigeo[2];

                if (direccion != '-' && direccion != "NULL") {
                    $('#direccion').val(direccion);
                }

                if(departamento && provincia && distrito)
                {
                    camposUbigeoApi(departamento, provincia, distrito);
                }

                var nombre = "";
                if (nombres != '-' && nombres != "NULL") {
                    nombre += nombres;
                }
                if (apellido_paterno !== '-' && apellido_paterno !== "NULL") {
                    nombre += (nombre.length === 0) ? apellido_paterno : ' ' + apellido_paterno
                }
                if (apellido_materno !== '-' && apellido_materno !== "NULL") {
                    nombre += (nombre.length === 0) ? apellido_materno : ' ' + apellido_materno
                }

                $("#nombre").val(nombre);
                $("#activo").val("ACTIVO");
                if (codigo_verificacion !== '-' && codigo_verificacion !== "NULL") {
                    $('#codigo_verificacion').val(codigo_verificacion);
                }
            }
            else{
                toastr.error('No se encontraron datos.')
            }
        }

        function camposRUC(objeto) {
            console.log(objeto)
            if (objeto.value === undefined)
                return;
            if(objeto.value.success)
            {
                var razonsocial = objeto.value.data.nombre_o_razon_social;
                var direccion = objeto.value.data.direccion;
                var departamento = objeto.value.data.ubigeo[0];
                var provincia = objeto.value.data.ubigeo[1];
                var distrito = objeto.value.data.ubigeo[2];
                var estado = objeto.value.data.estado;

                if (razonsocial != '-' && razonsocial != "NULL") {
                    $('#nombre').val(razonsocial);
                }

                if (estado == "ACTIVO") {
                    $('#activo').val(estado);
                } else {
                    toastr.error('Cliente con RUC no se encuentra "Activo"', 'Error');
                }

                if (direccion != '-' && direccion != "NULL") {
                    $('#direccion').val(direccion);
                }

                if(departamento && provincia && distrito)
                {
                    camposUbigeoApi(departamento, provincia, distrito);
                }
            }
            else
            {
                toastr.error('No se encontraron datos.')
            }
        }

        function consultar() {
            var tipo = $('#tipo_documento').val()
            switch (tipo) {
                case 'DNI':
                    // $('#entidad').text('Reniec')
                    // consultarDocumento()
                    break;
                case 'CARNET EXT.':
                    toastr.error('El tipo de documento no tiene entidad para consultar', 'Error');
                    $('#entidad').text('Entidad')
                    break;
                case 'RUC':
                    // $('#entidad').text('Sunat')
                    // consultarDocumento()
                    break;
                case 'P. NAC.':
                    $('#entidad').text('Entidad')
                    toastr.error('El tipo de documento no tiene entidad para consultar', 'Error');
                    break;
                case 'PASAPORTE':
                    $('#entidad').text('Entidad')
                    toastr.error('El tipo de documento no tiene entidad para consultar', 'Error');
                    break;
                    // default:
                    //     $('#entidad').text('Entidad')
                    //     toastr.error('El tipo de documento no tiene entidad para consultar','Error');
            }

        }

        function zonaDepartamento(depar) {
            // alert(depar.value)
            @foreach (departamentos() as $departamento)
                if ("{{ $departamento->id }}" == depar.value){
                $('#zona').val("{{ $departamento->zona }}")
                }
            @endforeach


        }

        function cargarProvincias() {
            var departamento_id = $("#departamento").val();
            if (departamento_id !== "" || departamento_id.length > 0) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {
                        _token: $('input[name=_token]').val(),
                        departamento_id: departamento_id
                    },
                    url: "{{ route('mantenimiento.ubigeo.provincias') }}",
                    success: function(data) {
                        // Limpiamos data
                        $("#provincia").empty();
                        $("#distrito").empty();

                        if (!data.error) {
                            // Mostramos la información
                            if (data.provincias != null) {
                                if (provincia_api != '') {
                                    $("#provincia").select2({
                                        data: data.provincias
                                    }).val(provincia_api).trigger('change');
                                    provincia_api='';
                                } else {
                                    $("#provincia").select2({
                                        data: data.provincias
                                    }).val($('#provincia').find(':selected').val()).trigger('change');

                                }
                            }
                        } else {
                            toastr.error(data.message, 'Mensaje de Error', {
                                "closeButton": true,
                                positionClass: 'toast-bottom-right'
                            });
                        }
                    }
                });
            }
        }

        function cargarDistritos() {
            var provincia_id = $("#provincia").val();
            if (provincia_id !== "" || provincia_id.length > 0) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {
                        _token: $('input[name=_token]').val(),
                        provincia_id: provincia_id
                    },
                    url: "{{ route('mantenimiento.ubigeo.distritos') }}",
                    success: function(data) {
                        // Limpiamos data
                        $("#distrito").empty();

                        if (!data.error) {
                            // Mostramos la información
                            if (data.distritos != null) {
                                var selected = $('#distrito').find(':selected').val();
                                if (distrito_api != '') {
                                    $("#distrito").select2({
                                        data: data.distritos
                                    }).val(distrito_api).trigger('change');
                                    distrito_api='';
                                } else {
                                    $("#distrito").select2({
                                        data: data.distritos
                                    });
                                }
                            }
                        } else {
                            toastr.error(data.message, 'Mensaje de Error', {
                                "closeButton": true,
                                positionClass: 'toast-bottom-right'
                            });
                        }
                    }
                });
            }
        }

        function camposUbigeoApi(departamento, provincia, distrito) {
            departamento_api = '';
            provincia_api = '';
            distrito_api = '';

            if (departamento !== '-' && departamento !== null && provincia !== '-' && provincia !== null &&
                distrito !== '-' && distrito !== null) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {
                        _token: $('input[name=_token]').val(),
                        departamento: departamento,
                        provincia: provincia,
                        distrito: distrito
                    },
                    url: "{{ route('mantenimiento.ubigeo.api_ruc') }}",
                    success: function(data) {
                        // Limpiamos data
                        $("#provincia").empty();
                        $("#distrito").empty();

                        if (!data.error) {
                            // Mostramos la información
                            if (data.ubigeo != null) {

                                departamento_api = data.ubigeo.departamento_id;
                                provincia_api = parseInt(data.ubigeo.provincia_id);
                                distrito_api = data.ubigeo.id;

                                $("#departamento").val(parseInt(departamento_api)).trigger('change');
                            }
                        } else {
                            toastr.error(data.message, 'Mensaje de Error', {
                                "closeButton": true,
                                positionClass: 'toast-bottom-right'
                            });
                        }
                    }
                });
            }
        }

        $("#documento").keyup(function() {
            $('#activo').val('SIN VERIFICAR');
        })

        $("#nombre").keyup(function() {
            $('#activo').val('SIN VERIFICAR');
        })

        $("#tipo_documento").on('change', function(e) {
            $('#activo').val('SIN VERIFICAR')
        })

        $('#frmCliente').submit(function(e) {
            e.preventDefault();

            let frmCliente = document.getElementById('frmCliente');

            let formData = new FormData(frmCliente);

            $.ajax({
                type : 'POST',
                url : '{{ route('ventas.cliente.storeFast') }}',
                data : formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    if (respuesta.result === 'success') {
                        document.getElementById('frmCliente').reset();
                        let cliente = respuesta.cliente;
                        obtenerClientes_(cliente);
                        $('#modal_cliente').modal('hide');
                    }
                    let mensaje = sHtmlErrores(respuesta.data.mensajes);
                    toastr[respuesta.result](mensaje);
                },
                error: function (respuesta) {
                    let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                    toastr[result.error](sHtmlMensaje);
                },
                complete: function () {
                    //
                }
            })
        })

        function obtenerClientes_(cliente) {
            $("#cliente_id").empty().trigger('change');
            clientes_global = [];
            let timerInterval;
            Swal.fire({
                title: 'Cargando Clientes...',
                icon: 'info',
                customClass: {
                    container: 'my-swal'
                },
                timer: 10,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    Swal.stopTimer();
                    $.ajax({
                        dataType: 'json',
                        url: '{{ route('ventas.customers_all') }}',
                        type: 'post',
                        data: {
                            '_token': $('input[name=_token]').val(),
                            'tipo_id': $('#tipo_venta').val()
                        },
                        success: function(data) {
                            timerInterval = 0;
                            Swal.resumeTimer();

                            clientes_global = data.clientes;
                            if (data.clientes.length > 0) {
                                $('#cliente_id').append('<option></option>').trigger('change');
                                for(var i = 0;i < data.clientes.length; i++)
                                {
                                    var newOption = '<option value="'+data.clientes[i].id+'" tabladetalle="'+data.clientes[i].tabladetalles_id+'">'+data.clientes[i].tipo_documento + ': ' + data.clientes[i].documento + ' - ' + data.clientes[i].nombre+'</option>';
                                    $('#cliente_id').append(newOption).trigger('change');
                                    //departamentos += '<option value="'+result.departamentos[i].id+'">'+result.departamentos[i].nombre+'</option>';
                                }

                                if(cliente.tipo_documento == 'RUC')
                                {
                                    //$('#tipo_venta').val("127").trigger("change");
                                    $('#cliente_id').val(cliente.id).trigger("change");
                                }
                                else
                                {
                                    //$('#tipo_venta').val("128").trigger("change");
                                    $('#cliente_id').val(cliente.id).trigger("change");
                                }
                            } else {
                                toastr.error('Clientes no encontrados.', 'Error');
                            }
                            $('#tipo_cliente_documento').val(data.tipo);
                        },
                    })
                },
                willClose: () => {
                    clearInterval(timerInterval)
                }
            });
        }

    </script>
@endpush
