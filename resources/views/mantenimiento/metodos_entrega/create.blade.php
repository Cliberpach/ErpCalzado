@extends('layout')

@section('content')

@section('mantenimiento-active', 'active')
@section('vendedores-active', 'active')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10 col-md-10">
       <h2  style="text-transform:uppercase"><b>Registrar Nuevo Método Entrega</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Panel de Control</a>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{ route('mantenimiento.vendedor.index') }}">Métodos Entrega</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Registrar</strong>
            </li>
        </ol>
    </div>
</div>


<div class="wrapper wrapper-content animated fadeIn">
    <div class="ibox">
        <div class="ibox-content">
            <form action="{{ route('mantenimiento.metodo_entrega.store') }}" method="POST" id="form_metodo_entrega">
                @csrf
                <div class="row">
                    <div class="col-lg-6 col-md-6 mt-3">
                        <label for="empresa" style="font-weight: bold;">
                            EMPRESA<span style="color: rgb(227, 160, 36);font-weight: bold;">*</span>
                        </label>
                        <input required id="empresa" name="empresa" class="form-control" placeholder="INGRESE UNA EMPRESA"></input>
                    </div>
                    <div class="col-lg-6 col-md-6 mt-3">
                        <label for="sede" style="font-weight: bold;">
                            SEDE<span style="color: rgb(227, 160, 36);font-weight: bold;">*</span>
                        </label>
                        <input required id="sede" name="sede" class="form-control" placeholder="INGRESE UNA SEDE"></input>
                    </div>
                    <div class="col-lg-6 col-md-6 mt-3">
                        <label for="direccion" style="font-weight: bold;">
                            DIRECCIÓN EMPRESA<span style="color: rgb(227, 160, 36);font-weight: bold;">*</span>
                        </label>
                        <input required id="direccion" name="direccion" class="form-control" placeholder="INGRESE UNA DIRECCIÓN"></input>
                    </div>
                    <div class="col-lg-6 col-md-6 mt-3">
                        <label for="tipo_envio" style="font-weight: bold;">
                            TIPO ENVÍO<span style="color: rgb(227, 160, 36);font-weight: bold;">*</span>
                        </label>
                        <select required name="tipo_envio" id="tipo_envio" class="form-control">
                            @foreach ($tipos_envio as $tipo_envio)
                                <option value="{{$tipo_envio->id}}">{{$tipo_envio->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-4 col-xs-12 mt-3">
                        <label class="required" style="font-weight: bold;">Departamento</label>
                        <select required id="departamento" name="departamento" class="select2_form form-control {{ $errors->has('departamento') ? ' is-invalid' : '' }}" style="width: 100%">
                            <option></option>
                            @foreach(departamentos() as $departamento)
                                <option value="{{ $departamento->id }}" {{ (old('departamento') == $departamento->id ? "selected" : "") }} >{{ $departamento->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-4 col-xs-12 mt-3">
                        <label class="required" style="font-weight: bold;">Provincia</label>
                        <select required id="provincia" name="provincia" class="select2_form form-control {{ $errors->has('provincia') ? ' is-invalid' : '' }}" style="width: 100%">
                            <option></option>
                        </select>
                    </div>
                    <div class="form-group col-lg-4 col-xs-12 mt-3">
                        <label class="required" style="font-weight: bold;">Distrito</label>
                        <select required id="distrito" name="distrito" class="select2_form form-control {{ $errors->has('distrito') ? ' is-invalid' : '' }}" style="width: 100%">
                            <option></option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="m-t-md col-12 d-flex justify-content-between">
                        <div class="col-6">
                            <i class="fa fa-exclamation-circle leyenda-required"></i> <small class="leyenda-required">Los campos marcados con asterisco (*) son obligatorios.</small>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary mr-2" onclick="window.location='{{ URL::previous() }}';">REGRESAR</button>
                            <button type="submit" class="btn btn-primary">GRABAR</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@stop
@push('styles')
    <link href="{{ asset('Inspinia/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('Inspinia/css/plugins/steps/jquery.steps.css') }}" rel="stylesheet">
    <style>
        .logo {
            width: 190px;
            height: 190px;
            border-radius: 10%;
            position: absolute;
        }
    </style>
@endpush

@push('scripts')
    <!-- iCheck -->
    <script src="{{ asset('Inspinia/js/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- Data picker -->
    <script src="{{ asset('Inspinia/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
    <!-- Date range use moment.js same as full calendar plugin -->
    <script src="{{ asset('Inspinia/js/plugins/fullcalendar/moment.min.js') }}"></script>
    <!-- Date range picker -->
    <script src="{{ asset('Inspinia/js/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('Inspinia/js/plugins/select2/select2.full.min.js') }}"></script>
    <!-- Steps -->
    <script src="{{ asset('Inspinia/js/plugins/steps/jquery.steps.min.js') }}"></script>
    <!-- Jquery Validate -->
    <script src="{{ asset('Inspinia/js/plugins/validate/jquery.validate.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });

            $(".select2_form").select2({
                placeholder: "SELECCIONAR",
                allowClear: true,
                height: '200px',
                width: '100%',
            });

            $("#tipo_documento").on('change', setLongitudDocumento);

            //$("#documento").on('change', consultarDocumento);

            $("#departamento").on("change", function (e) {
                var departamento_id = this.value;
                if (departamento_id !== "" || departamento_id.length > 0) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        data: {
                            _token: $('input[name=_token]').val(),
                            departamento_id: departamento_id
                        },
                        url: "{{ route('mantenimiento.ubigeo.provincias') }}",
                        success: function (data) {
                            // Limpiamos data
                            $("#provincia").empty();
                            $("#distrito").empty();

                            if (!data.error) {
                                // Mostramos la información
                                if (data.provincias != null) {
                                    $("#provincia").select2({
                                        data: data.provincias
                                    }).val($('#provincia').find(':selected').val()).trigger('change');
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
            });

            $('#provincia').on("change", function (e) {
                var provincia_id = this.value;
                if (provincia_id !== "" || provincia_id.length > 0) {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        data: {
                            _token: $('input[name=_token]').val(),
                            provincia_id: provincia_id
                        },
                        url: "{{ route('mantenimiento.ubigeo.distritos') }}",
                        success: function (data) {
                            // Limpiamos data
                            $("#distrito").empty();

                            if (!data.error) {
                                // Mostramos la información
                                if (data.distritos != null) {
                                    $("#distrito").select2({
                                        data: data.distritos
                                    });
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
            });

            $("#correo_electronico").on('change', validarEmail);

        })

        function setLongitudDocumento() {
            var tipo_documento = $('#tipo_documento').val();
            if (tipo_documento !== undefined && tipo_documento !== null && tipo_documento !== "" && tipo_documento.length > 0) {
                clearDatosPersona();
                switch (tipo_documento) {
                    case 'DNI':
                        $("#documento").attr('maxlength', 8);
                        break;

                    case 'CARNET EXT.':
                        $("#documento").attr('maxlength', 20);
                        break;

                    case 'PASAPORTE':
                        $("#documento").attr('maxlength', 20);
                        break;

                    case 'P. NAC.':
                        $("#documento").attr('maxlength', 25);
                        break;
                }
            }
        }

        function consultarDocumento() {
            var tipo_documento = $('#tipo_documento').val();
            var documento = $('#documento').val();

            // Consultamos nuestra BBDD
            $.ajax({
                dataType : 'json',
                type : 'post',
                url : '{{ route('mantenimiento.vendedor.getDni') }}',
                data : {
                    '_token' : $('input[name=_token]').val(),
                    'tipo_documento' : tipo_documento,
                    'documento' : documento,
                    'id': null
                }
            }).done(function (result){
                if (result.existe) {
                    toastr.error('El DNI ingresado ya se encuentra registrado en la empresa (Vendedor o Colaborador)','Error');
                    $('#documento').focus();
                    $('#estado_documento').val('SIN VERIFICAR');
                    $('#nombres').val('');
                    $('#apellido_paterno').val('');
                    $('#apellido_materno').val('');
                } else {
                    if (tipo_documento === "DNI") {
                        if (documento.length === 8) {
                            consultarAPI(documento);
                        } else {
                            toastr.error('El DNI debe de contar con 8 dígitos','Error');
                            $('#documento').focus();
                        }
                    }else{
                        toastr.error('La consulta Reniec solo aplica para Tipo Documento DNI','Error');
                    }
                }
            });

        }

        function consultarAPI(documento) {
            Swal.fire({
                title: 'Consultar',
                text: "¿Desea consultar DNI a RENIEC?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#1ab394",
                confirmButtonText: 'Si, Confirmar',
                cancelButtonText: "No, Cancelar",
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    var url= '{{ route("getApidni", ":dni")}}';
                    url = url.replace(':dni',documento);
                    return fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText)
                            }
                            return response.json()
                        })
                        .catch(error => {
                            $('#estado_documento').val('SIN VERIFICAR')
                            Swal.showValidationMessage(
                                `Ruc erróneo: ${error}`
                            )
                        })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value !== undefined && result.isConfirmed) {
                    $('#documento').removeClass('is-invalid')
                    camposDNI(result);
                    consultaExitosa();
                }
            });
        }

        function camposDNI(objeto) {
            if (objeto.value === undefined)
                return;

            var nombres = objeto.value.data.nombres;
            var apellido_paterno = objeto.value.data.apellido_paterno;
            var apellido_materno = objeto.value.data.apellido_materno;
           // var codigo_verificacion = objeto.value.data.codVerifica;

            if (nombres !== '-' && nombres !== "NULL" ) {
                $('#nombres').val(nombres);
            }
            if (apellido_paterno !== '-' && apellido_paterno !== "NULL" ) {
                $('#apellido_paterno').val(apellido_paterno);
            }
            if (apellido_materno !== '-' && apellido_materno !== "NULL" ) {
                $('#apellido_materno').val(apellido_materno);
            }
            // if (codigo_verificacion !== '-' && codigo_verificacion !== "NULL" ) {
            //     $('#codigo_verificacion').val(codigo_verificacion);
            // }
            $('#estado_documento').val('ACTIVO')
        }

        function clearDatosPersona() {
            $('#documento').val("");
            $('#nombres').val("");
            $('#apellido_paterno').val("");
            $('#apellido_materno').val("");
            $('#codigo_verificacion').val("");
        }

        // $("#form_registrar_vendedor").steps({
        //     bodyTag: "fieldset",
        //     transitionEffect: "fade",
        //     labels: {
        //         current: "actual paso:",
        //         pagination: "Paginación",
        //         finish: "Finalizar",
        //         next: "Siguiente",
        //         previous: "Anterior",
        //         loading: "Cargando ..."
        //     },
        //     onStepChanging: function (event, currentIndex, newIndex)
        //     {
        //         // Always allow going backward even if the current step contains invalid fields!
        //         if (currentIndex > newIndex)
        //         {
        //             return true;
        //         }

        //         var form = $(this);

        //         // Clean up if user went backward before
        //         if (currentIndex < newIndex)
        //         {
        //             // To remove error styles
        //             $(".body:eq(" + newIndex + ") label.error", form).remove();
        //             $(".body:eq(" + newIndex + ") .error", form).removeClass("error");
        //         }

        //         // Start validation; Prevent going forward if false
        //         return validarDatos(currentIndex + 1);
        //     },
        //     onStepChanged: function (event, currentIndex, priorIndex)
        //     {

        //     },
        //     onFinishing: function (event, currentIndex)
        //     {
        //         var form = $(this);

        //         // Start validation; Prevent form submission if false
        //         return true;
        //     },
        //     onFinished: function (event, currentIndex)
        //     {
        //         var form = $(this);
        //         $("#estado_documento").prop('disabled', false);
        //         // Submit form input
        //         form.submit();
        //     }
        // });

        function validarDatos(paso) {
            //console.log("paso: " + paso);
            switch (paso) {
                case 1:
                    return validarDatosPersonales();

                case 2:
                    return validarDatosContacto();

                case 3:
                    return validarDatosLaborales();

                case 4:
                    return validarDatosAdicionales();

                default:
                    return false;
            }
        }

        function validarDatosPersonales() {
            debugger;
            var tipo_documento = $("#tipo_documento").val();
            var documento = $("#documento").val();
            var nombres = $("#nombres").val();
            var apellido_paterno = $("#apellido_paterno").val();
            var apellido_materno = $("#apellido_materno").val();
            var fecha_nacimiento = $("#fecha_nacimiento").find("input").val();
            $('.datepicker-days').removeAttr("style").hide();
            var sexo = $("#sexo_hombre").is(':checked') ? 'H' : 'M';

            if ((tipo_documento !== null && tipo_documento.length === 0) || documento.length === 0 || nombres.length === 0 || apellido_paterno.length === 0
                || apellido_materno.length === 0 || sexo.length === 0 || fecha_nacimiento.length === 0 ) {
                toastr.error('Complete la información de los campos obligatorios (*)','Error');
                return false;
            }

            switch (tipo_documento) {
                case 'DNI':
                    if (documento.length !== 8) {
                        toastr.error('El DNI debe de contar con 8 dígitos','Error');
                        return false;
                    }
                    break;

                case 'CARNET EXT.':
                    if (documento.length !== 20) {
                        toastr.error('El CARNET DE EXTRANJERIA debe de contar con 20 dígitos','Error');
                        return false;
                    }
                    break;

                case 'PASAPORTE':
                    if (documento.length !== 20) {
                        toastr.error('El PASAPORTE debe de contar con 20 dígitos','Error');
                        return false;
                    }
                    break;

                case 'P. NAC.':
                    if (documento.length !== 25) {
                        toastr.error('La PARTIDAD DE NACIMIENTO debe de contar con 25 dígitos','Error');
                        return false;
                    }
                    break;
            }

            return true;
        }

        function validarDatosContacto() {
            var departamento = $("#departamento").val();
            var provincia = $("#provincia").val();
            var distrito = $("#distrito").val();
            var direccion = $("#direccion").val();
            var correo_electronico = $("#correo_electronico").val();
            var telefono_movil = $("#telefono_movil").val();

            if ((departamento === null || departamento.length === 0) || (provincia === null || provincia.length === 0)
                || (distrito === null || distrito.length === 0)  || direccion.length === 0
                || correo_electronico.length === 0 || telefono_movil.length === 0) {
                toastr.error('Complete la información de los campos obligatorios (*)','Error');
                return false;
            }
            return true;
        }

        function validarDatosLaborales() {
            debugger;
            var area = $("#area").val();
            var profesion = $("#profesion").val();
            var cargo = $("#cargo").val();
            var sueldo = $("#sueldo").val();
            var sueldo_bruto = $("#sueldo_bruto").val();
            var sueldo_neto = $("#sueldo_neto").val();
            var moneda_sueldo = $("#moneda_sueldo").val();
            var tipo_banco = $("#tipo_banco").val();
            var numero_cuenta = $("#numero_cuenta").val();
            var zona = $("#zona").val();
            var comision = $("#comision").val();
            var moneda_comision = $("#moneda_comision").val();

            var fecha_inicio_actividad = $("#fecha_inicio_actividad").find("input").val();
            var fecha_fin_actividad = $("#fecha_fin_actividad").find("input").val();
            var fecha_inicio_planilla = $("#fecha_inicio_planilla").find("input").val();
            var fecha_fin_planilla = $("#fecha_fin_planilla").find("input").val();
            $('.datepicker-days').removeAttr("style").hide();

            if ((area === null || area.length === 0) || (profesion === null || profesion.length === 0) || (cargo === null || cargo.length === 0)
                || sueldo.length === 0 || sueldo_bruto.length === 0 || sueldo_neto.length === 0 || (moneda_sueldo === null || moneda_sueldo.length === 0)
                || fecha_inicio_actividad.length === 0 || zona.length === 0 || comision.length === 0 || moneda_comision.length === 0) {
                toastr.error('Complete la información de los campos obligatorios (*)','Error');
                return false;
            }
            /*if (fecha_inicio_actividad.length > 0 && fecha_fin_actividad.length > 0) {
                if (fechaInicioActividad > fechaFinActividad) {
                    toastr.error('La fecha de inicio de actividad no debe ser mayor a la fecha fin de actividad','Error');
                    return false;
                }
            }
            if (fecha_inicio_planilla.length > 0 && fecha_fin_planilla.length > 0) {
                if (fechaInicioPlanilla > fechaFinPlanilla) {
                    toastr.error('La fecha de inicio de planilla no debe ser mayor a la fecha fin de planilla','Error');
                    return false;
                }
            }*/

            return true;
        }

        function validarEmail() {
            if (!emailIsValid($('#correo_electronico').val())) {
                toastr.error('El formato del email es incorrecto','Error');
                $('#correo_electronico').focus();
            }
        }

        /* Limpiar imagen */
        $('#limpiar_logo').click(function () {
            $('.logo').attr("src", "{{asset('storage/empresas/logos/default.png')}}")
            var fileName="Seleccionar"
            $('.custom-file-label').addClass("selected").html(fileName);
            $('#imagen').val('')
        })

        $('.custom-file-input').on('change', function() {
            var fileInput = document.getElementById('imagen');
            var filePath = fileInput.value;
            var allowedExtensions = /(.jpg|.jpeg|.png)$/i;
            $imagenPrevisualizacion = document.querySelector(".logo");

            if(allowedExtensions.exec(filePath)){
                var userFile = document.getElementById('imagen');
                userFile.src = URL.createObjectURL(event.target.files[0]);
                var data = userFile.src;
                $imagenPrevisualizacion.src = data
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            }else{
                toastr.error('Extensión inválida, formatos admitidos (.jpg . jpeg . png)','Error');
                $('.logo').attr("src", "{{asset('storage/empresas/logos/default.png')}}")
            }
        });

    </script>
@endpush
